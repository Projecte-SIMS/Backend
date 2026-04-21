<!DOCTYPE html>
<html lang="es" class="dark scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Fleetly - Sistema Inteligente de Movilidad Sostenible. Gestión de flotas de vehículos eléctricos con IoT en tiempo real.">

    <title>Fleetly - Sistema Inteligente de Movilidad Sostenible</title>
    <link rel="icon" type="image/svg+xml" href="/brand/isotipo/fleetly_isotip_negre.svg">

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
                    },
                    colors: {
                        brand: {
                            primary: {
                                50: '#eef2ff',
                                100: '#e0e7ff',
                                200: '#c7d2fe',
                                300: '#a5b4fc',
                                400: '#818cf8',
                                500: '#6366f1',
                                600: '#4f46e5',
                                700: '#4338ca',
                                800: '#3730a3',
                                900: '#312e81',
                                950: '#1e1b4b',
                            }
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="antialiased bg-gray-950 text-white font-sans">
    
    @php
        $frontendUrl = 'https://frontend-phi-seven-21.vercel.app';
    @endphp

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gray-950/80 backdrop-blur-xl border-b border-gray-800">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 flex items-center justify-between h-20">
            <div class="flex items-center gap-3 group cursor-pointer">
                <div class="h-10 w-10 rounded-2xl bg-white p-1 shadow-xl shadow-brand-primary-500/30 transition-transform group-hover:scale-110">
                    <img src="/logo.png" alt="Fleetly Logo" class="h-full w-full object-contain" />
                </div>
                <span class="text-2xl font-black text-white tracking-tighter">Fleetly</span>
            </div>
            <div class="flex items-center gap-8">
                <div class="hidden md:flex items-center gap-8">
                    <a href="#caracteristicas" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-colors">Características</a>
                    <a href="#planes" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-colors">Planes</a>
                    <a href="#video" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-colors">Demo</a>
                </div>
                <a href="{{ $frontendUrl }}/login" class="inline-flex items-center gap-2 bg-brand-primary-600 hover:bg-brand-primary-700 text-white px-7 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all shadow-xl shadow-brand-primary-500/30 hover:shadow-brand-primary-500/50 active:scale-95">
                    Acceder
                </a>
            </div>
        </div>
    </nav>

    <main class="pt-20">
        <!-- Hero Section -->
        <div class="relative isolate overflow-hidden min-h-[90vh] flex items-center">
            <!-- Background gradient -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 right-0 w-[1000px] h-[1000px] bg-gradient-to-br from-brand-primary-600/20 to-purple-700/20 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-0 left-0 w-[800px] h-[800px] bg-gradient-to-tr from-brand-primary-600/10 to-transparent rounded-full blur-[100px]"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">
                <div class="lg:flex lg:items-center lg:gap-20">
                    <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-2xl lg:shrink-0">
                        <div class="inline-flex items-center gap-2 mb-10">
                            <span class="inline-flex items-center rounded-full bg-brand-primary-500/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-brand-primary-400 ring-1 ring-inset ring-brand-primary-500/30">
                                <span class="mr-2 h-2 w-2 rounded-full bg-brand-primary-400 animate-pulse"></span>
                                Sprint 6 - Identidad Visual para Empresas
                            </span>
                        </div>
                        
                        <h1 class="text-6xl font-black tracking-tighter text-white sm:text-8xl leading-[0.85]">
                            Tu flota en la
                            <span class="bg-gradient-to-r from-brand-primary-400 via-indigo-400 to-purple-400 bg-clip-text text-transparent"> nube.</span>
                        </h1>
                        
                        <p class="mt-10 text-xl leading-relaxed text-gray-400 font-medium max-w-xl">
                            Infraestructura SaaS multi-inquilino para gestionar flotas eléctricas. Control IoT en tiempo real, colores de marca personalizables y soporte con inteligencia artificial.
                        </p>
                        
                        <div class="mt-12 flex flex-col sm:flex-row items-start sm:items-center gap-6">
                            <a href="{{ $frontendUrl }}/empresa/alta" class="inline-flex items-center justify-center gap-2 bg-brand-primary-600 hover:bg-brand-primary-700 text-white px-12 py-6 rounded-[2rem] text-sm font-black uppercase tracking-widest transition-all shadow-[0_20px_50px_-12px_rgba(79,70,229,0.5)] hover:shadow-brand-primary-500/60 active:scale-95">
                                Crear mi Empresa
                            </a>
                            <a href="#planes" class="inline-flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white border border-white/10 px-12 py-6 rounded-[2rem] text-sm font-black uppercase tracking-widest transition-all active:scale-95">
                                Ver Planes
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="mt-16 grid grid-cols-3 gap-12 border-t border-gray-800/50 pt-10">
                            <div>
                                <p class="text-4xl font-black text-brand-primary-400 tracking-tighter italic">IoT</p>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mt-2">Tiempo real</p>
                            </div>
                            <div>
                                <p class="text-4xl font-black text-brand-primary-400 tracking-tighter italic">SaaS</p>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mt-2">Multi-inquilino</p>
                            </div>
                            <div>
                                <p class="text-4xl font-black text-brand-primary-400 tracking-tighter italic">24/7</p>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mt-2">Soporte IA</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dashboard Preview -->
                    <div class="mt-20 lg:mt-0 flex-1">
                        <div class="relative group">
                            <div class="absolute -inset-10 bg-gradient-to-r from-brand-primary-600/30 to-purple-600/30 rounded-[3rem] blur-[80px] group-hover:blur-[100px] transition-all duration-700"></div>
                            <div class="relative bg-gray-950/50 backdrop-blur-3xl rounded-[3rem] border border-white/10 shadow-2xl overflow-hidden transform rotate-2 group-hover:rotate-0 transition-all duration-700">
                                <div class="h-12 bg-gray-900/50 border-b border-white/5 flex items-center px-6 gap-2">
                                    <div class="w-3 h-3 rounded-full bg-red-500/30"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-500/30"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-500/30"></div>
                                </div>
                                <img src="https://tailwindcss.com/plus-assets/img/component-images/project-app-screenshot.png" 
                                     alt="Panel de Control Fleetly" 
                                     class="w-full opacity-80 group-hover:opacity-100 transition-opacity duration-700" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="caracteristicas" class="py-32 border-t border-gray-800/50 bg-gray-950">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-brand-primary-500 mb-6">Infraestructura de Próxima Generación</p>
                    <h2 class="text-5xl font-black tracking-tighter text-white sm:text-6xl">
                        Todo lo que necesitas con tu propia identidad
                    </h2>
                    <p class="mt-8 text-xl text-gray-400 font-medium leading-relaxed">
                        No solo te damos el software. Te damos un ecosistema completo para que lances tu propio negocio de movilidad con tus propios colores y marca.
                    </p>
                </div>
                
                <div class="mx-auto mt-24 max-w-2xl lg:max-w-none">
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                        
                        <!-- Feature 1 -->
                        <div class="relative p-1 bg-gradient-to-b from-white/10 to-transparent rounded-[3rem] hover:from-brand-primary-500/40 transition-all group">
                            <div class="h-full bg-gray-950 rounded-[2.9rem] p-10 group-hover:bg-gray-900/50 transition-colors">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-primary-500/10 text-brand-primary-400 mb-8 group-hover:scale-110 transition-transform">
                                    <span class="material-icons text-3xl">location_on</span>
                                </div>
                                <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-4">Tracking IoT</h3>
                                <p class="text-gray-500 leading-relaxed font-medium">
                                    Localización precisa con latencia mínima mediante hardware basado en Raspberry Pi 4.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="relative p-1 bg-gradient-to-b from-white/10 to-transparent rounded-[3rem] hover:from-green-500/40 transition-all group">
                            <div class="h-full bg-gray-950 rounded-[2.9rem] p-10 group-hover:bg-gray-900/50 transition-colors">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-green-500/10 text-green-400 mb-8 group-hover:scale-110 transition-transform">
                                    <span class="material-icons text-3xl">settings_remote</span>
                                </div>
                                <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-4">Telemetría</h3>
                                <p class="text-gray-500 leading-relaxed font-medium">
                                    Control de encendido, estado de batería y diagnósticos remotos seguros mediante WebSockets.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="relative p-1 bg-gradient-to-b from-white/10 to-transparent rounded-[3rem] hover:from-purple-500/40 transition-all group">
                            <div class="h-full bg-gray-950 rounded-[2.9rem] p-10 group-hover:bg-gray-900/50 transition-colors">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-500/10 text-purple-400 mb-8 group-hover:scale-110 transition-transform">
                                    <span class="material-icons text-3xl">palette</span>
                                </div>
                                <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-4">Temas de Marca</h3>
                                <p class="text-gray-500 leading-relaxed font-medium">
                                    Personaliza los colores del panel para que se alineen perfectamente con la identidad visual de tu empresa.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Section -->
        <div id="planes" class="py-32 border-t border-gray-800/50 relative overflow-hidden">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-brand-primary-600/5 blur-[120px] rounded-full"></div>
            
            <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
                <div class="mx-auto max-w-2xl text-center mb-20">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-brand-primary-500 mb-6">Planes Flexibles</p>
                    <h2 class="text-5xl font-black tracking-tighter text-white">Precios transparentes</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-5xl mx-auto">
                    <!-- Plan Basic -->
                    <div class="relative p-10 bg-gray-900/40 backdrop-blur-xl rounded-[3rem] border border-white/5 flex flex-col">
                        <div class="mb-8">
                            <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.3em] mb-4">Hub Basic</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-black text-white">49€</span>
                                <span class="text-gray-500 text-sm font-bold">/mes</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-center gap-3 text-sm text-gray-300 font-medium">
                                <span class="material-icons text-brand-primary-500 text-sm">check_circle</span>
                                Hasta 50 vehículos
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-300 font-medium">
                                <span class="material-icons text-brand-primary-500 text-sm">check_circle</span>
                                Gestión de Inquilinos Estándar
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-300 font-medium">
                                <span class="material-icons text-brand-primary-500 text-sm">check_circle</span>
                                Panel Admin Fleetly
                            </li>
                            <li class="flex items-center gap-3 text-sm text-gray-500 font-medium line-through">
                                Colores de Marca Personalizados
                            </li>
                        </ul>
                        <a href="{{ $frontendUrl }}/empresa/alta" class="w-full py-4 text-center rounded-2xl bg-white/5 hover:bg-white/10 text-white text-xs font-black uppercase tracking-widest border border-white/10 transition-all">
                            Empezar ahora
                        </a>
                    </div>

                    <!-- Plan Pro -->
                    <div class="relative p-10 bg-brand-primary-600 rounded-[3rem] shadow-[0_30px_60px_-15px_rgba(79,70,229,0.4)] flex flex-col transform md:-translate-y-4">
                        <div class="absolute top-6 right-10">
                            <span class="bg-white/20 text-white text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1 rounded-full backdrop-blur-md">Recomendado</span>
                        </div>
                        <div class="mb-8">
                            <h3 class="text-xs font-black text-brand-primary-100 uppercase tracking-[0.3em] mb-4">Pro Business</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-black text-white">79€</span>
                                <span class="text-brand-primary-200 text-sm font-bold">/mes</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-1 text-white">
                            <li class="flex items-center gap-3 text-sm font-medium">
                                <span class="material-icons text-white text-sm">check_circle</span>
                                Vehículos ilimitados
                            </li>
                            <li class="flex items-center gap-3 text-sm font-medium">
                                <span class="material-icons text-white text-sm">check_circle</span>
                                **Colores de Marca Personalizados**
                            </li>
                            <li class="flex items-center gap-3 text-sm font-medium">
                                <span class="material-icons text-white text-sm">check_circle</span>
                                **Asistente Inteligente IA**
                            </li>
                            <li class="flex items-center gap-3 text-sm font-medium">
                                <span class="material-icons text-white text-sm">check_circle</span>
                                Soporte Prioritario 24/7
                            </li>
                        </ul>
                        <a href="{{ $frontendUrl }}/empresa/alta" class="w-full py-4 text-center rounded-2xl bg-white text-brand-primary-700 text-xs font-black uppercase tracking-widest shadow-xl hover:bg-gray-50 transition-all active:scale-95">
                            Elegir Profesional
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Section -->
        <div id="video" class="py-32 border-t border-gray-800/50 bg-gray-950">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-brand-primary-400 mb-6">Demo Interactiva</p>
                <h2 class="text-5xl font-black tracking-tight text-white">Descubre SIMS en acción</h2>
                
                <div class="mt-20 relative max-w-5xl mx-auto group">
                    <div class="absolute -inset-10 bg-gradient-to-r from-brand-primary-600/20 to-purple-600/20 rounded-[4rem] blur-[80px] group-hover:blur-[100px] transition-all duration-700"></div>
                    <div class="relative bg-gray-900 rounded-[4rem] border border-white/5 overflow-hidden shadow-2xl">
                        <div class="aspect-video flex items-center justify-center bg-slate-900/50">
                            <div class="text-center px-8">
                                <button class="group/play relative size-24 mx-auto mb-10 rounded-full bg-brand-primary-600 flex items-center justify-center shadow-[0_20px_40px_-10px_rgba(79,70,229,0.5)] hover:scale-110 transition-all duration-300">
                                    <div class="absolute inset-0 rounded-full bg-brand-primary-500 animate-ping opacity-20"></div>
                                    <svg class="w-10 h-10 text-white ml-1 relative z-10" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </button>
                                <p class="text-white text-xl font-black uppercase tracking-[0.2em] mb-4">Reproducir Showcase</p>
                                <p class="text-gray-500 text-sm font-bold uppercase tracking-widest italic">Movilidad Conectada &middot; Fleetly Hub</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="relative bg-gradient-to-br from-brand-primary-600 via-indigo-600 to-purple-800 rounded-[4rem] px-8 py-24 sm:px-20 sm:py-32 overflow-hidden shadow-2xl shadow-brand-primary-500/20">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.05%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
                    <div class="relative text-center max-w-4xl mx-auto">
                        <h2 class="text-5xl font-black tracking-tighter text-white sm:text-7xl leading-none">
                            Lleva tu flota al siguiente nivel.
                        </h2>
                        <p class="mt-10 text-2xl text-brand-primary-100 font-medium">
                            La plataforma que crece con tu negocio. Empieza hoy mismo.
                        </p>
                        <div class="mt-14 flex flex-col sm:flex-row items-center justify-center gap-8">
                            <a href="{{ $frontendUrl }}/empresa/alta" class="inline-flex items-center justify-center bg-white hover:bg-gray-100 text-brand-primary-700 px-14 py-6 rounded-2xl text-base font-black uppercase tracking-widest transition-all shadow-2xl active:scale-95">
                                Crear mi Empresa
                            </a>
                            <a href="{{ $frontendUrl }}/login" class="inline-flex items-center gap-3 text-white hover:text-brand-primary-100 font-black uppercase tracking-widest text-sm group">
                                Iniciar sesión
                                <span class="material-icons group-hover:translate-x-2 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>
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
                
                <div class="flex items-center gap-10">
                    <a href="https://github.com/Projecte-SIMS" target="_blank" class="text-gray-500 hover:text-brand-primary-400 transition-all hover:scale-125">
                        <span class="sr-only">GitHub</span>
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                        </svg>
                    </a>
                </div>
                
                <div class="text-center md:text-right">
                    <p class="text-sm text-gray-500 font-black uppercase tracking-widest">
                        &copy; 2026 Fleetly Project
                    </p>
                    <p class="text-[10px] text-gray-600 mt-2 font-bold uppercase tracking-widest">
                        Licencia EUPL &middot; Sprint 6 Production
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</body>
</html>
