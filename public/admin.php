<?php
session_start();

$adminPass = getenv('ADMIN_PASSWORD') ?: 'latveria2026';
$storageDir = __DIR__ . '/../storage';
$cookiesFile = $storageDir . '/cookies.txt';
$message = '';
$messageType = '';

// Cerrar sesión del panel
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// 1. Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (hash_equals($adminPass, $_POST['password'])) {
        $_SESSION['all_loader_admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $message = 'Acceso denegado: Credenciales inválidas.';
        $messageType = 'error';
    }
}

// 2. Procesar subida de cookies.txt (solo si ya está autenticado)
if (!empty($_SESSION['all_loader_admin']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cookies_file'])) {
    $file = $_FILES['cookies_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Error en la subida (Código PHP: ' . $file['error'] . ').';
        $messageType = 'error';
    } elseif (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'txt') {
        $message = 'Formato inválido: El archivo debe terminar en .txt';
        $messageType = 'error';
    } else {
        $content = file_get_contents($file['tmp_name']);
        
        // Validación flexible: comprobar que no esté vacío y contenga dominios de YouTube o Google
        if (empty(trim($content)) || (strpos($content, 'youtube.com') === false && strpos($content, '.google.com') === false)) {
            $message = 'El archivo .txt no contiene cookies válidas de YouTube o Google.';
            $messageType = 'error';
        } else {
            if (!is_dir($storageDir)) {
                @mkdir($storageDir, 0777, true);
            }

            if (@file_put_contents($cookiesFile, $content) !== false) {
                $message = '✅ Cookies inyectadas correctamente (' . round(strlen($content) / 1024, 1) . ' KB).';
                $messageType = 'success';
            } else {
                $message = 'Error de permisos: Apache no puede escribir en la carpeta storage/. Revisa los permisos del volumen.';
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
    <title>All-loader Admin - Control de Credenciales</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="icon" type="image/jpeg" href="/logo.jpg">
</head>
<body>
    <div class="bg-atmosphere"></div>

    <div class="doom-card">
        <div class="doom-header">
            <span class="doom-badge">Nivel de Seguridad: Latveria</span>
            <h1 class="doom-title">PANEL DE CONTROL</h1>
            <p class="doom-subtitle">Gestión de tokens de extracción</p>
        </div>

        <?php if ($message): ?>
            <div class="doom-result" style="margin-top: 0; margin-bottom: 1.25rem;">
                <p class="<?php echo $messageType === 'success' ? 'text-green-400' : 'text-red-400'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if (empty($_SESSION['all_loader_admin'])): ?>
            <!-- PASO 1: Formulario de acceso -->
            <form method="POST" action="admin.php" class="doom-form">
                <div class="form-group">
                    <label for="password">Clave de Autorización</label>
                    <input type="password" id="password" name="password" required placeholder="Introduce la clave maestra..." class="doom-input">
                </div>
                <button type="submit" class="doom-btn">Autenticar</button>
            </form>
        <?php else: ?>
            <!-- PASO 2: Panel de subida tras autenticar -->
            <div class="doom-result" style="margin-top: 0; margin-bottom: 1.25rem; text-align: left;">
                <p class="text-gray-300" style="font-size: 0.8rem; line-height: 1.5;">
                    <strong>Archivo cookies.txt:</strong> <?php echo $hasCookies ? '<span class="text-green-400">Cargado y activo</span>' : '<span class="text-red-400">No encontrado</span>'; ?><br>
                    <strong>Última actualización:</strong> <?php echo $lastUpdated; ?><br>
                    <strong>Permisos de carpeta:</strong> <?php echo $isWritable ? '<span class="text-green-400">Escritura OK</span>' : '<span class="text-red-400">Bloqueado (Sin permisos)</span>'; ?>
                </p>
            </div>

            <form method="POST" action="admin.php" enctype="multipart/form-data" class="doom-form">
                <div class="form-group">
                    <label for="cookies_file">Subir archivo de sesión (.txt)</label>
                    <input type="file" id="cookies_file" name="cookies_file" accept=".txt" required class="doom-input">
                </div>
                <button type="submit" class="doom-btn">Subir e Inyectar Cookies</button>
            </form>

            <div style="margin-top: 1.25rem; display: flex; justify-content: space-between; font-size: 0.75rem;">
                <a href="/" style="color: #34d399; text-decoration: none;">← Volver a All-loader</a>
                <a href="admin.php?logout=1" style="color: #f87171; text-decoration: none;">Cerrar sesión</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>