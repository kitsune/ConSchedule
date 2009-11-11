<?php
/*
 *      add.php
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
$page = new Webpage("Add Event");
$connection = new Connection();

#only admins can add events
if(!$user->is_Admin())
{
	$page->printError("Only Admins can create new events.");
	echo "<center>";
	$page->addURL("index.php","Return to event schedule.");
	echo "</center>";
	exit(0);
}

if(isset($_GET['action']) && count($_POST) == 15 ) // there are 15 fields used in the add form.
{
	//ok we'll add the entry here.	
	$name = $connection->validate_string($_POST['name']);
	$name = trim($name);
	
	if ( str_word_count($name) == 0 )
	{	
		$page->printError("Name cannot be blank.");
		echo "<center>";
		echo "Please go back and supply one.<br /><br />";
		$page->addURL("add.php","Try again.");
		echo "<br /><br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	
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
	$desc = $connection->validate_string($_POST['desc']);
	//trim of excess whitepsace
	$color = trim($color);
	$panelist = trim($panelist);
	$desc = trim($panelist);
	
	/*
	// verify dates are in half-hour format
	if( $start->format("i") != "00" && $start->format("i") != "30" )
	{
		$page->printError("Start date must be on a half-hour mark.");
		echo "<center>";
		echo "You tried: ". $start->format("Y-m-d H:");
		echo "<span style='color: red;' >";
		echo $start->format("i");
		echo "</span><br /><br />";
		$page->addURL("add.php","Try again.");
		echo "<br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	
	if( $end->format("i") != "00" && $end->format("i") != "30")
	{
		$page->printError("End date must be on a half-hour mark.");
		echo "<center>";
		echo "You tried: ". $end->format("Y-m-d H:");
		echo "<span style='color: red;' >";
		echo $end->format("i");
		echo "</span><br /><br />";
		$page->addURL("add.php","Try again.");
		echo "<br />";
		$page->addURL("index.php","Return to event schedule.");
		echo "</center>";
		exit(0);
	}
	*/
	
	//make sure end event isn't = or earlier than the start date
	$diff = $end->format("U") - $start->format("U");
	
	if( $diff <= 0 )
	{
		$page->printError("Incorrect dates(s) passed.");
		echo "<center>";
		echo "Make sure the end date is not the same as, or earlier than, the start date.";
		$page->addURL("add.php","Try again.");
		echo "<br />";
		$page->addURL("index.php", "Back to schedule.");
		echo "</center>";
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
		echo "<table class='addPage' id='conflicts' cellpadding=0 cellspacing=0><thead>";
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
		$page->addURL("add.php", "Try again.");
		echo "<br /><br />";
		$page->addURL("index.php", "Return to event schedule.");
		exit(0);
	}
	
	//no conflicts found. Put the event in the schedule! 
	$query = "
		INSERT INTO 
			events(	e_roomID, 
					e_dateStart, 
					e_dateEnd, 
					e_eventName, 
					e_color, 
					e_eventDesc, 
					e_panelist)
		VALUES 
			($roomID,
			'" . $start->format("Y-m-d H:i:s") . "',
			'" . $end->format("Y-m-d H:i:s") . "',
			'$name', 
			'$color', 
			'$desc', 
			'$panelist')
	;";
		
	$connection->query($query);
	$row = $connection->get_insert_ID();
	$eventID = $row[0];
	
	$page->printError("Successfully created event!");
	echo "<center>";
	$page->addURL("view.php?event=$eventID","View Event");
	echo "<br />";
	$page->addURL("add.php","Add another Event");
	echo "<br />";
	$page->addURL("index.php","Return to main schedule");
	echo "</center>";
}
else
{
	$page->printError("Add an event");
	echo "<center>";
	echo "</center>";
	echo "<hr /><hr />";
	$page->createEventForm($connection);
}
?>
