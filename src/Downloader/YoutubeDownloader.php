<?php

namespace App\Downloader;

class YoutubeDownloader
{
    private string $downloadPath;
    private string $ytDlpPath;
    private string $ffmpegPath;

    public function __construct(string $downloadPath = __DIR__ . '/../../storage/downloads/')
    {
        $this->downloadPath = rtrim($downloadPath, '/') . '/';

        // Asegurar que el directorio temporal existe
        if (!is_dir($this->downloadPath)) {
            mkdir($this->downloadPath, 0775, true);
        }

        // Detectar automáticamente si corre en Windows (local) o Linux (Docker)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $baseDir = realpath(__DIR__ . '/../../') ?: (__DIR__ . '/../../');
            $this->ytDlpPath = $baseDir . DIRECTORY_SEPARATOR . 'yt-dlp.exe';
            $this->ffmpegPath = $baseDir;
        } else {
            // En Docker (Linux) usa los binarios instalados en el sistema
            $this->ytDlpPath = 'yt-dlp';
            $this->ffmpegPath = '/usr/bin';
        }
    }

    /**
     * Obtiene la información del video de YouTube sin descargarlo
     */
    public function getVideoInfo(string $url): ?array
    {
        $command = sprintf(
            '%s --dump-json %s 2>&1',
            escapeshellcmd($this->ytDlpPath),
            escapeshellarg($url)
        );

        $output = [];
        $resultCode = 0;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || empty($output)) {
            return null;
        }

        $info = json_decode(implode("\n", $output), true);

        return [
            'title' => $info['title'] ?? 'Desconocido',
            'duration' => $info['duration_string'] ?? '',
            'thumbnail' => $info['thumbnail'] ?? '',
            'uploader' => $info['uploader'] ?? ''
        ];
    }

    /**
     * Descarga contenido de YouTube (Audio o Video) y devuelve la ruta del archivo listo
     */
    public function download(string $url, string $type = 'audio'): array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'file' => null, 'error' => 'URL no válida.'];
        }

        $prefix = ($type === 'audio') ? 'Audio_' : 'Video_';
        $uniqueId = uniqid();
        
        // Plantilla con %(ext)s para que yt-dlp y ffmpeg gestionen correctamente la extensión
        $outputTemplate = $this->downloadPath . $prefix . $uniqueId . '.%(ext)s';

        if ($type === 'audio') {
            $command = sprintf(
                '%s --ffmpeg-location %s -x --audio-format mp3 --audio-quality 0 -o %s %s 2>&1',
                escapeshellcmd($this->ytDlpPath),
                escapeshellarg($this->ffmpegPath),
                escapeshellarg($outputTemplate),
                escapeshellarg($url)
            );
        } else {
            $command = sprintf(
                '%s --ffmpeg-location %s -f "bv*+ba/b" --merge-output-format mp4 -o %s %s 2>&1',
                escapeshellcmd($this->ytDlpPath),
                escapeshellarg($this->ffmpegPath),
                escapeshellarg($outputTemplate),
                escapeshellarg($url)
            );
        }

        $output = [];
        $resultCode = 0;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            return [
                'success' => false,
                'file' => null,
                'error' => 'Error al descargar con yt-dlp: ' . implode("\n", $output)
            ];
        }

        // Buscar el archivo generado con ese uniqueId
        $generatedFiles = glob($this->downloadPath . $prefix . $uniqueId . '.*');
        $finalFile = !empty($generatedFiles) ? realpath($generatedFiles[0]) : null;

        if (!$finalFile || !file_exists($finalFile)) {
            return [
                'success' => false,
                'file' => null,
                'error' => 'La descarga finalizó pero no se encontró el archivo resultante.'
            ];
        }

        return [
            'success' => true,
            'file' => $finalFile,
            'error' => null
        ];
    }
}