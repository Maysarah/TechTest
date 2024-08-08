# Articles API


## Overview

The Article API is a RESTful service designed to manage and interact with articles. This API allows users to create, read, update, and delete articles and manage associated images. It provides endpoints for listing articles, viewing details of individual articles, and managing article data.

This API is built using Laravel 10 with PHP version 8.3, providing a robust and scalable solution for article management. It leverages Laravel Sail for a simple and convenient local development environment, allowing you to build and manage the project using Docker containers effortlessly.
## Deployed Project Access:
    EC2 : http://ec2-44-202-32-108.compute-1.amazonaws.com/articles/
    Lambda : https://tbsnpm14rk.execute-api.us-east-1.amazonaws.com/articles
    Swagger documentation : http://ec2-44-202-32-108.compute-1.amazonaws.com/api/documentation
## Getting Started (For local installation)

### Prerequisites

- Docker installed
- Docker Compose installed

### Installation

1. Clone the repository:

   ```bash
   git clone git@github.com:Maysarah/TechTest.git

### Run The project
1. Reach to the project folder:
   ```bash
   cd TechTest

2. Copy the .env.example file to create a new .env file:
    ```bash
   cp .env.example .env

3. Update the .env file with the database configuration and other settings.
    ```bash

   local:
       DB_CONNECTION=mysql \
       DB_HOST=mysql \ 
       DB_PORT=3306 \
       DB_DATABASE=laravel-db_db \ this is an example, You can choose your own
       DB_USERNAME=root \
       DB_PASSWORD=password
   cloud:
       DB_CONNECTION=mysql \
       DB_HOST= rds_link 
       DB_PORT=3306 \
       DB_DATABASE=laravel-db \ this is an example, You can choose your own
       DB_USERNAME=admin
       DB_PASSWORD=laravelpassword

       

4. Add S3 configuration to .env to manage the images part .
    ```bash
    AWS_ACCESS_KEY_ID= 
    AWS_SECRET_ACCESS_KEY= 
    AWS_DEFAULT_REGION=
    AWS_BUCKET=
   
5. Install the composer to generate the vendor directory:
    ```bash
    composer install
6. If you face issues related to your platform requirements use:
    ```bash
    composer install --ignore-platform-reqs
7. Make sure the vendor directory is generated.
8. Build and start the Docker containers using Laravel Sail:
    ```bash
   ./vendor/bin/sail up -d

9. Generate an application key:
    ```bash
   ./vendor/bin/sail artisan key:generate

10. Run database migrations:
      ```bash
    ./vendor/bin/sail artisan migrate

11. Execute the database seeder to populate the database with simulated/mock data:
      ```bash
    ./vendor/bin/sail artisan db:seed

12. The application should now be accessible at http://localhost/articles.

### API Documentation
1. Visit http://localhost/api/documentation for API documentation for the list articles RESTful api  Using Swagger.

### Run Tests
1. To run the Unit test tests
    ```bash
   ./vendor/bin/sail artisan test

## Fibonacci Implementation
### Service Class
A service class named FibonacciService was created to handle the Fibonacci sequence calculations. The function calculateUpTo(int $n) calculates Fibonacci numbers up to a given number n. The service class is implemented to separate the Fibonacci logic from other parts of the application, following best practices for clean code and maintainability
