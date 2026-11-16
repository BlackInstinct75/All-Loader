<?php
$storageDir = __DIR__ . '/../storage';
$cookiesFile = $storageDir . '/cookies.txt';
$message = '';
$messageType = '';

// Procesar subida directa de cookies.txt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cookies_file'])) {
    $file = $_FILES['cookies_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Error al recibir el archivo (Código: ' . $file['error'] . ').';
        $messageType = 'error';
    } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'txt') {
        $message = 'Formato inválido: debe ser un archivo .txt';
        $messageType = 'error';
    } else {
        $content = file_get_contents($file['tmp_name']);

        if (empty(trim($content))) {
            $message = 'El archivo está vacío.';
            $messageType = 'error';
        } else {
            if (!is_dir($storageDir)) {
                @mkdir($storageDir, 0777, true);
            }

            if (@file_put_contents($cookiesFile, $content) !== false) {
                $message = '✅ Cookies guardadas correctamente (' . round(strlen($content) / 1024, 1) . ' KB).';
                $messageType = 'success';
            } else {
                $message = 'Error de permisos: no se pudo escribir en storage/cookies.txt.';
                $messageType = 'error';
            }
        }
    }
}

$hasCookies = file_exists($cookiesFile) && filesize($cookiesFile) > 0;
$lastUpdated = $hasCookies ? date('d/m/Y H:i:s', filemtime($cookiesFile)) : 'Ninguno';
$isWritable = is_dir($storageDir) ? is_writable($storageDir) : is_writable(dirname($storageDir));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All-loader Admin - Subida Directa</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="icon" type="image/jpeg" href="/logo.jpg">
</head>
<body>
    <div class="bg-atmosphere"></div>

    <div class="doom-card">
        <div class="doom-header">
            <span class="doom-badge">All-loader Systems</span>
            <h1 class="doom-title">GESTOR DE COOKIES</h1>
            <p class="doom-subtitle">Inyección directa de sesión</p>
        </div>

        <?php if ($message): ?>
            <div class="doom-result" style="margin-top: 0; margin-bottom: 1.25rem;">
                <p class="<?php echo $messageType === 'success' ? 'text-green-400' : 'text-red-400'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="doom-result" style="margin-top: 0; margin-bottom: 1.25rem; text-align: left;">
            <p class="text-gray-300" style="font-size: 0.8rem; line-height: 1.5;">
                <strong>Archivo cookies.txt:</strong> <?php echo $hasCookies ? '<span class="text-green-400">Activo</span>' : '<span class="text-red-400">No encontrado</span>'; ?><br>
                <strong>Última actualización:</strong> <?php echo $lastUpdated; ?><br>
                <strong>Permisos de carpeta:</strong> <?php echo $isWritable ? '<span class="text-green-400">Escritura OK</span>' : '<span class="text-red-400">Sin permisos</span>'; ?>
            </p>
        </div>

        <form method="POST" action="admin.php" enctype="multipart/form-data" class="doom-form">
            <div class="form-group">
                <label for="cookies_file">Seleccionar archivo cookies.txt</label>
                <input type="file" id="cookies_file" name="cookies_file" accept=".txt" required class="doom-input">
            </div>
            <button type="submit" class="doom-btn">Subir e Inyectar</button>
        </form>

        <div style="margin-top: 1.25rem; text-align: center; font-size: 0.75rem;">
            <a href="/" style="color: #34d399; text-decoration: none;">← Volver a All-loader</a>
        </div>
    </div>
</body>
</html>