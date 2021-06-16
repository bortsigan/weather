<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

### Setup Installation

## -Docker is required to run the app

## 1. clone the repo (git clone https://github.com/bortsigan/weather.git)
## 2. Go to the folder (cd weather)
## 3. type in the terminal (docker-compose up -d --build)
	3.1 (docker ps) to check the app, web and mysql containers running
## 4. type in the terminal (npm install && npm run prod)
## 5. type in the terminal (cp .env.example .env)

## 6.
	6.1 for windows : winpty docker-compose exec app sh
	6.2 for mac : docker-compose exec app /bin/bash

## 7. type in the terminal (composer install)
## 8. type in the terminal (php artisan key:generate)
## 9. type in the terminal (php artisan config:cache)
 ### if it does not work try : php artisan config:cache && php artisan config:clear && php artisan cache:clear



### NOTE :

If you're using windows : you need to allow Hyper V and BIOS Virtualization (enabled)

### License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
