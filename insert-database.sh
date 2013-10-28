#!/bin/bash
APPDIR=$(dirname $(readlink -f $0))
sqlite3 -batch "$APPDIR/database.sqlite" <<INSERTDATA
PRAGMA foreign_keys = ON;

insert into User(password) values("number");
insert into User(password) values("people");
insert into User(password) values("little");
insert into User(password) values("before");
insert into User(password) values("follow");
insert into User(password) values("around");
INSERTDATA
