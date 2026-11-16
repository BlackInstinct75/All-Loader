<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All-loader</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/jpeg" href="logo.jpg">
</head>
<body>

    <!-- Efecto de fondo atmosférico -->
    <div class="bg-atmosphere"></div>

    <!-- Contenedor principal estilo armadura de Doom -->
    <div class="doom-card">
        
        <!-- Cabecera con temática imperial -->
        <div class="doom-header">
            <span class="doom-badge">Portam Secure Systems</span>
            <h1 class="doom-title">PORTAM MULTIMEDIA</h1>
            <p class="doom-subtitle">El poder absoluto sobre YouTube y Spotify</p>
        </div>
        
        <form id="downloadForm" action="download_handler.php" method="POST" class="doom-form">
            <div class="form-group">
                <label for="url">Enlace de Archivo (YouTube / Spotify)</label>
                <input type="url" id="url" name="url" required placeholder="Pega el enlace del objetivo aquí..." class="doom-input">
            </div>

            <div class="form-group">
                <label for="type">Protocolo de Conversión (Solo YouTube)</label>
                <select id="type" name="type" class="doom-select">
                    <option value="video">Vídeo de Alta Definición (MP4)</option>
                    <option value="audio">Audio Maestro (MP3)</option>
                </select>
            </div>

            <button type="submit" id="submitBtn" class="doom-btn">
                Ejecutar Extracción
            </button>
        </form>

        <!-- Indicador de carga -->
        <div id="loading" class="doom-loading hidden">
            <p>⚡ Procesando decreto en los servidores de Portam...</p>
        </div>

        <!-- Contenedor del resultado -->
        <div id="result" class="doom-result hidden"></div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>