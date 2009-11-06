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

// get the user's current event schedule for the year.
// Limits pulling to be from YEAR-12-01 00:00:00 to YEAR+1-02-01 00:00:00
// (00:00 of Feb 1 is just after 23:59 of Jan 31)

$now = date_create();
$pullStart = date_create();
$pullEnd = date_create();

//set pull times to midnight
$pullStart->setTime( 0, 0, 0 );
$pullEnd->setTime( 0, 0, 0 );

// set initial pull dates. One of these will be off by a year depending
// on when we're trying to pull.
$pullStart->setDate( $now->format("Y"), 12, 01 );
$pullEND->setDate( $now->format("Y"), 02, 01 ); 

//if we're within the month of January, pull "last year's" user schedule.
if( $now->format("M") == "Jan")
{
	$pullStart->modify("-1 year");
}
else
{
	$pullEnd->modify("+1 year");
}

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
	AND
	e_dateStart
		BETWEEN
		". $pullStart->format("Y-m-d H:i:s") ."
		AND
		". $pullEnd->format("Y-m-d H:i:s") ."
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

// print out the table
$page->printError("Custom schedule for ". $user->get_Username() .".");

echo "<center>";
echo '<table class="userSchedule" cellpadding=0 cellspacing=0>';
echo '<thead><td id="eventName">';
echo 'Event Name';
echo '</td><td id="room">';
echo 'Room';
echo '</td><td id="day">';
echo 'Day';
echo '</td><td id="startTime">';
echo 'Start Time';
echo '</td><td id="endTime">';
echo 'End Time';
echo '</td></thead>';

$prevE = NULL; // holder for previous event as we run through the loop
$maxLen = 44; // max length for event name

foreach( $userEvents as $e )
{	
	$id = $e->getEventID();
	$name = $e->getEventName();
	$day = $e->getStartDate()->format("D, d M 'y");
	$startTime = $e->getStartDate()->format("H:i");
	$endTime = $e->getEndDate()->format("H:i");
	
	$tdClass = "";
	
	if( isset($prevE) )
	{
		// check for conflict
		if( isTimeConflict($prevE, $e) )
		{
			if( isset($conflicts)){
				if( ! in_array($prevE, $conflicts) )
				{
					$conflicts[] = $prevE;
				}
				
				if( ! in_array($e, $conflicts) )
				{
					$conflicts[] = $e;
				}
			}
			else {
				$conflicts[0] = $prevE;
				$conflicts[1] = $e;
			}
		}
	
		// check for day change
		$prevSDF = $prevE->getStartDate()->format("Y-m-d");
		$currSDF = $e->getStartDate()->format("Y-m-d");
		
		if( $prevSDF != $currSDF )
		{
			$tdClass = 'class="dayBreak"';	
		}
	}
	
	echo '<tr><td '. $tdClass .'>';
	
	if( strlen($name) > $maxLen )
	{
		$tName = subStr( $name, 0, $maxLen );
		$page->addURL("view.php?event=". $id, $tName . "&#133;");
	}
	else 
	{
		$page->addURL("view.php?event=". $id, $name);
	}
	
	echo '</td><td '. $tdClass .'>';
	echo $e->getRoomName();
	echo '</td><td '. $tdClass .'>';
	echo $day;
	echo '</td><td '. $tdClass .'>';
	echo $startTime;
	echo '</td><td '. $tdClass .'>'; 
	echo $endTime;
	echo '</td></tr>';
	
	$prevE = $e;
}

echo '</table><br />';

// exit if no conflicts were found
if( count($conflicts) == 0 )
{
	$page->addURL("index.php","Return to event schedule.");
	exit(0);
}

echo "<hr /><hr />";

$page->printError("Conflicts");

echo "<center>";
echo '<table class="userSchedule" id="conflicts" cellpadding=0 cellspacing=0>';
echo '<thead><td id="eventName">';
echo 'Event Name';
echo '</td><td id="room">';
echo 'Room';
echo '</td><td id="day">';
echo 'Day';
echo '</td><td id="startTime">';
echo 'Start Time';
echo '</td><td id="endTime">';
echo 'End Time';
echo '</td></thead>';

foreach( $conflicts as $e )
{	
	$id = $e->getEventID();
	$name = $e->getEventName();
	$day = $e->getStartDate()->format("D, d M 'y");
	$startTime = $e->getStartDate()->format("H:i");
	$endTime = $e->getEndDate()->format("H:i");
	
	$tdClass = "";
	
	if( isset($prevE) )
	{
		// check for day change
		$prevSDF = $prevE->getStartDate()->format("Y-m-d");
		$currSDF = $e->getStartDate()->format("Y-m-d");
		
		if( $prevSDF != $currSDF )
		{
			$tdClass = 'class="dayBreak"';	
		}
	}
	
	echo '<tr><td '. $tdClass .'>';
	
	if( strlen($name) > $maxLen )
	{
		$tName = subStr( $name, 0, $maxLen );
		$page->addURL("view.php?event=". $id, $tName . "&#133;");
	}
	else 
	{
		$page->addURL("view.php?event=". $id, $name);
	}
	
	echo '</td><td '. $tdClass .'>';
	echo $e->getRoomName();
	echo '</td><td '. $tdClass .'>';
	echo $day;
	echo '</td><td '. $tdClass .'>';
	echo $startTime;
	echo '</td><td '. $tdClass .'>'; 
	echo $endTime;
	echo '</td></tr>';
	
	$prevE = $e;
}

echo '</table><br />';


$page->addURL("index.php","Return to event schedule.");
echo '</center>';


/* =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 * FUNCTION DEFINITIONS
 * =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */


function isTimeConflict($reqEvent, $schedEvent) {
	$req_start = $reqEvent->getStartDate()->format("U");
	$req_end = $reqEvent->getEndDate()->format("U");
	
	$se_start = $schedEvent->getStartDate()->format("U");
	$se_end = $schedEvent->getEndDate()->format("U");
	
	// requested time lies within scheduled time
	if( $req_start >= $se_start && $req_start < $se_end )
	{	
		return TRUE;
	} 
	// scheduled time lies within requested time
 	else if( $se_start >= $req_start && $se_start < $req_end )
 	{
 		return TRUE;
 	}
 	else
 	{
 		return FALSE;
 	}
}
?>