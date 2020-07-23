<?php
/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework
 * 
 * 	Strings manipulation class  
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core\Helpers;

class Strings
{
	/**
	 * Returns class name without namespace
	 * 
	 * @param  String $class Full class name
	 * @return String        Class name without namespace
	 */
	static function classShortName($class)
	{ 
		if(is_object($class))
		{
			$class=get_class($class);
		}
		return substr(strrchr($class,'\\'),1); 
	}
	
	/**
	 * Check if string starts with given characters
	 * 
	 * @param  String $haystack String to check
	 * @param  String $needle   Characters to use
	 * @return Boolean          TRUE if string start with given characters, FALSE otherwise
	 */
	static function startsWith($haystack, $needle) 
	{ 
		$length = strlen($needle); 
		return (substr($haystack, 0, $length) === $needle); 
	} 
	
	/**
	 * Check if string ends with given characters
	 * 
	 * @param  String $haystack String to check
	 * @param  String $needle   Characters to use
	 * @return Boolean          TRUE if string ends with given characters, FALSE otherwise
	 */
	static function endsWith($haystack, $needle) 
	{ 
		$length = strlen($needle); 
		if ($length == 0) { return true; } 
		return (substr($haystack, -$length) === $needle); 
	}
	
	/**
	 * Check if string contains given characters
	 * 
	 * @param  String $haystack String to check
	 * @param  String $needle   Characters to use
	 * @return Boolean          TRUE if string contains given characters, FALSE otherwise
	 */
	static function contains($haystack,$needle)
	{
		$needle=is_array($needle)?$needle:[$needle];
		foreach ($needle as $value) 
		{
			if (strlen($value)<1)
			{
				return FALSE;
			}
			if (strpos($haystack, $value) === FALSE)
			{
				return FALSE;
			}	
		}
		return TRUE;
	}
	
	/**
	 *  Determine if given string is valid JSON
	 * 
	 *  @param  String  $haystack String to check
	 *  @return Boolean           TRUE if given string is valid JSON or FALSE otherwise
	 */
	static function isJson($haystack)
	{
		json_decode($haystack);
 		return (json_last_error() == JSON_ERROR_NONE);
	}
}
?>