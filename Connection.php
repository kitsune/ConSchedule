<?php
/*
 *      Connection.php
 *      This file contains a class for dealing with mysql connections
 *      
 *      Copyright 2008 Dylan Enloe <ninina@koneko-hime>
 *		Copyright 2009 Drew Fisher <kakudevel@gmail.com>
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

class Connection {

	private $connection;
	private $result;

	function __construct()
	{
		$username = "mewuser";
		$password = "kittens";
		$databasename = "mewschedule_test";
		$hostname = "localhost";

		$this->connection = mysql_connect($hostname, $username, $password) or die ("could not connect");

		mysql_select_db($databasename, $this->connection) or die (mysql_error());
	}

	

	function __destruct()
	{
		mysql_close($this->connection);
	}

	public function query($query)
	{
		//$query = $this->db_validate_string($query);
		$this->result = mysql_query($query, $this->connection) or die("query error: " . mysql_error());
	}
	public function fetch_row()
	{
		$row = mysql_fetch_row($this->result);
		return $row;
	}
	
	public function fetch_assoc()
	{
		$row = mysql_fetch_assoc($this->result);
		return $row;
	}

	public function result_size()
	{
		return mysql_num_rows($this->result);
	}

	public function validate_string($string)
	{
		if (get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}

		$string = strip_tags($string);
		$string = mysql_real_escape_string($string);
		
		return $string;
	}

	public function get_insert_ID()
	{
		$this->query("SELECT LAST_INSERT_ID();");

		return $this->fetch_row();
	}

}

function db_validate_string($string)
{
	if (get_magic_quotes_gpc())
	{
		$string = stripslashes($string);
	}
	$string = strip_tags($string);
	$string = mysql_real_escape_string($string);
	
	return $string;
}
?>
