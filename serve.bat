@echo off
title SanvenDocs Server
echo Starting SanvenDocs...
echo.
echo Access from this PC:   http://localhost:8000
echo Access from other devices: http://192.168.1.105:8000
echo.
echo Press Ctrl+C to stop.
echo ==============================
D:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe artisan serve --host=0.0.0.0 --port=8000
