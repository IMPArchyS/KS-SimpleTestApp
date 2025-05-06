# KS-SimpleTestApp

Simple full stack app for creating unit tests for backend in php using PHPUnit

Before pulling make sure you ran this command else, your tests might not run because the CRLF & LF character incompatibility between Windows and Linux:

`git config --global core.autocrlf false`

## Building the app

-   make sure you have WSL2 installed
-   on Windows use the docker app
-   in VSCode you can build your docker container with `container tools` extension by running the `docker-compose.yaml` file
-   you can build your docker container using this command in terminal `docker compose -f 'docker-compose.yaml' up -d --build` in the directory where the `docker-compose.yaml` is located

### Ports

-   site runs on http://localhost/
-   site uses the default http port :80
-   phpmyadmin runs on :8080
-   swagger ui runs on :8082

## Running tests

### Run specific test

`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit tests/CourseTest.php"`

### Run all tests

`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit tests/*"`

### Code coverage - HTML report (most user-friendly)

`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit --coverage-html ./coverage"`

Your test results are then saved in the coverage file under src.
