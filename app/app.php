<?php
ob_start();
error_reporting( E_ALL | E_STRICT );

//make all errors into exceptions
function exception_error_handler( $errno, $errstr, $errfile, $errline ) {
	throw new ErrorException( $errstr, 0, $errno, $errfile, $errline );
}
set_error_handler("exception_error_handler");

//Debug echo, dump a var and stop executing
function decho( $vars, $title=null, $die=true ) {
	echo ($title) ? "<h1>".$title."</h1>" : null,"<pre>";
	var_dump( $vars );
	echo "</pre>";
	if( $die ){
		die();
	}
}

//General Functions
function define_constants() {
	//APPDIR is the directory where this file resides
	define( "APPDIR", realpath( dirname( __FILE__ ) . "/../" ) . "/" );

	//Figure out our app-url by config file
	if( !is_readable( APPDIR . "/config.ini" ) ) {
		throw new Exception("No config.ini found, expected one at " . APPDIR);
	}

	$config = parse_ini_file( APPDIR . "config.ini" );
	if( !isset( $config['path'] ) ) {
		throw new Exception("'path' key is missing in " . APPDIR . "/config.ini, try \"path=/\" " );
	}

	define( "URLPATH", $config['path'] );


	//check session path
	if( session_save_path() == "" ) {
		if( !is_writable( APPDIR . "sessions/" ) ) {
			if( !mkdir( APPDIR . "sessions/" ) ) {
				throw new Exception("Couldn't create \"" . APPDIR . "sessions/" . "\" directory");
			}
		}
		session_save_path( APPDIR . "sessions/" );
	}
}

//put into a function as not to pollute the global namespace
define_constants();
