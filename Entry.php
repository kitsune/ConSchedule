<?php
/*
 *      Entry.php
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

class Entry {
	private $eventname;
	private $eventID;
	private $color;
	private $size;
	
	function __construct($eventID, $eventname, $color = 0, $size = 1)
	{
		$this->eventID = $eventID;
		$this->eventname = $eventname;
		$this->color = $color;
		$this->size = $size;
	}
	
	public function getColor()
	{
		return $this->color;
	}
	
	public function getEventName()
	{
		return $this->eventname;
	}
	
	public function getEventID()
	{
		return $this->eventID;
	}

	public function getSize()
	{
		return $this->size;
	}
}
?>
