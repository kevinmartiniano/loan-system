# Loan System App

> ### RESTful application with a balance transfer flow between wallets.

----------

# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/8.x)

Alternative installation is possible without local dependencies relying on [Docker](#docker). 

Clone the repository

    git@github.com:kevinmartiniano/loan-system.git

Switch to the repo folder

    cd loan-system

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env
    ## The database credentials written in .env will be defined at the time of the build.

Run docker create network command

    docker network create loan-network
    
Run docker compose command to build application

    docker-compose build

Run up in docker-compose to startup application

    docker-compose up -d

Discover a name docker instance web getting a return of command docker ps 

    docker ps

Access the docker discovered

    docker exec -it loan-system_web bash

In the docker terminal, generate a new application key

    php artisan key:generate

Generate a new JWT authentication secret key

    php artisan passport:install

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone git@github.com:kevinmartiniano/loan-system.git
    cd loan-system
    composer install
    cp .env.example .env
    docker-compose build
    docker-compose up -d
    docker exec -it loan-system_web bash
    php artisan key:generate
    php artisan passport:install
    php artisan migrate
    
## Database seeding

**Populate the database with seed data with relationships which includes users, articles, comments, tags, favorites and follows. This can help you to quickly start testing the api or couple a frontend and start using it with ready content.**

Open the DummyDataSeeder and set the property values as per your requirement

    database/seeders/UserTypeSeeder.php

Run the database seeder and you're done

    php artisan db:seed

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh
    
```

The api can be accessed at [http://localhost:8000/api]

# Code overview

## Dependencies

- [laravel/passport](https://github.com/laravel/passport) - For authentication using OAuth 2.0
- [laravel-cors](https://github.com/barryvdh/laravel-cors) - For handling Cross-Origin Resource Sharing (CORS)
- [zircote-swagger-php](https://github.com/zircote/swagger-php) - For documentation of a routes

## Folders

- `app` - Contains all the Eloquent models
- `app/Http/Controllers/Api` - Contains all the api controllers
- `app/Http/Middleware` - Contains the JWT auth middleware
- `app/Http/Requests/Api` - Contains all the api form requests
- `config` - Contains all the application configuration files
- `database/factories` - Contains the model factory for all the models
- `database/migrations` - Contains all the database migrations
- `database/seeds` - Contains the database seeder
- `routes` - Contains all the api routes defined in api.php file
- `tests` - Contains all the application tests
- `tests/Feature/Api` - Contains all the api tests

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

----------

# Authentication
 
OAuth 2.0 is the industry-standard protocol for authorization. OAuth 2.0 focuses on client developer simplicity while providing specific authorization flows for web applications, desktop applications, mobile phones, and living room devices. This specification and its extensions are being developed within the IETF OAuth Working Group.
 
- https://oauth.net/2/

----------
