<?php

namespace App\Downloader;

class YoutubeDownloader
{
    private string $downloadPath;
    private string $ytDlpPath;

    public function __construct(string $downloadPath = __DIR__ . '/../../storage/downloads/')
    {
        $this->downloadPath = rtrim($downloadPath, '/') . '/';
        
        // Si yt-dlp está en el PATH del sistema, basta con llamarlo por su nombre.
        // Si prefieres una ruta absoluta en Windows, puedes cambiarlo aquí.
        $this->ytDlpPath = 'yt-dlp'; 
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

        // Nombre de archivo de salida template (usando ID único y título)
        $outputTemplate = $this->downloadPath . '%(title)s.%(ext)s';

        // Construir el comando según el tipo
        if ($type === 'audio') {
            // Descargar mejor audio y convertir a MP3 usando ffmpeg
            $command = sprintf(
                '%s -x --audio-format mp3 --audio-quality 0 -o %s %s',
                escapeshellcmd($this->ytDlpPath),
                escapeshellarg($outputTemplate),
                escapeshellarg($url)
            );
        } else {
            // Descargar mejor video y mejor audio combinados
            $command = sprintf(
                '%s -f "bv*+ba/b" --merge-output-format mp4 -o %s %s',
                escapeshellcmd($this->ytDlpPath),
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
        // (Nota: en un entorno de producción masivo convendría parsear la salida de yt-dlp, 
        // pero para empezar, buscar el archivo más reciente o extraerlo del log es funcional).
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

        // Ordenar por fecha de modificación (el más reciente primero)
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        return realpath($files[0]);
    }
}