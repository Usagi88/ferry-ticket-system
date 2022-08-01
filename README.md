<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## What is this?
It's a management system for ferries. All the routes, schedules, vessels, booking are done through the system. Actors are admin, staff, agent, and user. 

## Requirements
1. MySQL server
2. Laravel 8.0+ (php 8.0)
3. Composer
4. Redis for windows
5. xampp


## How to use?
1. git clone the project
2. Setup redis
    1. Download redis windows (php 8.0.0, windows 64 bit, thread safety version) https://pecl.php.net/package/redis/5.3.3/windows
    2. **Optional:** can set up redis-server as environmental variable so that you can run it from anywhere.
    3. Copy phpredis thread safety php version 8 folder’s dll (downloaded file) to the xampp/php/ext folder. E.g. C:\xampp\php\ext
    4. Open the php.ini in file located in xampp/php. Add this **extension=php_redis.dll** . Any location is fine. For example, at the top.
3. Download https://github.com/microsoftarchive/redis/releases/tag/win-3.2.100
4. Open redis-server. Keep it open
5. Open project & run **npm install**
6. run **composer update**
7. Setup MySQL server
    a. Download and install MySQL server
    b. If using password then edit xampp > phpMyAdmin > config.inc.php (pass & change to cookie auth if you want). If this step is confusing there are guides to setup xampp/mysql/phpmyadmin
8. Setup DB credentials in env
    a. In this project I am using a password so if you don’t want a password you need to remove it from .env file.
    b. If you want your own password then go to phpmyadmin > Privileges > edit privileges > change password
    c. In .env the project is already looking for a database called ticket-system if you want to you can change database name. So, we need to create a database called ticket-system in phpmyadmin
    d. After creating it run this command **php artisan migrate:fresh --seed** Seed creates test data to fill in the database which includes already created users.
9. run **php artisan websockets:serve** (to see if it is working check http://127.0.0.1:8000/laravel-websockets)
10. run **php artisan queue:work redis**
11. run **php artisan serve**
12. Finished. Now you can log in. Here are some users created
    a. Admin -  email: admin@mail.com pass: test
    b. Staff -  email: staff@mail.com pass: test
    c. Agent -  email: agent@mail.com pass: test
    d. Merchant -  email: merchant@mail.com pass: test

## Features
1. Registration
2. Forgot password
3. Email verification
4. Login
5. Roles & permissions
6. Laravel policy
7. Factory seed
8. Redis queue
9. Real time notification
10. Push notification
11. CRUD (Create, Read, Update, Delete) for routes, vessels, bookings, schedules, ticket types, and assigning


### Dashboard
![Screenshot_2](https://user-images.githubusercontent.com/65016084/182113063-3a56ea6a-7afd-45a6-8bcc-5a9a9684568a.png)
