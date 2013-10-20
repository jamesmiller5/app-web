<?php
require realpath( dirname( __FILE__ ) . '/../app/app.php' );
//test user adds

echo "<pre>";

$user = new User;
$user->load(1);
var_dump($user);
$user->password = "blah";
var_dump($user);

var_dump($GLOBALS);

?>
