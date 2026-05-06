<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagos y Facturación FAQ - Fleetly</title>
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
                <span class="text-xl font-black text-white tracking-tighter">Pagos</span>
            </a>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-3xl px-6">
            <h1 class="text-4xl font-black mb-12">Pagos y Facturación</h1>
            
            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-bold text-purple-500 mb-4">Métodos y Proceso</h2>
                    <div class="space-y-4">
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Qué métodos de pago puedo usar?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Aceptamos tarjetas de crédito y débito <strong>Visa, Mastercard y American Express</strong>. También puedes configurar Apple Pay o Google Pay para pagos rápidos desde la app.
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Cuándo se realiza el cobro del viaje?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Al iniciar un viaje, realizamos una <strong>pre-autorización de seguridad</strong> (normalmente entre 1€ y 15€ según el vehículo). El cobro final se procesa automáticamente al terminar el alquiler y cerrar el vehículo.
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Cómo puedo descargar mis facturas?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Recibirás un resumen por email tras cada viaje. Además, puedes acceder al historial completo y descargar facturas oficiales en PDF desde la sección "Mis Viajes > Facturación" de la app.
                            </div>
                        </details>
                    </div>
                </section>

                <section class="mt-12">
                    <h2 class="text-xl font-bold text-purple-500 mb-4">Tarifas y Cargos</h2>
                    <div class="space-y-4">
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Hay cargos por limpieza o mal uso?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                <p>Sí, aplicamos cargos adicionales en situaciones específicas:</p>
                                <ul class="list-disc ml-6 mt-2 space-y-1">
                                    <li>Limpieza especial (si se devuelve el coche muy sucio): desde 30€.</li>
                                    <li>Fumar en el vehículo: 50€.</li>
                                    <li>Finalizar el viaje fuera de la zona permitida: desde 25€.</li>
                                    <li>Pérdida de llaves (si aplica): según coste de reposición.</li>
                                </ul>
                            </div>
                        </details>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
