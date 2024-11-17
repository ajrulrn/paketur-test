## Requirements
- PHP 8+
- [Composer](https://composer.org)
- PostgreSQL 15+

## Installation
1.  Clone this repository
	```
	git clone https://github.com/ajrulrn/paketur-test.git
	cd paketur-test
    ```
2.  Create a copy of the environment variable
	```
	cp .env.example .env
	```
3. Setup your configuration database
    ```
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=paketur_test
    DB_USERNAME=postgres
    DB_PASSWORD=postgres
    ```
4.  Install dependencies
    ```
    composer install
    ```
5.  Generate keys
	```
    php artisan key:generate
    php artisan jwt:secret
	```
6.  Run migration and seeder
    ```
    php artisan migrate --seed
    ```
7.  Run App
    ```
    php artisan serve
    ```

## Testing
```
php artisan test

// need enable extension Xdebug or pcov
php artisan test --coverage
```

## API Documentation
You'll find the API documentation on [here](https://github.com/ajrulrn/paketur-test/tree/main/doc)
