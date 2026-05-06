<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Car Sharing FAQ - Fleetly</title>
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
                <span class="text-xl font-black text-white tracking-tighter">Car Sharing</span>
            </a>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-3xl px-6">
            <h1 class="text-4xl font-black mb-12">Preguntas sobre Car Sharing</h1>
            
            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-bold text-brand-primary-500 mb-4">Registro y Acceso</h2>
                    <div class="space-y-4">
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Cómo me registro en Fleetly?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                <p class="mb-4">Descarga la app, sube tu DNI y carnet de conducir. Validaremos tu perfil en menos de 24 horas mediante nuestro sistema de verificación automática.</p>
                                <a href="https://frontend-phi-seven-21.vercel.app/registro" class="text-brand-primary-400 hover:underline font-bold">Ir a Registro de Usuario →</a>
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Qué documentos necesito y qué edad mínima?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                <p>Debes tener al menos <strong>19 años</strong> y poseer un carnet de conducir (Tipo B) con al menos 1 año de antigüedad. Necesitarás:</p>
                                <ul class="list-disc ml-6 mt-2 space-y-1">
                                    <li>DNI, NIE o Pasaporte vigente.</li>
                                    <li>Carnet de conducir físico (no aceptamos fotos de copias).</li>
                                    <li>Una tarjeta de crédito o débito a tu nombre.</li>
                                </ul>
                            </div>
                        </details>
                    </div>
                </section>

                <section class="mt-12">
                    <h2 class="text-xl font-bold text-brand-primary-500 mb-4">Reservas y Conducción</h2>
                    <div class="space-y-4">
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Cuánto tiempo puedo reservar un coche?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Puedes reservar tu coche durante <strong>20 minutos de forma gratuita</strong>. Si necesitas más tiempo, puedes extender la reserva con un coste adicional por minuto.
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Qué seguro incluyen los viajes?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Todos los viajes incluyen un seguro a todo riesgo con franquicia. En caso de accidente bajo tu responsabilidad, el coste máximo que asumirás será el importe de la franquicia (variable según el vehículo).
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Cómo abro y cierro el vehículo?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                <p>Todo el control está en la app. Una vez al lado del coche:</p>
                                <ol class="list-decimal ml-6 mt-2 space-y-1">
                                    <li>Pulsa "Abrir" en la pantalla de reserva activa.</li>
                                    <li>Revisa el estado del coche y reporta daños si los hay.</li>
                                    <li>Para finalizar, aparca en la zona permitida y pulsa "Finalizar Viaje".</li>
                                </ol>
                            </div>
                        </details>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
