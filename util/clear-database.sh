#!/bin/bash
APPDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../" && pwd )"
sqlite3 -batch "$APPDIR/database.sqlite" <<DROPTABLES
PRAGMA foreign_keys = ON;

drop table email;
drop table user;
drop table citation;
drop table trust;
drop table subject;
DROPTABLES
