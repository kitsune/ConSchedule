<?php
/*
 *      User.php
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

$sServerDocRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($sServerDocRoot . '/forum/SSI.php');

class User {
	private $admin;
	private $user;
	private $username;
	private $userID;
	
	function __construct()
	{
		global $context;
		$this->admin = $context['user']['is_admin'];
		$this->user = !$context['user']['is_guest'];
		$this->username = $context['user']['username'];
		$this->userID = $context['user']['id'];
	}
	
	public function is_Admin()
	{
		return $this->admin;
	}
	
	public function is_User()
	{
		return $this->user;
	}
	
	public function get_Username()
	{
		return $this->username;
	}

	public function get_UserID()
	{
		return $this->userID;
	}
}
?>
