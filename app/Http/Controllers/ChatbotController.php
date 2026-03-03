<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * System prompts customized by user role.
     * Provides context-aware responses based on user permissions.
     */
    private function getSystemPromptForRole(string $roleName): string
    {
        $baseContext = "Eres el asistente AI de SIMS (Sistema Inteligente de Movilidad Sostenible), una plataforma de carsharing eléctrico. ";

        $roleContexts = [
            'Admin' => $baseContext . "El usuario actual es un Administrador con acceso completo al sistema. Puede:
                - Gestionar usuarios, roles y permisos
                - Administrar la flota de vehículos (crear, editar, eliminar)
                - Ver y gestionar todas las reservas del sistema
                - Responder tickets de soporte
                - Ver estadísticas y métricas del sistema
                Ayúdale con consultas técnicas, gestión administrativa y resolución de problemas del sistema.",

            'Client' => $baseContext . "El usuario actual es un Cliente del servicio. Puede:
                - Buscar y reservar vehículos disponibles
                - Ver el mapa de vehículos cercanos
                - Gestionar sus propias reservas (activar, cancelar, finalizar)
                - Crear tickets de soporte para reportar incidencias
                - Consultar su historial de viajes
                Ayúdale con información sobre cómo usar el servicio, resolver dudas sobre reservas y orientarle en el uso de la app.",

            'Maintenance' => $baseContext . "El usuario actual es personal de Mantenimiento. Puede:
                - Ver y gestionar el estado técnico de los vehículos
                - Reportar y resolver incidencias técnicas
                - Actualizar el estado de disponibilidad de vehículos
                Ayúdale con información técnica de la flota y procedimientos de mantenimiento.",
        ];

        return $roleContexts[$roleName] ?? $roleContexts['Client'];
    }

    /**
     * Handle the chatbot request and proxy it to Open WebUI.
     * Includes role-based context to provide relevant responses.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:user,assistant,system',
            'messages.*.content' => 'required|string',
        ]);

        $apiKey = env('OPEN_WEBUI_API_KEY');
        $baseUrl = env('OPEN_WEBUI_BASE_URL');
        $model = env('OPEN_WEBUI_MODEL');

        // Validar configuración
        $missingConfig = [];
        if (!$apiKey) $missingConfig[] = 'OPEN_WEBUI_API_KEY';
        if (!$baseUrl) $missingConfig[] = 'OPEN_WEBUI_BASE_URL';
        if (!$model) $missingConfig[] = 'OPEN_WEBUI_MODEL';

        if (!empty($missingConfig)) {
            Log::error('Chatbot configuration missing', ['missing' => $missingConfig]);
            return response()->json([
                'error' => 'El chatbot no está configurado correctamente. Faltan las siguientes variables de entorno: ' . implode(', ', $missingConfig)
            ], 500);
        }

        // Get user role for context-aware responses
        $user = $request->user();
        $roleName = 'Client'; // Default role
        
        if ($user && $user->roles->isNotEmpty()) {
            $roleName = $user->roles->first()->name;
        }

        // Build messages with role-specific system prompt
        $messages = $request->messages;
        
        // Check if first message is already a system message
        $hasSystemMessage = !empty($messages) && $messages[0]['role'] === 'system';
        
        if (!$hasSystemMessage) {
            // Prepend role-specific system prompt
            array_unshift($messages, [
                'role' => 'system',
                'content' => $this->getSystemPromptForRole($roleName)
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
            ]);

            if ($response->failed()) {
                Log::error('Open WebUI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $baseUrl . '/chat/completions',
                ]);
                
                $errorMessage = 'Error al conectar con el servicio de chatbot.';
                if ($response->status() === 401) {
                    $errorMessage = 'API Key inválida. Verifica OPEN_WEBUI_API_KEY en el archivo .env';
                } elseif ($response->status() === 404) {
                    $errorMessage = 'Modelo no encontrado. Verifica OPEN_WEBUI_MODEL en el archivo .env (actual: ' . $model . ')';
                } elseif ($response->status() === 503) {
                    $errorMessage = 'El servicio de chatbot no está disponible. Verifica que Open WebUI esté ejecutándose en: ' . $baseUrl;
                }
                
                return response()->json([
                    'error' => $errorMessage
                ], $response->status());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Chatbot exception', [
                'message' => $e->getMessage(),
                'url' => $baseUrl . '/chat/completions',
            ]);
            
            $errorMessage = 'Error inesperado al conectar con el chatbot.';
            if (str_contains($e->getMessage(), 'Could not resolve host') || str_contains($e->getMessage(), 'Connection refused')) {
                $errorMessage = 'No se puede conectar con el servidor de chatbot. Verifica que Open WebUI esté ejecutándose en: ' . $baseUrl;
            }
            
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
}
