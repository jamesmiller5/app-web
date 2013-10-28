#!/bin/bash
APPDIR=$(dirname $(readlink -f $0))
sqlite3 -batch "$APPDIR/database.sqlite" <<CREATETABLES
PRAGMA foreign_keys = ON;

create table if not exists User(
  id integer primary key autoincrement,
  password string not null
);

create table if not exists Email(
  address string primary key,
  userId integer,
  foreign key(userId) references user(id)
);

create table if not exists Citation(
  id integer primary key autoincrement,
  description string not null,
  source string not null
);

create table if not exists Trust(
  trusterId integer,
  trusteeId integer,
  citeId integer,
  foreign key(trusterId) references user(id),
  foreign key(trusteeId) references user(id),
  foreign key(citeId) references citation(id)
);
CREATETABLES
