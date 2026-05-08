<!DOCTYPE html>
<html lang="es" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Características Técnicas - Fleetly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { brand: { primary: { 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5' } } } } } }
    </script>
</head>
<body class="bg-gray-950 text-white font-sans antialiased">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gray-950/80 backdrop-blur-xl border-b border-gray-800">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 flex items-center justify-between h-20">
            <a href="/" class="flex items-center gap-3 group">
                <span class="text-2xl font-black text-white tracking-tighter">Fleetly</span>
            </a>
            <div class="flex items-center gap-8">
                <a href="/" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-colors">Volver</a>
                <a href="https://fleetly.deltahost.asix2.iesmontsia.cat/login" class="inline-flex items-center gap-2 bg-brand-primary-600 hover:bg-brand-primary-700 text-white px-7 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">Acceder</a>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center mb-24">
                <h1 class="text-6xl font-black tracking-tighter mb-6">Tu flota, bajo <span class="text-brand-primary-500">control total.</span></h1>
                <p class="text-xl text-gray-400 max-w-3xl mx-auto font-medium">Diseñamos Fleetly para ser simple por fuera y potente por dentro. Gestiona cientos de vehículos con la misma facilidad que uno solo.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Hardware Specs -->
                <div class="lg:col-span-2 space-y-12">
                    <section class="bg-gray-900/40 p-12 rounded-[3rem] border border-white/5">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="material-icons text-brand-primary-400 text-4xl">bolt</span>
                            <h2 class="text-3xl font-black uppercase tracking-tight text-white">Control Instantáneo</h2>
                        </div>
                        <p class="text-gray-400 mb-8 leading-relaxed text-lg">Olvídate de las esperas. Nuestra tecnología <strong>Ultra-Link</strong> permite que las órdenes que envías desde el panel se ejecuten en el vehículo en menos de un segundo.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white/5 p-6 rounded-2xl">
                                <h4 class="font-black text-brand-primary-400 text-sm mb-2 uppercase tracking-widest">Apertura Remota</h4>
                                <p class="text-sm text-gray-300">Desbloquea puertas y activa el encendido de forma segura desde cualquier lugar del mundo.</p>
                            </div>
                            <div class="bg-white/5 p-6 rounded-2xl">
                                <h4 class="font-black text-brand-primary-400 text-sm mb-2 uppercase tracking-widest">Estado en Vivo</h4>
                                <p class="text-sm text-gray-300">Visualiza el nivel de batería, ubicación exacta y salud del motor en tiempo real.</p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-gray-900/40 p-12 rounded-[3rem] border border-white/5">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="material-icons text-brand-primary-400 text-4xl">shield</span>
                            <h2 class="text-3xl font-black uppercase tracking-tight text-white">Privacidad y Seguridad</h2>
                        </div>
                        <p class="text-gray-400 mb-8 leading-relaxed text-lg">Tus datos son solo tuyos. Fleetly utiliza un sistema de aislamiento de grado bancario para que la información de tu empresa esté siempre protegida y separada del resto.</p>
                        <div class="space-y-6">
                            <div class="flex gap-6">
                                <span class="material-icons text-brand-primary-500">verified_user</span>
                                <div>
                                    <h4 class="font-bold text-white mb-1">Cifrado de Extremo a Extremo</h4>
                                    <p class="text-sm text-gray-400">Toda la comunicación entre el vehículo y el servidor está protegida por los protocolos más avanzados de la industria.</p>
                                </div>
                            </div>
                            <div class="flex gap-6">
                                <span class="material-icons text-brand-primary-500">cloud_done</span>
                                <div>
                                    <h4 class="font-bold text-white mb-1">Fiabilidad del 99.9%</h4>
                                    <p class="text-sm text-gray-400">Nuestra infraestructura está diseñada para no fallar nunca, asegurando que tu flota esté operativa 24/7.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Sidebar Features -->
                <div class="space-y-8">
                    <div class="bg-brand-primary-600 p-10 rounded-[3rem] shadow-2xl">
                        <h3 class="text-xl font-black mb-4 uppercase tracking-tight text-white">Tu Marca, Tus Colores</h3>
                        <p class="text-sm text-brand-primary-100 mb-6 font-medium">Personaliza el panel de control para que tus empleados y clientes sientan que el software es 100% tuyo.</p>
                        <ul class="text-xs space-y-3 font-bold uppercase tracking-widest text-white/90">
                            <li class="flex items-center gap-2"><span class="material-icons text-sm">palette</span> Logo y Colores Propios</li>
                            <li class="flex items-center gap-2"><span class="material-icons text-sm">language</span> Subdominio de Empresa</li>
                            <li class="flex items-center gap-2"><span class="material-icons text-sm">support_agent</span> Soporte Personalizado</li>
                        </ul>
                    </div>
                    <div class="bg-white/5 p-10 rounded-[3rem] border border-white/10">
                        <h3 class="text-xl font-black mb-4 uppercase tracking-tight text-white">Hardware Simple</h3>
                        <p class="text-sm text-gray-400 mb-6">Instalación Plug & Play compatible con la mayoría de vehículos eléctricos del mercado.</p>
                        <ul class="text-xs space-y-3 font-bold uppercase tracking-widest text-gray-500">
                            <li class="flex items-center gap-2 text-gray-300"><span class="material-icons text-sm text-brand-primary-400">settings</span> Fácil Instalación</li>
                            <li class="flex items-center gap-2 text-gray-300"><span class="material-icons text-sm text-brand-primary-400">cell_tower</span> Conexión Global</li>
                            <li class="flex items-center gap-2 text-gray-300"><span class="material-icons text-sm text-brand-primary-400">battery_charging_full</span> Bajo Consumo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
