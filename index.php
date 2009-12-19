<?php
/*
 *      index.php
 *
 *      Copyright © 2008 Dylan Enloe <ninina@koneko-hime>
 *      Copyright © 2009 Drew Fisher <kakudevel@gmail.com>
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

$C = new Connection();
$page = new Webpage("Con Schedule Test");
$user = new User();

$defaultStartTime = "08:00:00";
$defaultEndTime = "03:00:00";

/* NOTE: there is no code in Webpage::printDaySchedule
 * that visually separates days, so if you put a "day's"
 * start and end time as something greater than 24 hours,
 * all you'll get is a big list of half-hour increments from
 * the day's start to its end, whatever time that may be.
 */
$conTimes[0]['start'] = "2009-12-31 09:00:00";
$conTimes[0]['end'] = "2010-01-01 03:00:00";
$conTimes[1]['start'] = "2010-01-01 09:00:00";
$conTimes[1]['end'] = "2010-01-02 02:00:00";
$conTimes[2]['start'] = "2010-01-02 09:00:00";
$conTimes[2]['end'] = "2010-01-03 00:00:00";

$conDayCount = count($conTimes);

// used if _GET['startTime'] or _GET['endTime'] are specified,
// filled with conTimes span otherwise.

$startDate = NULL;
$endDate = NULL;
$date = NULL;

// check for index.php?date=YYYYMMDD[&startTime=HHMM][&endTime=HHMM]
// or index.php?conday=#[&startTime=HHMM][&endTime=HHMM]

$GETvar = $_GET['conday'];
if( isset($GETvar) && strlen($GETvar) <= 2) //I doubt we'll ever have more than 99 days of con...
{
	$conday = $C->validate_string($GETvar);
	if( isset($conTimes[$conday]) )
	{
		$ex = explode(" ", $conTimes[$conday]['start'] );
		$date = $ex[0];
	}
	else
	{
		$conday = NULL;
	}	
}

$GETvar = $_GET['date'];
if( isset($GETvar) && strlen($GETvar) == 8 && ! isset($conday) )
{
	$date = $C->validate_string($GETvar);
}	

//if either ?conday or ?date were passed, check for ?startTime and ?endTime
if( isset($conday) || isset($_GET['date']) )
{	
	$GETvar = $_GET['startTime'];
	if( isset($GETvar) && strlen($GETvar) == 4 )
	{
		$startTime = $C->validate_string($GETvar);
		$defaultStartTime = $startTime . "00";
	}
	
	$GETvar = $_GET['endTime'];
	if( isset($GETvar) && strlen($GETvar) == 4 )
	{
		$endTime = $C->validate_string($GETvar);
		$defaultEndTime = $endTime . "00";
	}
	
	$startDate = date_create($date ." ". $defaultStartTime);
	$endDate = date_create($date ." ". $defaultEndTime);
	
	// if the end time is before or equal to the start time, assume 
	// they meant the end time was on the following day.
	
	$diff = $endDate->format("U") - $startDate->format("U");

	if( $diff <= 0 )
	{
		$endDate->modify("+1 day");
	}
}

/* special check when ?conday is passed, possibly with a ?startTime, in case
 * the $conTimes[$conday]['end'] is before the $defaultEndTime.
 * we don't want to print out more times than the official con runs.
 *
 * (NOTE that if a user specifies an ?endTime that goes beyond the conday time,
 * we assume the user knows what they're doing and wants to see till, say, 4am
 * even if the con only goes till 2am.) 
 */
if( isset($conday) && ! isset($_GET['endTime']) )
{
	$conEndTime = date_create( $conTimes[$conday]['end'] );
	
	$diff = $conEndTime->format("U") - $endDate->format("U");
	
	if( $diff < 0)
	{
		$endDate = date_create( $conTimes[$conday]['end'] );
	}
}

//fill in the start and end times with conTimes if not specified via url params
if( ! isset($startDate) && ! isset($endDate) )
{
	$startDate = date_create( $conTimes[0]['start'] );
	$endDate = date_create( $conTimes[$conDayCount-1]['end'] );	
}

$schedule = NULL;

// events query
$q = "
SELECT 
	e_eventID, e_eventName, r_roomName, e_dateStart, 
	e_dateEnd, e_eventName, e_eventDesc, e_color, e_panelist
FROM 
	events, rooms
WHERE 
	e_roomID = r_roomID
	AND
	e_dateStart 
		BETWEEN 
		'". $startDate->format("Y-m-d H:i:s") ."'
		AND
		'". $endDate->format("Y-m-d H:i:s") ."'
ORDER BY
	r_roomID ASC
;";

$C->query( $q );
$eventCount = $C->result_size();

if( $eventCount < 1 ) 
{
	if( isset($_GET['date']) )
	{
		$page->printError("No events scheduled for ". $startDate->format("F d, Y"));
		echo "<center>";
		$page->addURL("index.php","Return to non-filtered event schedule.");
		echo "</center>";
		exit(0);
	}
	else {
		$page->printError("No events scheduled.");
	}
	
	echo "<center>";
	
	if( ! $user->is_Admin() )
	{
		echo "Please check back later. We'll be posting events soon =^.^=";
	}
	else
	{
		echo "Oi, you: ";
		$page->addURL("add.php","add an event");
		echo "!";	
	}
	echo "</center>";
	exit(0);
}

// create the events and roomNames arrays
for( $i=0; $i<$eventCount; $i++ ) {
	$row = $C->fetch_assoc();

	$events[$i] = new Event( 
		$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
		$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
		$row['e_panelist'], $row['e_color'] 
	);
	
	if( ! in_array($row['r_roomName'], $roomNames))
	{
		$roomNames[$i] = $row['r_roomName'];
	}
}

unset($C); // close the connection

// set up the schedule var

foreach( $roomNames as $roomName )
{
	foreach( $events as $event )
	{
		if( $event->getRoomName() == $roomName )
		{
			$fmt = $event->getStartDate()->format("Y-m-d H:i:s");
			$schedule[$fmt][$roomName] = $event;
		}
	}
}

// print the schedule(s)
echo "<center>";
if( isset($conday) || isset($_GET['date']) )
{
	echo "<hr><hr>";
	$page->printError("Schedule for " . $startDate->format("F d, Y"));
	echo "<hr><hr><br>";
	$page->printDaySchedule($schedule, $roomNames, $startDate, $endDate);
}
else
{
	for( $i = 0; $i < $conDayCount; $i++ )
	{
		$dayStarts = date_create( $conTimes[$i]['start'] );
		$dayEnds = date_create( $conTimes[$i]['end'] );
	
		echo "<hr><hr>";
		echo "<h2>";
		echo "Schedule for " . $dayStarts->format("F d, Y");
		echo "</h2>";
		echo "<hr><hr><br>";
		$page->printDaySchedule($schedule, $roomNames, $dayStarts, $dayEnds); 
	}
}
echo "</center>"; 
?>