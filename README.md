# JWT app

This app test the registry and login of your front program with JWT.
I use [jwt-auth](https://jwt-auth.readthedocs.io/en)

## How To Run

 1. copy ***.env.example*** then change name file to ***.env***
 2. create a 'jwt' db in database, complete *DB_USERNAME* & *DB_PASSWORD*
 3. run 

> composer install
>
> php artisan key generate
4. Generate secret key
	I have included a helper command to generate a key for you.
    This will update your *.env* file with something like *JWT_SECRET=foobar*
> php artisan jwt:secret

 5. To start server, run

> php artisan serve

