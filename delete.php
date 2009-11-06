<?php
/*
 *      delete.php
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

$user = new User();
$page = new Webpage("Delete Event");
$connection = new Connection();

$eventID = $page->_GET_checkEventID( $_GET['event'], $connection, FALSE );

if( ! isset($eventID) ) exit(0);

if($user->is_Admin())
{
	if(isset($_GET['confirm']))
	{
		// they want to delete it so lets delete it
		$query = "DELETE FROM events WHERE e_eventID = $eventID;";
		$connection->query($query);
		
		$page->printError("Event successfully deleted.");
		echo "<center>";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	else
	{
		$query = "SELECT e_eventName FROM events WHERE e_eventID = $eventID";
		$connection->query($query);
		
		$row = $connection->fetch_row();
		
		$eventName = $row[0];
		
		echo "<center>Do you really want to delete the event \"<strong>$eventName</strong>?\"<br />";
		$page->addURL("delete.php?event=$eventID&confirm='Yes'","Yes");
		echo "&nbsp;&nbsp;&nbsp;";
		$page->addURL("view.php?event=$eventID","No"); 
	}
}
else
{
	$page->printError("You cannot delete events.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
}
?>
