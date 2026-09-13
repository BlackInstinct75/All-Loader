import yt_dlp

url = input("Introduce el Url: ")

navegadores = ['firefox', 'chrome', 'edge', 'opera', 'brave']

descargado = False
for nav in navegadores:
    try:
        ydl_opts = {
            'extractor_args': {'youtube': {'player_client': ['android']}},
            'retries': 10,
            'fragment_retries': 10,
            'cookiesfrombrowser': (nav,),
        }
        with yt_dlp.YoutubeDL(ydl_opts) as ydl:
            ydl.download([url])
        descargado = True
        break
    except Exception as e:
        print(f"Fallo con {nav}: {e}")
        continue

if descargado:
    print("Video descargado!!")
else:
    print("No se pudo descargar con ningún navegador.")