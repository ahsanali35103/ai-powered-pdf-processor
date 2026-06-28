@echo off
mkdir C:\data\db 2>nul
"C:\Program Files\MongoDB\Server\8.2\bin\mongod.exe" --dbpath C:\data\db --replSet=rs0
