<?php
/*
 *      view.php
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

$connection = new Connection();
$page = new Webpage("Test View Event");
$user = new User();

$event = $page->_GET_checkEventID($_GET['event'], $connection);
if( $event == NULL ) exit(0);

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
		echo "<br />";
		echo '<span style="font-size: small;">[';
		$page->addURL("deleteUserEvent.php?event=$eventID","Remove");
		echo "]</span>";			
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
else if( $user->get_Username() == $event->getPanelist() && $user->is_User())
{
	//this is the panelist for this panel so give them access to the desc editing
	echo "<br /><hr /><hr /><br />";
	$page->printPanelistEdit($event, $eventID);
}

echo "<center>";
$page->addURL("index.php","Back to event schedule.");
echo "</center>";
?>
