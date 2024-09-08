# Dockerized Laravel Application

This repository contains a Dockerized Laravel application with Nginx, PHP-FPM, PostgreSQL, and pgAdmin.

## Prerequisites

Ensure that you have the following installed on your system:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone git@github.com:maenard/dockerize-lara.git
   cd dockerize-lara

2. Copy the environment file:
   ```bash
   cp .env.example .env
   
3. Build and start the containers:
    ```bash
    docker-compose up --build -d

4. Install Laravel dependencies:

    ```bash
    docker-compose exec app composer install
    
5. Generate an application key:

    ```bash
    docker-compose exec app php artisan key:generate
    
6. Run database migrations:

    ```bash
    docker-compose exec app php artisan migrate
    

## Accessing the Application
- Laravel App: http://localhost
- pgAdmin: http://localhost:5050

## Containers Overview
- Nginx: Serves the Laravel application.
- PHP-FPM: Handles the PHP processing.
- PostgreSQL: Database server.
- pgAdmin: Database management tool for PostgreSQL.

## Docker Services
- app: PHP-FPM container that runs the Laravel application.
- nginx: Nginx container that serves the Laravel application.
- postgres: PostgreSQL container.
- pgadmin: pgAdmin container.

## Commands
- Start the containers: `docker compose up -d`
- Stop the containers: `docker compose down`
- Access the Laravel container: `docker compose exec app bash`
- Run Laravel Artisan commands: `docker compose exec app php artisan`

## Customization
You can customize the services by editing the docker-compose.yml file.

## Troubleshooting
- Permission Issues: If you encounter permission issues, you might need to change the ownership of some directories:

    ```bash
    sudo chown -R $USER:$USER storage bootstrap/cache
- Rebuilding Containers: If you make changes to the Dockerfile or the docker-compose.yml, you need to rebuild the containers:

    ```bash
    docker-compose up --build -d

