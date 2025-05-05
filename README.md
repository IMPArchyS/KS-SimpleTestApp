# KS-SimpleTestApp

Simple full stack app for creating unit tests

Run specific test
`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit tests/CourseTest.php"`

Run all tests
`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit tests/*"`

# HTML report (most user-friendly)

`docker exec -it ks-simpletestapp-php-1 bash -c "cd /var/www/php && ./vendor/bin/phpunit --coverage-html ./coverage"`
