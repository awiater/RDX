<?php
/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework  
 * 
 *  Request manipulation class
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core;

use RDX\Core\Helpers\Strings as Str;
use RDX\Core\Helpers\Arrays as Arr;

class Request
{
	/**
	 * Triggered when invoking inaccessible method
	 * 
	 * @param  String $func Function name
	 * @param  Array  $args Function arguments
	 * @return mixed
	 */
	function __call($func,$args)
	{
		$func=strtolower($func);
		if (Str::startsWith($func,'clear'))
		{
			$this->clear(substr($func, 5),count($args)==1?$args[0]:null);
		}else
		if (Str::startsWith($func,'set'))
		{
			$func=strtolower(substr($func, 3));
			if ($func=='get'&&count($args)==2)
			{
				$_GET[$args[0]]=$args[1];
			}else
			if ($func=='get'&&count($args)==1 && is_array($args[0]))
			{
				$_GET=$args[0];
			}else
			if ($func=='post'&&count($args)==2)
			{
				$_POST[$args[0]]=$args[1];
			}else
			if ($func=='post'&&count($args)==1 && is_array($args[0]))
			{
				$_POST=$args[0];
			}	
			
		}else
		if (Str::startsWith($func,'isset'))
		{ 
			$func=substr($func,5);
			switch(strtolower($func)) 
			{
				case 'get': return count($args)>0?Arr::KeysExists($args,$_GET):count($_GET)>0;
				case 'post':return count($args)>0?Arr::KeysExists($args,$_POST):count($_POST)>0;
				case 'files':return count($args)>0?Arr::KeysExists($args,$_FILES):count($FILES)>0;
				case 'cookie':return count($args)>0?Arr::KeysExists($args,$_COOKIE):count($_COOKIE)>0;
			}
		}else
		{
			switch(strtolower($func))
			{
				case 'get':return count($args)==1?$_GET[$args[0]]:$_GET;
				case 'server':return count($args)==1?$_SERVER[$args[0]]:$_SERVER;
				case 'post':return count($args)==1?$_POST[$args[0]]:$_POST;
				case 'files':return count($args)==1?$_FILES[$args[0]]:$_FILES;
				case 'cookie':return count($args)==1?$_COOKIE[$args[0]]:$_COOKIE;
			}
		}	
	}
	
	/**
	 * Clear request variable(s)
	 * 
	 * @param String $request Request type (get, post, cookies, session)
	 * @param String $var     Variable name (key)
	 */
	protected function clear($request,$var=null)
	{
		if ($request=='get')
		{
			$this->unsetvar($_GET, $var);			
		}
		if ($request=='post')
		{
			$this->unsetvar($_POST, $var);	
					
		}
		if ($request=='cookies')
		{
			$this->unsetvar($_COOKIE, $var);			
		}
		if ($request=='session')
		{
			$this->unsetvar($_SESSION, $var);			
		}
	}
	
	/**
	 * Clear variable
	 * 
	 * @param reference &$array Request data collection by reference
	 * @param String    $key    Variable name (key)
	 */
	protected function unsetvar(&$array,$key)
	{
		if ($key!=null&&array_key_exists($key, $array))
		{
			unset($array[$key]);
		}else
		if ($key==null)
		{
			unset($array);
		}
	}
}
?>