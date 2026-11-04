<?php

namespace App\Downloader;

class SpotifyDownloader
{
    private string $clientId;
    private string $clientSecret;
    private YoutubeDownloader $youtubeDownloader;

    public function __construct()
    {
        // Cargamos las credenciales directamente desde las variables de entorno (.env)
        $this->clientId = getenv('SPOTIFY_CLIENT_ID') ?: ($_ENV['SPOTIFY_CLIENT_ID'] ?? '');
        $this->clientSecret = getenv('SPOTIFY_CLIENT_SECRET') ?: ($_ENV['SPOTIFY_CLIENT_SECRET'] ?? '');
        $this->youtubeDownloader = new YoutubeDownloader();
    }

    /**
     * Procesa una URL de Spotify (canción), extrae metadatos y la descarga vía YouTube
     */
    public function downloadTrack(string $spotifyUrl): array
    {
        // 1. Extraer el ID de la pista desde la URL de Spotify
        if (!preg_match('/track\/([a-zA-Z0-9]+)/', $spotifyUrl, $matches)) {
            return ['success' => false, 'file' => null, 'error' => 'URL de Spotify no válida o no soportada (actualmente soportamos canciones individuales).'];
        }

        $trackId = $matches[1];

        // 2. Obtener el Token de acceso de Spotify
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'file' => null, 'error' => 'No se pudo autenticar con la API de Spotify. Revisa tus credenciales.'];
        }

        // 3. Obtener los metadatos de la pista (Artista y Título)
        $trackData = $this->getTrackMetadata($trackId, $accessToken);
        if (!$trackData) {
            return ['success' => false, 'file' => null, 'error' => 'No se pudieron obtener los metadatos de la pista de Spotify.'];
        }

        // Unir artista y título en una sola línea separadas por un espacio
        $searchQuery = $trackData['artist'] . ' ' . $trackData['title'];
        
        // Construir la consulta de búsqueda optimizada para yt-dlp
        $ytSearchUrl = 'ytsearch1:' . $searchQuery;

        // 4. Delegar la descarga en el descargador de YouTube como audio MP3
        return $this->youtubeDownloader->download($ytSearchUrl, 'audio');
    }

    /**
     * Obtiene el token de acceso usando Client Credentials
     */
    private function getAccessToken(): ?string
    {
        $ch = curl_init('https://accounts.spotify.com/api/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    /**
     * Obtiene el título y el artista principal de la pista
     */
    private function getTrackMetadata(string $trackId, string $token): ?array
    {
        $ch = curl_init("https://api.spotify.com/v1/tracks/{$trackId}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);

        if (!isset($data['name'], $data['artists'][0]['name'])) {
            return null;
        }

        return [
            'title' => $data['name'],
            'artist' => $data['artists'][0]['name']
        ];
    }
}