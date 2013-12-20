Authority Publishing Platform
=============================

Overview
--------
Everyday we make subjective decisions on who we trust and what we trust them about. 
In a professional context, this could be hiring or partnering with other individuals 
who have a specific skill set necessary for the task at hand. The Authority Publishing Platform
aims to provide a visual representation of this authority network, in it's naturally subjective way.
Every users view into the graph will be individual as trust is subjective and may not be reciprocated. For example, a student may trust their professor on "C++" but that professor only trusts other professors. The purpose of APP is to expose this network and allow a user to connect with new peers that are trusted by the user.

Mechanics
---------
The authority network is a directed cyclic graph with edges being skill or subject matter and nodes being email address identities.
This web application has been built in PHP 5.3 with a Sqlite backed database.


Possible Objectives
-------------------
+ Security: Passwords are currently stored as plain-text and loaded from the database. Follow best practices and implement a set of security practices such as using a standard hash + salt combination. PHP's "crypt" & "hash" functions may be of use.
  + Extra points for having a member of the faculty evalute your teams solutions and implementation while providing their feedback 
  + Extra extra points: Your database and source code have been leaked! Simulate and measure the mean time it takes to extract a single user password. If the time is very large, give an estimated time and with supporting explination. Draft an apology message for your users explaining the situation and how they can protect themselves.
+ Currently, the list of Subjects is static and requires manual entries into the database. Design and implement a way for users 
  to add new Subjects while also normalizing upper and lower case, different punctuation, etc.
  + Extra points for automatic suggestions to aid user input
  + Extra extra points for normalizing subjects across vocabularies & languages eg. "color = colour" 

+ Currently, only registered users can be given authority by peers. Let a user give authority on a subject to any email and send an 
  invitation to that email if there is no account associated with it.
  + Extra points if a single profile can claim multiple emails. Consider cases such as "editor@purdueexponent.org" which represent a title rather than a specific person.
  + Extra extra points if rate-limiting/spam prevention logic disables abuse of invitation system

+ New Feature: In the graph view, label the edges with the subject that the user is trusted on.
  + Extra points if multiple edges are coalesced into a single edge with comma separated subjects
  + Extra extra points if graph areas are organized so connected sub-graphs on a subject are obvious to a user. 
    Eg: visually group similar subjects on the graph by pushing them into a corner. "C" & "C++" edges will be visually closer than "C" and "Baking" 

+ New Feature: Rather than having trust be a binary indicator develop a scale with 0 being not trusted, 0.5 being equally trusted as oneself and 1.0 being of greater trust than oneself
  + Extra points for clearly showing a user who is most trusted in the graph, perferably using visual cues
  + Extra extra points, when walking the graph use this scale to weigh nodes in the network. Develop a stategy eg: muliply scales, add scales linearly, etc. and give an explination as to why that strategy is a benifit to users.

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
