@echo off
REM Set paths to your frontend and backend project folders
set FRONTEND_PATH=D:\freelance-projects\kiosk
set BACKEND_PATH=D:\freelance-projects\kiosk-server
set XAMPP_PATH=C:\xampp

REM Step 1: Start XAMPP Control Panel
start "" "%XAMPP_PATH%\xampp-control.exe"

echo Waiting for XAMPP to start Apache and MySQL...
pause

REM Step 2: Start Laravel backend
start "Laravel Server" cmd /k "cd /d %BACKEND_PATH% && php artisan serve --host=192.168.100.55 --port=8001"

REM Step 3: Wait to ensure Laravel is running
timeout /t 5 >nul

REM Step 4: Start Vite frontend
start "Vite Server" cmd /k "cd /d %FRONTEND_PATH% && npm run dev -- --host"

REM Step 5: Wait before launching browser
timeout /t 5 >nul

REM Step 6: Open frontend in Chrome
start chrome http://localhost:5173
