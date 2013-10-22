<?php
require realpath( dirname( __FILE__ ) . '/../app/app.php' );

//include all our classes
require APPDIR . "app/data-classes.php";

//include Request
require APPDIR . "app/Request.class.php";

//include Router
require APPDIR . "app/Router.class.php";

//include Pages
require APPDIR . "app/pages/Page.class.php";
require APPDIR . "app/pages/pages.php";

//Load Session data
Session::load();

//Make a new Request
$request = new Request();
$request->importSuperGlobals();
$request->exportSuperGlobals();

//Route our Request
Router::getDefault()->route( $request );
?>
