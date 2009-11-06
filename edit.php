<?php
/*
 *      edit.php
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
$page = new Webpage("Edit Event");
$connection = new Connection();

// if the visitor isn't even a forum user, they shouldn't be using this page.
if( ! $user->is_User() )
{
	$page->printError("Hey, I don't know you! =O.O=");
	echo "<center>";
	echo "Please register or sign in to the ";
	$page->addURL("http://www.mewcon.com/forum/index.php","forums");
	echo " if you fancy doing fun things with panels.<br />";
	$page->addURL("index.php","Return to the event schedule.");
	echo "<br />";
	echo "</center>";
	exit(0);
}

$event = $page->_GET_checkEventID($_GET['event'], $connection);
if( ! isset($event) ) exit(0);

// typecheck $_GET['update']
if(!isset($_GET['update']) || $_GET['update'] == "" || is_numeric($_GET['update']) )
{
	$page->printError("Invalid update parameter.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

$action = $_GET['update'];

// make sure the supplied update field is valid
if( $action != "panelist" && $action != "admin" )
{
	$page->printError("Unknown edit level: $action");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

$name = $connection->validate_string( $_POST['name'] );
if ( str_word_count($name) == 0 )
{
	$page->printError("Name cannot be blank.");
	echo "<center>";
	echo "Please go back and supply one.<br />";
	echo "<br />";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}


$panelist = $event->getPanelist();

// variables pertinent to either admin or panelist editing
$eventID = $event->getEventID();
$name = $connection->validate_string($_POST['name']);
$desc = $connection->validate_string($_POST['desc']);

if($user->is_Admin() && $action == "admin")
{
	$start = date_create($connection->validate_string($_POST['start']));
	$end = date_create($connection->validate_string($_POST['end']));
	$color = $connection->validate_string($_POST['color']);
	$panelist = $connection->validate_string($_POST['panalist']);
	$room = $connection->validate_string($_POST['room']);
	
	//verify the dates are on a half-hour
	if( $start->format("m") != "00" || $start->format("m") != "30" )
	{
		$page->printError("Start date must be on a half-hour mark.");
		echo "<center>";
		$page->addURL("view.php?event=$eventID","Try again.");
		echo "<br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	
	if( $end->format("m") != 00 || $end->format("m") != "30")
	{
		$page->printError("End date must be on a half-hour mark.");
		echo "<center>";
		$page->addURL("view.php?event=$eventID","Try again.");
		echo "<br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}

	$diff = $end->format("U") - $start->format("U");
	
	if( $diff <= 0 )
	{
		$page->printError("Invalid dates used.");
		echo "<center>"; 
		echo "Make sure the end date is not the same as, or earlier than, the start date.";
		echo "<br />";
		$page->addURL("view.php?event=$eventID","Try again.");
		echo "<br />";
		$page->addURL("index.php", "Back to schedule.");
		exit(0);
	}

	$query = "
		UPDATE 
			events
		SET 
			e_eventName = '$name', e_roomID = $room, 
			e_dateStart = '" . $start->format("Y-m-d H:i:s") . "', 
			e_dateEnd = '" . $end->format("Y-m-d H:i:s") . "',
			e_eventDesc = '$desc', e_panelist = '$panelist', e_color = '$color'
		WHERE 
			e_eventID = $eventID
	;";

	$connection->query($query);
}
else if( $user->get_Username() == $panelist && $action == "panelist" )
{	
	$query = "
		UPDATE events 
		SET e_eventName = '$name', e_eventDesc = '$desc' 
		WHERE e_eventID = $eventID;";
	
	$connection->query($query);
}

$page->printError("Event update successful! =^.^=")
echo "<center>";
$page->addURL("index.php","Return to main schedule");
echo "</center>";
?>
