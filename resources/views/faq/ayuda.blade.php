<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ayuda Rápida FAQ - Fleetly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { brand: { primary: { 500: '#6366f1', 600: '#4f46e5' } } } } } }
    </script>
</head>
<body class="bg-gray-950 text-white font-sans antialiased">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gray-950/80 backdrop-blur-xl border-b border-gray-800">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 flex items-center justify-between h-20">
            <a href="/faq" class="flex items-center gap-3 group">
                <span class="material-icons text-gray-400 group-hover:text-white transition-colors">arrow_back</span>
                <span class="text-xl font-black text-white tracking-tighter">Ayuda</span>
            </a>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-3xl px-6">
            <h1 class="text-4xl font-black mb-12">Ayuda Rápida y Emergencias</h1>
            
            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-bold text-red-500 mb-4">Soporte 24/7</h2>
                    <div class="space-y-4">
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center">
                                ¿Qué hago si tengo un accidente?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Asegúrate de estar a salvo, llama a emergencias si es necesario y después pulsa el botón de "Asistencia" en la app. Nuestro equipo te guiará y enviará una grúa si es necesario.
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center">
                                He olvidado algo dentro del coche
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Si acabas de terminar el viaje, tienes 5 minutos para volver a abrir el coche desde la app sin coste. Si ha pasado más tiempo, contacta con soporte para localizar el vehículo.
                            </div>
                        </details>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
