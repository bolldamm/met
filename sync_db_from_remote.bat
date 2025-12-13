@echo off
setlocal EnableDelayedExpansion

REM ============================================================================
REM MET Database Sync Script
REM Pulls the remote production database to your local Laragon environment
REM run it from the command line in the project directory:
REM    cd /d F:\laragon\www
REM    sync_db_from_remote.bat
REM Or simply double-click the file in Windows Explorer.
REM
REM IMPORTANT: Make sure your SSH tunnel is active on port 3308 before running!
REM ============================================================================

REM --- Configuration ---
set "MYSQL_PATH=F:\laragon\bin\mysql\mysql-8.0.40-winx64\bin"
set "LOCAL_DB=metmeetings_edatamet"
set "LOCAL_USER=root"
set "LOCAL_PASS="

REM Remote database settings - uses config file to handle special chars in password
set "REMOTE_CNF=F:\laragon\www\.my_remote.cnf"
set "REMOTE_DB=metmeetings_edatamet"

REM Backup directory
set "BACKUP_DIR=F:\laragon\www\db_backups"
set "TIMESTAMP=%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%"
set "TIMESTAMP=%TIMESTAMP: =0%"

REM --- Pre-flight checks ---
echo.
echo ============================================
echo   MET Database Sync (Remote to Local)
echo ============================================
echo.

if not exist "%MYSQL_PATH%\mysql.exe" (
    echo ERROR: MySQL not found at %MYSQL_PATH%
    echo Please update MYSQL_PATH in this script.
    pause
    exit /b 1
)

if not exist "%REMOTE_CNF%" (
    echo ERROR: Remote config file not found at %REMOTE_CNF%
    pause
    exit /b 1
)

REM Create backup directory if it doesn't exist
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM --- Step 1: Test remote connection ---
echo.
echo Testing remote connection...
echo (Make sure your SSH tunnel is active on port 3308)
echo.
"%MYSQL_PATH%\mysql.exe" --defaults-extra-file="%REMOTE_CNF%" -e "SELECT 1" >nul 2>&1
if errorlevel 1 (
    echo ERROR: Could not connect to remote database.
    echo Check that your SSH tunnel is running and credentials in .my_remote.cnf are correct.
    pause
    exit /b 1
)
echo Remote connection OK.

REM --- Step 2: Export from remote ---
echo.
echo Exporting from remote database...
echo This may take a few minutes depending on database size...

"%MYSQL_PATH%\mysqldump.exe" ^
    --defaults-extra-file="%REMOTE_CNF%" ^
    --routines ^
    --triggers ^
    --single-transaction ^
    --set-gtid-purged=OFF ^
    %REMOTE_DB% > "%BACKUP_DIR%\met_sync_%TIMESTAMP%.sql"

if errorlevel 1 (
    echo ERROR: Failed to export remote database.
    pause
    exit /b 1
)

echo Export complete: met_sync_%TIMESTAMP%.sql

REM --- Step 3: Backup local database (safety) ---
echo.
echo Backing up local database first (safety measure)...

"%MYSQL_PATH%\mysqldump.exe" ^
    -u %LOCAL_USER% ^
    --routines ^
    --triggers ^
    %LOCAL_DB% > "%BACKUP_DIR%\met_local_backup_%TIMESTAMP%.sql" 2>nul

if exist "%BACKUP_DIR%\met_local_backup_%TIMESTAMP%.sql" (
    echo Local backup saved: met_local_backup_%TIMESTAMP%.sql
) else (
    echo Note: No existing local database to backup (this is OK for first sync)
)

REM --- Step 4: Import to local ---
echo.
echo Importing to local database...

REM Drop and recreate local database to ensure clean slate
"%MYSQL_PATH%\mysql.exe" -u %LOCAL_USER% -e "DROP DATABASE IF EXISTS %LOCAL_DB%; CREATE DATABASE %LOCAL_DB% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

REM Import the dump
"%MYSQL_PATH%\mysql.exe" -u %LOCAL_USER% %LOCAL_DB% < "%BACKUP_DIR%\met_sync_%TIMESTAMP%.sql"

if errorlevel 1 (
    echo ERROR: Failed to import database.
    echo Your local backup is at: met_local_backup_%TIMESTAMP%.sql
    pause
    exit /b 1
)

echo Import complete!

REM --- Step 5: Cleanup old backups (keep last 5) ---
echo.
echo Cleaning up old backups (keeping last 5)...
for /f "skip=5 delims=" %%F in ('dir /b /o-d "%BACKUP_DIR%\met_sync_*.sql" 2^>nul') do del "%BACKUP_DIR%\%%F"
for /f "skip=5 delims=" %%F in ('dir /b /o-d "%BACKUP_DIR%\met_local_backup_*.sql" 2^>nul') do del "%BACKUP_DIR%\%%F"

REM --- Done ---
echo.
echo ============================================
echo   Sync Complete!
echo ============================================
echo.
echo Remote: %REMOTE_DB% (via SSH tunnel on port 3308)
echo Local:  %LOCAL_DB% @ localhost
echo.
echo Backup stored at: %BACKUP_DIR%\met_sync_%TIMESTAMP%.sql
echo.

pause
