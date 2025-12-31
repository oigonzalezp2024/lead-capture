# lead-capture
Sistema de captación de leads con PHP

### Paso 1: Instalar el motor (Node.js)

Para usar TypeScript, primero necesitamos **Node.js**, que es el entorno que permite ejecutar herramientas de programación en tu PC.

1. Ve a [nodejs.org](https://nodejs.org/).
2. Descarga la versión que dice **"LTS"** (es la más estable).
3. Instálalo como cualquier otro programa (Siguiente, Siguiente, Finalizar).

### Paso 2: Instalar TypeScript

Ahora instalaremos el "traductor" de TypeScript.

1. Abre una terminal en tu computadora (en Windows busca **"CMD"** o **"PowerShell"**; en Mac busca **"Terminal"**).
2. Escribe el siguiente comando y pulsa Enter:
```bash
npm install -g typescript

```


3. Para verificar que se instaló bien, escribe: `tsc -v`. Debería aparecer un número de versión (ej. `Version 5.x.x`).

### Paso 3: Organizar tu proyecto

Crea una carpeta para tu proyecto (por ejemplo, en el Escritorio llamada `admin-leads`). Dentro de esa carpeta, crea estos archivos:

1. **`index.html`**: Tu página donde se verán los leads.
2. **`admin.ts`**: Aquí pega el código TypeScript que te proporcioné anteriormente.
3. **`tsconfig.json`**: Aquí pega la configuración de seguridad que definimos.

### Paso 4: La "Magia" (Compilación)

Aquí es donde TypeScript revisa tu código.

1. En la terminal, asegúrate de estar dentro de la carpeta de tu proyecto (puedes usar el comando `cd nombre-de-la-carpeta`).
2. Escribe simplemente:
```bash
tsc

```


3. **¿Qué pasará?** TypeScript leerá tu archivo `admin.ts`, revisará que todo respete el "contrato sagrado" de tu encuesta y creará automáticamente una carpeta llamada `dist` con un archivo `admin.js` adentro.

### Paso 5: Conectar al HTML

En tu archivo `index.html`, cuando vayas a llamar al script, asegúrate de llamar al archivo **generado** (el .js), no al original (.ts):

```html
<script src="dist/admin.js"></script>

```


### Paso 6: Ejecutar el backend

El backend esta desarrollado en PHP-MYSQL.  
La carpeta bbdd contiene la base de datos (MySQL).  

Cambia las credenciales necesarias.

```bash
# Configuración de BD

DB_HOST=localhost
DB_NAME=lead_capture
DB_USER=root
DB_PASS=''
DB_CHARSET=utf8mb4

# Credenciales iniciales (Opcional, para que el script las tome)
ADMIN_INIT_USER=admin_empresa
ADMIN_INIT_PASS=ClaveMaestra2025!

```

Ejecuta el gestor de dependencias composer:
> lead-capture> composer install
