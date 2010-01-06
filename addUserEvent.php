<?php
/*
 *      addUserSchedule.php
 *      
 *      Copyright © 2009, 2010 Drew Fisher <kakudevel@gmail.com>
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
$user = new User();
$page = new Webpage("Add User Event", $user);


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

$reqEvent = $page->_GET_checkEventID($_GET['event'], $C);
if( ! isset($reqEvent)) exit(0);

$uID = $user->get_UserID();
$eID = $reqEvent->getEventID();

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

$startDate = $reqEvent->getStartDate()->format("Y-m-d H:i:s");
$endDate = $reqEvent->getEndDate()->format("Y-m-d H:i:s");

$q = " SELECT *
FROM events, userSchedule
WHERE (('$startDate' > e_dateStart AND '$startDate' < e_dateEnd ) OR
('$endDate' > e_dateStart AND '$endDate' < e_dateEnd ) OR
(e_dateStart > '$startDate' AND e_dateStart < '$endDate' ) OR
(e_dateEnd > '$startDate' AND e_dateEnd < '$endDate')) 
AND us_eventID = e_eventID AND us_userID = $uID;";

$C->query($q);

$numConflicts = 0;

if($C->result_size() > 0) {	
	echo "<form method='post' action='addUserEvent.php?event=$eID'>";
	
	while($row = $C->fetch_assoc())
	{
		$ue = new Event( 
			$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
			$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
			$row['e_panelist'], $row['e_color'] 
		);
		
		$conflictType = getConflictType($reqEvent, $ue);
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
			$page->printTableRows($ue, $reqEvent, $numConflicts, 2);
		}
		// sched. event starts within req. event
		else if( $conflictType == 2)
		{
			$page->printTableRows($reqEvent, $ue, $numConflicts, 1);
		}
		
		echo "</tr>";
		echo "</table></center><br />";
		
	}
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

 
function getConflictType($reqEvent, $schedEvent) {
	$req_start = $reqEvent->getStartDate()->format("U");
	
	$se_start = $schedEvent->getStartDate()->format("U");
	$se_end = $schedEvent->getEndDate()->format("U");
	
	// requested time lies within scheduled time
	if( $req_start >= $se_start && $req_start < $se_end )
	{	
		return 1;
	} 
	return 2;
}
?>

