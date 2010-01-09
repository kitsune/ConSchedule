<?php
/* 
 * Copyright © 2009 Dylan Enloe
 * ALL RIGHTS RESERVED
 * 
 * This program is distributed under the terms of
 * version 3 of the GNU Lesser General Public License.
 * Please see the file LICENSE in this distribution
 * for licensing information.
 *
 * Connection.php
 * handles the connection to the mysql database as well as validation of
 * input to avoid injection
 * */

//a hack to make sure the settings stick...need to find out why I need
//to do this.
global $database_username, $database_password, $database_server,
			$database_name;

if(!file_exists('settings.php')){
	die('rename settings.default.php to settings.php and edit it to include your database settings');
}

require_once 'settings.php';

class Connection {
	private $connection;
	private $result;
	
	function __construct()
	{	
		global $database_username, $database_password, $database_server,
			$database_name;
			
		$this->connection = mysql_connect($database_server, 
			$database_username, $database_password) 
			or die ("could not connect");
		
		mysql_select_db($database_name, $this->connection) 
			or die (mysql_error());
		
	}
	
	function __destruct()
	{
	//	mysql_close($this->connection);
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

?>
