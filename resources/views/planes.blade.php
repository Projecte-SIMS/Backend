<!DOCTYPE html>
<html lang="es" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Planes y Precios - Fleetly</title>
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
                <a href="https://frontend-phi-seven-21.vercel.app/login" class="inline-flex items-center gap-2 bg-brand-primary-600 hover:bg-brand-primary-700 text-white px-7 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all">Acceder</a>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center mb-24">
                <h1 class="text-6xl font-black tracking-tighter mb-6">Elige el plan que mejor se adapte a tu <span class="text-brand-primary-500">crecimiento.</span></h1>
                <p class="text-xl text-gray-400 max-w-3xl mx-auto font-medium">Sin sorpresas. Escala tu flota de forma inteligente con precios claros desde el primer día.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto mb-32">
                <!-- Hub Basic -->
                <div class="bg-gray-900/40 p-12 rounded-[4rem] border border-white/5 flex flex-col hover:border-brand-primary-500/20 transition-all">
                    <div class="mb-10">
                        <span class="text-brand-primary-400 font-black uppercase tracking-[0.3em] text-xs">Ideal para Startups</span>
                        <h2 class="text-4xl font-black mt-2 uppercase tracking-tight text-white">Hub Basic</h2>
                        <div class="mt-6 flex items-baseline gap-2">
                            <span class="text-6xl font-black text-white">49€</span>
                            <span class="text-gray-400 font-bold uppercase text-sm tracking-widest">/mes</span>
                        </div>
                    </div>
                    <ul class="space-y-6 mb-12 flex-1">
                        <li class="flex items-center gap-4 text-gray-200 font-medium">
                            <span class="material-icons text-brand-primary-500">check_circle</span>
                            Hasta 50 vehículos IoT
                        </li>
                        <li class="flex items-center gap-4 text-gray-200 font-medium">
                            <span class="material-icons text-brand-primary-500">check_circle</span>
                            Panel de Administración Estándar
                        </li>
                        <li class="flex items-center gap-4 text-gray-200 font-medium">
                            <span class="material-icons text-brand-primary-500">check_circle</span>
                            Soporte vía Email
                        </li>
                        <li class="flex items-center gap-4 text-gray-500 font-medium line-through italic">
                            Colores de Marca Personalizados
                        </li>
                    </ul>
                    <a href="https://frontend-phi-seven-21.vercel.app/empresa/alta" class="w-full py-6 text-center rounded-[2rem] bg-white/5 hover:bg-white/10 border border-white/10 text-white font-black uppercase tracking-widest text-xs transition-all">Empezar con Basic</a>
                </div>

                <!-- Pro Business -->
                <div class="bg-brand-primary-600 p-12 rounded-[4rem] flex flex-col shadow-[0_40px_80px_-20px_rgba(79,70,229,0.5)] transform md:-translate-y-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        <span class="bg-white/30 text-white text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-full backdrop-blur-md">Recomendado</span>
                    </div>
                    <div class="mb-10">
                        <span class="text-brand-primary-100 font-black uppercase tracking-[0.3em] text-xs">Para Flotas Grandes</span>
                        <h2 class="text-4xl font-black mt-2 uppercase tracking-tight text-white">Pro Business</h2>
                        <div class="mt-6 flex items-baseline gap-2">
                            <span class="text-6xl font-black text-white">79€</span>
                            <span class="text-brand-primary-100 font-bold uppercase text-sm tracking-widest">/mes</span>
                        </div>
                    </div>
                    <ul class="space-y-6 mb-12 flex-1 text-white">
                        <li class="flex items-center gap-4 font-bold">
                            <span class="material-icons text-white">check_circle</span>
                            Vehículos Ilimitados
                        </li>
                        <li class="flex items-center gap-4 font-bold">
                            <span class="material-icons text-white">check_circle</span>
                            Personalización Total de Marca
                        </li>
                        <li class="flex items-center gap-4 font-bold">
                            <span class="material-icons text-white">check_circle</span>
                            Asistente IA 24/7
                        </li>
                        <li class="flex items-center gap-4 font-bold">
                            <span class="material-icons text-white">check_circle</span>
                            Soporte Prioritario
                        </li>
                    </ul>
                    <a href="https://frontend-phi-seven-21.vercel.app/empresa/alta" class="w-full py-6 text-center rounded-[2rem] bg-white text-brand-primary-600 font-black uppercase tracking-widest text-xs shadow-xl hover:bg-gray-100 transition-all active:scale-95">Elegir Pro Business</a>
                </div>
            </div>

            <!-- FAQ Mini -->
            <div class="max-w-3xl mx-auto border-t border-gray-800 pt-20">
                <h3 class="text-2xl font-black mb-10 text-center uppercase tracking-tight">Preguntas sobre Facturación</h3>
                <div class="space-y-6">
                    <details class="group bg-white/5 rounded-3xl p-8 border border-white/5">
                        <summary class="cursor-pointer font-bold flex justify-between items-center list-none">
                            ¿Puedo cambiar de plan en cualquier momento?
                            <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <p class="mt-4 text-gray-400">Sí, puedes subir o bajar de plan cuando quieras. La diferencia se prorrateará en tu siguiente factura.</p>
                    </details>
                    <details class="group bg-white/5 rounded-3xl p-8 border border-white/5">
                        <summary class="cursor-pointer font-bold flex justify-between items-center list-none">
                            ¿Qué pasa si supero los 50 vehículos en el plan Basic?
                            <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                        </summary>
                        <p class="mt-4 text-gray-400">Te avisaremos con antelación para que puedas migrar al plan Pro. No cortaremos el servicio de tus vehículos operativos.</p>
                    </details>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
