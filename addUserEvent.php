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

/*
// make sure the provided event is valid, make an object of it while we're at it
if( ! isset($_GET['event']) )
{
	$page->printError("EventID must be supplied.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

$eID = $C->validate_string($_GET['event']);

// get the actual event from the db
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
*/

$reqEvent = $page->_GET_checkEventID($_GET['event'], $C);
if( ! isset($reqEvent) ) exit(0);

// make sure the visitor is a forum user
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

$uID = $user->get_UserID();

// check if we're processing from the conflict resolution form
if(isset($_POST['submit']) )
{
	$keepReq = $C->validate_string( $_POST['keepReq'] );
	$cc = $C->validate_string( $_POST['conflictCount'] );
	
	$page->printError("Conflicts resolved!");
	echo "<center>";
	
	// go ahead and insert the req. event if the user wanted to
	if( $keepReq == "TRUE" ) // not sure why this has to be in quotes here...
	{
		$q = "INSERT INTO userSchedule(us_userID, us_eventID) VALUES ($uID, $eID);";
		$C->query($q);
		echo "Requested event added successfully.<br /><br />";
	}
	
	for($i = 0; $i < $cc; $i++)
	{
		$id_bool = $_POST['keepSched_' . $i];
		$ex = explode("_", $id_bool);
		if( $ex[1] == "FALSE") 
		{
			$deleteIDs[$i] = $ex[0];
		}
	}
	
	if( isset($deleteIDs) )
	{
		foreach( $deleteIDs as $id)
		{
			$q = "DELETE FROM userSchedule WHERE us_userID = $uID AND us_eventID = $id;";
			$C->query($q);
			echo "Removed scheduled eventID# $id<br /><br />";
		}
	}
	
	$page->addURL("userSchedule.php","Return to your custom schedule.");
	echo "<br /><br />";
	$page->addURL("index.php","Return to the event schedule.");
	echo "</center>";
	exit(0);
}

// get the user's current event schedule
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

// check if user has any events scheduled
if ( $C->result_size() < 1 )
{
	$page->printError("Silly ". $user->get_Username() .", you have no events scheduled. =^.^=");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

// populate an events array with the user's schedule
for( $i = 0; $i < $C->result_size(); $i++ )
{
	$row = $C->fetch_assoc();
	
	// stop executing if the user's already got the requested event in their schedule.
	if( $row['e_eventID'] == $reqEvent->getEventID() )
	{
		$page->printError("That event is already in your schedule.");
		echo "<center>";
		$page->addURL("view.php?event=$eID", "Return to event details.");
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

$numConflicts = 0;

echo "<form method='post' action='addUserEvent.php?event=$eID'>";


foreach( $userEvents as $ue)
{
	$conflictType = isTimeConflict($reqEvent, $ue); // see function def for what 0/1/2 means
	
	if( $conflictType > 0 )
	{
		$numConflicts++;
		
		if($numConflicts == 1)
		{
			$page->printError("Time Conflict(s) found!");	
		}
		else
		{
			echo "<hr /><hr /><br />";
		}
		
		echo "<center>";
		echo "<table class='conflicted' cellpadding=0 cellspacing=0>";
		echo "<thead>";
		echo "<td class='name'>Event Name</td>";
		echo "<td>Room</td>";
		echo "<td>Day</td>";
		echo "<td>Start Time</td>";
		echo "<td>End Time</td>";
		echo "<td>Status</td>";
		echo "<td>Keep</td>";
		echo "<td>Delete</td>";
		echo "</thead>";
		
		// req. event starts within the sched. event
		if( $conflictType == 1)
		{
			printTableRows($ue, $reqEvent, $numConflicts, 2);
		}
		// sched. event starts within req. event
		else if( $conflictType == 2)
		{
			printTableRows($reqEvent, $ue, $numConflicts, 1);
		}
		
		echo "</tr>";
		echo "</table></center><br />";
	}
}

if( $numConflicts > 0 )
{
	echo '<div id="addUserEvent_submit">';
	echo "<input type='hidden' name='conflictCount' value='$numConflicts' />";
	echo '&nbsp; ';
	echo '<input type="submit" name="submit" value="submit" />';
	echo "</div>";
	echo "</form>";
	exit(0);
}

// no conflict found, go ahead and add
// the event to the database

$q = "
INSERT INTO
	userSchedule(us_userID, us_eventID)
VALUES
	( $uID, ". $eID ." )
;";

$C->query($q);

$page->printError("Successfully added: " . $reqEvent->getEventName());
echo "<center>";
$page->addURL("userSchedule.php","View your custom schedule.");
echo "<br /><br />";
$page->addURL("index.php","Return to event schedule.");
echo "</center>";



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
		return 1;
	} 
	// scheduled time lies within requested time
 	else if( $se_start >= $req_start && $se_start < $req_end )
 	{
 		return 2;
 	}
 	else
 	{
 		return 0;
 	}
}

function printTableRows($row1, $row2, $cCount, $hilightRow) {

	$maxLen = 44; // max length for event name
	$dayFormat = "d M 'y";
	
	// ROW 1
	
	if( $hilightRow == 1)
	{
		echo "<tr style='background:#CCF;'>";
	}
	else
	{
		echo "<tr>";
	}
	
	echo "<td>";
	$name = $row1->getEventName();
	
	if( strlen($name) > $maxLen )
	{
		$sub = subStr( $name, 0, $maxLen );
		echo $sub . "&#133;";
	}
	else
	{
		echo $name;
	}
	echo "</td>";
	echo "<td>". $row1->getRoomName() ."</td>";
	echo "<td>". $row1->getStartDate()->format($dayFormat) ."</td>";
	echo "<td style='background: #F99;'>";
	echo $row1->getStartDate()->format("H:i");
	echo "</td>";
	echo "<td style='background: #F99;'>";
	echo $row1->getEndDate()->format("H:i");
	echo "</td>";
	if( $hilightRow == 1 )
	{
		echo "<td>Req. Event</td>";
		if( $cCount == 1 )
		{
			echo "<td>";
			echo "<input type='radio' name='keepReq' value='TRUE' >";
			echo "</td>";
			echo "<td>";
			echo "<input type='radio' name='keepReq' value='FALSE' checked=1>";
			echo "</td>";
		} 
		else
		{
			echo "<td colspan=2></td>";
		}
	}
	else
	{
		echo "<td>Sched. Event</td>";
		echo "<td>";
		echo "<input type='radio' name='keepSched_". ($cCount-1) ."' ";
		echo "value='". $row1->getEventID() ."_TRUE' checked=1>";
		echo "</td>";
		echo "<td>";
		echo "<input type='radio' name='keepSched_". ($cCount-1) ."' ";
		echo "value='". $row1->getEventID() ."_FALSE' >";
		echo "</td>";
	}
	echo "</tr>";
	
	// ROW 2
	
	if( $hilightRow == 2 )
	{
		echo "<tr style='background: #CCF;'>";
	}
	else 
	{
		echo "<tr>";
	}
	echo "<td>";
	$name = $row2->getEventName();
	
	if( strlen($name) > $maxLen )
	{
		$sub = subStr( $name, 0, $maxLen );
		echo $sub . "&#133;";
	}
	else
	{
		echo $name;
	}
	echo "</td>";
	echo "<td>". $row2->getRoomName() ."</td>";
	echo "<td>". $row2->getStartDate()->format($dayFormat) ."</td>";
	echo "<td style='background: #F00;'>";
	echo $row2->getStartDate()->format("H:i");
	echo "</td>";
	echo "<td>". $row2->getEndDate()->format("H:i") ."</td>";
	if( $hilightRow == 2 )
	{
		echo "<td>Req. Event</td>";
		if( $cCount == 1 )
		{
			echo "<td>";
			echo "<input type='radio' name='keepReq' value='TRUE' >";
			echo "</td>";
			echo "<td>";
			echo "<input type='radio' name='keepReq' value='FALSE' checked=1>";
			echo "</td>";
		} 
		else
		{
			echo "<td colspan=2></td>";
		}
		
	}
	else
	{
		echo "<td>Sched. Event</td>";
		echo "<td>";
		echo "<input type='radio' name='keepSched_". ($cCount-1) ."' ";
		echo "value='". $row2->getEventID() ."_TRUE' checked=1>";
		echo "</td>";
		echo "<td>";
		echo "<input type='radio' name='keepSched_". ($cCount-1) ."' ";
		echo "value='". $row2->getEventID() ."_FALSE' >";
		echo "</td>";
	}
	echo "</tr>";
}
?>


