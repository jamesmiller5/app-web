#!/bin/bash
APPDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../" && pwd )"
sqlite3 -batch "$APPDIR/database.sqlite" <<INSERTDATA
PRAGMA foreign_keys = ON;

insert into Email values("foo@bar", "thisisfoobar123");
insert into Email values("bar@foo", "456barfoo");

insert into User(email,password) values("foo@bar", "number");
INSERTDATA
