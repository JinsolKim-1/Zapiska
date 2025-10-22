<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join or Create | Zapiska</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    {{-- Vite compiled CSS --}}
    @vite(['resources/css/welcmain.css'])
</head>
<body class="font-[Inter] bg-[#051A23] overflow-x-hidden">

    {{-- Background Layers --}}
    <div class="hero-background">
        <div class="moon"></div>
        <div class="shooting-star shooting-star-1"></div>
        <div class="shooting-star shooting-star-2"></div>
        <div class="mountain-layer-2"></div>
        <div class="mountain-layer-1"></div>
    </div>

    {{-- Main Content --}}
    <div class="content-container min-h-screen text-white flex flex-col">
        
        {{-- Navigation --}}
        <header class="p-4 md:p-6 flex justify-between items-center bg-transparent">
            <div class="text-xl font-bold text-white tracking-wide">
                ZAPISKA<span style="color: #32e875; text-shadow: 0 0 10px #32e875, 0 0 20px #32e875;">.</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                    class="px-5 py-2 font-semibold rounded-full text-[#32e875] border border-[#32e875] transition-all duration-300 hover:bg-[#32e875] hover:text-[#051A23] hover:shadow-[0_0_15px_#32e875]">
                    Logout
                </button>
            </form>
        </header>

        {{-- Hero Section --}}
        <main class="flex-grow flex flex-col justify-center items-center text-center p-8">
            <h1 class="text-5xl md:text-8xl font-extrabold tracking-tight mb-4 uppercase">
                WELCOME
            </h1>

            <p class="text-lg md:text-xl max-w-3xl text-gray-300 mt-2 mb-10">
                Streamline your asset lifecycle and procurement process with Zapiska. Our platform offers robust asset tracking, automated pre-requisition approval workflows, and comprehensive inventory management, empowering teams to optimize resource utilization and control costs.
            </p>

            <h2 class="text-2xl font-light text-teal-accent mt-8 mb-6">
                Ready to manage your enterprise resources?
            </h2>
            
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="{{ route('company.create')}}"
                 class="cta-button bg-teal-accent text-gray-900 shadow-lg shadow-teal-accent/50 hover:bg-white hover:text-gray-900 transform hover:scale-105">
                    Create Company
                </a>
                
                <button onclick="joinCompany()" class="cta-button border-2 border-teal-accent text-teal-accent hover:bg-teal-accent/10 transform hover:scale-105">
                    Join a Company
                </button>
            </div>
            
            <div id="message-box" class="mt-8 p-3 rounded-lg bg-gray-700/50 text-sm text-gray-200 hidden"></div>
        </main>
    </div>

    {{-- JS --}}
    @vite(['resources/js/welcmain.js'])
</body>
</html>
