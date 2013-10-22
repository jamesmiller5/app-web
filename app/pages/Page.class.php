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
		echo <<<HTML
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
HTML;
				$this->loginBox();
		echo <<<HTML
			</header>
HTML;
	}

	function foot() {
		echo <<<HTML
			<footer><p>Copyright No One but me</p></footer>
		</section>
	</body>
</html>
HTML;
	}

	function loginBox() {
		if( !isset( Session::$user ) ) {
			echo <<<HTML
			<p><a href="login">Login</a></p>
HTML;
		} else {
			echo <<<HTML
			<p><a href="login?logout">Logout</a></p>
HTML;
		}
	}

	function alert( $title ) {
		$title = htmlentities(trim($title));
		echo <<<HTML
		<h2>{$title}</h2>
HTML;
	}
}
