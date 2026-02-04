<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="bg-white dark:bg-gray-900">
        <!-- Topbar (no fijada; alineada con el contenedor principal) -->
        <div class="w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur shadow-md border-b border-gray-200/20 dark:border-white/10">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 flex items-center justify-between h-16">
                <div class="flex items-center">
                    <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company" class="h-8 dark:hidden" />
                    <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="h-8 not-dark:hidden" />
                </div>
                <div>
                    <a href="/login" class="text-md font-medium text-neutral-500 dark:text-gray-300 hover:text-black dark:hover:text-white">Login →</a>
                </div>
            </div>
        </div>
        <main>
            <!-- Hero section -->
            <div class="relative isolate overflow-hidden bg-white dark:bg-gray-900 flex flex-col min-h-screen">
                <svg aria-hidden="true"
                    class="absolute inset-0  -z-10 size-full mask-[radial-gradient(100%_100%_at_top_right,white,transparent)] stroke-gray-200 dark:stroke-white/10">
                    <defs>
                        <pattern id="983e3e4c-de6d-4c3f-8d64-b9761d1534cc" width="200" height="200" x="50%" y="-1"
                            patternUnits="userSpaceOnUse">
                            <path d="M.5 200V.5H200" fill="none" />
                        </pattern>
                    </defs>
                    <svg x="50%" y="-1" class="overflow-visible fill-gray-50 dark:fill-gray-800/20">
                        <path
                            d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z"
                            stroke-width="0" />
                    </svg>
                    <rect width="100%" height="100%" fill="url(#983e3e4c-de6d-4c3f-8d64-b9761d1534cc)"
                        stroke-width="0" />
                </svg>
                <div aria-hidden="true"
                    class="absolute top-10 left-[calc(50%-4rem)] -z-10 transform-gpu blur-3xl sm:left-[calc(50%-18rem)] lg:top-[calc(50%-30rem)] lg:left-48 xl:left-[calc(50%-24rem)]">
                    <div style="clip-path: polygon(73.6% 51.7%, 91.7% 11.8%, 100% 46.4%, 97.4% 82.2%, 92.5% 84.9%, 75.7% 64%, 55.3% 47.5%, 46.5% 49.4%, 45% 62.9%, 50.3% 87.2%, 21.3% 64.1%, 0.1% 100%, 5.4% 51.1%, 21.4% 63.9%, 58.9% 0.2%, 73.6% 51.7%)"
                        class="aspect-1108/632 w-277 bg-linear-to-r from-[#80caff] to-[#4f46e5] opacity-20"></div>
                </div>
                <div class="mx-auto max-w-7xl px-6 pt-10 pb-24 sm:pb-32 lg:flex lg:px-8 lg:py-20">
                    <div class="mx-auto max-w-2xl shrink-0 lg:mx-0 lg:pt-8">
                        <h1
                            class="mt-10 text-5xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-7xl dark:text-white">
                             Manage your fleet anytime, anywhere.</h1>
                        <p class="mt-8 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8 dark:text-gray-400">
                            Manage your vehicles, drivers, and routes from a single dashboard: real-time status, alerts, and analytics to make your life easier.
                        </p>
                    </div>
                    <div
                        class="mx-auto mt-16 flex max-w-2xl sm:mt-24 lg:mt-0 lg:mr-0 lg:ml-10 lg:max-w-none lg:flex-none xl:ml-32">
                        <div class="max-w-3xl flex-none sm:max-w-5xl lg:max-w-none">
                            <img width="1920" height="1080"
                                src="https://tailwindcss.com/plus-assets/img/component-images/project-app-screenshot.png"
                                alt="App screenshot"
                                class="w-304 rounded-md bg-gray-50 shadow-xl ring-1 ring-gray-900/10 dark:hidden" />
                            <img width="40%"
                                src="https://tailwindcss.com/plus-assets/img/component-images/dark-project-app-screenshot.png"
                                alt="App screenshot"
                                class="w-304 rounded-md bg-white/5 shadow-2xl ring-1 ring-white/10 not-dark:hidden" />
                        </div>
                    </div>
                </div>

                <!-- Scroll down indicator -->
                <div class="absolute bottom-24 left-1/2 -translate-x-1/2">
                    <div class="relative">
                        <span class="absolute inline-flex w-12 h-12 rounded-full bg-indigo-600 opacity-20 animate-ping left-0 top-0"></span>
                        <span class="relative inline-flex w-12 h-12 rounded-full bg-white dark:bg-gray-900 shadow flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5 text-indigo-600 animate-bounce">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Feature section -->
            <div class="mx-auto mt-24 max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base/7 font-semibold text-indigo-600 dark:text-indigo-400">All in one</h2>
                    <p
                        class="mt-2 text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl lg:text-balance dark:text-white">
                        Everything you need to manage your fleet</p>
                    <p class="mt-6 text-lg/8 text-gray-600 dark:text-gray-300">Here in Sims, we've thought of everything so you don't have to.</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <div class="flex flex-col">
                            <dt class="text-base/7 font-semibold text-gray-900 dark:text-white">
                                <div
                                    class="mb-6 flex size-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        data-slot="icon" aria-hidden="true" class="size-6 text-white">
                                        <path d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                Real-time tracking
                            </dt>
                            <dd class="mt-1 flex flex-auto flex-col text-base/7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">See live locations, usage and status for every vehicle on the interactive map. Filter by driver, or status, and replay historical routes to investigate incidents.</p>

                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="text-base/7 font-semibold text-gray-900 dark:text-white">
                                <div
                                    class="mb-6 flex size-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        data-slot="icon" aria-hidden="true" class="size-6 text-white">
                                        <path
                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                Roles management
                            </dt>
                            <dd class="mt-1 flex flex-auto flex-col text-base/7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">Create and assign roles and permissions for your team, control who can view, manage or delete vehicles, tickets, users...</p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="text-base/7 font-semibold text-gray-900 dark:text-white">
                                <div
                                    class="mb-6 flex size-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        data-slot="icon" aria-hidden="true" class="size-6 text-white">
                                        <path
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                No idea
                            </dt>
                            <dd class="mt-1 flex flex-auto flex-col text-base/7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">Et quod quaerat dolorem quaerat architecto aliquam accusantium. Ex
                                    adipisci et doloremque autem quia quam. Quis eos molestiae at iure impedit.</p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Feature section -->
            <div class="my-32 sm:my-56">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl sm:text-center">
                        <h2 class="text-base/7 font-semibold text-indigo-600 dark:text-indigo-400">Everything you need
                        </h2>
                        <p
                            class="mt-2 text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl sm:text-balance dark:text-white">
                            No server? No problem.</p>
                        <p class="mt-6 text-lg/8 text-gray-600 dark:text-gray-300">Lorem ipsum, dolor sit amet
                            consectetur adipisicing elit. Maiores impedit perferendis suscipit eaque, iste dolor
                            cupiditate blanditiis.</p>
                    </div>
                </div>
                <div class="relative overflow-hidden pt-16">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <img width="2432" height="1442"
                            src="https://tailwindcss.com/plus-assets/img/component-images/project-app-screenshot.png"
                            alt="App screenshot"
                            class="mb-[-12%] rounded-xl shadow-2xl ring-1 ring-gray-900/10 dark:hidden dark:ring-white/10" />
                        <img width="2432" height="1442"
                            src="https://tailwindcss.com/plus-assets/img/component-images/dark-project-app-screenshot.png"
                            alt="App screenshot"
                            class="mb-[-12%] rounded-xl shadow-2xl ring-1 ring-gray-900/10 not-dark:hidden dark:ring-white/10" />
                        <div aria-hidden="true" class="relative">
                            <div
                                class="absolute -inset-x-20 bottom-0 bg-linear-to-t from-white pt-[7%] dark:from-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mx-auto mt-16 max-w-7xl px-6 sm:mt-20 md:mt-24 lg:px-8">
                    <dl
                        class="mx-auto grid max-w-2xl grid-cols-1 gap-x-6 gap-y-10 text-base/7 text-gray-600 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3 lg:gap-x-8 lg:gap-y-16 dark:text-gray-400">
                        <div class="relative pl-9">
                            <dt class="inline font-semibold text-gray-900 dark:text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="absolute top-1 left-1 size-5 text-indigo-600 dark:text-indigo-400">
                                    <path
                                        d="M5.5 17a4.5 4.5 0 0 1-1.44-8.765 4.5 4.5 0 0 1 8.302-3.046 3.5 3.5 0 0 1 4.504 4.272A4 4 0 0 1 15 17H5.5Zm3.75-2.75a.75.75 0 0 0 1.5 0V9.66l1.95 2.1a.75.75 0 1 0 1.1-1.02l-3.25-3.5a.75.75 0 0 0-1.1 0l-3.25 3.5a.75.75 0 1 0 1.1 1.02l1.95-2.1v4.59Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                Push to deploy.
                            </dt>
                            <dd class="inline">Lorem ipsum, dolor sit amet consectetur adipisicing elit aute id magna.
                            </dd>
                        </div>
                        <div class="relative pl-9">
                            <dt class="inline font-semibold text-gray-900 dark:text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="absolute top-1 left-1 size-5 text-indigo-600 dark:text-indigo-400">
                                    <path
                                        d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                SSL certificates.
                            </dt>
                            <dd class="inline">Anim aute id magna aliqua ad ad non deserunt sunt. Qui irure qui lorem
                                cupidatat commodo.</dd>
                        </div>
                        <div class="relative pl-9">
                            <dt class="inline font-semibold text-gray-900 dark:text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="absolute top-1 left-1 size-5 text-indigo-600 dark:text-indigo-400">
                                    <path
                                        d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                Simple queues.
                            </dt>
                            <dd class="inline">Ac tincidunt sapien vehicula erat auctor pellentesque rhoncus.</dd>
                        </div>
                        <div class="relative pl-9">
                            <dt class="inline font-semibold text-gray-900 dark:text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="absolute top-1 left-1 size-5 text-indigo-600 dark:text-indigo-400">
                                    <path
                                        d="M10 2.5c-1.31 0-2.526.386-3.546 1.051a.75.75 0 0 1-.82-1.256A8 8 0 0 1 18 9a22.47 22.47 0 0 1-1.228 7.351.75.75 0 1 1-1.417-.49A20.97 20.97 0 0 0 16.5 9 6.5 6.5 0 0 0 10 2.5ZM4.333 4.416a.75.75 0 0 1 .218 1.038A6.466 6.466 0 0 0 3.5 9a7.966 7.966 0 0 1-1.293 4.362.75.75 0 0 1-1.257-.819A6.466 6.466 0 0 0 2 9c0-1.61.476-3.11 1.295-4.365a.75.75 0 0 1 1.038-.219ZM10 6.12a3 3 0 0 0-3.001 3.041 11.455 11.455 0 0 1-2.697 7.24.75.75 0 0 1-1.148-.965A9.957 9.957 0 0 0 5.5 9c0-.028.002-.055.004-.082a4.5 4.5 0 0 1 8.996.084V9.15l-.005.297a.75.75 0 1 1-1.5-.034c.003-.11.004-.219.005-.328a3 3 0 0 0-3-2.965Zm0 2.13a.75.75 0 0 1 .75.75c0 3.51-1.187 6.745-3.181 9.323a.75.75 0 1 1-1.186-.918A13.687 13.687 0 0 0 9.25 9a.75.75 0 0 1 .75-.75Zm3.529 3.698a.75.75 0 0 1 .584.885 18.883 18.883 0 0 1-2.257 5.84.75.75 0 1 1-1.29-.764 17.386 17.386 0 0 0 2.078-5.377.75.75 0 0 1 .885-.584Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                Advanced security.
                            </dt>
                            <dd class="inline">Lorem ipsum, dolor sit amet consectetur adipisicing elit aute id magna.
                            </dd>
                        </div>
                        <div class="relative pl-9">
                            <dt class="inline font-semibold text-gray-900 dark:text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="absolute top-1 left-1 size-5 text-indigo-600 dark:text-indigo-400">
                                    <path
                                        d="M7.84 1.804A1 1 0 0 1 8.82 1h2.36a1 1 0 0 1 .98.804l.331 1.652a6.993 6.993 0 0 1 1.929 1.115l1.598-.54a1 1 0 0 1 1.186.447l1.18 2.044a1 1 0 0 1-.205 1.251l-1.267 1.113a7.047 7.047 0 0 1 0 2.228l1.267 1.113a1 1 0 0 1 .206 1.25l-1.18 2.045a1 1 0 0 1-1.187.447l-1.598-.54a6.993 6.993 0 0 1-1.929 1.115l-.33 1.652a1 1 0 0 1-.98.804H8.82a1 1 0 0 1-.98-.804l-.331-1.652a6.993 6.993 0 0 1-1.929-1.115l-1.598.54a1 1 0 0 1-1.186-.447l-1.18-2.044a1 1 0 0 1 .205-1.251l1.267-1.114a7.05 7.05 0 0 1 0-2.227L1.821 7.773a1 1 0 0 1-.206-1.25l1.18-2.045a1 1 0 0 1 1.187-.447l1.598.54A6.992 6.992 0 0 1 7.51 3.456l.33-1.652ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                Powerful API.
                            </dt>
                            <dd class="inline">Anim aute id magna aliqua ad ad non deserunt sunt. Qui irure qui lorem
                                cupidatat commodo.</dd>
                        </div>
                        <div class="relative pl-9">
                            <dt class="inline font-semibold text-gray-900 dark:text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="absolute top-1 left-1 size-5 text-indigo-600 dark:text-indigo-400">
                                    <path
                                        d="M4.632 3.533A2 2 0 0 1 6.577 2h6.846a2 2 0 0 1 1.945 1.533l1.976 8.234A3.489 3.489 0 0 0 16 11.5H4c-.476 0-.93.095-1.344.267l1.976-8.234Z" />
                                    <path
                                        d="M4 13a2 2 0 1 0 0 4h12a2 2 0 1 0 0-4H4Zm11.24 2a.75.75 0 0 1 .75-.75H16a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75h-.01a.75.75 0 0 1-.75-.75V15Zm-2.25-.75a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75H13a.75.75 0 0 0 .75-.75V15a.75.75 0 0 0-.75-.75h-.01Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                Database backups.
                            </dt>
                            <dd class="inline">Ac tincidunt sapien vehicula erat auctor pellentesque rhoncus.</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="border-t border-gray-200 py-12 md:flex md:items-center md:justify-between dark:border-white/10">
                <div class="flex justify-center gap-x-6 md:order-2">
                    <a href="https://github.com/orgs/Sprint4-ProjectSIMS-Team1" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
                        <span class="sr-only">GitHub</span>
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="size-6">
                            <path
                                d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
                                clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <p class="mt-8 text-center text-sm/6 text-gray-600 md:order-1 md:mt-0 dark:text-gray-400">&copy; 2026
                    Sims, Inc. All rights reserved.</p>
            </div>
        </footer>
    </div>

</body>

</html>