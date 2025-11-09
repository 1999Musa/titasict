<!DOCTYPE html>
<html lang="en">

<head>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .active-link {
            background-color: #10b981;
            color: white !important;
        }

        .active-group {
            background-color: #a8e910 !important;
            /* Tailwind yellow-300 */
        }
    </style>
</head>

<body class="min-h-screen bg-[#F4F7F6] text-gray-800 p-4 lg:p-8">

    <div class="flex h-full max-h-[95vh] transition-all duration-300"
        x-data="{ open: localStorage.getItem('openGroup') || '', sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') ?? 'true') }"
        x-init="
            $watch('open', val => localStorage.setItem('openGroup', val));
            $watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', JSON.stringify(val)));
        ">

        <!-- Sidebar -->
        <aside class="bg-white rounded-2xl shadow-lg flex flex-col p-6 transition-all duration-300 overflow-hidden"
            :class="sidebarOpen ? 'w-72' : 'w-20'">
            <!-- Logo / Header -->
            <div class="p-2 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3" x-show="sidebarOpen" x-transition>
                    <div class="bg-emerald-500 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7.014A8.003 8.003 0 0117.657 18.657z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.879 16.121A3 3 0 1014.12 11.88a3 3 0 00-4.242 4.242z" />
                        </svg>
                    </div>
                    <div>
                        <a href="{{ route('dashboard') }}">
                            <h1 class="text-xl font-bold text-gray-800">Titas ICT</h1>
                        </a>
                        <p class="text-xs text-gray-500">Manage your world</p>
                    </div>
                </div>

                <!-- Collapse Button -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="bg-indigo-400 hover:bg-gray-200 rounded-lg p-2 transition" title="Toggle Sidebar">
                    <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" x-transition>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>

                    <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" x-transition>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Links -->
            <nav class="flex-1 overflow-y-auto mt-6 px-2 space-y-2" x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-x-3" x-transition:enter-end="opacity-100 translate-x-0">

                {{-- Batch --}}
                <div>
                    <button :class="open === 'batch' ? 'active-group' : ''"
                        @click="open = (open === 'batch' ? '' : 'batch')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Batch</span>
                        <svg :class="open === 'batch' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open === 'batch'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.batch-days.index" label="Create Batch Days" class="ajax-link" />
                    </div>
                </div>

                {{-- Students --}}
                <div>
                    <button :class="open === 'students' ? 'active-group' : ''"
                        @click="open = (open === 'students' ? '' : 'students')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Students</span>
                        <svg :class="open === 'students' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open === 'students'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.students.index" label="All Students" class="ajax-link" />
                        <x-admin-link route="admin.students.ex" label="Ex Students" class="ajax-link" />
                    </div>
                </div>

                {{-- Payments --}}
                <div>
                    <button :class="open === 'payments' ? 'active-group' : ''"
                        @click="open = (open === 'payments' ? '' : 'payments')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Payments</span>
                        <svg :class="open === 'payments' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open === 'payments'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.payments.index" label="All Payments" class="ajax-link" />
                    </div>
                </div>
            </nav>

            <!-- Logout (always visible) -->
            <div class="mt-auto p-2" x-show="sidebarOpen" x-transition>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-3 bg-red-500 text-white font-semibold py-3 rounded-lg shadow-md hover:bg-red-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-4 lg:ml-8 transition-all duration-300" :class="sidebarOpen ? 'ml-4' : 'ml-2'">
            <div id="mainContent"
                class="bg-white h-full rounded-2xl shadow-lg p-6 lg:p-10 overflow-y-auto transition-all">
                @yield('content')
                <!-- Developer Credit Section -->
                <div class="mt-10 border-t pt-6 text-center">
                    <p class="text-gray-600 text-sm mb-3">
                        Software developed by <span class="font-semibold text-gray-800">Musa Md Obayed</span>
                        (<a href="tel:01722402173" class="text-emerald-500 hover:underline">01722402173</a>)
                    </p>
                    <div class="flex justify-center">
                        {!! QrCode::size(65)->generate('https://www.linkedin.com/in/musa-md-obayed-52aa66251/') !!}
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>


</html>