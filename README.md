app-web
=======

Web version of APP

Setup
=====

Create a "config.ini" in the root directory. If you are running the webserver from command line with a specific port use this the config:
	
	--config.ini--
	[app]
		path="/"

To start the server, use "php -S 0.0.0.0:9121". It will listen on port 9121 which you can connect with a web browser
