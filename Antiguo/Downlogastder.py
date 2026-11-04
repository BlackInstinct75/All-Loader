import tkinter as tk
from tkinter import messagebox
import yt_dlp
import threading

NAVEGADORES = ['firefox', 'chrome', 'edge', 'opera', 'brave']

def descargar():
    url = entry_url.get()
    if not url:
        messagebox.showwarning("Aviso", "Introduce un URL")
        return
    boton.config(state="disabled", text="Descargando...")

    def run():
        descargado = False
        errores = []

        for nav in NAVEGADORES:
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
                errores.append(f"{nav}: {e}")
                continue

        if descargado:
            messagebox.showinfo("Listo", "¡Video descargado!")
        else:
            messagebox.showerror("Error", "No se pudo descargar con ningún navegador.\n\n" + "\n".join(errores))

        boton.config(state="normal", text="Descargar")

    threading.Thread(target=run, daemon=True).start()

ventana = tk.Tk()
ventana.title("Descargador de YouTube")
ventana.geometry("420x130")

tk.Label(ventana, text="URL del video:").pack(pady=5)
entry_url = tk.Entry(ventana, width=55)
entry_url.pack(pady=5)

boton = tk.Button(ventana, text="Descargar", command=descargar)
boton.pack(pady=10)

ventana.mainloop()