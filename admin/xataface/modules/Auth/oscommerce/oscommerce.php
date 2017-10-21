<?php
/*-------------------------------------------------------------------------------
 * Dataface Web Application Framework
 * Copyright (C) 2005-2006  Steve Hannah (shannah@sfu.ca)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *-------------------------------------------------------------------------------
 */

/**
 *<p>This module extends Dataface to allow its applications to use oscommerce authenticaiton. </p>
 *
 *
 * @author Steve Hannah (shannah@sfu.ca)
 * @created March 7, 2007
 * @version 0.1
 */

//include_once $_SERVER['DOCUMENT_ROOT'].'/includes/local/configure.php';
//include_once $_SERVER['DOCUMENT_ROOT'].'/includes/filenames.php';
//include_once $_SERVER['DOCUMENT_ROOT'].'/includes/database_tables.php';
include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/dev/includes/global-configure.php';

class dataface_modules_oscommerce {

	function checkCredentials(){
		$app =& Dataface_Application::getInstance();
		$auth =& Dataface_AuthenticationTool::getInstance();
		if ( !$auth->authEnabled ) return true;
		// The user is attempting to log in.
		$creds = $auth->getCredentials();
		if ( !isset( $creds['UserName'] ) || !isset($creds['Password']) ){
			// The user did not submit a username of password for login.. trigger error.
			//throw new Exception("Username or Password Not specified", E_USER_ERROR);
			return false;
		}
		import('Dataface/Serializer.php');
		$serializer = new Dataface_Serializer($auth->usersTable);
		//$res = mysql_query(
		$sql =	"SELECT `".$auth->usernameColumn."`, `".$auth->passwordColumn."` FROM `".$auth->usersTable."`
			 WHERE `".$auth->usernameColumn."`='".addslashes($serializer->serialize($auth->usernameColumn, $creds['UserName']))."'";
//			 AND `".$auth->passwordColumn."`='".
//				$serializer->serialize($auth->usernameColumn, tep_encrypt_password($creds['Password']))."'";  // osCommerce password hash )
		$res = mysql_query($sql, $app->db());
		if ( !$res ) throw new Exception(mysql_error($app->db()), E_USER_ERROR);

		if ( mysql_num_rows($res) === 0 ){
			return false;
		}
		$found = false;
		while ( $row = mysql_fetch_assoc($res) ){
			if ( strcmp($row['UserName'], $creds['UserName'])===0 && tep_validate_password($creds['Password'], $row['Password'])){
				$found=true;
				break;
			}
		}
		@mysql_free_result($res);
		return $found;
	}


	function setPassword($password){
		$app =& Dataface_Application::getInstance();
		$auth =& Dataface_AuthenticationTool::getInstance();

		$user = $auth->getLoggedInUser();
		if ( !$user ){

			throw new Exception("Failed to set password because there is no logged in user.");
		}

		$user->setValue($auth->passwordColumn, tep_encrypt_password($password));
		$res = $user->save();
		if ( PEAR::isError($res) ){
			throw new Exception($res->getMessage());
		}
		return true;
	}
}
?>