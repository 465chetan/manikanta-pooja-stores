@echo off
title SMPS MySQL Keep-Alive
color 0A
echo ============================================
echo  SMPS MySQL Keep-Alive (Port 3307)
echo  Keep this window open while using site!
echo ============================================
echo.

:LOOP
:: Kill old instances on port 3307
for /f "tokens=5" %%a in ('netstat -aon 2^>nul ^| findstr ":3307 "') do (
    taskkill /F /PID %%a >nul 2>&1
)
timeout /t 1 /nobreak >nul

:: Start MySQL
start /B "" "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe" --defaults-file="C:\laragon\data\mysql-8.4\my_smps.ini"
timeout /t 12 /nobreak >nul

:: Check if running
"C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -h 127.0.0.1 --port=3307 --execute "SELECT 1;" >nul 2>&1
if %errorlevel% == 0 (
    echo [%time%] MySQL RUNNING on port 3307 - OK
) else (
    echo [%time%] MySQL stopped - restarting...
    goto LOOP
)

:: Monitor loop - restart if it dies
:MONITOR
timeout /t 30 /nobreak >nul
"C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -h 127.0.0.1 --port=3307 --execute "SELECT 1;" >nul 2>&1
if %errorlevel% neq 0 (
    echo [%time%] MySQL died - restarting...
    goto LOOP
)
echo [%time%] MySQL still running - OK
goto MONITOR
