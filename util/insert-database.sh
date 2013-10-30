#!/bin/bash
APPDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../" && pwd )"
sqlite3 -batch "$APPDIR/database.sqlite" <<INSERTDATA
PRAGMA foreign_keys = ON;

insert into User(email,password) values("foo@bar", "number");
insert into User(email,password) values("bar@foo", "people");
insert into User(email,password) values("unit@test", "little");
insert into User(email,password) values("test@unit", "before");
INSERTDATA
