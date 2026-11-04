<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargador YouTube & Spotify</title>
    <!-- Tailwind CSS por CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-gray-800 p-8 rounded-xl shadow-2xl w-full max-w-md border border-gray-700">
        <h1 class="text-2xl font-bold mb-6 text-center text-indigo-400">Descargador Multimedia</h1>
        
        <form id="downloadForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="url">URL de YouTube o Spotify</label>
                <input type="url" id="url" name="url" required placeholder="https://www.youtube.com/... o https://open.spotify.com/..." 
                       class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg focus:outline-none focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="type">Formato (Solo para YouTube)</label>
                <select id="type" name="type" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg focus:outline-none focus:border-indigo-500 text-sm">
                    <option value="video">Video (MP4)</option>
                    <option value="audio">Audio (MP3)</option>
                </select>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg transition duration-200">
                Procesar y Descargar
            </button>
        </form>

        <!-- Indicador de carga -->
        <div id="loading" class="hidden mt-6 text-center text-indigo-300">
            <p class="animate-pulse">⏳ Procesando contenido, esto puede tardar unos segundos...</p>
        </div>

        <!-- Contenedor del resultado -->
        <div id="result" class="hidden mt-6 p-4 rounded-lg bg-gray-900 border border-gray-700 text-center"></div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>