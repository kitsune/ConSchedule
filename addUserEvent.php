<?php
/*
 *      addUserSchedule.php
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
$page = new Webpage("Add User Event");
$user = new User();

if( ! $user->is_User() )
{
	
	$page->printError("You must be a forum user to create your own schedule.");
	echo "<center>";
	$page->addURL("http://www.mewcon.com/forum/index.php","Go to the forums to Register or Sign In.");
	echo "<br /><br />";
	$page->addURL("index.php","Return to the event schedule.");
	echo "</center>";
	exit(0);
}

if( ! isset($_GET['event']) )
{
	$page->printError("EventID must be supplied.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

// get the actual event from the db
$eID = $C->validate_string($_GET['event']);
$eID = intval($eID);

$q = "
SELECT
	e_eventID, r_roomName, e_dateStart, e_dateEnd, 
	e_eventName, e_color, e_eventDesc, e_panelist
FROM
	events, rooms
WHERE 
	e_eventID = ". $eID ."
	AND
	r_roomID = e_roomID
;";

$C->query($q);

if( $C->result_size() != 1 )
{
	$page->printError("Problem with passed eventID.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

$row = $C->fetch_assoc();

$reqEvent = new Event(
	$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
	$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
	$row['e_panelist'], $row['e_color']
);

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
	
	// stop executing if the user's already got the requested event in their schedule.
	if( $row['e_eventID'] == $reqEvent->getEventID() )
	{
		$page->printError("That event is already in your schedule.");
		echo "<center>";
		$page->addURL("view.php?event=$eID", "View event details.");
		echo "<br /><br />";
		$page->addURL("index.php", "Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	
	$userEvents[$i] = new Event( 
		$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
		$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
		$row['e_panelist'], $row['e_color'] 
	);
	
}

$req_start = $reqEvent->getStartDate()->format("U");
$req_end = $reqEvent->getEndDate()->format("U");

$foundConflict = FALSE;

foreach($userEvents as $ue)
{
	$ue_start = $ue->getStartDate()->format("U");
	$ue_end = $ue->getEndDate()->format("U");
	// requested event's start time is within another event
	if( $req_start >= $ue_start && $req_start < $ue_end)
	{
		if( ! $foundConflict )
		{
			$page->printError("Time Conflict(s) found!");
			$foundConflict = TRUE;
		}
		else
		{
			echo "<hr /><hr /><br />";
		}
		
		echo "<center>";
		echo '<table id="conflictTable" cellpadding=0 cellspacing=0><thead>';
		echo "<td style='width: 20%;'>Event Name</td><td>Room</td><td>Day</td>";
		echo "<td>Start Time</td><td>End Time</td><td>Status</td><td>Keep?</td>";
		echo "</thead><tr>";
		echo "<td>". $ue->getEventName() ."</td>";
		echo "<td>". $ue->getRoomName() ."</td>";
		echo "<td>". $ue->getStartDate()->format("D, d M Y") ."</td>";
		echo "<td style='background: #F99;'>". $ue->getStartDate()->format("H:i") ."</td>";
		echo "<td style='background: #F99;'>". $ue->getEndDate()->format("H:i") ."</td>";
		echo "<td>Scheduled Event</td>";
		echo "<td>";
		echo "<input type='checkbox' name='keep' value='". $ue->getEventID() ."' />";
		echo "</td>";
		echo "</tr><tr style='background: #CCF;'>";
		echo "<td>". $reqEvent->getEventName() ."</td>";
		echo "<td>". $reqEvent->getRoomName() ."</td>";
		echo "<td>". $reqEvent->getStartDate()->format("D, d M Y") ."</td>";
		echo "<td style='background: #F00;'>". $reqEvent->getStartDate()->format("H:i") ."</td>";
		echo "<td>". $reqEvent->getEndDate()->format("H:i") ."</td>";
		echo "<td> Requested Event</td>";
		echo "<td>";
		echo "<input type='checkbox' name='keep' value='". $reqEvent->getEventID() ."' />";
		echo "</td>";
		echo "</tr>";
		echo "</table></center><br />";
	}
	// other event's start time is within requested event
	else if( $ue_start >= $req_start && $ue_start < $req_end )
	{
		if( ! $foundConflict )
		{
			$page->printError("Time Conflict(s) found!");
			$foundConflict = TRUE;
		}
		else
		{
			echo "<hr /><hr /><br />";
		}
		echo "<center>";
		echo '<table id="conflictTable" cellpadding=0 cellspacing=0><thead>';
		echo "<td style='width: 20%;'>Event Name</td><td>Room</td><td>Day</td>";
		echo "<td>Start Time</td><td>End Time</td><td>Status<td>Keep?</td>";
		echo "</thead><tr style='background: #CCF;'>";
		echo "<td>". $reqEvent->getEventName() ."</td>";
		echo "<td>". $reqEvent->getRoomName() ."</td>";
		echo "<td>". $reqEvent->getStartDate()->format("D, d M Y") ."</td>";
		echo "<td style='background: #F99;'>". $reqEvent->getStartDate()->format("H:i") ."</td>";
		echo "<td style='background: #F99;'>". $reqEvent->getEndDate()->format("H:i") ."</td>";
		echo "<td>Requested Event</td>";
		echo "<td>";
		echo "<input type='checkbox' name='keep' value='". $reqEvent->getEventID() ."' />";
		echo "</td>";
		echo "</tr><tr>";
		echo "<td>". $ue->getEventName() ."</td>";
		echo "<td>". $ue->getRoomName() ."</td>";
		echo "<td>". $ue->getStartDate()->format("D, d M Y") ."</td>";
		echo "<td style='background: #F00;'>". $ue->getStartDate()->format("H:i") ."</td>";
		echo "<td>". $ue->getEndDate()->format("H:i") ."</td>";
		echo "<td>Scheduled Event</td>";
		echo "<td>";
		echo "<input type='checkbox' name='keep' value='". $ue->getEventID() ."' />";
		echo "</td>";
		echo "</table></center><br />";
	}
}

if( $foundConflict )
{
	echo '<div id="addUserEvent_submit">';
	echo '<input type="button" name="cancel" value="Cancel" />';
	echo '&nbsp; ';
	echo '<input type="submit" value="Submit" />';
	echo "</div>";
	exit(0);
}

// no conflict found, go ahead and add
// the event to the database

$q = "
INSERT INTO
	userSchedule(us_userID, us_eventID)
VALUES
	( $uID, ". $reqEvent->getEventID() ." )
;";

$C->query($q);

$page->printError("Successfully added: " . $reqEvent->getEventName());
echo "<center>";
$page->addURL("userSchedule.php","View your custom schedule.");
echo "<br /><br />";
$page->addURL("index.php","Return to event schedule.");
echo "</center>";
?>