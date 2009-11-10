<?php
/*
 *      edit.php
 *      
 *      Copyright © 2008 Dylan Enloe <ninina@Siren>
 *		Copyright © 2009 Drew Fisher <kakudevel@gmail.com>
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

#first login info
$user = new User();
$page = new Webpage("Edit Event");
$connection = new Connection();

// if the visitor isn't even a forum user, they shouldn't be using this page.
if( ! $user->is_User() )
{
	$page->printError("Hey, I don't know you! =O.O=");
	echo "<center>";
	echo "Please register or sign in to the ";
	$page->addURL("http://www.mewcon.com/forum/index.php","forums");
	echo " if you fancy doing fun things with panels.<br />";
	$page->addURL("index.php","Return to the event schedule.");
	echo "<br />";
	echo "</center>";
	exit(0);
}

$event = $page->_GET_checkEventID($_GET['event'], $connection);
if( ! isset($event) ) exit(0);

// typecheck $_GET['update']
$GETvar = $connection->validate_string( $_GET['update'] );
if(!isset($GETvar) || $GETvar == "" || is_numeric($GETvar) )
{
	$page->printError("Invalid update parameter.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

$action = $GETvar;

// make sure the supplied update field is valid
if( $action != "panelist" && $action != "admin" )
{
	$page->printError("Unknown edit level: $action");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

$name = $connection->validate_string( $_POST['name'] );
$name = trim($name);

if ( str_word_count($name) == 0 )
{
	$page->printError("Name cannot be blank.");
	echo "<center>";
	echo "Please go back and supply one.<br />";
	echo "<br />";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

$panelist = $event->getPanelist();

// variables pertinent to either admin or panelist editing
$eventID = $event->getEventID();
$name = $connection->validate_string($_POST['name']);
$desc = $connection->validate_string($_POST['desc']);

// trim of excess whitespace
$name = trim($name);
$desc = trim($desc);

if($user->is_Admin() && $action == "admin")
{
	//validate the _POST vars
	$name = $connection->validate_string($_POST['name']);
	$roomID = $connection->validate_string($_POST['room']);
	
	$sYear = $connection->validate_string($_POST['startYear']);
	$sMonth = $connection->validate_string($_POST['startMonth']);
	$sDay = $connection->validate_string($_POST['startDay']);
	$sHour = $connection->validate_string($_POST['startHour']);
	$sMinute = $connection->validate_string($_POST['startMinute']);
	
	$eYear = $connection->validate_string($_POST['endYear']);
	$eMonth = $connection->validate_string($_POST['endMonth']);
	$eDay = $connection->validate_string($_POST['endDay']);
	$eHour = $connection->validate_string($_POST['endHour']);
	$eMinute = $connection->validate_string($_POST['endMinute']);

	$start = date_create($sYear . $sMonth . $sDay . $sHour . $sMinute . "00");
	$end = date_create($eYear . $eMonth . $eDay . $eHour . $eMinute . "00");
	$color = $connection->validate_string($_POST['color']);
	$panelist = $connection->validate_string($_POST['panelist']);
	
	$panelist = trim($panelist);
	
	/*
	//verify the dates are on a half-hour
	if( $start->format("i") != "00" && $start->format("i") != "30" )
	{
		$page->printError("Start date must be on a half-hour mark.");
		echo "<center>";
		echo "You tried: ". $start->format("Y-m-d H:");
		echo "<span style='color: red;' >";
		echo $start->format("i");
		echo "</span><br /><br />";
		$page->addURL("view.php?event=$eventID","Try again.");
		echo "<br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	
	if( $end->format("i") != 00 && $end->format("i") != "30")
	{
		$page->printError("End date must be on a half-hour mark.");
		echo "<center>";
		echo "You tried: ". $end->format("Y-m-d H:");
		echo "<span style='color: red;' >";
		echo $end->format("i");
		echo "</span><br /><br />";
		$page->addURL("view.php?event=$eventID","Try again.");
		echo "<br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	*/
	
	//verify the end time isn't before the start time
	$diff = $end->format("U") - $start->format("U");
	
	if( $diff <= 0 )
	{
		$page->printError("Invalid dates used.");
		echo "<center>"; 
		echo "Make sure the end date is not the same as, or earlier than, the start date.";
		echo "<br />";
		$page->addURL("view.php?event=$eventID","Try again.");
		echo "<br />";
		$page->addURL("index.php", "Back to schedule.");
		exit(0);
	}
	
	// make sure there isn't a conflict with another event
	$startStr = $start->format("Y-m-d H:i:00");
	$endStr = $end->format("Y-m-d H:i:00");
	
	$query = "
		SELECT
			e_eventName, e_dateStart, e_dateEnd
		FROM
			events, rooms
		WHERE
			e_eventID != $eventID
			AND
			e_roomID = r_roomID
			AND
			r_roomID = $roomID
			AND
			(
				e_dateStart >= '$startStr' AND e_dateStart < '$endStr'
				OR
				e_dateEnd > '$startStr' AND e_dateEnd <= '$endStr'
			)
	;";
	
	$connection->query($query);
	
	// we found some conflicts!
	if( $connection->result_size() > 0 ) {
	
		$page->printError("Conflict(s) found!");
		
		echo "<center>";
		
		echo "<h3>Your Event:</h3>";
		echo "<table class='addPage' id='addedEvent' cellpadding=0 cellspacing=0><thead>";
		echo "<td>Event Name</td>";
		echo "<td>Start Time</td>";
		echo "<td>End Time</td>";
		echo "</thead>";
		echo "<tr align='center'>";
		echo "<td>". $name ."</td>";
		echo "<td>". $start->format("H:i") ."</td>";
		echo "<td>". $end->format("H:i") ."</td>";
		echo "</tr></table>";
		
		echo "<h3>Conflicts:</h3>";
		echo "<table  id='conflicts' cellpadding=0 cellspacing=0><thead>";
		echo "<td>Event Name</td>";
		echo "<td>Start Time</td>";
		echo "<td>End Time</td>";
		echo "</thead>";
		
		for($i = 0; $i < $connection->result_size(); $i++)
		{
			$row = $connection->fetch_assoc();
			
			$startDate = date_create( $row['e_dateStart'] );
			$endDate = date_create( $row['e_dateEnd'] );
			
			echo "<tr align='center'>";
			echo "<td>";
			echo $row['e_eventName'];
			echo "</td><td>";
			echo $startDate->format("H:i");
			echo "</td><td>";
			echo $endDate->format("H:i");
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<br />";
		$page->addURL("view.php?event=$eventID", "Try again.");
		echo "<br /><br />";
		$page->addURL("index.php", "Return to event schedule.");
		exit(0);
	}

	// no conflicts found. Put the event in the schedule!
	$query = "
		UPDATE 
			events
		SET 
			e_eventName = '$name',
			e_roomID = $roomID, 
			e_dateStart = '" . $start->format("Y-m-d H:i:s") . "', 
			e_dateEnd = '" . $end->format("Y-m-d H:i:s") . "',
			e_eventDesc = '$desc',
			e_panelist = '$panelist', 
			e_color = '$color'
		WHERE 
			e_eventID = $eventID
	;";

	$connection->query($query);
	
	$page->printError("Event update successful! =^.^=");
	echo "<center>";
	$page->addURL("index.php","Return to main schedule");
	echo "</center>";
}
else if( $user->get_Username() == $panelist && $action == "panelist" )
{	
	$query = "
		UPDATE events 
		SET e_eventName = '$name', e_eventDesc = '$desc' 
		WHERE e_eventID = $eventID;";
	
	$connection->query($query);
}
?>
