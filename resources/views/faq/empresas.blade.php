<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Empresas FAQ - Fleetly</title>
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
                <span class="text-xl font-black text-white tracking-tighter">Empresas</span>
            </a>
        </div>
    </nav>

    <main class="pt-32 pb-20">
        <div class="mx-auto max-w-3xl px-6">
            <h1 class="text-4xl font-black mb-12">Fleetly for Business</h1>
            
            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-bold text-blue-500 mb-4">Gestión Corporativa</h2>
                    <div class="space-y-4">
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Cómo registro mi empresa y activo el panel?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                <p class="mb-4">El proceso es 100% digital. Una vez completes el alta, nuestro sistema creará tu instancia multi-tenant de forma automática.</p>
                                <a href="https://frontend-phi-seven-21.vercel.app/empresa/alta" class="inline-flex items-center gap-2 bg-brand-primary-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-primary-700 transition-all">
                                    Darse de alta ahora →
                                </a>
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Qué es el sistema Multi-tenant y por qué es seguro?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Cada empresa tiene un <strong>ID de inquilino (Tenant ID)</strong> único. Todas las peticiones a la API y consultas a la base de datos están filtradas por este ID, asegurando que tus datos operativos y de empleados estén físicamente aislados del resto de clientes.
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Puedo personalizar la identidad visual (Branding)?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Sí. En el plan Pro Business puedes configurar:
                                <ul class="list-disc ml-6 mt-2 space-y-1">
                                    <li>Color primario de la interfaz.</li>
                                    <li>Logotipo de la empresa en el panel.</li>
                                    <li>Subdominio personalizado (ej: miempresa.fleetly.com).</li>
                                </ul>
                            </div>
                        </details>
                    </div>
                </section>

                <section class="mt-12">
                    <h2 class="text-xl font-bold text-blue-500 mb-4">Integración y API</h2>
                    <div class="space-y-4">
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Ofrecéis acceso a los datos por API?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Sí, proporcionamos una <strong>REST API completa</strong> para que puedas integrar los datos de telemetría y viajes en tu propio software de ERP o BI. El acceso está disponible para clientes del plan Pro.
                            </div>
                        </details>
                        <details class="group bg-gray-900/50 rounded-2xl border border-white/5">
                            <summary class="p-6 cursor-pointer font-bold list-none flex justify-between items-center text-white">
                                ¿Cómo se factura el servicio?
                                <span class="material-icons text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="px-6 pb-6 text-gray-400">
                                Facturamos mensualmente por adelantado la cuota del plan elegido. Los costes variables de uso se cargan a mes vencido. Aceptamos domiciliación bancaria (SEPA) y tarjeta corporativa.
                            </div>
                        </details>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
