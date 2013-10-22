#!/bin/bash
APPDIR=$(dirname $(readlink -f $0))
sqlite3 -batch "$APPDIR/database.sqlite" <<CREATETABLES
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS user( 
	id INTEGER PRIMARY KEY,
	password STRING
);
CREATE TABLE IF NOT EXISTS email( 
	id INTEGER PRIMARY KEY,
	userId INTEGER, 
	address STRING,

	FOREIGN KEY(userId) REFERENCES user(id)
);
CREATETABLES
