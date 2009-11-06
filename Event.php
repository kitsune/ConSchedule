<?php
/*
 *      Event.php
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

class Event
{
	private $eventID;
	private $eventname;
	private $roomname;
	private $startDate;
	private $endDate;
	private $desc;
	private $panelist;
	private $color;
	
	function __construct($eventID, $eventname, $roomname, $startDate, $endDate, $desc, $panelist, $color)
	{
		$this->eventID = $eventID;
		$this->eventname = $eventname;
		$this->roomname = $roomname;
		$this->startDate = date_create($startDate);
		$this->endDate = date_create($endDate);
		$this->desc = $desc;
		$this->panelist = $panelist;
		$this->color = $color;
	}
	
	public function getEventID()
	{
		return $this->eventID;
	}
	
	public function getEventName()
	{
		return $this->eventname;
	}
	
	public function getRoomName()
	{
		return $this->roomname;
	}
	
	public function getStartDate()
	{
		return $this->startDate;
	}
	
	public function getEndDate()
	{
		return $this->endDate;
	}
	
	public function getDesc()
	{
		return $this->desc;
	}
	
	public function getPanelist()
	{
		return $this->panelist;
	}
	
	public function getColor()
	{
		return $this->color;
	}
	
	public function getEventLength()
	{
		return ($this->endDate->format("U") - $this->startDate->format("U"))/60/60;
	}
	
	public function getEventLengthInHalfHours()
	{
		return (($this->endDate->format("U") - $this->startDate->format("U"))/60/60)*2;
	}
}
?>
