<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carga FAQ - Fleetly</title>
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
                <span class="text-xl font-black text-white tracking-tighter">Carga y Energía</span>
            </a>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-3xl px-6">
            <h1 class="text-4xl font-black mb-12">Carga y Energía</h1>
            
            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-bold text-yellow-500 mb-4">Vehículos Eléctricos</h2>
                    <div class="space-y-4">
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center">
                                ¿Cómo cargo un coche eléctrico?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Utiliza el cable que encontrarás en el maletero y conéctalo a cualquier estación de carga asociada (puedes verlas en el mapa de la app). Escanea el código QR del cargador desde la app de Fleetly para iniciar la carga.
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center">
                                ¿Tengo que pagar por la carga?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                No, el coste de la energía está incluido en tu tarifa. De hecho, si devuelves el coche con más del 80% de batería, ¡te regalamos crédito para tu próximo viaje!
                            </div>
                        </details>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
