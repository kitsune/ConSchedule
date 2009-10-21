<?php
/*
 *      Webpage.php
 *      
 *      Copyright 2008 Dylan Enloe <ninina@koneko-hime>
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
		echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head>
	<title>Mewcon: $title</title>
	<meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\" />
</head>
<body>";
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
		echo "<center>No events were found</center>";
	}
	
	public function printDaySchedule($schedule, $rooms, $day, $start = 0, $end = 48)
	{
		echo '<table border="1"><thead><td></td>';
		//initialize the wait on each room to zero
		//mind as well print out the top row too
		foreach($rooms as $id => $room)
		{
			echo "<td>$room</td>";
			$wait[$id] = 0;
		}
		echo "</thead>";
		for($i=$start; $i < $end;$i+=1)
		{
			echo '<tr>';
			$time = $this->getRealTime($i);
			echo "<td>$time</td>";
			foreach($rooms as $id => $room)
			{
				if($wait[$id] == 0)
				{
					if(isset($schedule[$day][$i][$id]))
					{
					
						//print the item
						$name = $schedule[$day][$i][$id]->getEventName();
						$color = $schedule[$day][$i][$id]->getColor();
						$size = $schedule[$day][$i][$id]->getSize();
						$eventID = $schedule[$day][$i][$id]->getEventID();
						echo "<td rowspan=\"$size\" bgcolor=\"$color\">";
						$this->addURL("view.php?event=$eventID",$name);
						echo"</td>";
						$wait[$id] = $size - 1;
					}
					else
					{
						echo "<td></td>";
					}
				}
				else
				{
					$wait[$id] -= 1;
				}
			}
			echo '</tr>';
		}
		echo '</table>';
	}
	
	public function createEventForm($connection)
	{
		$query ="
SELECT r_roomID, r_roomname
FROM rooms
ORDER BY (r_roomID);";
		$connection->query($query);
	
		echo "<center>
<form action=\"add.php?action=add\" method=\"post\" enctype=\"multipart/form-data\">
Event Name: 
<br>
<input type=\"text\" name=\"name\">
<br>
Time of the event:
<br>
*Hack alert: For this year I'm using a speed hack so I don't have to do alot of dev
<br> Day is an integer, 1 being the first day, 0 being set up, 2 being third day
<br> time is the number of hours since the beginning of the day. ie 8:30 is 8.5
<br> If an event spans multiple days it needs to be entered once of each day.
<br>
Day : <input type=\"text\" name=\"day\" size=\"3\"> 
Start : <input type=\"text\" name=\"start\" size=\"3\"> 
End: <input type=\"text\" name=\"end\" size=\"3\">
<br>
Color to make event (html color):
<br>
<input type=\"text\" name=\"color\">
<br>
Primary panelist's forum name(may be empty):
<br>
<input type=\"text\" name=\"panalist\">
<br>
Description of the event(may be empty):
<br>
<textarea name=\"desc\" rows=\"10\" cols=\"60\"></textarea>
<br>
Select Room:
<br>
<select name=\"room\">";
		while($row = $connection->fetch_row())
		{
			$roomID = $row[0];
			$roomname = $row[1];
			echo "<option value=\"$roomID\">$roomname</option>";
		}
		echo "</select>
<br><br>
<input type=\"submit\" name=\"add\" value=\"Finished\">
</form>
</center>";	
	}
	
	public function printEvent($event)
	{
		$name = $event->getEventName();
		$room = $event->getRoomName();
		$day = $this->getRealDate($event->getDay());
		$start = $this->getRealTime($event->getStart());
		$end = $this->getRealTime($event->getEnd());
		$desc = $event->getDesc();
		$panelist = $event->getPanelist();
		echo "
Panel Name: $name<br>
The panel will be held in the $room.<br>
The panel will be from $start to $end on the $day.<br>";
		if($panelist != "")
		{
			echo "The primary panalist can be reached on the forum under the name of $panalist.<br>";
		}
		if($desc != "")
		{
			echo "A brief description of the panal:<br> $desc<br>";
		}
		echo "<br>";
		$this->addURL("index.php", "Back to event schedule");	
	}
	
	public function printAdminEdit($event, $eventID, $connection)
	{
		$name = $event->getEventName();
		$room = $event->getRoomName();
		$day = $event->getDay();
		$start = floatval($event->getStart())/2;
		$end = floatval($event->getEnd())/2;
		$desc = $event->getDesc();
		$panelist = $event->getPanelist();
		$color = $event->getColor();
		echo "
<form action=\"edit.php?update=admin&event=$eventID\" method=\"post\" enctype=\"multipart/form-data\">
Update Event Name: 
<br>
<input type=\"text\" name=\"name\" value=\"$name\">
<br><br>
Color to make event (html color):
<br>
<input type=\"text\" name=\"color\" value=\"$color\">
<br><br>
Change the time of the event:
<br>
*Hack alert: For this year I'm using a speed hack so I don't have to do alot of dev
<br> Day is an integer, 1 being the first day, 0 being set up, 2 being third day
<br> time is the number of hours since the beginning of the day. ie 8:30 is 8.5
<br> If an event spans multiple days it needs to be entered once of each day.
<br>
Day : <input type=\"text\" name=\"day\" size=\"3\" value = \"$day\"> 
Start : <input type=\"text\" name=\"start\" size=\"3\" value = \"$start\"> 
End: <input type=\"text\" name=\"end\" size=\"3\" value = \"$end\">
<br><br>
Primary panelist's forum name(may be empty):
<br>
<input type=\"text\" name=\"panalist\" value= \"$panelist\">
<br><br>
Change Room:
<br>
<select name=\"room\">";
		$query ="
SELECT r_roomID, r_roomname
FROM rooms
ORDER BY (r_roomID);";
		$connection->query($query);
		while($row = $connection->fetch_row())
		{
			$roomID = $row[0];
			$roomname = $row[1];
			if($room == $roomname)
			{
				echo "<option value=\"$roomID\" selected=\"yes\">$roomname</option>";
			}
			else
			{
				echo "<option value=\"$roomID\">$roomname</option>";
			}
		}
		echo "</select>
<br><br>
Edit the Description of this Panel:<br>
<textarea name=\"desc\" rows=\"10\" cols=\"60\">$desc</textarea>
<br><br>
<input type=\"submit\" name=\"add\" value=\"Update\"></form><br>";
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
<br>
Edit the Description of this Panel:<br>
<textarea name=\"desc\" rows=\"10\" cols=\"60\">$desc</textarea>
<br><br>
<input type=\"submit\" name=\"add\" value=\"Update\"></form><br>";
	}
	
	private function getRealTime($time)
	{
		$hours = intval($time/2);
		if($hours * 2 != $time)
		{
			#it's a 1/2 hour
			return "$hours:30";
		}else
		{
			return "$hours:00";
		}
	}
	
	private function getRealDate($date)
	{
		#this is just a big if switch
		if($date == 0)
			return "December 30th";
		if($date == 1)
			return "December 31st";
		if($date == 2)
			return "January 1st";
	}
}
?>
