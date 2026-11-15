<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latverian Media Vault | Doctor Doom Edition</title>
    <!-- Tailwind CSS por CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="logo.jpg">
</head>
<body class="bg-[#0b0f0c] text-emerald-100 flex items-center justify-center min-h-screen relative overflow-hidden">

    <!-- Efecto de fondo atmosférico (Gris acero y verde Latveriano) -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-emerald-950/20 via-gray-950/80 to-[#050706] z-0"></div>

    <!-- Contenedor principal estilo armadura de Doom -->
    <div class="relative z-10 bg-gradient-to-b from-gray-900 to-[#121814] p-8 rounded-xl shadow-[0_0_35px_rgba(6,78,59,0.3)] w-full max-w-md border-2 border-emerald-800/60">
        
        <!-- Cabecera con temática imperial -->
        <div class="text-center mb-8">
            <span class="inline-block px-3 py-1 text-xs font-semibold tracking-widest text-emerald-400 uppercase bg-emerald-950/80 border border-emerald-700/50 rounded-full mb-3">
                Portam Secure Systems
            </span>
            <h1 class="text-3xl font-black tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-gray-200 to-emerald-600 uppercase drop-shadow-md">
                PORTAM MULTIMEDIA
            </h1>
            <p class="text-xs text-gray-400 mt-1">El poder absoluto sobre YouTube y Spotify</p>
        </div>
        
        <form id="downloadForm" action="download_handler.php" method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-2" for="url">
                    Enlace de Archivo (YouTube / Spotify)
                </label>
                <input type="url" id="url" name="url" required placeholder="Pega el enlace del objetivo aquí..." 
                       class="w-full px-4 py-3 bg-gray-950 border border-emerald-800/80 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm text-gray-200 placeholder-gray-600 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-2" for="type">
                    Protocolo de Conversión (Solo YouTube)
                </label>
                <select id="type" name="type" class="w-full px-4 py-3 bg-gray-950 border border-emerald-800/80 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm text-gray-200 transition-all cursor-pointer">
                    <option value="video">Vídeo de Alta Definición (MP4)</option>
                    <option value="audio">Audio Maestro (MP3)</option>
                </select>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-emerald-800 hover:from-emerald-700 to-emerald-950 text-emerald-100 font-bold tracking-wider py-3 rounded-lg border border-emerald-600/50 shadow-lg shadow-emerald-950/50 transition-all duration-200 uppercase text-sm">
                Ejecutar Extracción
            </button>
        </form>

        <!-- Indicador de carga -->
        <div id="loading" class="hidden mt-6 text-center text-emerald-400">
            <p class="animate-pulse text-sm font-semibold tracking-wider">⚡ Procesando decreto en los servidores de Portam...</p>
        </div>

        <!-- Contenedor del resultado -->
        <div id="result" class="hidden mt-6 p-4 rounded-lg bg-gray-950 border border-emerald-900/60 text-center text-sm"></div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>