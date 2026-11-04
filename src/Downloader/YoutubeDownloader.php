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
        
        // Rutas absolutas a los ejecutables en la raíz del proyecto
        $this->ytDlpPath = realpath(__DIR__ . '/../../yt-dlp.exe') ?: (__DIR__ . '/../../yt-dlp.exe');
        $this->ffmpegPath = realpath(__DIR__ . '/../../') ?: (__DIR__ . '/../../');
    }

    /**
     * Obtiene la información del video de YouTube sin descargarlo
     */
    public function getVideoInfo(string $url): ?array
    {
        $command = sprintf('%s --dump-json %s', escapeshellcmd($this->ytDlpPath), escapeshellarg($url));
        
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
     * Descarga contenido de YouTube (Audio o Video) con nombres secuenciales
     */
    public function download(string $url, string $type = 'audio'): array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'file' => null, 'error' => 'URL no válida.'];
        }

        // Definir el prefijo según el tipo de descarga
        $prefix = ($type === 'audio') ? 'Audio_' : 'Video_';

        // Escanear la carpeta de descargas para calcular el siguiente número secuencial
        $files = glob($this->downloadPath . $prefix . '*');
        $maxNum = 0;
        foreach ($files as $file) {
            $basename = pathinfo($file, PATHINFO_FILENAME);
            if (preg_match('/^' . $prefix . '(\d+)$/', $basename, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        $nextNum = $maxNum + 1;

        // Construir la plantilla de salida con el nombre secuencial (ej. Video_1.mp4, Audio_2.mp3)
        $outputTemplate = $this->downloadPath . $prefix . $nextNum . '';

        if ($type === 'audio') {
            $command = sprintf(
                '%s --ffmpeg-location %s -x --audio-format mp3 --audio-quality 0 -o %s %s',
                escapeshellcmd($this->ytDlpPath),
                escapeshellarg($this->ffmpegPath),
                escapeshellarg($outputTemplate),
                escapeshellarg($url)
            );
        } else {
            $command = sprintf(
                '%s --ffmpeg-location %s -f "bv*+ba/b" --merge-output-format mp4 -o %s %s',
                escapeshellcmd($this->ytDlpPath),
                escapeshellarg($this->ffmpegPath),
                escapeshellarg($outputTemplate),
                escapeshellarg($url)
            );
        }

        $output = [];
        $resultCode = 0;
        exec($command . ' 2>&1', $output, $resultCode);

        if ($resultCode !== 0) {
            return [
                'success' => false,
                'file' => null,
                'error' => 'Error al descargar con yt-dlp: ' . implode("\n", $output)
            ];
        }

        return [
            'success' => true,
            'file' => $this->getLatestDownloadedFile(),
            'error' => null
        ];
    }

    private function getLatestDownloadedFile(): ?string
    {
        $files = glob($this->downloadPath . '*');
        if (empty($files)) {
            return null;
        }

        $validFiles = array_filter($files, fn($f) => !str_ends_with($f, '.part') && !str_ends_with($f, '.ytdl'));
        if (empty($validFiles)) {
            $validFiles = $files;
        }

        usort($validFiles, fn($a, $b) => filemtime($b) - filemtime($a));

        return realpath($validFiles[0]);
    }
}