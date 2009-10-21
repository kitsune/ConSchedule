<?php
/*
 *      view.php
 *      
 *      Copyright 2008 Dylan Enloe <ninina@Siren>
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
$page = new Webpage("View Event");

#get the entry and print out it's info
if(isset($_GET['event']))
{
	#open the database connection to validate the eventID
	$connection = new Connection();
	$eventID = $connection->validate_string($_GET['event']);
	$query = "
SELECT e_eventname, r_roomname, e_day, e_start, e_end, e_desc, e_panelist, e_color
FROM events, rooms
WHERE e_eventID = $eventID AND e_roomID = r_roomID;";
	$connection->query($query);
	if($connection->result_size() != 1)
	{
		echo "Invalid Event ID, please check that you are using the correct EventID.  If you are report this to Kitsune on the Mewcon Forums via PM";
	}
	$row = $connection->fetch_row();
	$event = new Event($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
	#ok lets pass this info to the page so it can display it
	$page->printEvent($event);
	if($user->is_Admin())
	{
		echo "<br><br>";
		$page->printAdminEdit($event, $eventID, $connection);
		echo "<br>";
		$page->addURL("delete.php?event=$eventID", "Delete this event");
	}
	else if($user->get_Username() == $event->getPanelist())
	{
		#this is the panalist for this panel so give them access to the desc editing
		echo "<br><br>";
		$page->printPanelistEdit($event, $eventID);
	}
	
}
else
{
	echo "You need to provide an event to view one";
}

?>
