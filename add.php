<?php
/*
 *      add.php
 *      
 *      Copyright 2008 Dylan Enloe <ninina@Siren>
 *		Copyright 2009 Drew Fisher <kakudevel@gmail.com>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

function __autoload($class_name) {
    require_once $class_name . '.php';
}

#first login info
$user = new User();
#only admins can add events
if(!$user->is_Admin())
{
	$page = new Webpage("Add Event");
	echo "Only Admins can create new Panels. Sorry";
	exit(0);
}

if(isset($_POST['add']))
{
	//ok we'll add the entry here.
	
	//get data from post and varify it
	//we need a connection to varify data
	$connection = new Connection();
	
	$name = $connection->validate_string($_POST['name']);
	$start = date_create($connection->validate_string($_POST['start']));
	$end = date_create($connection->validate_string($_POST['end']));
	$color = $connection->validate_string($_POST['color']);
	$panelist = $connection->validate_string($_POST['panalist']);
	$desc = $connection->validate_string($_POST['desc']);
	$room = $connection->validate_string($_POST['room']);
	
	
	$page = new Webpage("Add Event");
	
	
	$diff = $end->format("U") - $start->format("U");
	
	if( $diff <= 0 )
	{
		echo "<center>"; 
		echo "<h2>Incorrect date(s) passed.</h2>";
		echo "Make sure the end date is not the same as, or earlier than, the start date.</h2>";
		echo "</center>"; 
		$page->addURL("add.php","Try again.");
		echo "<br />";
		$page->addURL("index.php", "Back to schedule.");
		exit(0);
	}
	
	//now we should create the query
	$query = "
INSERT INTO events(e_roomID, e_dateStart, e_dateEnd, e_eventName, e_color, e_eventDesc, e_panelist)
VALUES ($room, '" . $start->format("Y-m-d H:i:s") . "', '" . $end->format("Y-m-d H:i:s") . "', '$name', '$color', '$desc', '$panelist');";
	
	//ok now insert the data
	$connection->query($query);
	$row = $connection->get_insert_ID();
	$eventID = $row[0];
	
	
	echo "<center>Successfully created Event<br>";
	$page->addURL("view.php?event=$eventID","View Event");
	echo "<br>";
	$page->addURL("add.php","Add another Event");
	echo "<br>";
	$page->addURL("index.php","Return to main schedule");
	echo "</center>";
}
else
{
	$connection = new Connection();

	$page = new Webpage("Add event");
	$page->createEventForm($connection);
}
?>
