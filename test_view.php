<?php
/*
 *      test_view.php
 *      
 *      Copyright 2009 Drew Fisher <kakudevel@gmail.com>
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

$page = new Webpage("Test View Event");

$admin = FALSE;
$username = "nobody";

if(isset($_GET['event']))
{
	#open the database connection to validate the eventID
	$connection = new Connection();
	$eventID = $connection->validate_string($_GET['event']);
	$query = "
SELECT e_eventID, e_eventname, r_roomname, e_dateStart, e_dateEnd, 
e_eventDesc, e_panelist, e_color
FROM events, rooms
WHERE e_eventID = $eventID AND e_roomID = r_roomID;";
	$connection->query($query);
	
	if($connection->result_size() != 1)
	{
		echo "Invalid Event ID, please check that you are using the correct EventID. ";
		echo "If you are, report this to Kitsune on the Mewcon Forums via PM. <br /><br />";
		$page->addURL("test.php","Back to event schedule.");
		break;
	}
	
	$row = $connection->fetch_row();
	$event = new Event($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
	
	//ok lets pass this info to the page so it can display it
	$page->printEvent($event);
	
	if( $admin == 1)
	{
		echo "<br/ ><br/ >";
		$page->printAdminEdit($event, $eventID, $connection);
		echo "<br />";
		$page->addURL("delete.php?event=$eventID", "Delete this event");
	}
	else if( $username == $event->getPanelist())
	{
		//this is the panelist for this panel so give them access to the desc editing
		echo "<br><br>";
		$page->printPanelistEdit($event, $eventID);
	}
	echo "<br />";
	$page->addURL("test.php","Back to event schedule.");
	
}
?>