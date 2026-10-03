@echo off
rem Windows/XAMPP: PHP needs an OpenSSL config to create the keys Web Push uses.
set OPENSSL_CONF=%~dp0..\..\php\extras\ssl\openssl.cnf
if not exist "%OPENSSL_CONF%" set OPENSSL_CONF=D:\xampp\php\extras\ssl\openssl.cnf
php artisan serve --port=8123
