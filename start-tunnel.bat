@echo off
echo ========================================
echo   SanvenDocs - Starting Server + Tunnel
echo   URL: https://sanvendocs.com
echo ========================================
echo.

echo [1/2] Starting Laravel server...
start "Laravel Server" cmd /c "D:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe" artisan serve --host=127.0.0.1 --port=8000

timeout /t 3 > nul

echo [2/2] Starting Cloudflare Tunnel...
echo.
echo App is live at: https://sanvendocs.com
echo Also available: https://app.sanvendocs.com
echo.
echo Press Ctrl+C to stop the tunnel.
echo ========================================
"C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel run sanvendocs
