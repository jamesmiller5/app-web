Authority Publishing Platform
=============================

Overview
--------
Everyday we make subjective decisions on who we trust and what we trust them about. 
In a professional context, this could be hiring or partnering with other individuals 
who have a specific skill set necessary for the task at hand. The Authority Publishing Platform
aims to provide a visual representation of this trust network, in it's naturally subjective way.

Mechanics
---------
The authority network is a directed cyclic graph with edges being skill or subject matter and nodes being email address identities.
This web application has been built in PHP 5.3 with a Sqlite backed database.


Possible Objectives
-------------------
+ Currently, the list of Subjects is statically created by manual entries into the database. Design and implement a way for users 
  to add new Subject types normalizing upper and lower case, different punctuation, etc.
  + Extra points for automatic suggestions to aid user input
  + Extra extra points for normalizing subjects across vocabularies & languages eg. "color = colour" 

+ Currently, only registered users can be given authority by peers. Let a user give authority on a subject to any email and send an 
  invitation to that email if there is no account associated with it.
  + Extra points if a single profile can claim multiple emails
  + Extra extra points if rate-limiting/spam prevention logic disables abuse of invitation system

+ New Feature: In the graph view, label the edges with the subject that the user is trusted on.
  + Extra points if multiple edges are coalesced into a single edge with comma separated subjects
  + Extra extra points if graph areas are organized so connected sub-graphs on a subject are obvious to a user. 
    Eg: visually group similar nodes by pushing them into a corner

Setup
=====

Create a "config.ini" in the root directory. If you are running the web server from command line with a specific port use this the config:
	
	--config.ini--
	[app]
		path="/"

If this application is not running at the root directory level aka "/" (such as in certian Apache configurations) set the path to the desired url, eg.

	--config.ini--
	[app]
		path="/~james/projects/app-web"

The above configuration will prepend all URL's in the application with "/~james/projects/app-web/"

To start the built-in PHP web server server, use the below. It will listen on port 9121 which you can connect with a web browser

	 bin/php -S 0.0.0.0:9121 -t docroot/
