<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Si usas autoloader, o require manual de clases

use App\Downloader\YoutubeDownloader;
use App\Downloader\SpotifyDownloader;
use App\Utils\FileCleaner;

// Cabecera para responder en JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

$url = trim($_POST['url'] ?? '');
$type = trim($_POST['type'] ?? 'audio'); // 'audio' o 'video'

if (empty($url)) {
    echo json_encode(['success' => false, 'error' => 'Por favor, introduce una URL válida.']);
    exit;
}

// Ejecutar limpieza preventiva de archivos antiguos antes de procesar
$cleaner = new FileCleaner();
$cleaner->clean();

$result = ['success' => false, 'error' => 'URL no soportada.'];

// Detectar plataforma según la URL
if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
    $downloader = new YoutubeDownloader();
    $result = $downloader->download($url, $type);
} elseif (str_contains($url, 'spotify.com')) {
    $downloader = new SpotifyDownloader();
    $result = $downloader->downloadTrack($url);
}

// Si la descarga fue exitosa, preparamos la ruta relativa web para que el usuario pueda descargar el archivo
if ($result['success'] && !empty($result['file'])) {
    $fileName = basename($result['file']);
    $result['download_url'] = '../storage/downloads/' . urlencode($fileName);
    // Limpiamos la ruta absoluta por seguridad interna en el JSON de salida
    unset($result['file']); 
}

echo json_encode($result);