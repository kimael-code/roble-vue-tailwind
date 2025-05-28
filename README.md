# ROBLE Vue-Tailwind

Starter kit for developing monolithic web applications based on Laravel, Inertia.js, Vue.js, and Tailwind CSS.

## Made with

- Laravel
- Vue
- Inertia
- Tailwind CSS (shadcn-vue)
- PosrgreSQL

## Getting started in Local Environment (`localhost`) using Docker & Laravel Sail

### Requirements

- Docker Engine v28.1 or higher
- Docker Compose v2.35 or higher
- Internet connection
- Shell alias configured to use Laravel Sail

1. Clone this repo

    ```sh
    git clone https://github.com/kimael-code/roble-vue-tailwind.git
    ```

2. Go to the root project folder and create the `.env` file

    ```sh
    cd roble-vue-tailwind && cp .env.example .env
    ```

3. Install Composer dependencies

    ```sh
    docker run --rm --interactive --tty \
    --volume $PWD:/app \
    --user $(id -u):$(id -g) \
    composer install --ignore-platform-reqs
    ```

4. Start the containers (*it's necessary to configure a shell alias*)

    ```sh
    sail up -d
    ```

5. Create the app encryption key

    ```sh
    sail artisan key:generate
    ```

6. Run database migrations and seeders

    ```sh
    sail artisan migrate:fresh --seed
    ```

7. Install Node dependencies

    ```sh
    sail npm i
    ```

8. Build Node dependencies

    ```sh
    sail npm run build
    ```

9. Oper your favorite web browser and go to <http://localhost>

## Getting started in Local Environment (`localhost`) using Laravel Herd

### Requirements

- Laravel Herd
- PostgreSQL with pgAdmin or any other universal database tool

1. Clone this repo inside Herd folder

    ```sh
    git clone https://github.com/kimael-code/roble-vue-tailwind.git
    ```

2. Go to the root project folder and create the `.env` file

    ```sh
    cd roble-vue-tailwind && cp .env.example .env
    ```

3. Set values ​​for database connection environment variables
    - `DB_HOST=localhost`.
    - `DB_USERNAME=postgres`.
    - `DB_PASSWORD=your_postgres_user_password`.  
    *You need to set the values of these variables according to your PostgreSQL installation and configurations (port, user, password, etc.)*

4. Install Composer dependencies

    ```sh
    composer install --ignore-platform-reqs
    ```

5. Create the app encryption key

    ```sh
    php artisan key:generate
    ```

6. Run database migrations and seeders

    ```sh
    php artisan migrate:fresh --seed
    ```

7. Install Node dependencies

    ```sh
    npm i
    ```

8. Build Node dependencies

    ```sh
    npm run build
    ```

9. Run the app

    ```sh
    herd open
    ```
