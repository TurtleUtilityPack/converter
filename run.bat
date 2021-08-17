@echo off
TITLE Turtle Converter
echo Initializing converter....

if exist php8\php.exe (

php8\php.exe Converter.php


) else (
echo Can't find PHP! Please download PHP8 Binaries for Windows and drop the bin folder where the .bat is located.
)
pause