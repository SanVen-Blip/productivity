@echo off
echo =============================================
echo   Update Worker Tunnel URL
echo =============================================
echo.
echo Masukkan URL Quick Tunnel yang baru (dari cloudflared):
echo (contoh: https://xxx-xxx.trycloudflare.com)
echo.
set /p TUNNEL_URL="URL: "
echo.
echo Updating worker/index.js...

powershell -Command "(Get-Content 'd:\laragon\www\sanvendocs\worker\index.js') -replace \"const TUNNEL_URL = '.*'\", \"const TUNNEL_URL = '%TUNNEL_URL%'\" | Set-Content 'd:\laragon\www\sanvendocs\worker\index.js'"

echo Deploying to Cloudflare Workers...
cd /d "d:\laragon\www\sanvendocs\worker"
npx wrangler deploy

echo.
echo =============================================
echo   DONE! URL tetap: https://sanvendocs.hasanarofahbkl18.workers.dev
echo =============================================
pause
