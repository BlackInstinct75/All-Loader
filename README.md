# Downlogastder

Aplicación de escritorio sencilla para descargar vídeos de YouTube usando [yt-dlp](https://github.com/yt-dlp/yt-dlp), con una interfaz gráfica hecha en Tkinter.

## Características

- Interfaz gráfica simple: pega el link y pulsa "Descargar".
- Usa las cookies de tu navegador (Firefox por defecto) para evitar errores de autenticación o bloqueos.
- Sistema de reintento automático entre varios navegadores si el principal falla.
- Descarga en segundo plano (no congela la ventana mientras se descarga).
- Configurado para usar el cliente `android` de YouTube, lo que evita muchos errores `403 Forbidden` comunes con yt-dlp.

## Requisitos

- Python 3.9 o superior
- [yt-dlp](https://pypi.org/project/yt-dlp/)
- Tkinter (incluido por defecto en la mayoría de instalaciones de Python)

Instala las dependencias con:

```bash
pip install yt-dlp
```

> **Nota:** Tkinter normalmente viene incluido con Python. Si no está disponible en tu sistema (algunas distros de Linux lo separan), instálalo con el gestor de paquetes de tu sistema operativo (por ejemplo `sudo apt install python3-tk` en Ubuntu/Debian).

## Uso

1. Ejecuta el script:

   ```bash
   python downlogastder.py
   ```

2. Se abrirá una ventana donde podrás pegar la URL del vídeo de YouTube.
3. Pulsa **Descargar**.
4. Espera a que aparezca el mensaje de confirmación "¡Video descargado!".

El vídeo se guardará en la misma carpeta desde donde se ejecuta el script, salvo que se configure una ruta de salida distinta.

## Configuración de navegadores para cookies

Por defecto, la app intenta leer las cookies de sesión de YouTube desde estos navegadores, en este orden:

```python
NAVEGADORES = ['firefox', 'chrome', 'edge']
```

Si solo usas un navegador (por ejemplo, Firefox), puedes simplificar esta lista para evitar intentos innecesarios:

```python
NAVEGADORES = ['firefox']
```

> **Importante:** para que la lectura de cookies funcione correctamente, se recomienda cerrar el navegador antes de ejecutar la descarga, ya que algunos navegadores bloquean el acceso a su base de datos de cookies mientras están abiertos.

## Convertir en ejecutable (opcional)

Si quieres distribuir la app como un `.exe` (Windows) o binario (Linux/Mac) sin necesidad de tener Python instalado, puedes usar [PyInstaller](https://pyinstaller.org/):

```bash
pip install pyinstaller
pyinstaller --onefile --windowed --collect-all yt_dlp downlogastder.py
```

El ejecutable resultante se generará en la carpeta `dist/`.

## Limitaciones conocidas

- Requiere tener el navegador indicado instalado y con sesión iniciada en YouTube.
- El ejecutable generado con PyInstaller solo funciona en el sistema operativo donde se compiló.
- yt-dlp depende de que YouTube no cambie su API interna; si los errores `403 Forbidden` reaparecen, actualiza yt-dlp:

  ```bash
  pip install -U yt-dlp
  ```

## Licencia

Uso personal / educativo.


C:\xampp\php\php.exe -S 127.0.0.1:8000 -t public