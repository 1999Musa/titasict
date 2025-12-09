<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TITAS ICT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #111827; /* Dark gray-blue background */
            overflow: hidden; /* Hide scrollbars */
            position: relative;
        }

        .aurora-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
        }
        
        /* Creates a soft, moving aurora-like effect */
        .aurora-background::before,
        .aurora-background::after {
            content: '';
            position: absolute;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            filter: blur(150px);
            opacity: 0.15;
            z-index: -1;
        }

        .aurora-background::before {
            background: radial-gradient(circle, #4F46E5, transparent 60%);
            top: -20%;
            left: -20%;
            animation: moveAurora1 25s infinite alternate;
        }

        .aurora-background::after {
            background: radial-gradient(circle, #10B981, transparent 60%);
            bottom: -20%;
            right: -20%;
            animation: moveAurora2 25s infinite alternate;
        }

        @keyframes moveAurora1 {
            from { transform: translate(-20%, -20%) rotate(0deg); }
            to { transform: translate(20%, 20%) rotate(360deg); }
        }
        
        @keyframes moveAurora2 {
            from { transform: translate(20%, 20%) rotate(0deg); }
            to { transform: translate(-20%, -20%) rotate(-360deg); }
        }

        .welcome-card {
            position: relative;
            z-index: 1;
            background: rgba(17, 24, 39, 0.6); /* Semi-transparent dark bg */
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            animation: fadeIn 1s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        
        .login-btn {
            background: linear-gradient(45deg, #3B82F6, #1D4ED8);
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
         .developer-link {
            position: absolute;
            margin-top: 25%;
            text-align: center;
            width: 100%;
        }

    </style>
</head>
<body class="min-h-screen flex items-center justify-center">

    <div class="aurora-background"></div>

    <div class="welcome-card rounded-3xl p-12 text-center shadow-2xl w-full max-w-md mx-4">
        <h1 class="text-4xl font-extrabold text-white mb-3 tracking-tight">
            Titas ICT
        </h1>
        <p class="text-lg text-gray-300 mb-8">
            Please log in to manage your dashboard.
        </p>
        
        <a href="{{ route('login') }}" class="login-btn inline-block text-white font-bold px-8 py-3 rounded-full shadow-lg">
            Proceed to Login
        </a>
    </div>
    <div class="developer-link text-white text-sm">
        Developed by <a href="https://www.linkedin.com/in/musa-md-obayed-52aa66251/" class="text-indigo-300 hover:underline " target="_blank">Musa Md Obayed</a>
        <p>( 01722402173 )</p>
    </div>

</body>
</html>
