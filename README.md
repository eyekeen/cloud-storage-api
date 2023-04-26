## Cloud storage api

Simple service for uploading, deleting, downloading and updating files.

## Requirements

- php >= v8.2
- composer 2.5 and above

## Installation

For installation this project need execute the following commands

```bash
git clone https://github.com/eyekeen/cloud-storage-api.git
cd cloud-storage-api
composer install
```

## Configuration and run

```bash
cp .env.example .env
```
Start all docker containers
```bash
./vendor/bin/sail up
```
OR

Start all of the Docker containers in the background in "detached" mode:
```bash
./vendor/bin/sail up -d
```
Run migration:
```bash
./vendor/bin/sail artisan migrate
// Or
php artisan migrate   
```

Generating jwt secret key:
```bash
php artisan jwt:secret  
```

# API

Launch postman and import [api collection](./cloud_rest_api.postman_collection.json).
After authorization, you need to use the "Bearer token" for further requests


## License

cloud-storage-api is open-sourced software released under the [MIT License](https://opensource.org/licenses/MIT).
