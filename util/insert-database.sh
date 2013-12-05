#!/bin/bash
APPDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../" && pwd )"
sqlite3 -batch "$APPDIR/database.sqlite" <<INSERTDATA
PRAGMA foreign_keys = ON;

insert into Email values("foo@bar", "thisisfoobar123");
insert into User(email, password) values("bar@foo", "password");
insert into User(email, password) values("foo@bar", "number");

insert into Email values("harold@app.com", "htoken");
insert into Email values("jennifer@app.com", "jetoken");
insert into Email values("joseph@app.com", "jotoken");
insert into Email values("kristen@app.com", "ktoken");
insert into Email values("daniel@app.com", "dtoken");

insert into User(email, password) values("harold@app.com", "harold");
insert into User(email, password) values("jennifer@app.com", "jennifer");
insert into User(email, password) values("joseph@app.com", "joseph");
insert into User(email, password) values("kristen@app.com", "kristen");
insert into User(email, password) values("daniel@app.com", "daniel");

insert into Subject values("PHP");
insert into Subject values("C++");
insert into Subject values("Java");
insert into Subject values("Python");
insert into Subject values("WWII History");

insert into Citation(subject, description) values("PHP", "they are great!");
insert into Citation(subject, description) values("PHP", "nobody PHPs as good as they do.");
insert into Citation(subject, description) values("Java", "they know how to Java.");
insert into Citation(subject, description) values("Java", "coffee doesn't know how to Java as hard as they do.");
insert into Citation(subject, description) values("WWII History", "they faught in it.");
insert into Citation(subject, description) values("WWII History", "they wrote a book about it.");
insert into Citation(subject, description) values("WWII History", "they own a mosin-nagant.");
insert into Citation(subject, description) values("Python", "they know python so well you'd think they were a snake!");

insert into Trust values(1,2,1);
insert into Trust values(1,3,2);
insert into Trust values(1,4,3);
insert into Trust values(1,5,4);
insert into Trust values(1,6,5);
insert into Trust values(2,5,6);
insert into Trust values(6,7,7);

INSERTDATA
