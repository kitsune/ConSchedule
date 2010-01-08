<?php
/*
 *      Webpage.php
 *      
 *      Copyright © 2008 Dylan Enloe <ninina@koneko-hime>
 *		Copyright © 2009 Drew Fisher <kakudevel@gmail.com>
 *		Copyright © 2009,2010 Mark Harviston <infinull@gmail.com>
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

class Webpage {
	function __construct($title)
	{
		echo <<<EOHTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<head>
	<title>Mewcon: $title</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link href="MEWschedule.css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="MEWschedule.js"></script>
</head>
<body>
<div id='headerMenu'>
EOHTML;

echo "<ul>";
echo "<li>";
	$this->addURL("http://www.mewcon.com","MEWcon Home");
echo "</li><li>";
	$this->addURL("index.php","Event Schedule");
echo "</li><li>";
	$this->addURL("userSchedule.php","User Schedule");
echo "</li></ul>";
echo "</div><p></p>";
	}
	
	function __destruct()
	{
		echo "</body></html>";
	}
	
	public function addURL($location, $text)
	{
		echo "<a href=\"$location\">$text</a>";
	}
	
	public function printNoEvents()
	{
		echo "<center>";
		echo "<h2>No events have yet been planned =T.T=</h2>";
		echo "</center>";
	}
	
	// conOpens and conCloses are epxected to be of type Date
	public function printDaySchedule($schedule, $roomNames, $conOpens, $conCloses) 
	{
	
		// only print 24 hours from beginning time
		$dayCheck = clone($conOpens);
		$dayCheck->modify("+1 days");
		if( $dayCheck->format("U") < $conCloses->format("U") )
		{
			$conCloses = $dayCheck;
		}
		
		$halfHoursOpen = ((($conCloses->format("U") - $conOpens->format("U"))/60/60)*2)+1;
		$tableTime = clone($conOpens);
		
		echo '<table class="daySchedule" cellpadding=0 cellspacing=0><thead>';
		echo '<tr><th class="timeColumn">Time</th>';
		//initialize the wait on each room to zero
		//might as well print out the top row too
		foreach($roomNames as $roomName)
		{
			echo "<th>$roomName</td>";
			$wait[$roomName] = 0;
		}
		echo "</thead>";
		
		//table body printout
		for($i=0; $i < $halfHoursOpen; $i+=1)
		{
			$tF = $tableTime->format("g:i a");
			echo "<tr>
			<th class=\"timeColumn\">$tF</th>";
		
			foreach($roomNames as $roomName)
			{
				if($wait[$roomName] == 0)
				{
					$tF = $tableTime->format("Y-m-d H:i:s");
					
					/* if an event for the current looped room starts
					 * at the current looped time, print the event and
					 * set the wait so that table printout skips cells it
					 * shouldn't print because of an event's rowspan.
					 */ 
					if(isset($schedule[$tF][$roomName]))
					{			
						$timeFormat = "g:i a";
						
						$event = $schedule[$tF][$roomName];
						$name = $event->getEventName();
						$color = $event->getColor();
						$size = $event->getEventLengthInHalfHours();
						$eventID = $event->getEventID();
						
						echo "<td class=\"foundEvent\" rowspan=\"$size\" bgcolor=\"$color\">
						<div class=\"event_container\">
						
						<div class=\"startTime\">
						{$event->getStartDate()->format($timeFormat)}
						</div>
						
						<div class=\"event\">";
						$this->addURL("view.php?event=$eventID",$name);
						echo "</div>
						
						<div class=\"endTime\">
						{$event->getEndDate()->format($timeFormat)}
						</div>
						</div>
						</td>";
						$wait[$roomName] = $size - 1;
					}
					else
					{
						echo "<td> </td>";
					}
				}
				else
				{
					$wait[$roomName] -= 1;
				}
			}
			echo '</tr>';
			$tableTime->modify("+30 minutes");
		}
		echo '</table>';
	}
	
	public function createEventForm($connection)
	{
		echo<<<ENDHTML
		<form action="add.php?action='add'" method="post"
			enctype="multipart/form-data">
		<p>
		Event Name: <br>
		<input type="text" name="name">
		</p>		
		<p>
		Room:<br>
		<select name="room">
ENDHTML;
		$query ="SELECT r_roomID, r_roomName FROM rooms ORDER BY (r_roomID) ASC;";
		$connection->query($query);
		while($row = $connection->fetch_assoc())
		{
			$roomID = $row['r_roomID'];
			$roomname = $row['r_roomName'];
			echo "<option value='$roomID'>$roomname</option>";
		}
		
		//fill in vars
		$sDYear = date_create()->format("Y");
		$eDYear = date_create()->format("Y");
		
		echo<<<ENDHTML
		</select>
		</p>
		Start Time:<br>
		<table class="timeForm" cellpadding=0 cellspacing=0">
		<thead>
		<td>Year &#150; Month &#150; Day</td>
		<td>Hour : Minute</td>
		</thead>
		<tr>
		<td>
			<input type="text" name="startYear" size=4 value="$sDYear"> &#150; 
			<select name="startMonth">
				<option value="12">Dec</option>
				<option value="01">Jan</option>
			</select> &#150;
			<input type="text" name="startDay" size=2>
		</td>
		<td>
			<input type="text" name="startHour" size=2> : 
			<select name="startMinute">
				<option value="00">00</option>
				<option value="30">30</option>
			</select>
		</td>	
		</tr>
		</table>
		<br>
		End Time:<br>
		<table class="timeForm" cellpadding=0 cellspacing=0">
		<thead>
		<td>Year &#150; Month &#150; Day</td>
		<td>Hour : Minute</td>
		</thead>
		<tr>
		<td>
			<input type="text" name="endYear" size=4 value="$eDYear"> &#150; 
			<select name="endMonth">
				<option value="12">Dec</option>
				<option value="01">Jan</option>
			</select> &#150;
			<input type="text" name="endDay" size=2>
		</td>
		<td>
			<input type="text" name="endHour" size=2> : 
			<select name="endMinute">
				<option value="00">00</option>
				<option value="30">30</option>
			</select>
		</td>	
		</tr>
		</table>
		</p>
		<p>
		Color of event (6-digit HTML color, including #)<br>
		<input type="text" name="color" size=7 value="#FFFFFF">
		</p>
		<p>
		Primary panelist's name (may be blank):<br>
		<input type="text" name="panelist">
		</p>
		<p>
		Description (may be blank):<br>
		<textarea name="desc" rows=10 cols=60></textarea>
		</p>
		<p>
		<input type="submit" value="Add Event">
		</p>
		</form>
ENDHTML;
	}
	
	public function printEvent($event)
	{
		$eventID = $event->getEventID();
		$name = $event->getEventName();
		$room = $event->getRoomName();
		$start = $event->getStartDate();
		$end = $event->getEndDate();
		$desc = $event->getDesc();
		$panelist = $event->getPanelist();
		
		echo "<center>";
		echo "<table id=\"viewEvent\" cellpadding=0 cellspacing=0>";
		echo "<colgroup>";
		echo "<col class=\"property\">";
		echo "<col class=\"value\">";
		echo "</colgroup>";
		echo "<tr><td>Panel<br>Name</td><td>" . $name . "</td></tr>";
		echo "<tr><td>Room</td><td>" . $room . "</td></tr>";
		echo "<tr><td>Date</td><td>" . $start->format("l, F d Y") . "</td></tr>";
		echo "<tr><td>Start Time</td><td>" . $start->format("H:i");
			echo "&nbsp; &nbsp; &nbsp;(" . $start->format("g:i a") . ")</td></tr>";
		echo "<tr><td>End Time</td><td>" . $end->format("H:i");
			echo "&nbsp; &nbsp; &nbsp;(" . $end->format("g:i a") . ")</td></tr>";
		
		if( $panelist != "")
		{
			echo "<tr><td>Panelist</td><td>" . $panelist . "</td></tr>";
		}
		
		if( $desc != "")
		{
			$desc = str_replace("\\n","<br><br>",$desc);
			echo "<tr><td>Description</td><td>" . $desc . "</td></tr>";
		}
		
		echo "</table>";
		echo "</center>";
	}
	
	public function printAdminEdit($event, $eventID, $connection)
	{
		$name = $event->getEventName();
		$room = $event->getRoomName();
		$start = $event->getStartDate();
		$end = $event->getEndDate();
		$desc = $event->getDesc();
		$panelist = $event->getPanelist();
		$color = $event->getColor();
		
		echo<<<ENDHTML
		<form action="edit.php?update=admin&event=$eventID" method="post"
			enctype="multipart/form-data">
		<p>
		Update Event Name: <br>
		<input type="text" name="name" value="$name">
		</p>		
		<p>
		Room:<br>
		<select name="room">
ENDHTML;
		$query ="SELECT r_roomID, r_roomName FROM rooms ORDER BY (r_roomID) ASC;";
		
		$connection->query($query);
		
		while($row = $connection->fetch_assoc())
		{
			$roomID = $row['r_roomID'];
			$roomname = $row['r_roomName'];
			if($room == $roomname)
			{
				echo "<option value='$roomID' selected='yes'>$roomname</option>";
			}
			else
			{
				echo "<option value='$roomID'>$roomname</option>";
			}
		}
		
		// set up fill-in variables for the date/time input values
		$sDYear = $start->format("Y");		
		$sDMonth = $start->format("m");		
		$sDDay = $start->format("d");		
		$sDHour = $start->format("H");		
		$sDMinute = $start->format("i");
		
		// current months/hours are supposed to be selected
		$sDDecSel = ($sDMonth == "12") ? "selected='yes'" : '';
		$sDJanSel = ($sDMonth == "01") ? "selected='yes'" : '';
		$sDMin00Sel = ($sDMinute == "00") ? "selected='yes'" : ''; 
		$sDMin30Sel = ($sDMinute == "30") ? "selected='yes'" : '';
		
		$eDYear = $end->format("Y");
		$eDMonth = $end->format("m");
		$eDDay = $end->format("d");
		$eDHour = $end->format("H");
		$eDMinute = $end->format("i");
		
		$eDDecSel = ($eDMonth == "12") ? "selected='yes'" : "";
		$eDJanSel = ($eDMonth == "01") ? "selected='yes'" : "";
		$eDMin00Sel = ($eDMinute == "00") ? "selected='yes'" : ''; 
		$eDMin30Sel = ($eDMinute == "30") ? "selected='yes'" : '';
		
		echo<<<ENDHTML
		</select>
		</p>
		Start Time:<br>
		<table class="timeForm" cellpadding=0 cellspacing=0">
		<thead>
		<td>Year &#150; Month &#150; Day</td>
		<td>Hour : Minute</td>
		</thead>
		<tr>
		<td>
			<input type="text" name="startYear" size=4 value="$sDYear"> &#150; 
			<select name="startMonth">
				<option value="12" $sDDecSel>Dec</option>
				<option value="01" $sDJanSel>Jan</option>
			</select> &#150;
			<input type="text" name="startDay" size=2 value="$sDDay">
		</td>
		<td>
			<input type="text" name="startHour" size=2 value="$sDHour"> : 
			<select name="startMinute">
				<option value="00" $sDMin00Sel>00</option>
				<option value="30" $sDMin30Sel>30</option>
			</select>
		</td>	
		</tr>
		</table>
		<br>
		End Time:<br>
		<table class="timeForm" cellpadding=0 cellspacing=0">
		<thead>
		<td>Year &#150; Month &#150; Day</td>
		<td>Hour : Minute</td>
		</thead>
		<tr>
		<td>
			<input type="text" name="endYear" size=4 value="$eDYear"> &#150; 
			<select name="endMonth">
				<option value="12" $eDDecSel>Dec</option>
				<option value="01" $eDJanSel>Jan</option>
			</select> &#150;
			<input type="text" name="endDay" size=2 value="$eDDay">
		</td>
		<td>
			<input type="text" name="endHour" size=2 value="$eDHour"> : 
			<select name="endMinute">
				<option value="00" $eDMin00Sel>00</option>
				<option value="30" $eDMin30Sel>30</option>
			</select>
		</td>	
		</tr>
		</table>
		</p>
		<p>
		Color of event (6-digit HTML color, including #)<br>
		<input type="text" name="color" size=7 value="$color">
		</p>
		<p>
		Primary panelist's name (may be blank):<br>
		<input type="text" name="panelist" value="$panelist">
		</p>
		<p>
		Description (may be blank):<br>
		<textarea name="desc" rows=10 cols=60>$desc</textarea>
		</p>
		<p>
		<input type="submit" value="Update">
		</p>
		</form>
ENDHTML;
		echo "<form action=\"delete.php?event=$eventID\" method=\"post\" enctype=\"multipart/form-data\">";
		echo "<input type=\"submit\" value=\"Delete Event\"></form><br>";
	}

	public function printPanelistEdit($event, $eventID)
	{
		$name = $event->getEventName();
		$desc = $event->getDesc();
		echo "
<form action=\"edit.php?update=panelist&event=$eventID\" method=\"post\" enctype=\"multipart/form-data\">
Update Event Name: 
<br>
<input type=\"text\" name=\"name\" value=\"$name\">
<br><br>
Edit the Description of this Panel:<br>
<textarea name=\"desc\" rows=\"10\" cols=\"60\">$desc</textarea>
<br><br>
<input type=\"submit\" name=\"add\" value=\"Update\"></form><br>";
	}
	
	public function printError($err)
	{
		echo "<center>";
		echo "<h2>". $err ."</h2>";
		echo "</center>";
	}
	
	
	/* _GET_checkEventID:
	 * Checks the passed GET param for validity.
	 * Prints an error if the param isn't set, or the eventID doesn't exist.
	 * On Success returns and event object when $createEventObj is TRUE, or
	 * the validated EventID when $createEventObj is FALSE.
	 * On failure always returns null. 
	 */
	public function _GET_checkEventID($_GETvar, $connection, $createEventObj = TRUE)
	{
		// make sure the provided event is valid
		if( ! isset($_GETvar) )
		{
			$this->printError("EventID must be supplied.");
			echo "<center>";
			$this->addURL("index.php","Return to event schedule.");
			echo "</center>";
			return NULL;
		}
		
		$eID = $connection->validate_string($_GETvar);
		
		// type check eventID
		if( is_null($eID) || $eID == '' || ! is_numeric($eID) )
		{
			$this->printError("Problem with passed EventID.");
			echo "<center>";
			$this->addURL("index.php","Return to event schedule.");
			echo "</center>";
			return NULL;
		}
		
		$q = NULL;
		
		if( $createEventObj == FALSE)
		{
			// we only care the the event exists, so keep the query simple.
			$q = "SELECT e_eventID FROM events WHERE e_eventID = $eID;";
		}
		else
		{
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
		}
		$connection->query($q);

		if( $connection->result_size() != 1 )
		{
			$this->printError("EventID doesn't exist in the database.");
			echo "<center>";
			$this->addURL("index.php","Return to event schedule.");
			echo "</center>";
			return NULL;
		}
		
		if( $createEventObj == FALSE)
		{
			return $eID;
		}
		else
		{
			$row = $connection->fetch_assoc();

			return new Event(
				$row['e_eventID'], $row['e_eventName'], $row['r_roomName'], 
				$row['e_dateStart'],$row['e_dateEnd'], $row['e_eventDesc'], 
				$row['e_panelist'], $row['e_color']
			);
		}
	}
	
	function printTableRows($row1, $row2, $cCount, $hilightRow) 
	{
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
}
?>
