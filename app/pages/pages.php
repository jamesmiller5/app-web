<?php
class Index extends Page {
	function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		$user = Session::getUser();

		$name = "";
		if( $user ) {
			$name = $user->email;
		}

		echo <<<HTML
		<h1> Hi "{$name}", this is the Index Page</h1>
HTML;
	}
}
//register this class as the default page aka '/'
Router::getDefault()->register( "/", new Index() );

class Login extends Page {
	public $logout = false;
	public $badLogin = false;

	function handle(Request $request) {

		if( isset($request['logout']) ) {
			Session::destroy();

			$this->logout = true;
		} else if( isset( $request->post ) ) {
			//attempt login
			$user = User::login( $request->post['username'], $request->post['password'] );
			if( $user != null ) {
				//set Session to this $user
				Session::setUser( $user );
			} else {
				$this->badLogin = true;
			}
		}

		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		if( $this->logout ) {
			$this->logoutPage();
		} else {
			$this->loginPage();
		}
	}

	function logoutPage() {
		echo <<<HTML
		<h1> All logged out! </h1>
HTML;
	}

	function loginPage() {
		echo <<<HTML
		<h1> Hi this is the Login page </h1>
HTML;
		if( $this->badLogin ) {
			Page::alert("Bad Login");
		}

		//use URLPATH in front of URL's or else links will break when we host from "/~mille168/" vs "/" vs "/some/subdir"
		$URLPATH = URLPATH;
		echo <<<HTML
		<form method="post" action="{$URLPATH}login">
			<label for="u">Username:</label><input type="text" id="u" name="username" />
			<label for="p">Password:</label><input type="password" id="p" name="password" />
			<input type="submit" />
		</form>
HTML;
	}
}
Router::getDefault()->register( "/login", new Login() );

class Test extends Page {
	function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		$email  = "";
		$user = User::login("foo@bar","number");
		if( $user )
			$email = htmlentities($user->email);
		echo <<<HTML
		<h1> Hi this is the Test Page id:"{$email}" </h1>
HTML;
	}
}
//register this class as the default page aka '/'
Router::getDefault()->register( "/test", new Test() );

class Graph extends Page {
  
  
  function handle(Request $request) {
		//no errors? lets render!
		parent::headAndFoot( function() { $this->render(); } );
	}

	function render() {
		echo <<<HTML
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script src="/js/springy.js"></script>
<script src="/js/springyui.js"></script>
<script>
var graph = new Springy.Graph();

var dennis = graph.newNode({
  label: 'Dennis',
  ondoubleclick: function() { console.log("Hello!"); }
});
var michael = graph.newNode({label: 'Michael'});
var jessica = graph.newNode({label: 'Jessica'});
var timothy = graph.newNode({label: 'Timothy'});
var barbara = graph.newNode({label: 'Barbara'});
var franklin = graph.newNode({label: 'Franklin'});
var monty = graph.newNode({label: 'Monty'});
var james = graph.newNode({label: 'James'});
var bianca = graph.newNode({label: 'Bianca'});

graph.newEdge(dennis, michael, {color: '#00A0B0'});
graph.newEdge(michael, dennis, {color: '#6A4A3C'});
graph.newEdge(michael, jessica, {color: '#CC333F'});
graph.newEdge(jessica, barbara, {color: '#EB6841'});
graph.newEdge(michael, timothy, {color: '#EDC951'});
graph.newEdge(franklin, monty, {color: '#7DBE3C'});
graph.newEdge(dennis, monty, {color: '#000000'});
graph.newEdge(monty, james, {color: '#00A0B0'});
graph.newEdge(barbara, timothy, {color: '#6A4A3C'});
graph.newEdge(dennis, bianca, {color: '#CC333F'});
graph.newEdge(bianca, monty, {color: '#EB6841'});

jQuery(function(){
  var springy = window.springy = jQuery('#springydemo').springy({
    graph: graph,
    nodeSelected: function(node){
      console.log('Node selected: ' + JSON.stringify(node.data));
    }
  });
});
</script>
		
		<canvas id="springydemo" width="640" height="480" >
		</canvas>
HTML;
	}
}
Router::getDefault()->register( "/graph", new Graph() );
