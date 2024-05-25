<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About This Project
**INITIAL**

I share a copy of the variables that must be included in the .env

JWT_SECRET=br1ja1C4KXCTvXC3c463p4BDtiWX9YW2Y32qS6zBPhApV9j7IsvcSl629CpBxQ4j

JWT_ALGO=HS256

LIMIT_VALUE=1000000
ACTIVE_EMAIL=false

DEFAULT_MESSAGE="The operation could not be performed"

**END INITIAL**

This project uses Laravel Docker, so all commands must use "Sail",
To launch the project you must use "sail up" followed by "sail composer install".

After this, the migrations must be run with the command "sail artisan migrate"

Finally, the services of postman, attachment, environment variables can be used.

First you must create users with the endpoint register, they can be type CC CE or NIT being NIT for businesses

Then you have to use the login endpoint to obtain the token and be able to use the create account.

Then, if you want to recharge balance to the generated account, you must use the recharge endpoint

Finally, you can use the transfer endpoint to transfer money from one account to another and the Authorization endpoint in case you require authorization.



## ---------------


