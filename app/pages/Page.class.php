<?php
class Page {
	function handle(Request $request) {
		throw new LogicException( __FILE__ . ":" . __FUNCTION__ . " shouldn't be called, extended only" );
	}

	function headAndFoot(callable $toRender) {
		$this->head();
		$toRender();
		$this->foot();
	}

	//header is a php function
	function head() {
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
				<?php $this->loginBox(); ?>
			</header>
<?php
	}

	function foot() {
?>
			<footer><p>Copyright No One but me</p></footer>
		</section>
	</body>
</html>
<?php
	}

	function loginBox() {
		if( !isset( Session::$user ) ) {
?>
			<p><a href="login">Login</a></p>
<?php
		} else {
?>
			<p><a href="login?logout">Logout</a></p>
<?php
		}
	}

	function alert( $title ) {
?>
	<h2><?=htmlentities($title)?></h2>
<?php
	}
}
