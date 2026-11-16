document.getElementById('downloadForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const url = document.getElementById('url').value;
    const type = document.getElementById('type').value;
    const loading = document.getElementById('loading');
    const resultDiv = document.getElementById('result');
    const submitBtn = document.getElementById('submitBtn');

    // Detectar plataforma automáticamente según la URL
    const platform = url.includes('spotify.com') ? 'spotify' : 'youtube';

    // UI: Mostrar estado de carga
    loading.classList.remove('hidden');
    resultDiv.classList.add('hidden');
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

    const formData = new FormData();
    formData.append('url', url);
    formData.append('type', type);
    formData.append('platform', platform);

    try {
        const response = await fetch('download_handler.php', {
            method: 'POST',
            body: formData
        });

        // Si el servidor devuelve un error en texto plano o JSON en lugar del archivo
        const contentType = response.headers.get('content-type');
        if (!response.ok || (contentType && contentType.includes('text/html'))) {
            const errorText = await response.text();
            throw new Error(errorText || 'Error al procesar el archivo.');
        }

        // Definir la estructura de nombres personalizada
        let filename;
        if (platform === 'spotify') {
            filename = 'Musica_Spotify.mp3';
        } else {
            filename = type === 'audio' ? 'Musica_Youtube.mp3' : 'Video_Youtube.mp4';
        }

        // Convertir la respuesta binaria en un Blob y forzar la descarga en el navegador
        const blob = await response.blob();
        const downloadUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(downloadUrl);

        // Mostrar confirmación
        resultDiv.innerHTML = `<p class="text-green-400 font-medium">✅ Descarga completada: <span class="text-gray-300">${filename}</span></p>`;
        resultDiv.classList.remove('hidden');

    } catch (error) {
        resultDiv.innerHTML = `<p class="text-red-400 text-sm">❌ ${error.message}</p>`;
        resultDiv.classList.remove('hidden');
    } finally {
        loading.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
});