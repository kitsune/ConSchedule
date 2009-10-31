<?php
/*
 *      us_test.php
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

$C = new Connection();
$U = new User();
$page = new Webpage("User Schedule Test");

$username = $U->get_Username();

$q = "SELECT us_selectedEvents FROM userSchedule WHERE us_forumName = '$username';";
$C->query($q);

if( $C->result_size() != 1 )
{
	echo "<center>";
	echo "<h2>Silly $username, you have no events scheduled. =^.^=</h2>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);	
}

$row = $C->fetch_row();

$q = "
SELECT 
	e_eventID, r_roomName, e_dateStart, e_dateEnd, 
	e_eventName, e_color, e_eventDesc, e_panelist
FROM 
	events, rooms
WHERE
	e_eventID IN ($row[0]) AND e_roomID = r_roomID
;";
	
$C->query($q);

$eventCount = $C->result_size();

for($i = 0; $i < $eventCount; $i++)
{
	$row = $C->fetch_assoc();
	
	$events[$i] = new Event( 
		$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
		$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
		$row['e_panelist'], $row['e_color'] 
	);
}

echo '<table class="userSchedule" cellpadding=0 cellspacing=0>';
echo '<thead><td>Event Name</td><td>Room</td><td>Day</td><td>Start Time</td><td>End Time</td></thead>';

foreach( $events as $e )
{
	$day = $e->getStartDate()->format("D, d M Y");
	$startTime = $e->getStartDate()->format("H:i");
	$endTime = $e->getEndDate()->format("H:i");
	
	echo '<tr><td>'. $e->getEventName() .'</td><td>'. $e->getRoomName() .'</td>';
	echo '<td>'. $day .'</td><td>'. $startTime .'</td><td>'. $endTime .'</td><tr>';
}

echo '</table>';


?>