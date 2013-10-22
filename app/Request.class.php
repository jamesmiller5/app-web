<?php
class Request extends ArrayObject {
	public $post;
	public $get;
	public $put;
	public $delete;
	public $cookie;
	public $files;
	public $request;
	public $server;
	public $env;
	public $session;

	function import( $request = null, $get = null, $post = null, $files = null,
		$cookie = null, $session = null, $server = null, $env = null,
		$put = null, $delete = null ) {

		if( isset( $request ) ) {
			parent::__construct( $request, ArrayObject::STD_PROP_LIST );
			$this->request = $this;
		}

		if( isset( $get ) )
			$this->get = $get;

		if( isset( $post ) )
			$this->post = $post;

		if( isset( $put ) )
			$this->put = $put;

		if( isset( $delete ) )
			$this->delete = $delete;

		//make file objects
		if( isset( $files ) )
			$this->files = self::filesToFileObjects( $files );

		if( isset( $cookie ) )
			$this->cookie = $cookie;

		if( isset( $session ) )
			$this->session = $session;

		if( isset( $server ) )
			$this->server = $server;

		if( isset( $env ) )
			$this->env = $env;
	}

	function exportSuperGlobals() {
		//point super globals to our objects mimicing natural behavior
		//must be reference due to magic
		if( isset( $this->request ) )
			$_REQUEST = &$this->request;

		if( isset( $this->get ) )
			$_GET = &$this->get;

		if( isset( $this->post ) )
			$_POST = &$this->post;

		if( isset( $this->put ) )
			$GLOBALS['_PUT'] = &$this->put;

		if( isset( $this->delete ) )
			$GLOBALS['_DELETE'] = &$this->delete;

		if( isset( $this->files ) )
			$_FILES = &$this->files;

		if( isset( $this->cookie ) )
			$_COOKIE = &$this->cookie;

		if( isset( $this->session ) )
			$_SESSION = &$this->session;

		if( isset( $this->server ) )
			$_SERVER = &$this->server;

		if( isset( $this->env ) )
			$_ENV = &$this->env;
	}

	function importSuperGlobals() {
		if( isset( $_SERVER['REQUEST_METHOD'] ) ) {
			//Next methods are not auto-parsed by PHP, we must invoke the param parser
			if( $_SERVER['REQUEST_METHOD'] == "PUT" ) {
				$put = array();
				parse_str( file_get_contents('php://input'), $put );
			} else if( $_SERVER['REQUEST_METHOD'] == "DELETE" ) {
				$delete = array();
				parse_str( file_get_contents('php://input'), $delete );
			}
		}

		$this->import(
			$_REQUEST,
			( isset( $_GET ) && count( $_GET ) ) ? $_GET : null,
			( isset( $_POST ) && count( $_POST ) ) ? $_POST : null,
			$_FILES,
			$_COOKIE,
			( isset( $_SESSION ) ) ? $_SESSION : null,
			$_SERVER,
			$_ENV,
			( isset( $put ) ) ? $put : NULL,
			( isset( $delete ) ) ? $delete : NULL
		);
	}

	static function filesToFileObjects( $farray = null, $flags = ArrayObject::STD_PROP_LIST, $iterator = null ) {
		if( !isset( $farray ) )
			return null;

		if( isset( $iterator ) )
			$rarray = new ArrayObject( array(), $flags, $iterator );
		else
			$rarray = new ArrayObject( array(), $flags );

		foreach( $farray as $field_name )
		{
			if( isset( $farray[$field_name]['tmp_name'] ) )
			{
				$rarray[$field_name] = new ArrayObject();

				if( is_array( $farray[$field_name]['tmp_name'] ) )
				{
					$count = 0;
					$limit = count( $farray[$field_name]['tmp_name'] );

					while( $count < $limit )
					{
						$rarray[$field_name][] = new PostFile( $farray[$field_name][$count] );
						$count++;
					}
				}
				else
					$rarray[$field_name] = new PostFile( $farray[$field_name] );
			}
		}

		return $rarray;
	}
}
