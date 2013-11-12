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
		$ASSETS = "/assets/";
		echo <<<HTML
<!DOCTYPE html>
<html>
	<head>
 		<title>Trust Networks</title>
		<meta charset="utf-8">
  		<meta name="viewport" content="width=device-width">
  		<link rel="stylesheet" href="{$ASSETS}css/foundation.min.css">
  		<script src="{$ASSETS}js/vendor/custom.modernizr.js"></script>
	</head>

	<body>
		<header class="row">
			<div class="large-12 columns">
				<h2 style="text-align:center">Authority Connections</h2>
				<hr />
			</div>
		</header>

		<nav class="top-bar large-11 columns large-centered" style="overflow: hidden">
HTML;
		if( Session::getUser() == NULL ) {
			$this->loginBox();
		} else {
			$this->navBar();
		}

		echo <<<HTML
		</nav>
		<section style="margin: auto 7%">
HTML;
	}

	function foot() {
		$ASSETS = "/assets/";
		echo <<<HTML
		</section>
		<footer class="row">
			<div class="large-12 columns">
				<hr />
				<h5 style="text-align:center">By James Miller, Mitchell Mounts, Andrew Mack, and David Zinn</h5>
			</div>
		</footer>
		<script>
		document.write('<script src=' +
		('__proto__' in {} ? '{$ASSETS}js/vendor/zepto' : '{$ASSETS}js/vendor/jquery') +
		'.js><\/script>')
		</script>

		<script src="{$ASSETS}js/foundation.min.js"></script>

		<script>
			$(document).foundation();
		</script>
	</body>
</html>
HTML;
	}

	function loginBox() {
		$URLPATH = URLPATH;
		echo <<<HTML
		  <section class="top-bar-section" style="left: 0%;">
            <!-- Login Field -->
            <ul class="right">
            	<form method="post" action="{$URLPATH}login">
            		<div class="small-5 columns">
            			<input type="text" placeholder="Email" name="email" >
            		</div>

            		<div class="small-5 columns">
        				<input type="password" placeholder="Password" name="password">
        			</div>
            		<div class="small-2 columns">
            			<input type="submit" class="button">Login</input>
            		</div>
            	</form>
            </ul>
          </section>
HTML;
	}

	function navBar() {
		$URLPATH = URLPATH;
		$name = "";
		$user = Session::getUser();
		if( isset( $user ) )
			$name = $user->email;

		echo <<<HTML
		  <ul class="title-area hide-for-small">
            <!-- Title Area -->
            <li class="name">
              	<h1>
                	<a href="#">{$name}</a>
              	</h1>
            </li>
            <li class="toggle-topbar"><a href="#"></a></li>
          </ul>

          <ul class="right">
              <li class=""><a href="#">Profile</a></li>
              <li class=""><a href="#">Trust Network</a></li>
              <li class=""><a href="#">Subjective Network</a></li>
              <li class="has-dropdown not-click">
                <a href="#">Graph Views</a>
                <ul class="dropdown"><li class="title back js-generated"><h5><a href="#">Back</a></h5></li>
                  <li class=""><a href="#">Subjective Network</a></li>
                  <li class=""><a href="#">Trust Network</a></li>
                </ul>
              </li>
              <li class="signout"<a href="{$URLPATH}login?logout">Logout</a></li>
          </ul>
HTML;
	}

	function alert( $title ) {
		$title = htmlentities(trim($title));
		echo <<<HTML
		<h2>{$title}</h2>
HTML;
	}
}
