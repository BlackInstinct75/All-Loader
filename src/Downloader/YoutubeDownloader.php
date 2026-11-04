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
        
        // Apuntar directamente a la ruta absoluta del ejecutable de yt-dlp guardado en la raíz del proyecto
        $this->ytDlpPath = realpath(__DIR__ . '/../../yt-dlp.exe');
        if (!$this->ytDlpPath) {
            $this->ytDlpPath = __DIR__ . '/../../yt-dlp.exe';
        }

        // Definir la ruta donde se encuentran ffmpeg.exe y ffprobe.exe (en la raíz del proyecto)
        $this->ffmpegPath = realpath(__DIR__ . '/../../') ?: (__DIR__ . '/../../');
    }

    /**
     * Descarga contenido de YouTube (Audio o Video)
     * 
     * @param string $url URL de YouTube
     * @param string $type 'audio' o 'video'
     * @return array ['success' => bool, 'file' => string|null, 'error' => string|null]
     */
    public function download(string $url, string $type = 'audio'): array
    {
        // Validación básica de URL para evitar inyecciones en la shell
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'file' => null, 'error' => 'URL no válida.'];
        }

        // Usamos doble porcentaje (%%) para que Windows no corrompa las variables internas de yt-dlp
        $outputTemplate = $this->downloadPath . '%%(title)s [%%(id)s].%%(ext)s';

        // Construir el comando incluyendo --ffmpeg-location
        if ($type === 'audio') {
            // Descargar mejor audio, convertir a MP3 usando FFmpeg local y limpiar fragmentos
            $command = sprintf(
                '%s --ffmpeg-location %s -x --audio-format mp3 --audio-quality 0 -o %s %s',
                escapeshellcmd($this->ytDlpPath),
                escapeshellarg($this->ffmpegPath),
                escapeshellarg($outputTemplate),
                escapeshellarg($url)
            );
        } else {
            // Descargar mejor video y audio, fusionarlos en MP4 y limpiar fragmentos
            $command = sprintf(
                '%s --ffmpeg-location %s -f "bv*+ba/b" --merge-output-format mp4 -o %s %s',
                escapeshellcmd($this->ytDlpPath),
                escapeshellarg($this->ffmpegPath),
                escapeshellarg($outputTemplate),
                escapeshellarg($url)
            );
        }

        // Ejecutar el comando en el sistema
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

        // Intentar localizar el archivo descargado recientemente en la carpeta
        return [
            'success' => true,
            'file' => $this->getLatestDownloadedFile(),
            'error' => null
        ];
    }

    /**
     * Devuelve el archivo más reciente en la carpeta de descargas
     */
    private function getLatestDownloadedFile(): ?string
    {
        $files = glob($this->downloadPath . '*');
        if (empty($files)) {
            return null;
        }

        // Filtrar archivos temporales o parciales de descarga
        $validFiles = array_filter($files, fn($f) => !str_ends_with($f, '.part') && !str_ends_with($f, '.ytdl'));
        if (empty($validFiles)) {
            $validFiles = $files;
        }

        // Ordenar por fecha de modificación (el más reciente primero)
        usort($validFiles, fn($a, $b) => filemtime($b) - filemtime($a));

        return realpath($validFiles[0]);
    }
}