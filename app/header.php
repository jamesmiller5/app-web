<?php
session_start();
ob_start();
error_reporting( E_ALL | E_STRICT );

require "functions.php";

?>
<!doctype html>
<html>
	<head>
		<style>
		</style>
	</head>
	<body>
		<section>
			<header>
				<h1>My Site</h1>
				<?php
				if( !isset( Session::$user ) ) {
				?>
				<p><a href="login.php">Login</a></p>
				<?php
				} else {
				?>
				<p><a href="login.php?logout">Logout</a></p>
				<?php
				}
				?>
			</header>
