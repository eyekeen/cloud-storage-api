## Cloud storage api

Simple service for uploading, deleting, downloading and updating files.

## Requirements

- docker v20.14 and above
- docker-compose v2.14 and above
- php v8.1 and above
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

Generating jwt token:
```bash
./vendor/bin/sail artisan jwt:secret
// Or
php artisan jwt:secret  
```

# REST API

Launch postman and import [api collection](./cloud_rest_api.postman_collection.json)

Examples.

## Register

#### Request
`POST /api/register`
```
curl --location --request POST 'localhost/api/register' \
--header 'Content-Type: application/json' \
--header 'Cookie: XSRF-TOKEN=<auto gen jwt token>' \
--data-raw '{
    "name": "<your name>",
    "email": "<your email>",
    "password": "<your password(length >= 6)>"
}'
```

#### Response

```
{
    "status": "success",
    "message": "User created successfully",
    "user": {
        "name": "<your name>",
        "email": "<your email>",
        "updated_at": "2023-01-16T08:19:55.000000Z",
        "created_at": "2023-01-16T08:19:55.000000Z",
        "id": <your id>
    },
    "authorisation": {
        "token": "<Your jwt token>",
        "type": "bearer"
    }
}
```

## Login

#### Request
`POST /api/login`
```
curl --location --request POST 'localhost/api/login' \
--header 'Content-Type: application/json' \
--header 'Cookie: XSRF-TOKEN=<auto gen jwt token>' \
--data-raw '{
    "email": "<your email>",
    "password": "<your password>"
}'
```

#### Response

```
{
    "status": "success",
    "user": {
        "id": <your id>,
        "name": "<your name>",
        "email": "<your email>",
        "email_verified_at": null,
        "created_at": "2023-01-16T06:39:23.000000Z",
        "updated_at": "2023-01-16T06:39:23.000000Z"
    },
    "authorisation": {
        "token": "<your token>",
        "type": "bearer"
    }
}
```

## Logout

#### Request
`POST /api/logout`
```
curl --location --request POST 'localhost/api/logout' \
--header 'Authorization: Bearer <your jwt token>' \
--header 'Cookie: XSRF-TOKEN=<auto gen token>'
```

#### Response

```
{
    "status": "success",
    "message": "Successfully logged out"
}
```

## Refresh

#### Request
`POST /api/refresh`
```
curl --location --request POST 'localhost/api/refresh' \
--header 'Authorization: Bearer <your jwt token>' \
--header 'Cookie: XSRF-TOKEN=<auto gen token>'
```

#### Response

```
{
    "status": "success",
    "user": {
        "id": <your id>,
        "name": "<your name>",
        "email": "<your email>",
        "email_verified_at": null,
        "created_at": "2023-01-16T06:39:23.000000Z",
        "updated_at": "2023-01-16T06:39:23.000000Z"
    },
    "authorisation": {
        "token": "<new jwt token>",
        "type": "bearer"
    }
}
```


## Get all files

#### Request
`GET /api/files`
```
curl --location --request GET 'localhost/api/files' \
--header 'Authorization: Bearer <your jwt token>' \
--header 'Cookie: XSRF-TOKEN=<auto gen token>'
```

#### Response

```
{
    "status": 200,
    "message": "Ok",
    "files": [<files and directory list>]
}
```

## Upload file

#### Request
`POST /api/files`
```
curl --location --request POST 'localhost/api/files' \
--header 'Authorization: Bearer <your jwt token>' \
--header 'Cookie: XSRF-TOKEN=<auto gen token>' \
--form 'file=@"/test_file.txt"' \
--form 'directory="test"' <- optional
```

#### Response

```
{
    "user_id": <auth user id>,
    "status": 200,
    "message": "file been save",
    "file_name": "<upload file name>",
    "size": "<file size>"
}
```



## License

cloud-storage-api is open-sourced software released under the [MIT License](https://opensource.org/licenses/MIT).
