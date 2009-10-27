<?php
/*
 *      edit.php
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
$page = new Webpage("Edit Event");
$connection = new Connection();

if(!isset($_GET['event']))
{
	echo "You need to provide an event to edit one";
	exit(0);
}

$eventID = $connection->validate_string($_GET['event']);

if(!isset($_GET['update']))
{
	echo "No edit level supplied";
	exit(0);
}

$action = $_GET['update'];

// make sure the supplied update field is valid
if( $action != "panelist" && $action != "admin" )
{
	echo "Unknown edit level: $action.";
	exit(0);
}

$name = $connection->validate_string( $_POST['name'] );

if ( str_word_count($name) == 0 )
{
	echo "Name cannot be blank. Please go back and supply one.<br />";
	exit(0);
}
// check to make sure the eventID exists within the db
$query = "SELECT e_eventID, e_panelist FROM events WHERE e_eventID = $eventID;";
$connection->query($query);

if( $connection->result_size() != 1 ) {
	echo "EventID does not exist.";
	exit(0);
}

$row = $connection->fetch_assoc();
$panelist = $row['e_panelist'];

if($user->is_Admin() && $action == "admin")
{
	$name = $connection->validate_string($_POST['name']);
	$start = date_create($connection->validate_string($_POST['start']));
	$end = date_create($connection->validate_string($_POST['end']));
	$color = $connection->validate_string($_POST['color']);
	$panelist = $connection->validate_string($_POST['panalist']);
	$desc = $connection->validate_string($_POST['desc']);
	$room = $connection->validate_string($_POST['room']);

	$diff = $end->format("U") - $start->format("U");
	
	if( $diff <= 0 )
	{
		echo "<center>"; 
		echo "<h2>Incorrect date(s) passed.</h2>";
		echo "Make sure the end date is not the same as, or earlier than, the start date.</h2>";
		echo "</center>"; 
		$page->addURL("view.php?event=$eventID","Try again.");
		echo "<br />";
		$page->addURL("index.php", "Back to schedule.");
		exit(0);
	}

	$query = "
UPDATE events
SET e_eventName = '$name', e_roomID = $room, e_dateStart = '" . $start->format("Y-m-d H:i:s") . "', e_dateEnd = '" . $end->format("Y-m-d H:i:s") . "', e_eventDesc = '$desc', e_panelist = '$panelist', e_color = '$color'
WHERE e_eventID = $eventID;";

	$connection->query($query);
	
}
else if( $user->get_Username() == $panelist && $action == "panelist" )
{
	$name = $connection->validate_string($_POST['name']);
	$desc = $connection->validate_string($_POST['desc']);
	
	$query = "UPDATE events 
SET e_eventName = '$name', e_eventDesc = '$desc' 
WHERE e_eventID = $eventID;";
	
	$connection->query($query);
}
echo "<center>Event successfully updated</center><br />";

$page->addURL("index.php","Return to main schedule");

?>
