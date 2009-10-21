<?php
/*
 *      edit.php
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

if($user->is_Admin() && $action = "admin")
{
	$name = $connection->validate_string($_POST['name']);
	$day = $connection->validate_string($_POST['day']);
	$start = floatval($connection->validate_string($_POST['start'])) * 2;
	$end = floatval($connection->validate_string($_POST['end'])) * 2;
	$color = $connection->validate_string($_POST['color']);
	$panelist = $connection->validate_string($_POST['panalist']);
	$desc = $connection->validate_string($_POST['desc']);
	$room = $connection->validate_string($_POST['room']);

	

	$query = "
UPDATE events
SET e_eventname = '$name', e_roomID = $room, e_day = $day, e_start = $start, e_end = $end, e_desc = '$desc', e_panelist = '$panelist', e_color = '$color'
WHERE e_eventID = $eventID;";

	$connection->query($query);
	
	echo "<center>Event successfully updated<br>";
	$page->addURL("index.php","Return to main schedule");
}
?>
