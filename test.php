<?php
/*
 *      test.php
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

$q = "
SELECT r_roomName
FROM rooms";
$C->query( $q );
$roomCount = $C->result_size();

if( $roomCount < 1 ) 
{
	echo "<center>";
	echo "<h2>Possible error with database involving rooms. =T.T=</h2>";
	echo "Please inform Kitsune of the problem via a PM in the forums.<br />";
	echo "Be sure to supply a copy of the URL to help with the debugging process.";
	echo "</center>";
	exit(0);
}

// store the room names
for( $i=0; $i < $roomCount; $i++ ) {
	$row = $C->fetch_assoc();
	$roomNames[$i] = $row['r_roomName'];
}

$q = "
SELECT e_eventID, e_eventName, r_roomName, e_dateStart, e_dateEnd, 
	e_eventName, e_eventDesc, e_color, e_panelist
FROM events, rooms
WHERE e_roomID = r_roomID
;";

$C->query( $q );
$eventCount = $C->result_size();

if( $eventCount < 1 ) 
{
	echo "<center>";
	echo "<h2>No events have yet been planned =T.T=</h2>";
	echo "</center>";
	exit(0);
}

// create the events
for( $i=0; $i<$eventCount; $i++ ) {
	$row = $C->fetch_assoc();

	$events[$i] = new Event( 
		$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
		$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
		$row['e_panelist'], $row['e_color'] 
	);
}

unset($C); // close the connection

// set up the schedule var
$t = date_create("2009-12-30 08:00:00");
for( $i = 0; $i < 37; $i++ ) 
{
	foreach( $roomNames as $roomName ) 
	{
		foreach( $events as $event ) 
		{
			
			$sDF = $event->getStartDate()->format("U");
			$tF = $t->format("U");
			$diff = $sDF - $tF;
			
			if( $event->getRoomName() == $roomName && $diff == 0 )
			{
				$tF = $t->format("Y-m-d H:i:s");
				$schedule[$tF][$roomName] = $event;
			}
			
		}
	}
	$t->modify("+30 minutes");
}

$page = new Webpage("Con Schedule Test");

$printDay =  array( FALSE, FALSE, FALSE );

if( !isset($_GET['day']) || ( $_GET['day'] > 2 || $_GET['day'] < 0 ) || $_GET['day'] == "" )
{
	$printDay = array( TRUE, TRUE, TRUE );
}
else if ( $_GET['day'] <= 2 && $_GET['day'] >= 0 ) 
{
	$printDay[ $_GET['day'] ] = TRUE;
}

echo "<center>";

if( $printDay[0] == TRUE )
{
	$conStarts = date_create("2009-12-31 08:00:00");
	$conEnds = date_create("2010-01-01 02:00:00");

	echo "<hr />";
	echo "<hr />";
	echo "<h2>";
	echo "Schedule for " . $conStarts->format("F d, Y");
	echo "</h2>";

	echo "<hr />";
	echo "<hr />";

	echo "<p>";
	$page->printDaySchedule($schedule, $roomNames, $conStarts, $conEnds);
	echo "</p>";
}

if( $printDay[1] == TRUE )
{
	$conStarts = date_create("2010-01-01 08:00:00");
	$conEnds = date_create("2010-01-02 02:00:00");
	
	echo "<hr />";
	echo "<hr />";
	echo "<h2>";
	echo "Schedule for " . $conStarts->format("F d, Y");
	echo "</h2>";
	echo "<hr />";
	echo "<hr />";

	echo "<p>";
	$page->printDaySchedule($schedule, $roomNames, $conStarts, $conEnds);
	echo "</p>";
}

if( $printDay[2] == TRUE )
{
	$conStarts = date_create("2010-01-02 08:00:00");
	$conEnds = date_create("2010-01-03 00:00:00");

	echo "<hr />";
	echo "<hr />";
	echo "<h2>";
	echo "Schedule for " . $conStarts->format("F d, Y");
	echo "</h2>";
	echo "<hr />";
	echo "<hr />";

	echo "<p>";
	$page->printDaySchedule($schedule, $roomNames, $conStarts, $conEnds);
	echo "</p>";
}

echo "</center>";
?>