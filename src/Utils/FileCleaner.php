<?php

namespace App\Utils;

class FileCleaner
{
    private string $downloadPath;
    private int $maxAgeMinutes;

    public function __construct(string $downloadPath = __DIR__ . '/../../storage/downloads/', int $maxAgeMinutes = 60)
    {
        $this->downloadPath = rtrim($downloadPath, '/') . '/';
        $this->maxAgeMinutes = $maxAgeMinutes; // Por defecto borra archivos de más de 1 hora
    }

    /**
     * Limpia los archivos antiguos de la carpeta de descargas
     */
    public function clean(): void
    {
        if (!is_dir($this->downloadPath)) {
            return;
        }

        $files = glob($this->downloadPath . '*');
        $now = time();

        foreach ($files as $file) {
            // Ignorar si es un directorio o archivos ocultos como .gitkeep
            if (is_dir($file) || basename($file) === '.gitkeep') {
                continue;
            }

            // Comprobar la antigüedad del archivo
            $fileAgeMinutes = ($now - filemtime($file)) / 60;

            if ($fileAgeMinutes > $this->maxAgeMinutes) {
                @unlink($file);
            }
        }
    }
}