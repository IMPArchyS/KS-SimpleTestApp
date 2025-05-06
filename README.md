# KS-SimpleTestApp

Simple full stack app for creating unit tests for backend in php using PHPUnit

Before pulling make sure you ran this command else, your tests might not run because the CRLF & LF character incompatibility between Windows and Linux:

`git config --global core.autocrlf false`

Run specific test
`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit tests/CourseTest.php"`

Run all tests
`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit tests/*"`

# HTML report (most user-friendly)

`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit --coverage-html ./coverage"`
