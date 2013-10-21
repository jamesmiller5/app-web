<?php
require realpath( dirname( __FILE__ ) . '/../app/app.php' );
//test user adds

echo "<pre>";

$makeme = new User;
$makeme->password = "hasfkl";
var_dump($makeme->store());
var_dump($makeme);

$user = new User;

var_dump($user->load(1));
$user->password = "blah";
var_dump($user);

var_dump($GLOBALS);

?>
