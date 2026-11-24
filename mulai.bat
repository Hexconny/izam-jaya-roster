@echo off
chcp 65001 >nul
title Izam Jaya Roster - Setup pertama kali
cd /d "%~dp0"

echo ==========================================================
echo   IZAM JAYA ROSTER - setup pertama kali
echo ==========================================================
echo.
echo Sebelum lanjut, pastikan:
echo   1. XAMPP Control Panel: MySQL sudah START (hijau)
echo   2. Database "izam_jaya_roster" sudah dibuat di phpMyAdmin
echo      (http://localhost/phpmyadmin, klik New, isi nama, Create)
echo.
echo PERHATIAN: setup ini MENGHAPUS dan membuat ulang semua tabel.
echo Jangan dijalankan lagi kalau sudah ada data asli. Untuk menyalakan
echo website seterusnya pakai jalankan.bat
echo.
pause

if not exist .env copy .env.example .env >nul

call php artisan key:generate --force
call php artisan migrate:fresh --seed --force
if errorlevel 1 (
  echo.
  echo GAGAL membuat tabel. Cek: MySQL sudah START? Database izam_jaya_roster sudah ada?
  pause
  exit /b 1
)
call php artisan storage:link
call php artisan optimize:clear 

echo.
echo Selesai. Website: http://127.0.0.1:8000
echo Admin: http://127.0.0.1:8000/admin/login
echo Tekan Ctrl+C untuk menghentikan website.
echo.
php artisan serve
