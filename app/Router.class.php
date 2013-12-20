<?php
//routes URLs to the correct pages
class Router {
	static private $default;

	private $handlers = array();

	static function getDefault() {
		if( Router::$default == null )
			Router::$default = new Router();

		return Router::$default;
	}

	function register($path, callable $handler ) {
		if( !isset( $path ) )
			throw new Exception( __FILE__ . ":" . __FUNCTION__ . " \$path was null" );

		if( $handler== null )
			throw new Exception( __FILE__ . ":" . __FUNCTION__ . " \$handler was null" );

		if( is_array( $path ) ) {
			foreach( $path as $value ) {
				$this->register( $value, $handler );
			}

			return;
		}

		$path = trim( $path );
		$this->handlers[$path] = $handler;
	}

	function route( Request $request ) {
		//use the PATH_INFO to evaluate the correct object, longest path match wins
		if( isset( $request->server['PATH_INFO'] ) ) {
			$inPath = $request->server['PATH_INFO'];
		} else {
			//sane default
			$inPath = "/";
		}

		$bestHandler = null;

		//easy case, path matches exactly
		if( isset( $this->handlers[$inPath] ) ) {
			$bestHandler = $this->handlers[$inPath];
		} else {
			$max = 0;

			//non easy case, find longest match counting '/'s and use that
			$r = strlen($inPath);
			foreach( $this->handlers as $matchPath => $handler ) {
				$c = 0;
				$i = 0;
				$l = strlen($matchPath);

				while( true ) {
					//matched a whole path, it wins (so far)
					if( $i == $l ) {
						$c++;
						break;
					}

					//end of the input, just stop
					if( $i == $r ) {
						break;
					}

					//check if we made it to the next slash
					if( $matchPath[$i] == '/' ) {
						$c++;
					}

					//check if we should continue matching
					if( $matchPath[$i] != $inPath[$i] ) {
						break;
					}

					$i++;
				}

				if( $c > $max ) {
					$bestHandler = $handler;
					$max = $c;
				}
			}

			if( $bestHandler == null ) {
				throw new Exception( __FILE__ . ":" . __FUNCTION__ . " couldn't route this request");
			}
		}

		//call the handler
		$bestHandler($request);
	}
}
