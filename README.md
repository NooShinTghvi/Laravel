
# Loghme API

with passport

## How To Run

1. copy ***.env.example*** then change name file to ***.env***
2. create a 'loghme' db in database, complete `DB_USERNAME` & `DB_PASSWORD`
3. run
> composer install  
> php artisan key generate
> php artisan migrate
> php artisan db:seed
> php artisan passport:install

4. To start server, run

> php artisan serve

5. Toggle to free  Deliver if delivery time has passed

> php artisan schedule:work

or run just one time

> php artisan command:delivery

## Funstions
The functions and routes are in `routes/api.php` file.
and postman collection file is in `loghme.postman_collection.json`
