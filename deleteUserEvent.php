<?php
/*
 *      userSchedule.php
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
$page = new Webpage("Remove User Event");
$C = new Connection();

if(!isset($_GET['event']))
{
	$page->printError("You need to provide an event to delete.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

$eID = $C->validate_string($_GET['event']);
$uID = $user->get_UserID();

if( isset($_GET['confirm']) )
{
	// they have confirmed they wish to delete the event
	$q = "DELETE FROM userSchedule WHERE us_userID = $uID AND us_eventID = $eID;";
	$C->query($q);
	
	$page->printError("Event removed successfully.");
	echo "<center>";
	$page->addURL("userSchedule.php","Return to your custom schedule.");
	echo "<br /><br />";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
}
else
{
		$q = "SELECT e_eventName FROM events WHERE e_eventID = $eID";
		$C->query($q);
		
		$row = $C->fetch_row();
		
		$eventName = $row[0];
		
		echo "<center>";
		echo "<h3>Do you really want to remove \"$eventName\" from your schedule?</h3>";
		$page->addURL("deleteUserEvent.php?event=$eID&confirm=1","Yes");
		echo "&nbsp; &nbsp; &nbsp;";
		$page->addURL("view.php?event=$eID","No"); 
		echo "</center>";
}

?>