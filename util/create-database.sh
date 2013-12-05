#!/bin/bash
APPDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../" && pwd )"
sqlite3 -batch "$APPDIR/database.sqlite" <<CREATETABLES
PRAGMA foreign_keys = ON;

create table if not exists User(
  id integer primary key autoincrement,
  email string not null,
  password string not null,
  name string null
);

create table if not exists Email(
  address string primary key,
  token string not null
  );

create table if not exists Citation(
  id integer primary key autoincrement,
  subject string not null,
  description string not null,
  source string not null,
  foreign key(subject) references Subject(name)
);

create table if not exists Trust(
  trusterId integer,
  trusteeId integer,
  citeId integer,
  foreign key(trusterId) references User(id),
  foreign key(trusteeId) references User(id),
  foreign key(citeId) references Citation(id)
);

create table if not exists Subject(
    name string primary key
);
CREATETABLES
