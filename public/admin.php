<?php
session_start();

// Contraseña de acceso (puedes definir ADMIN_PASSWORD en Render/Docker o cambiar el valor por defecto)
$adminPass = getenv('ADMIN_PASSWORD') ?: 'latveria2026';
$cookiesFile = __DIR__ . '/../storage/cookies.txt';
$message = '';
$messageType = '';

// Cerrar sesión del panel
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (hash_equals($adminPass, $_POST['password'])) {
        $_SESSION['all_loader_admin'] = true;
    } else {
        $message = 'Acceso denegado: Credenciales inválidas.';
        $messageType = 'error';
    }
}

// Procesar subida de cookies.txt
if (!empty($_SESSION['portam_admin']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cookies_file'])) {
    $file = $_FILES['cookies_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Error al subir el archivo.';
        $messageType = 'error';
    } elseif (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'txt') {
        $message = 'Formato inválido: Debe ser un archivo .txt';
        $messageType = 'error';
    } else {
        $content = file_get_contents($file['tmp_name']);
        // Verificar que parezca un archivo de cookies en formato Netscape
        if (strpos($content, 'youtube.com') === false && strpos($content, '# Netscape HTTP Cookie File') === false) {
            $message = 'El archivo no contiene cookies válidas de YouTube.';
            $messageType = 'error';
        } else {
            if (!is_dir(dirname($cookiesFile))) {
                mkdir(dirname($cookiesFile), 0775, true);
            }
            if (file_put_contents($cookiesFile, $content) !== false) {
                $message = 'Protocolo actualizado: cookies.txt desplegado en el servidor.';
                $messageType = 'success';
            } else {
                $message = 'Error de permisos al escribir en storage/cookies.txt.';
                $messageType = 'error';
            }
        }
    }
}

$hasCookies = file_exists($cookiesFile);
$lastUpdated = $hasCookies ? date('d/m/Y H:i:s', filemtime($cookiesFile)) : 'Sin archivo activo';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portam Admin - Control de Credenciales</title>
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

        <?php if (empty($_SESSION['portam_admin'])): ?>
            <!-- Formulario de acceso -->
            <form method="POST" class="doom-form">
                <div class="form-group">
                    <label for="password">Clave de Autorización</label>
                    <input type="password" id="password" name="password" required placeholder="Introduce la clave maestra..." class="doom-input">
                </div>
                <button type="submit" class="doom-btn">Autenticar</button>
            </form>
        <?php else: ?>
            <!-- Panel de subida -->
            <div class="doom-result" style="margin-top: 0; margin-bottom: 1.25rem; text-align: left;">
                <p class="text-gray-300" style="font-size: 0.8rem;">
                    <strong>Estado:</strong> <?php echo $hasCookies ? '<span class="text-green-400">Activo</span>' : '<span class="text-red-400">No configurado</span>'; ?><br>
                    <strong>Última actualización:</strong> <?php echo $lastUpdated; ?>
                </p>
            </div>

            <form method="POST" enctype="multipart/form-data" class="doom-form">
                <div class="form-group">
                    <label for="cookies_file">Archivo de Sesión (cookies.txt)</label>
                    <input type="file" id="cookies_file" name="cookies_file" accept=".txt" required class="doom-input">
                </div>
                <button type="submit" class="doom-btn">Inyectar Cookies</button>
            </form>

            <div style="margin-top: 1.25rem; display: flex; justify-content: space-between; font-size: 0.75rem;">
                <a href="/" style="color: #34d399; text-decoration: none;">← Volver al extractor</a>
                <a href="?logout=1" style="color: #f87171; text-decoration: none;">Cerrar sesión</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>