#!/bin/bash
APPDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../" && pwd )"
sqlite3 -batch "$APPDIR/database.sqlite" <<PEEKTABLES
PRAGMA foreign_keys = ON;

select * from user;
select * from email;
select * from citation;
select * from trust;
select * from subject;
PEEKTABLES
