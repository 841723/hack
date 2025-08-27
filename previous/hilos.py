import threading

FICHERO = "/usr/share/wordlists/rockyou.txt"
N_HILOS = 200

def worker(hilo_id):
    with open(FICHERO, "r") as f:
        for i, linea in enumerate(f):
            if i % N_HILOS == hilo_id:
                # Aquí procesas la línea que toca
                print(f"Hilo {hilo_id} → línea {i}: {linea.strip()}")

# Crear y lanzar hilos
hilos = []
for i in range(N_HILOS):
    t = threading.Thread(target=worker, args=(i,))
    t.start()
    hilos.append(t)

# Esperar a que todos terminen
for t in hilos:
    t.join()
