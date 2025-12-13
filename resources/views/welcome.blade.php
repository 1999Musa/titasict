<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coaching Pro | Titas ICT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&family=Baloo+Da+2:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0F172A;
            /* Slate 900 - Dark Blue/Black */
            overflow: hidden;
            position: relative;
        }

        /* Reusable Colors */
        .color-primary-green {
            background-color: #10B981;
        }

        .color-accent-gold {
            color: #FBBF24;
        }


        .welcome-card {
            position: relative;
            z-index: 1;
            /* Darker, higher contrast card background */
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(30px);
            animation: fadeIn 1.2s ease-out forwards;
        }


        .login-btn {
            background: linear-gradient(45deg, #101b79, #053123);
            border: .5px solid #e4e6e5;
            /* border-color: #ffffff; */
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .login-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.5);
        }

        .developer-link {
            /* Separator line style */
            border-top: 1px dashed rgba(255, 255, 255, 0.15);
        }

        .title-font {
            font-family: 'Baloo Da 2', sans-serif;
        }

        /* Styling for the QR code background (if necessary, though the QR code itself should stand out) */
        .qr-container>svg {
            background-color: white !important;
            /* Ensure QR code is readable */
            padding: 5px;
            /* Small padding around the QR code */
            border-radius: 6px;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">

    {{-- <div class=""></div> --}}

    <div
        class="welcome-card rounded-2xl md:rounded-[2rem] p-8 md:p-12 text-center shadow-2xl w-full max-w-sm md:max-w-md mx-4">

        <div class="mb-2">
            <svg class="mx-auto w-14 h-14 text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 14l9-5-9-5-9 5 9 5zm0 0v7m-9-7h18" class="text-emerald-500"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 12h14v1a1 1 0 01-1 1H6a1 1 0 01-1-1v-1z" class="text-blue-500"></path>
            </svg>
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white title-font tracking-tighter">
                Coaching Pro
            </h1>
            <h2 class="text-lg text-amber-400 mt-1 mb-2">
                Titas ICT (Radhabollov, Rangpur)
            </h2>
        </div>

        <p class="text-md text-gray-100 mb-8 leading-relaxed">
            স্বাগতম! আপনার কোচিং সেন্টার পরিচালনার জন্য সেরা সমাধান।
        </p>

        <a href="{{ route('login') }}"
            class="login-btn inline-block text-white font-bold text-lg px-10 py-4 rounded-full shadow-xl uppercase tracking-wider mb-5">
            লগইন / Login
        </a>

        <div class="developer-link pt-6 text-center">
            <p class="text-gray-400 text-sm mb-3">
                Developed By / ডেভলপ করেছেন: <br>
                <a href="https://www.linkedin.com/in/musa-md-obayed-52aa66251/"
                    class=" font-semibold text-emerald-400" target="_blank">Musa Md Obayed</a>
                (<a href="tel:01722402173" class="text-blue-400 hover:underline">01722402173</a>)
            </p>
            <div class="flex justify-center qr-container">
                {!! QrCode::size(65)->generate('https://www.linkedin.com/in/musa-md-obayed-52aa66251/') !!}
            </div>
            <p class="text-xs text-gray-300 mt-2">Scan for Developer's Portfolio (LinkedIn)</p>
        </div>

    </div>

</body>

</html>