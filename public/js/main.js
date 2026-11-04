document.getElementById('downloadForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const urlInput = document.getElementById('url').value;
    const typeInput = document.getElementById('type').value;
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.getElementById('loading');
    const resultDiv = document.getElementById('result');

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
            // Extraemos el nombre limpio del archivo decodificando la URL
            const fileName = decodeURIComponent(data.download_url.split('/').pop());
            
            resultDiv.innerHTML = `
                <div class="space-y-2">
                    <p class="text-green-400 font-semibold text-lg">✔ ¡Ya está descargado!</p>
                    <p class="text-sm text-gray-300 bg-gray-800 p-2 rounded border border-gray-700 truncate" title="${fileName}">
                        📁 <strong>Archivo:</strong> ${fileName}
                    </p>
                    <p class="text-xs text-gray-400">El archivo se ha guardado correctamente en el servidor.</p>
                </div>
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