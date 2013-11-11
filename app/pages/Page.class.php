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
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
  		<meta name="viewport" content="width=device-width">
 		<title>Trust Networks</title>
  		<link rel="stylesheet" href="css/foundation.css">
  		<script src="js/vendor/custom.modernizr.js"></script>
	</head>

	<body>
		<header class="row">
			<div class="large-12 columns">
				<h2 style="text-align:center">Authority Connections</h2>
				<hr />
			</div>
		</header>

		<nav class="top-bar large-11 columns large-centered" style="">
HTML;
		if( !isset( Session::$user ) ) {
			$this->loginBox();
		} else {
			$this->navBar();
		}

		echo <<<HTML
		</nav>
HTML;
	}

	function foot() {
		echo <<<HTML
	<footer class="row">
		<div class="large-12 columns">
			<hr />
			<h5 style="text-align:center">By James Miller, Mitchell Mounts, Andrew Mack, and David Zinn</h5>
		</div>
	</footer>
 	<script>
  	document.write('<script src=' +
  	('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') +
  	'.js><\/script>')
  	</script>

  	<script src="js/foundation.min.js"></script>

  	<script>
    	$(document).foundation();
  	</script>

	</body>
</html>
HTML;
	}

	function loginBox() {
		echo <<<HTML
		  <section class="top-bar-section" style="left: 0%;">
            <!-- Login Field -->
            <ul class="right">
            	<form>
            		<div class="small-5 columns">
            			<input type="text" placeholder="Email">
            		</div>

            		<div class="small-5 columns">
        				<input type="text" placeholder="Password">
        			</div>
            		<div class="small-2 columns">
            			<a href="login" class="button">Login</a>
            		</div>
            	</form>
            </ul>
          </section>
HTML;
	}

	function navBar() {
		echo <<<HTML
		  <ul class="title-area hide-for-small">
            <!-- Title Area -->
            <li class="name">
              	<h1>
                	<a href="#">
                  		(Profile Name)
                	</a>
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
              <li class="signout"<a href="login?logout">Logout</a></li>
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
