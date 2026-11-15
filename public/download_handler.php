<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Downloader\YoutubeDownloader;
use App\Downloader\SpotifyDownloader;

// Recoger los datos enviados por GET o POST desde tu formulario web
$url = $_POST['url'] ?? $_GET['url'] ?? '';
$type = $_POST['type'] ?? 'audio'; // 'audio' o 'video'
$platform = $_POST['platform'] ?? 'youtube'; // 'youtube' o 'spotify'

if (empty($url)) {
    die('URL no proporcionada.');
}

// Instanciar el descargador correspondiente
if ($platform === 'spotify') {
    $downloader = new SpotifyDownloader();
    $result = $downloader->downloadTrack($url);
} else {
    $downloader = new YoutubeDownloader();
    $result = $downloader->download($url, $type);
}

// Verificar si la descarga en el servidor fue exitosa
if (!$result['success'] || empty($result['file']) || !file_exists($result['file'])) {
    die('Error en la descarga: ' . ($result['error'] ?? 'Archivo no encontrado.'));
}

$filePath = $result['file'];
$fileName = basename($filePath);

// Forzar la descarga en el navegador mediante cabeceras HTTP
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

// Limpiar el búfer de salida para evitar archivos corruptos
flush();
readfile($filePath);

// Opcional: Eliminar el archivo del servidor una vez que el usuario se lo ha descargado
@unlink($filePath);
exit;