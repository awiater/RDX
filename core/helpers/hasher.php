<?php
/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework  
 * 
 *  Hashing and password manipulation helper class
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core\Helpers;

class Hasher
{
	/**
	 *  Create hashed password
	 * 	
	 *  @param  String $password Password string minimum 8 characters
	 * 	@param  String $salt     Optional salt string
	 * 	@param  Int    $cost     Cost used for hashing
	 *  @return String
	 */
	static function HashPassword(string $password,string $salt=null,$cost=12)
	{
		if (strlen($password)<8)
		{
			throw new \Exception('Invalid password length');
		}
		$options=['cost'=>$cost];
		if ($salt!=null&&strlen($salt)>8)
		{
			$options['salt']=$salt;
		}
		return password_hash($password,PASSWORD_DEFAULT,$options);
	}
	
	/**
	 *  Verify if given password and hashed password matches
	 *  
	 *  @param  String $password       Password which will be checked
	 * 	@param  String $hashedPassword Hashed password string
	 *  @return Boolean                TRUE | FALSE   
	 */
	static function VerifyPaswords(string $password,string $hashedPassword)
	{
		return password_verify($password, $hashedPassword);
	}
	
	/**
	 *  Create unique id
	 *  
	 *  @return String
	 */
	static function createUID()
	{
		return base64_encode(uniqid());
	}
}
?>