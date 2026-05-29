@echo off
setlocal

cd /d C:\laragon\www\she_sistem
php artisan schedule:run >> storage\logs\scheduler.log 2>&1

endlocal