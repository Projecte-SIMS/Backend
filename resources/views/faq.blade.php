<!DOCTYPE html>
<html lang="es" class="dark scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Centro de Ayuda - Fleetly</title>
    <link rel="icon" type="image/svg+xml" href="/brand/isotipo/fleetly_isotip_negre.svg">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            primary: {
                                50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc',
                                400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca',
                                800: '#3730a3', 900: '#312e81', 950: '#1e1b4b',
                            }
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="antialiased bg-gray-950 text-white font-sans">
    
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gray-950/80 backdrop-blur-xl border-b border-gray-800">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 flex items-center justify-between h-20">
            <a href="/" class="flex items-center gap-3 group">
                <div class="h-10 w-10 rounded-2xl bg-white p-1 shadow-xl shadow-brand-primary-500/30 transition-transform group-hover:scale-110">
                    <img src="/logo.png" alt="Fleetly Logo" class="h-full w-full object-contain" />
                </div>
                <span class="text-2xl font-black text-white tracking-tighter">Fleetly</span>
            </a>
            <div class="flex items-center gap-8">
                <a href="/" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-colors">Volver a Inicio</a>
                <a href="https://fleetly.deltahost.asix2.iesmontsia.cat/login" class="inline-flex items-center gap-2 bg-brand-primary-600 hover:bg-brand-primary-700 text-white px-7 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">
                    Acceder
                </a>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-20">
                <h1 class="text-5xl font-black tracking-tighter text-white sm:text-7xl mb-6">¿Cómo podemos ayudarte?</h1>
                <p class="text-xl text-gray-400 max-w-2xl mx-auto font-medium">Busca respuestas detalladas sobre el funcionamiento de nuestra plataforma de movilidad inteligente.</p>
            </div>

            <!-- FAQ Grid inspired by Free2move -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Section: Car Sharing (Coche compartido) -->
                <div class="bg-gray-900/40 rounded-[2.5rem] border border-white/5 p-10 hover:border-brand-primary-500/30 transition-all group">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-primary-500/10 text-brand-primary-400 mb-8">
                        <span class="material-icons">directions_car</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tight">Car Sharing</h3>
                    <ul class="space-y-4">
                        <li><a href="/faq/car-sharing" class="text-gray-400 hover:text-brand-primary-400 text-sm font-bold block transition-colors">¿Cómo me registro en Fleetly?</a></li>
                        <li><a href="/faq/car-sharing" class="text-gray-400 hover:text-brand-primary-400 text-sm font-bold block transition-colors">¿Qué tipo de vehículos puedo usar?</a></li>
                        <li><a href="/faq/car-sharing" class="text-gray-400 hover:text-brand-primary-400 text-sm font-bold block transition-colors">¿Cómo abro el coche desde la app?</a></li>
                        <li><a href="/faq/car-sharing" class="text-gray-400 hover:text-brand-primary-400 text-sm font-bold block transition-colors underline decoration-brand-primary-500/30 underline-offset-4">Ver las 12 preguntas →</a></li>
                    </ul>
                </div>

                <!-- Section: Reservas y Alquiler -->
                <div class="bg-gray-900/40 rounded-[2.5rem] border border-white/5 p-10 hover:border-green-500/30 transition-all group">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500/10 text-green-400 mb-8">
                        <span class="material-icons">event_available</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tight">Reservas</h3>
                    <ul class="space-y-4">
                        <li><a href="/faq/reservas" class="text-gray-400 hover:text-green-400 text-sm font-bold block transition-colors">¿Tengo que pagar fianza?</a></li>
                        <li><a href="/faq/reservas" class="text-gray-400 hover:text-green-400 text-sm font-bold block transition-colors">¿Puedo reservar con antelación?</a></li>
                        <li><a href="/faq/reservas" class="text-gray-400 hover:text-green-400 text-sm font-bold block transition-colors underline decoration-green-500/30 underline-offset-4">Ver las 8 preguntas →</a></li>
                    </ul>
                </div>

                <!-- Section: Repostar y Cargar -->
                <div class="bg-gray-900/40 rounded-[2.5rem] border border-white/5 p-10 hover:border-yellow-500/30 transition-all group">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-500/10 text-yellow-400 mb-8">
                        <span class="material-icons">ev_station</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tight">Carga y Energía</h3>
                    <ul class="space-y-4">
                        <li><a href="/faq/carga" class="text-gray-400 hover:text-yellow-400 text-sm font-bold block transition-colors">¿Cómo cargo un coche eléctrico?</a></li>
                        <li><a href="/faq/carga" class="text-gray-400 hover:text-yellow-400 text-sm font-bold block transition-colors underline decoration-yellow-500/30 underline-offset-4">Ver las 6 preguntas →</a></li>
                    </ul>
                </div>

                <!-- Section: Cuenta y Pagos -->
                <div class="bg-gray-900/40 rounded-[2.5rem] border border-white/5 p-10 hover:border-purple-500/30 transition-all group">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500/10 text-purple-400 mb-8">
                        <span class="material-icons">payments</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tight">Pagos y Facturas</h3>
                    <ul class="space-y-4">
                        <li><a href="/faq/pagos" class="text-gray-400 hover:text-purple-400 text-sm font-bold block transition-colors">¿Qué métodos de pago aceptáis?</a></li>
                        <li><a href="/faq/pagos" class="text-gray-400 hover:text-purple-400 text-sm font-bold block transition-colors underline decoration-purple-500/30 underline-offset-4">Ver las 10 preguntas →</a></li>
                    </ul>
                </div>

                <!-- Section: Ayuda Rápida (Emergencias) -->
                <div class="bg-gray-900/40 rounded-[2.5rem] border border-white/5 p-10 hover:border-red-500/30 transition-all group">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-500/10 text-red-400 mb-8">
                        <span class="material-icons">report_problem</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tight">Ayuda Rápida</h3>
                    <ul class="space-y-4">
                        <li><a href="/faq/ayuda" class="text-gray-400 hover:text-red-400 text-sm font-bold block transition-colors">¿Qué hago si tengo un accidente?</a></li>
                        <li><a href="/faq/ayuda" class="text-gray-400 hover:text-red-400 text-sm font-bold block transition-colors underline decoration-red-500/30 underline-offset-4">Asistencia 24/7 →</a></li>
                    </ul>
                </div>

                <!-- Section: Empresas -->
                <div class="bg-gray-900/40 rounded-[2.5rem] border border-white/5 p-10 hover:border-blue-500/30 transition-all group">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400 mb-8">
                        <span class="material-icons">business</span>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-6 uppercase tracking-tight">Para Empresas</h3>
                    <ul class="space-y-4">
                        <li><a href="/faq/empresas" class="text-gray-400 hover:text-blue-400 text-sm font-bold block transition-colors">¿Cómo registro mi empresa?</a></li>
                        <li><a href="/faq/empresas" class="text-gray-400 hover:text-blue-400 text-sm font-bold block transition-colors underline decoration-blue-500/30 underline-offset-4">Fleetly for Business →</a></li>
                    </ul>
                </div>

            </div>

            <!-- Detailed FAQ List (Expandable) -->
            <div class="mt-32">
                <h2 class="text-4xl font-black tracking-tighter text-white mb-12">Todas las preguntas frecuentes</h2>
                <div class="space-y-4">
                    <!-- Example Items inspired by Free2move Car Sharing -->
                    <details class="group bg-gray-900/40 rounded-3xl border border-white/5 overflow-hidden transition-all">
                        <summary class="flex items-center justify-between p-8 cursor-pointer list-none">
                            <span class="text-lg font-bold text-white group-open:text-brand-primary-400 transition-colors">¿En qué aeropuertos se ubican los coches de Fleetly?</span>
                            <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="px-8 pb-8 text-gray-400 leading-relaxed font-medium">
                            Nuestra flota está presente en los principales hubs de transporte. Actualmente puedes encontrar zonas Fleetly en Madrid-Barajas, Barcelona-El Prat y Palma de Mallorca. Busca el icono de "Avión" en el mapa de la app para ver las plazas reservadas.
                        </div>
                    </details>

                    <details class="group bg-gray-900/40 rounded-3xl border border-white/5 overflow-hidden transition-all">
                        <summary class="flex items-center justify-between p-8 cursor-pointer list-none">
                            <span class="text-lg font-bold text-white group-open:text-brand-primary-400 transition-colors">¿Puedo aparcar en un parking privado o centro comercial?</span>
                            <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="px-8 pb-8 text-gray-400 leading-relaxed font-medium">
                            Solo puedes finalizar el alquiler en parkings públicos dentro de la zona operativa o en parkings asociados señalizados en la app. Si aparcas en un parking privado no asociado, los costes de estancia y posibles multas correrán a cargo del usuario.
                        </div>
                    </details>

                    <details class="group bg-gray-900/40 rounded-3xl border border-white/5 overflow-hidden transition-all">
                        <summary class="flex items-center justify-between p-8 cursor-pointer list-none">
                            <span class="text-lg font-bold text-white group-open:text-brand-primary-400 transition-colors">¿Cómo cargo un coche eléctrico si no hay cargadores cerca?</span>
                            <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="px-8 pb-8 text-gray-400 leading-relaxed font-medium">
                            Fleetly cuenta con un equipo de mantenimiento que se encarga de cargar los vehículos. Sin embargo, si decides cargarlo tú mismo en una estación asociada, recibirás crédito Fleetly como recompensa en tu cuenta.
                        </div>
                    </details>

                    <details class="group bg-gray-900/40 rounded-3xl border border-white/5 overflow-hidden transition-all">
                        <summary class="flex items-center justify-between p-8 cursor-pointer list-none">
                            <span class="text-lg font-bold text-white group-open:text-brand-primary-400 transition-colors">¿Puedo compartir mi cuenta con un familiar?</span>
                            <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <div class="px-8 pb-8 text-gray-400 leading-relaxed font-medium">
                            No. Las cuentas son personales e intransferibles por motivos de seguridad y seguros. Si otra persona conduce con tu cuenta, la cobertura del seguro quedará anulada y podrías enfrentarte a una sanción grave.
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-800/50 bg-gray-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 py-20">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="flex items-center gap-5">
                    <div class="h-14 w-14 rounded-2xl bg-white p-1 shadow-lg shadow-brand-primary-500/20">
                        <img src="/logo.png" alt="Fleetly Logo" class="h-full w-full object-contain" />
                    </div>
                    <div>
                        <span class="text-3xl font-black text-white tracking-tighter block leading-none">Fleetly</span>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-[0.4em] mt-1 block">Project SIMS</span>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-sm text-gray-500 font-black uppercase tracking-widest">&copy; 2026 Fleetly Project</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</body>
</html>
