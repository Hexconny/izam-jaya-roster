@echo off
chcp 65001 >nul
title Izam Jaya Roster
cd /d "%~dp0"
echo Pastikan MySQL di XAMPP sudah START.
echo Website: http://127.0.0.1:8000   Admin: http://127.0.0.1:8000/admin/login
echo Tekan Ctrl+C untuk menghentikan.
echo.
php artisan serve
