document.getElementById('downloadForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const urlInput = document.getElementById('url').value;
    const typeInput = document.getElementById('type').value;
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.getElementById('loading');
    const resultDiv = document.getElementById('result');

    // Preparar interfaz para carga
    submitBtn.disabled = true;
    loading.classList.remove('hidden');
    resultDiv.classList.add('hidden');
    resultDiv.innerHTML = '';

    try {
        const formData = new URLSearchParams();
        formData.append('url', urlInput);
        formData.append('type', typeInput);

        const response = await fetch('download.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData.toString()
        });

        const data = await response.json();

        if (data.success && data.download_url) {
            resultDiv.innerHTML = `
                <p class="text-green-400 font-medium mb-3">¡Archivo listo para descargar!</p>
                <a href="${data.download_url}" download class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition duration-200">
                    📥 Descargar Archivo
                </a>
            `;
        } else {
            resultDiv.innerHTML = `<p class="text-red-400 font-medium">Error: ${data.error || 'Ocurrió un error desconocido.'}</p>`;
        }
    } catch (err) {
        resultDiv.innerHTML = `<p class="text-red-400 font-medium">Error de conexión con el servidor.</p>`;
    } finally {
        submitBtn.disabled = false;
        loading.classList.add('hidden');
        resultDiv.classList.remove('hidden');
    }
});