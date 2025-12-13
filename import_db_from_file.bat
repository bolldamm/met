@echo off
setlocal EnableDelayedExpansion

REM ============================================================================
REM MET Database Import Script
REM Imports a database dump file to your local Laragon environment
REM
REM Usage:
REM    1. Export remote database (with routines and triggers)
REM    2. Save downloaded file to F:\laragon\www\db_backups\
REM    3. Double-click import_db_from_file.bat or run from command line
REM    4. Type the filename and press Enter
REM    5. Script backs up current local database first
REM    6. Imports the file
REM ============================================================================

REM --- Configuration ---
set "MYSQL_PATH=F:\laragon\bin\mysql\mysql-8.0.40-winx64\bin"
set "LOCAL_DB=met"
set "LOCAL_USER=root"
set "BACKUP_DIR=F:\laragon\www\db_backups"

REM --- Pre-flight checks ---
echo.
echo ============================================
echo   MET Database Import (from file)
echo ============================================
echo.

if not exist "%MYSQL_PATH%\mysql.exe" (
    echo ERROR: MySQL not found at %MYSQL_PATH%
    echo Please update MYSQL_PATH in this script.
    pause
    exit /b 1
)

REM Create backup directory if it doesn't exist
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM --- List available SQL files ---
echo Available SQL files in %BACKUP_DIR%:
echo.
dir /b "%BACKUP_DIR%\*.sql" 2>nul
if errorlevel 1 (
    echo No .sql files found in %BACKUP_DIR%
    echo.
    echo Please download the database export from cPanel/phpMyAdmin
    echo and save it to: %BACKUP_DIR%
    pause
    exit /b 1
)

REM --- Get filename from user ---
echo.
echo Enter the filename to import (e.g., remote.sql):
set /p "IMPORT_FILE="

if "%IMPORT_FILE%"=="" (
    echo ERROR: Filename cannot be empty.
    pause
    exit /b 1
)

if not exist "%BACKUP_DIR%\%IMPORT_FILE%" (
    echo ERROR: File not found: %BACKUP_DIR%\%IMPORT_FILE%
    pause
    exit /b 1
)

REM --- Confirm before proceeding ---
echo.
echo WARNING: This will replace your local '%LOCAL_DB%' database!
echo File to import: %IMPORT_FILE%
echo.
set /p "CONFIRM=Are you sure? (Y/N): "
if /i not "%CONFIRM%"=="Y" (
    echo Import cancelled.
    pause
    exit /b 0
)

REM --- Backup current local database first ---
echo.
echo Backing up current local database (safety measure)...
set "TIMESTAMP=%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%"
set "TIMESTAMP=%TIMESTAMP: =0%"

"%MYSQL_PATH%\mysqldump.exe" -u %LOCAL_USER% --routines --triggers %LOCAL_DB% > "%BACKUP_DIR%\met_local_backup_%TIMESTAMP%.sql" 2>nul

if exist "%BACKUP_DIR%\met_local_backup_%TIMESTAMP%.sql" (
    echo Local backup saved: met_local_backup_%TIMESTAMP%.sql
) else (
    echo Note: No existing local database to backup (this is OK for first import)
)

REM --- Drop and recreate database ---
echo.
echo Preparing local database...
"%MYSQL_PATH%\mysql.exe" -u %LOCAL_USER% -e "DROP DATABASE IF EXISTS %LOCAL_DB%; CREATE DATABASE %LOCAL_DB% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

REM --- Import the file ---
echo.
echo Importing %IMPORT_FILE%...
echo This may take a few minutes depending on file size...

"%MYSQL_PATH%\mysql.exe" -u %LOCAL_USER% %LOCAL_DB% < "%BACKUP_DIR%\%IMPORT_FILE%"

if errorlevel 1 (
    echo.
    echo ERROR: Failed to import database.
    echo Your local backup is at: met_local_backup_%TIMESTAMP%.sql
    pause
    exit /b 1
)

REM --- Done ---
echo.
echo ============================================
echo   Import Complete!
echo ============================================
echo.
echo Imported: %IMPORT_FILE%
echo Database: %LOCAL_DB%
echo.

pause
