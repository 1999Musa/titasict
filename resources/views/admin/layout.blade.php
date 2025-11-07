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

    <div class="flex h-full max-h-[95vh]" x-data="{ open: localStorage.getItem('openGroup') || '' }"
        x-init="$watch('open', val => localStorage.setItem('openGroup', val))">

        <!-- Sidebar -->
        <aside class="w-72 bg-white rounded-2xl shadow-lg flex flex-col p-6">
            <div class="p-2 border-b border-gray-200">
                <div class="flex items-center gap-3">
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
                        <p class="text-xs text-gray-500">Manage your world, beautifully</p>
                    </div>
                </div>
            </div>

            <nav x-data class="flex-1 overflow-y-auto mt-6 px-2 space-y-2">

                {{-- LANDING PAGE --}}
                    {{-- <div>
                        <button :class="open === 'landing' ? 'active-group' : ''"
                            @click="open = (open === 'landing' ? '' : 'landing')"
                            class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                            <span>Landing Page</span>
                            <svg :class="open === 'landing' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open === 'landing'" x-collapse class="ml-4 mt-1 space-y-1">
                            <x-admin-link route="admin.logo.index" label="Logo" class="ajax-link" />
                            <x-admin-link route="admin.hero-sliders.index" label="Hero Sliders" class="ajax-link" />
                            <x-admin-link route="admin.product-sliders.index" label="Product Sliders" class="ajax-link" />
                            <x-admin-link route="admin.front-factory.index" label="Front Factory" class="ajax-link" />
                            <x-admin-link route="admin.certified-logos.index" label="Certified Logos" class="ajax-link" />
                            <x-admin-link route="admin.short-story.index" label="Short Story Video" class="ajax-link" />
                        </div>
                    </div> --}}

                {{-- ABOUT US --}}
                {{-- <div>
                    <button :class="open === 'about' ? 'active-group' : ''"
                        @click="open = (open === 'about' ? '' : 'about')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>About Us</span>
                        <svg :class="open === 'about' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'about'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.about-hero.index" label="About Hero" class="ajax-link" />
                        <x-admin-link route="admin.team-members.index" label="Team Members" class="ajax-link" />
                        <x-admin-link route="admin.clients.index" label="Clients" class="ajax-link" />
                    </div>
                </div> --}}

                {{-- PRODUCTS --}}
                {{-- <div>
                    <button :class="open === 'products' ? 'active-group' : ''"
                        @click="open = (open === 'products' ? '' : 'products')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Products</span>
                        <svg :class="open === 'products' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'products'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.categories.index" label=" Categories" class="ajax-link" />
                        <x-admin-link route="admin.products.index" label="Products" class="ajax-link" />
                    </div>
                </div> --}}

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
                    </div>
                </div>

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




                {{-- WHY CHOOSE ARBELLA --}}
                {{-- <div>
                    <button :class="open === 'choose' ? 'active-group' : ''"
                        @click="open = (open === 'choose' ? '' : 'choose')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Why Choose Arbella</span>
                        <svg :class="open === 'choose' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'choose'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.chooseimg.index" label="Choose Section Hero" class="ajax-link" />
                    </div>
                </div> --}}

                {{-- OUR FACTORY --}}
                {{-- <div>
                    <button :class="open === 'factory' ? 'active-group' : ''"
                        @click="open = (open === 'factory' ? '' : 'factory')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Our Factory</span>
                        <svg :class="open === 'factory' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'factory'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.excellence.index" label="Excellence Section" class="ajax-link" />
                        <x-admin-link route="admin.factory.index" label="Factory" class="ajax-link" />
                    </div>
                </div> --}}

                {{-- SUSTAINABILITY --}}
                {{-- <div>
                    <button :class="open === 'sustain' ? 'active-group' : ''"
                        @click="open = (open === 'sustain' ? '' : 'sustain')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Sustainability</span>
                        <svg :class="open === 'sustain' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'sustain'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.sustainability.index" label="Hero Image" class="ajax-link" />
                    </div>
                </div> --}}

                {{-- COMMUNITY --}}
                {{-- <div>
                    <button :class="open === 'community' ? 'active-group' : ''"
                        @click="open = (open === 'community' ? '' : 'community')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Community</span>
                        <svg :class="open === 'community' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'community'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.community.index" label="Community" class="ajax-link" />
                    </div>
                </div> --}}

                {{-- CONTACT US --}}
                {{-- <div>
                    <button :class="open === 'contact' ? 'active-group' : ''"
                        @click="open = (open === 'contact' ? '' : 'contact')"
                        class="flex justify-between items-center w-full px-4 py-3 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        <span>Contact Us</span>
                        <svg :class="open === 'contact' ? 'rotate-180' : ''" class="w-4 h-4 transition-transform"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 'contact'" x-collapse class="ml-4 mt-1 space-y-1">
                        <x-admin-link route="admin.contacthero.index" label="Contact Hero" class="ajax-link" />
                    </div>
                </div> --}}
            </nav>

            <div class="mt-auto p-2">
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
        <main class="flex-1 ml-4 lg:ml-8">
            <div id="mainContent" class="bg-white h-full rounded-2xl shadow-lg p-6 lg:p-10 overflow-y-auto">
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">@yield('title')</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage your students, Smoothly</p>
                    </div>
                    <a href="{{ url('/') }}" target="_blank"
                        class="flex items-center gap-2 bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-600 shadow-md transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Manage profile
                    </a>
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const mainContent = document.getElementById("mainContent");

            document.querySelectorAll(".ajax-link").forEach(link => {
                link.addEventListener("click", function (e) {
                    e.preventDefault();
                    const url = this.getAttribute("href");

                    // highlight current link
                    document.querySelectorAll(".ajax-link").forEach(l => l.classList.remove("active-link"));
                    this.classList.add("active-link");

                    // load content
                    fetch(url)
                        .then(res => res.text())
                        .then(html => {
                            mainContent.innerHTML = html;
                            history.pushState({ page: url }, "", url);
                        })
                        .catch(err => {
                            mainContent.innerHTML = `<div class='text-red-500'>Error loading content: ${err}</div>`;
                        });
                });
            });

            // Back button navigation
            window.addEventListener("popstate", e => {
                if (e.state?.page) {
                    fetch(e.state.page)
                        .then(res => res.text())
                        .then(html => mainContent.innerHTML = html);
                }
            });
        });
    </script>

</body>

</html>