# ROBLE Vue-Tailwind

Starter kit for developing monolithic web applications based on Laravel, Inertia.js, Vue.js, and Tailwind CSS (with PostgreSQL as the database).

## Made with

- Laravel
- Vue (shadcn-vue)
- Inertia
- Tailwind CSS
- PostgreSQL

## Users

Default users are created depending on the value of the `APP_ENV` variable.

- For local environment only:
  - `root.dev`:
    - password `12345678`
    - role `Superuser`
  - `admin.dev`:
    - password `12345678`
    - role `Systems Administrator`
- for any other environment:
  - `root`:
    - password `root`
    - role `Superuser`

The `root.dev` and `root` users are read-only; they cannot be updated or deleted. These users act like the root user on Unix/Unix-like operating systems: they have full access to any route, but they are not allowed to bypass the system's defined policies.

## Getting started in Local Environment (`localhost`) using Docker & Laravel Sail

### Requirements

- Docker Engine v28.1 or higher
- Docker Compose v2.35 or higher
- Internet connection
- Shell alias configured to use Laravel Sail (<https://laravel.com/docs/12.x/sail#configuring-a-shell-alias>)

1. Clone this repo:

    ```sh
    git clone https://github.com/kimael-code/roble-vue-tailwind.git
    ```

2. Go to the root project folder and create the `.env` file:

    ```sh
    cd roble-vue-tailwind && cp .env.example .env
    ```

3. Set the credentials for **Laravel Reverb**:

    ```env
    REVERB_APP_ID=my-reverb-app-id
    REVERB_APP_KEY=my-reverb-app-key
    REVERB_APP_SECRET=my-reverb-app-secret
    ```

    *More info here*: <https://laravel.com/docs/12.x/reverb#main-content>  
    *To generate random numbers*: <https://www.random.org/integers>  
    *To generate random strings*: <https://www.random.org/strings>

4. Install Composer dependencies:

    ```sh
    docker run --rm --interactive --tty \
    --volume $PWD:/app \
    --user $(id -u):$(id -g) \
    composer install --ignore-platform-reqs
    ```

5. Start the containers (*it's necessary to configure a shell alias*):

    ```sh
    sail up -d
    ```

6. Create the app encryption key:

    ```sh
    sail artisan key:generate
    ```

7. Run database migrations and seeders:

    ```sh
    sail artisan migrate:fresh --seed
    ```

8. Install Node dependencies:

    ```sh
    sail npm i
    ```

9. Build Node dependencies:

    ```sh
    sail npm run build
    ```

10. Open your favorite web browser and go to <http://localhost>.

## Getting started in Local Environment (`localhost`) using Laravel Herd

### Requirements

- Laravel Herd
- PostgreSQL with pgAdmin or any other universal database tool

1. Clone this repo inside Herd folder:

    ```sh
    git clone https://github.com/kimael-code/roble-vue-tailwind.git
    ```

2. Go to the root project folder and create the `.env` file:

    ```sh
    cd roble-vue-tailwind && cp .env.example .env
    ```

3. Set values ​​for database connection environment variables:
    - `DB_HOST=localhost`.
    - `DB_USERNAME=postgres`.
    - `DB_PASSWORD=your_postgres_user_password`.  
    *You need to set the values of these variables according to your PostgreSQL installation and configurations (port, user, password, etc.)*

4. Set the credentials for **Laravel Reverb**:

    ```env
    REVERB_APP_ID=my-reverb-app-id
    REVERB_APP_KEY=my-reverb-app-key
    REVERB_APP_SECRET=my-reverb-app-secret
    ```

    *More info here*: <https://laravel.com/docs/12.x/reverb#main-content>  
    *To generate random numbers*: <https://www.random.org/integers>  
    *To generate random strings*: <https://www.random.org/strings>

5. Install Composer dependencies:

    ```sh
    composer install --ignore-platform-reqs
    ```

6. Create the app encryption key:

    ```sh
    php artisan key:generate
    ```

7. Run database migrations and seeders:

    ```sh
    php artisan migrate:fresh --seed
    ```

8. Install Node dependencies:

    ```sh
    npm i
    ```

9. Build Node dependencies:

    ```sh
    npm run build
    ```

10. Run the app:

    ```sh
    herd open
    ```
