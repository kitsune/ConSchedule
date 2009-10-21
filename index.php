<?php
/*
 *      index.php
 *      
 *      Copyright 2008 Dylan Enloe <ninina@koneko-hime>
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

/*First find out if we are dealing with any parameters*/
if(isset($_GET['day']))
	$params = "WHERE e_day = " +$_GET['day'];
	
if(isset($_GET['start']))
	if(isset($params))
		$params += "AND e_start >= " +$_GET['start'];
	else
		$params = "WHERE e_start >= " +$_GET['start'];

if(isset($_GET['end']))
	if(isset($params))
		$params += "AND e_end >= " +$_GET['end'];
	else
		$params = "WHERE e_end >= " +$_GET['end'];

if(isset($_GET['room']))
	if(isset($params))
		$params += "AND e_roomID >= " +$_GET['room'];
	else
		$params = "WHERE e_roomID >= " +$_GET['room'];

$connection = new Connection();

$query = "
SELECT e_eventID, e_eventname, e_roomID, e_start, e_end, e_color, e_day
FROM events;";

if(isset($params))
	$query += $params;
	
$connection->query($query);

if($connection->result_size() == 0)
{
	$page = new Webpage("Con Schedule");
	$page->printNoEvents();
	exit(0);
}

while($row = $connection->fetch_row())
{
	$size = $row[4] - $row[3];
	$schedule[$row[6]][$row[3]][$row[2]] = new Entry($row[0], $row[1], $row[5], $size);
}

//get rooms and their names
$query = "
SELECT r_roomID, r_roomname
FROM rooms
ORDER BY r_roomID";

$connection->query($query);

while($row = $connection->fetch_row())
{
	$rooms[$row[0]] = $row[1];
}


/*Ok we have the array of requested entries so now we make a webpage and print to it*/
$page = new Webpage("Con Schedule");

echo "<center>Schedule for December 31st";
$page->printDaySchedule($schedule, $rooms, 1, 16);
echo "Schedule for January 1st";
$page->printDaySchedule($schedule, $rooms, 2);
echo "</center>";
?>
