<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SIMS - Sistema Inteligente de Movilidad Sostenible. Gestión de flotas de vehículos eléctricos con IoT en tiempo real.">

    <title>SIMS - Sistema Inteligente de Movilidad Sostenible</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>

<body class="antialiased bg-gray-950 text-white font-sans">
    
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gray-950/80 backdrop-blur-xl border-b border-gray-800">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-2xl bg-white p-1 shadow-xl shadow-indigo-500/30">
                    <img src="/logo.png" alt="SIMS Logo" class="h-full w-full object-contain" />
                </div>
                <span class="text-xl font-bold text-white">SIMS</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="#caracteristicas" class="hidden sm:block text-sm font-medium text-gray-400 hover:text-white transition-colors">Características</a>
                <a href="#video" class="hidden sm:block text-sm font-medium text-gray-400 hover:text-white transition-colors">Demo</a>
                <a href="https://frontend-nine-orcin-waqisje40z.vercel.app/map" class="hidden sm:block text-sm font-medium text-gray-400 hover:text-white transition-colors">Mapa</a>
                <a href="https://frontend-nine-orcin-waqisje40z.vercel.app/login" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-2xl text-sm font-bold uppercase tracking-widest transition-all shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50">
                    Acceder
                </a>
            </div>
        </div>
    </nav>

    <main class="pt-16">
        <!-- Hero Section -->
        <div class="relative isolate overflow-hidden min-h-screen flex items-center">
            <!-- Background gradient -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-gradient-to-br from-indigo-600/20 to-purple-700/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-gradient-to-tr from-indigo-600/10 to-transparent rounded-full blur-3xl"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">
                <div class="lg:flex lg:items-center lg:gap-16">
                    <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-xl lg:shrink-0">
                        <div class="inline-flex items-center gap-2 mb-8">
                            <span class="inline-flex items-center rounded-full bg-indigo-500/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-indigo-400 ring-1 ring-inset ring-indigo-500/30">
                                <span class="mr-2 h-1.5 w-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                                Sprint 5 - Producción
                            </span>
                        </div>
                        
                        <h1 class="text-4xl font-black tracking-tight text-white sm:text-6xl leading-tight">
                            Movilidad sostenible
                            <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent"> inteligente</span>
                        </h1>
                        
                        <p class="mt-6 text-lg leading-8 text-gray-400">
                            Gestiona tu flota de vehículos eléctricos en tiempo real. Seguimiento GPS, control remoto IoT, reservas automatizadas y soporte con inteligencia artificial.
                        </p>
                        
                        <div class="mt-10 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <a href="https://frontend-nine-orcin-waqisje40z.vercel.app/login" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50 active:scale-95">
                                Iniciar Sesión
                            </a>
                            <a href="https://frontend-nine-orcin-waqisje40z.vercel.app/map" class="inline-flex items-center gap-2 text-gray-400 hover:text-white font-medium transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Ver mapa público
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="mt-12 grid grid-cols-3 gap-8 border-t border-gray-800 pt-8">
                            <div>
                                <p class="text-3xl font-black text-indigo-400">IoT</p>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mt-1">Tiempo real</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-indigo-400">100%</p>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mt-1">Eléctrico</p>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-indigo-400">24/7</p>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mt-1">Disponible</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dashboard Preview -->
                    <div class="mt-16 lg:mt-0 lg:ml-10 flex-1">
                        <div class="relative">
                            <div class="absolute -inset-4 bg-gradient-to-r from-indigo-600/30 to-purple-600/30 rounded-[2rem] blur-2xl"></div>
                            <div class="relative bg-gray-900 rounded-[2rem] border border-gray-800 shadow-2xl overflow-hidden">
                                <div class="h-8 bg-gray-900 border-b border-gray-800 flex items-center px-4 gap-2">
                                    <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                                </div>
                                <img src="https://tailwindcss.com/plus-assets/img/component-images/project-app-screenshot.png" 
                                     alt="Panel de Control SIMS" 
                                     class="w-full" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="caracteristicas" class="py-24 sm:py-32 border-t border-gray-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-xs font-black uppercase tracking-widest text-indigo-400 mb-4">Plataforma completa</p>
                    <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                        Todo lo que necesitas para gestionar tu flota
                    </h2>
                    <p class="mt-6 text-lg leading-8 text-gray-400">
                        Una solución integral que combina hardware IoT con software inteligente para optimizar la movilidad urbana sostenible.
                    </p>
                </div>
                
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-6 lg:max-w-none lg:grid-cols-3">
                        
                        <!-- Feature 1 -->
                        <div class="bg-gray-900 rounded-[2rem] p-8 border border-gray-800 hover:border-gray-700 transition-colors">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 shadow-lg shadow-indigo-500/30 mb-6">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <dt class="text-lg font-bold text-white">
                                Seguimiento GPS
                            </dt>
                            <dd class="mt-3 text-sm text-gray-400 leading-relaxed">
                                Localiza todos tus vehículos al instante con dispositivos Raspberry Pi y sensores GPS de alta precisión.
                            </dd>
                        </div>

                        <!-- Feature 2 -->
                        <div class="bg-gray-900 rounded-[2rem] p-8 border border-gray-800 hover:border-gray-700 transition-colors">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-green-600 to-emerald-700 shadow-lg shadow-green-500/30 mb-6">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <dt class="text-lg font-bold text-white">
                                Control remoto IoT
                            </dt>
                            <dd class="mt-3 text-sm text-gray-400 leading-relaxed">
                                Enciende, apaga y monitoriza tus vehículos de forma remota mediante comandos seguros por WebSocket.
                            </dd>
                        </div>

                        <!-- Feature 3 -->
                        <div class="bg-gray-900 rounded-[2rem] p-8 border border-gray-800 hover:border-gray-700 transition-colors">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-600 to-orange-700 shadow-lg shadow-amber-500/30 mb-6">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <dt class="text-lg font-bold text-white">
                                Sistema de reservas
                            </dt>
                            <dd class="mt-3 text-sm text-gray-400 leading-relaxed">
                                Gestiona reservas, activa viajes y registra automáticamente el uso de cada vehículo de tu flota.
                            </dd>
                        </div>

                        <!-- Feature 4 -->
                        <div class="bg-gray-900 rounded-[2rem] p-8 border border-gray-800 hover:border-gray-700 transition-colors">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-600 to-pink-700 shadow-lg shadow-purple-500/30 mb-6">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <dt class="text-lg font-bold text-white">
                                Asistente con IA
                            </dt>
                            <dd class="mt-3 text-sm text-gray-400 leading-relaxed">
                                Chatbot inteligente con contexto personalizado según tu rol para resolver dudas al instante.
                            </dd>
                        </div>

                        <!-- Feature 5 -->
                        <div class="bg-gray-900 rounded-[2rem] p-8 border border-gray-800 hover:border-gray-700 transition-colors">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-rose-600 to-red-700 shadow-lg shadow-rose-500/30 mb-6">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <dt class="text-lg font-bold text-white">
                                Seguridad avanzada
                            </dt>
                            <dd class="mt-3 text-sm text-gray-400 leading-relaxed">
                                Autenticación con tokens, control de acceso por roles y rate limiting para proteger tu sistema.
                            </dd>
                        </div>

                        <!-- Feature 6 -->
                        <div class="bg-gray-900 rounded-[2rem] p-8 border border-gray-800 hover:border-gray-700 transition-colors">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-600 to-blue-700 shadow-lg shadow-cyan-500/30 mb-6">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <dt class="text-lg font-bold text-white">
                                Soporte técnico
                            </dt>
                            <dd class="mt-3 text-sm text-gray-400 leading-relaxed">
                                Sistema de tickets integrado con conversaciones bidireccionales entre usuarios y administradores.
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Video Section -->
        <div id="video" class="py-24 sm:py-32 border-t border-gray-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
                <p class="text-xs font-black uppercase tracking-widest text-indigo-400 mb-4">Demo</p>
                <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                    Descubre SIMS en acción
                </h2>
                <p class="mt-4 text-lg text-gray-400 max-w-2xl mx-auto">
                    Mira nuestro vídeo comercial para entender cómo estamos transformando la gestión de flotas de vehículos eléctricos.
                </p>
                
                <div class="mt-12 relative max-w-4xl mx-auto">
                    <div class="absolute -inset-4 bg-gradient-to-r from-indigo-600/30 to-purple-600/30 rounded-[2rem] blur-2xl"></div>
                    <div class="relative bg-gray-900 rounded-[2rem] border border-gray-800 overflow-hidden">
                        <div class="aspect-video flex items-center justify-center bg-gray-900">
                            <div class="text-center px-8">
                                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-800 flex items-center justify-center border border-gray-700">
                                    <svg class="w-10 h-10 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">
                                    [ Insertar vídeo comercial aquí ]
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-24 sm:py-32 border-t border-gray-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="relative bg-gradient-to-br from-indigo-600 to-purple-700 rounded-[2.5rem] px-8 py-16 sm:px-16 sm:py-24 overflow-hidden">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.05%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
                    <div class="relative text-center">
                        <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                            ¿Listo para empezar?
                        </h2>
                        <p class="mt-4 text-lg text-indigo-100 max-w-2xl mx-auto">
                            Accede a la plataforma y descubre una nueva forma de gestionar la movilidad sostenible.
                        </p>
                        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="https://frontend-nine-orcin-waqisje40z.vercel.app/login" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-100 text-indigo-700 px-8 py-4 rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-xl active:scale-95">
                                Acceder ahora
                            </a>
                            <a href="https://frontend-nine-orcin-waqisje40z.vercel.app/register" class="inline-flex items-center gap-2 text-white hover:text-indigo-100 font-bold transition-colors">
                                Crear cuenta gratuita
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-800">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-white p-1 shadow-lg shadow-indigo-500/30">
                        <img src="/logo.png" alt="SIMS Logo" class="h-full w-full object-contain" />
                    </div>
                    <span class="text-xl font-bold text-white">SIMS</span>
                </div>
                
                <div class="flex items-center gap-6">
                    <a href="https://github.com/orgs/Sprint4-ProjectSIMS-Team1" target="_blank" class="text-gray-500 hover:text-indigo-400 transition-colors">
                        <span class="sr-only">GitHub</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                        </svg>
                    </a>
                </div>
                
                <p class="text-sm text-gray-500 font-medium">
                    &copy; 2026 SIMS Project. Licencia EUPL.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>