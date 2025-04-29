# ROBLE Tailwind

Plantilla para desarrollar aplicaciones web monolíticas.

## Construido con

    - Laravel 12
    - Vue 3
    - Inertia 2
    - Tailwind CSS (shadcn-vue)
    - PosrgreSQL 17+

## Primeros pasos en Entorno Local (`localhost`) usando Docker

### Requisitos

    - Docker v28.1 o superior
    - Docker Compose v2.35 o superior
    - Conexión a internet

1. Clonar el repositorio
    ```sh
    git clone https://gitlab.com/profemaik/roble.git
    ```

2. Cambiar la rama a `laravel-12x-vue`
    ```sh
    git checkout laravel-12x-vue
    ```

3. Crear el archivo `.env`
    ```sh
    cp .env.example .env
    ```

4. Instalar las dependencias de Composer
    ```sh
    docker run --rm --interactive --tty \
    --volume $PWD:/app \
    --user $(id -u):$(id -g) \
    composer install --ignore-platform-reqs
    ```

5. Desplegar los contenedores (recomendable configurar un alias)
    ```sh
    # sin alias
    ./vendor/bin/sail up -d

    # con alias configurado
    sail up -d
    ```

6. Crear la clave de encriptación
    ```sh
    # sin alias
    ./vendor/bin/sail artisan key:generate

    # con alias configurado
    sail artisan key:generate
    ```

7. Definir el esquema de la base de datos y alimentarla con datos iniciales
    ```sh
    # sin alias
    ./vendor/bin/sail artisan migrate:fresh --seed

    # con alias configurado
    sail artisan migrate:fresh --seed
    ```

8. Instalar las dependencias de Node
    ```sh
    # sin alias
    ./vendor/bin/sail npm i

    # con alias configurado
    sail npm i
    ```

9. Construir las pantallas o vistas
    ```sh
    # sin alias
    ./vendor/bin/sail npm run build

    # con alias configurado
    sail npm run build
    ```

10. Abrir el navegador web e ir a <http://localhost>

## Primeros pasos en Entorno Local (`localhost`)

### Requisitos

    - PHP v8.3 o superior
    - Composer v2.8 o superior
    - Node 22 o superior
    - Conexión a internet

1. Clonar el repositorio
    ```sh
    git clone https://gitlab.com/profemaik/roble.git
    ```

2. Cambiar la rama a `laravel-12x-vue`
    ```sh
    git checkout laravel-12x-vue
    ```

3. Crear el archivo `.env`
    ```sh
    cp .env.example .env
    ```

4. Establecer los valores para las variables de conexión a la base de datos
    - `DB_HOST` (computadora donde está la base de datos).
    - `DB_PORT` (puerto de la computadora donde está la base de datos).
    - `DB_DATABASE` (nombre de la base de datos).
    - `DB_USERNAME` (usuario de la base de datos).
    - `DB_PASSWORD` (contraseña del usuario de la base de datos).

5. Instalar las dependencias de Composer
    ```sh
    composer install --ignore-platform-reqs
    ```

6. Crear la clave de encriptación
    ```sh
    php artisan key:generate
    ```

7. Definir el esquema de la base de datos y alimentarla con datos iniciales
    ```sh
    php artisan migrate:fresh --seed
    ```

8. Instalar las dependencias de Node
    ```sh
    npm i
    ```

9. Construir las pantallas o vistas
    ```sh
    npm run build
    ```

10. Desplegar el servidor local
    ```sh
    composer run dev
    ```

10. Abrir el navegador web e ir a <http://localhost:8000>
