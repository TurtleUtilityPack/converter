@echo off
TITLE Turtle Converter
echo Initializing converter....

if exist bin\php\php.exe (
set PHPRC=""
set PHP_BINARY=bin\php\php
%PHP_BINARY% Converter.php
) else (
echo Can't find PHP! Please download PHP8 Binaries and drop them (bin folder) where the .bat is located.
)
pause