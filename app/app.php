<?php
//Output buffering prevents cookie modification errors
ob_start();

//Report every error, makes PHP less lazy and more strict
error_reporting( E_ALL | E_STRICT );

//make all errors into exceptions which haults execution
set_error_handler( function( $errno, $errstr, $errfile, $errline ) {
	throw new ErrorException( $errstr, 0, $errno, $errfile, $errline );
});

//Pretty print exceptions, could be a 500 page for users
function debug_exception_handler( Exception $e ) {
	echo "<h2>Uncaught exception \"", get_class($e), "\" thrown at</h2>";
	echo "<h3>", $e->getFile(), " on line ", $e->getLine(), "</h3>";
	echo "<h4> Reason </h4>";
	echo "<pre style='white-space: pre-line'>", $e->getMessage(), "</pre>";
	echo "<h4>Stack Trace</h4>";
	echo "<pre>";
	echo $e->getTraceAsString();
	echo "</pre>";

	if( $e->getPrevious() ) {
		echo "<hr />";
		echo "<h1> Previous Exception </h1>";
		echo "<div style='margin-left: 25px'>";
			debug_exception_handler( $e->getPrevious() );
		echo "</div>";
	}
}
set_exception_handler("debug_exception_handler");

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

//put into a function as not to pollute the global variable namespace
define_constants();
