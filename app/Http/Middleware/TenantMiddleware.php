<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Extraer el subdominio (ej: 'testclient' de 'testclient.localhost')
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        // Si es el dominio principal (ej: localhost o api.sims.com), no hacemos nada
        if ($subdomain === 'localhost' || $subdomain === '127' || $subdomain === 'api') {
            return $next($request);
        }

        // 2. Buscar al tenant en la base de datos CENTRAL
        // Usamos la conexión por defecto (que es la central/landlord)
        $tenant = DB::table('tenants')->where('id', $subdomain)->first();

        if (!$tenant) {
            return response()->json(['error' => "Tenant '{$subdomain}' no encontrado."], 404);
        }

        // 3. CAMBIAR LA CONEXIÓN DINÁMICAMENTE
        // Clonamos la configuración de pgsql pero cambiamos el nombre de la base de datos
        $tenantConfig = config('database.connections.pgsql');
        $tenantConfig['database'] = $tenant->db_name;

        // Establecemos la nueva conexión 'tenant'
        Config::set('database.connections.tenant', $tenantConfig);
        
        // Hacemos que 'tenant' sea la conexión por defecto para esta petición
        DB::purge('tenant');
        DB::setDefaultConnection('tenant');

        return $next($request);
    }
}
