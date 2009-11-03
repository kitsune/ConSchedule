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

$C = new Connection();
$user = new User();
$page = new Webpage("User Schedule Test");

if( ! $user->is_User())
{
	$page->printError("You must be a forum user to create your own schedule.");
	echo "<center>";
	$page->addURL("http://www.mewcon.com/forum/index.php","Go to the forums to Register or Sign In.");
	echo "<br /><br />";
	$page->addURL("index.php","Return to the event schedule.");
	echo "</center>";
	exit(0);
}

// get the user's current event schedule

$uID = $user->get_UserID();

$q = "
SELECT
	e_eventID, e_eventName, r_roomName, e_dateStart, 
	e_dateEnd, e_eventDesc, e_panelist, e_color
FROM
	events, rooms, userSchedule
WHERE
	us_userID = $uID
	AND
	us_eventID = e_eventID
	AND
	e_roomID = r_roomID
ORDER BY
	e_dateStart
	ASC
;";

$C->query($q);

if ( $C->result_size() < 1 )
{
	$page->printError("Silly ". $user->get_Username() .", you have no events scheduled. =^.^=");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

for( $i = 0; $i < $C->result_size(); $i++ )
{
	$row = $C->fetch_assoc();
	
	
	$userEvents[$i] = new Event( 
		$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
		$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
		$row['e_panelist'], $row['e_color'] 
	);
	
}

$page->printError("Custom schedule for ". $user->get_Username() .".");

echo "<center>";
echo '<table id="userSchedule" cellpadding=0 cellspacing=0>';
echo '<thead><td id="eventName">Event Name</td><td id="room">Room</td><td id="day">Day</td><td id="startTime">Start Time</td><td id="endTime">End Time</td></thead>';

foreach( $userEvents as $e )
{
	$name = $e->getEventName();
	$day = $e->getStartDate()->format("D, d M Y");
	$startTime = $e->getStartDate()->format("H:i");
	$endTime = $e->getEndDate()->format("H:i");
	
	echo '<tr><td>';
	
	if( strlen($name) > 35 )
	{
		echo substr($name,0,35) . "&#133;"; //&#133; is an elipsis
	}
	else 
	{
		echo $name;
	}
	
	echo '</td><td>'. $e->getRoomName() .'</td>';
	echo '<td>'. $day .'</td><td>'. $startTime .'</td><td>'. $endTime .'</td><tr>';
}

echo '</table>';
echo '</center>';


?>