<?php
/*
 *      Event.php
 *      
 *      Copyright 2008 Dylan Enloe <ninina@Siren>
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
	private $eventname;
	private $roomname;
	private $day;
	private $start;
	private $end;
	private $desc;
	private $panelist;
	private $color;
	
	function __construct($eventname, $roomname, $day, $start, $end, $desc, $panelist, $color)
	{
		$this->eventname = $eventname;
		$this->roomname = $roomname;
		$this->day = $day;
		$this->start = $start;
		$this->end = $end;
		$this->desc = $desc;
		$this->panelist = $panelist;
		$this->color = $color;
	}
	
	public function getEventName()
	{
		return $this->eventname;
	}
	
	public function getRoomName()
	{
		return $this->roomname;
	}
	
	public function getDay()
	{
		return $this->day;
	}
	
	public function getStart()
	{
		return $this->start;
	}
	
	public function getEnd()
	{
		return $this->end;
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
}
?>
