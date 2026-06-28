@echo off
cd /d "C:\Program Files\MongoDB\Server\8.2\bin"
mongosh.exe --eval "rs.initiate()"
