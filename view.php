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

$user = new User();
$page = new Webpage("Test View Event");


if(isset($_GET['event']))
{
	#open the database connection to validate the eventID
	$connection = new Connection();
	$eventID = $connection->validate_string($_GET['event']);
	$query = "
SELECT e_eventID, e_eventName, r_roomName, e_dateStart, e_dateEnd, 
e_eventDesc, e_panelist, e_color
FROM events, rooms
WHERE e_eventID = $eventID AND e_roomID = r_roomID;";
	$connection->query($query);
	
	if($connection->result_size() != 1)
	{
		echo "Invalid Event ID, please check that you are using the correct EventID. ";
		echo "If you are, report this to Kitsune on the Mewcon Forums via PM. <br /><br />";
		$page->addURL("index.php","Back to event schedule.");
		break;
	}
	
	$row = $connection->fetch_assoc();
	
	$event = new Event( 
		$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
		$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
		$row['e_panelist'], $row['e_color']
	);
	
	//ok lets pass this info to the page so it can display it
	$page->printEvent($event);
	echo "<center>";
	echo "<div id=\"addBox\">";
		
	if( $user->is_User() )
	{
		// figure out if the event is already in the user's schedule
		$eventID = $event->getEventID();
		$query = "SELECT us_eventID FROM userSchedule WHERE us_eventID = $eventID;";
		
		$connection->query($query);
		
		if( $connection->result_size() == 0 )
		{
			$page->addURL("addUserEvent.php?event=$eventID","Add this event to your schedule.");
		}
		else
		{
			echo "This event is in ";
			$page->addURL("userSchedule.php","your schedule.");
		}


	}
	else
	{
		echo "Register or Sign In on the ";
		$page->addURL("http://www.mewcon.com/forum/index.php","forums");
		echo " to add this event to your own custom schedule!";
	}	
	echo "</div>";
	echo "</center>";
	
	if( $user->is_Admin() == TRUE)
	{
		echo "<br/ ><hr /><hr /><br/ >";
		$page->printAdminEdit($event, $eventID, $connection);
	}
	else if( $user->get_Username() == $event->getPanelist())
	{
		//this is the panelist for this panel so give them access to the desc editing
		echo "<br /><hr /><hr /><br />";
		$page->printPanelistEdit($event, $eventID);
	}
	echo "<br />";
}
else
{
	echo "<center>";
	echo "<h2>No eventID provided</h2>";
	echo "</center>";
}
echo "<center>";
$page->addURL("index.php","Back to event schedule.");
echo "</center>";
?>