<?php
/*
 *      add.php
 *      
 *      Copyright © 2008 Dylan Enloe <ninina@Siren>
 *		Copyright © 2009 Drew Fisher <kakudevel@gmail.com>
 *		ALL RIGHTS RESERVED
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
$page = new Webpage("Add Event");

#only admins can add events
if(!$user->is_Admin())
{
	$page->printError("Only Admins can create new events.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

if(isset($_POST['add']))
{
	//ok we'll add the entry here.
	
	//get data from post and varify it
	//we need a connection to varify data
	$connection = new Connection();
	
	$name = $connection->validate_string($_POST['name']);
	$name = trim($name);
	
	if ( str_word_count($name) == 0 )
	{	
		$page->printError("Name cannot be blank.");
		echo "<center>";
		echo "Please go back and supply one.<br /><br />";
		$page->addURL("add.php","Return to adding an event.");
		echo "<br /><br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	
	$start = date_create($connection->validate_string($_POST['start']));
	$end = date_create($connection->validate_string($_POST['end']));
	$color = $connection->validate_string($_POST['color']);
	$panelist = $connection->validate_string($_POST['panalist']);
	$desc = $connection->validate_string($_POST['desc']);
	$room = $connection->validate_string($_POST['room']);
	
	//trim of excess whitepsace
	$color = trim($color);
	$panelist = trim($panelist);
	$desc = trim($panelist);
	
	if( $start->format("i") != "00" && $start->format("i") != "30" )
	{
		$page->printError("Start date must be on a half-hour mark.");
		echo "<center>";
		echo "You tried: ". $start->format("Y-m-d H:");
		echo "<span style='color: red;' >";
		echo $start->format("i");
		echo "</span><br /><br />";
		$page->addURL("add.php","Try again.");
		echo "<br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	
	if( $end->format("i") != "00" && $end->format("i") != "30")
	{
		$page->printError("End date must be on a half-hour mark.");
		echo "<center>";
		echo "You tried: ". $end->format("Y-m-d H:");
		echo "<span style='color: red;' >";
		echo $end->format("i");
		echo "</span><br /><br />";
		$page->addURL("add.php","Try again.");
		echo "<br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	
	//make sure end event isn't = or earlier than the start date
	$diff = $end->format("U") - $start->format("U");
	
	if( $diff <= 0 )
	{
		$page->printError("Incorrect dates(s) passed.");
		echo "<center>";
		echo "Make sure the end date is not the same as, or earlier than, the start date.</h2>";
		$page->addURL("add.php","Try again.");
		echo "<br />";
		$page->addURL("index.php", "Back to schedule.");
		echo "</center>";
		exit(0);
	}
	
	//now we should create the query
	$query = "
		INSERT INTO 
			events(	e_roomID, 
					e_dateStart, 
					e_dateEnd, 
					e_eventName, 
					e_color, 
					e_eventDesc, 
					e_panelist)
		VALUES 
			($room,
			'" . $start->format("Y-m-d H:i:s") . "',
			'" . $end->format("Y-m-d H:i:s") . "',
			'$name', 
			'$color', 
			'$desc', 
			'$panelist')
		;";
		
	
	//ok now insert the data
	$connection->query($query);
	$row = $connection->get_insert_ID();
	$eventID = $row[0];
	
	
	echo "<center>Successfully created Event<br>";
	$page->addURL("view.php?event=$eventID","View Event");
	echo "<br />";
	$page->addURL("add.php","Add another Event");
	echo "<br />";
	$page->addURL("index.php","Return to main schedule");
	echo "</center>";
}
else
{
	$connection = new Connection();
	
	$page->printError("Add an event");
	echo "<center>";
	echo "</center>";
	echo "<hr /><hr />";
	$page->createEventForm($connection);
}
?>
