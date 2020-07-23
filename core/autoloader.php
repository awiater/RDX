<?php
/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework  
 * 
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core;

class Autoloader
{
	/**
	 * @var Array Replacements items collection
	 */
	private $replace;
	
	/**
	 * Return autoloader class
	 * 
	 * @param  Array $replace Replacements items collection
	 * @return RDX\Core\Autoloader
	 */
	public static function init(array $replace)
	{
		return new Autoloader($replace);
	}
	
	/**
	 * Register given function as __autoload() implementation
	 * 
	 * @param callable $loader The autoload function being registered
	 */
	public static function Register($loader)
	{
		spl_autoload_register($loader);
	}
	
	/**
	 * Objects (Classes) auto load helper class
	 * 
	 * @param Array $replace Replacements items collection
	 */
	function __construct(array $replace)
	{
		$this->replace=array_merge(['rdx'=>BASEDIR],$replace);
		spl_autoload_register(array($this,'rdx'));
		spl_autoload_register(array($this,'db'));
		spl_autoload_register(array($this,'twig'));
	}
	
	/**
	 * Load class from file
	 * 
	 * @param String $data class definition
	 */
	private function load($data)
	{
		$files=array();
		$files[]=$this->rdx($data);
		$files[]=$this->filebase($data);
		$files[]=$this->cms($data);
		foreach($files as $file)
		{
			if (file_exists($file))
			{
				require_once($file);
			}
		}
	}
	
	/**
	 * Load flat db classes from file
	 * 
	 * @param String $data class definition
	 */
	public function db($data)
		{
			$data.=".php";
      		$data=str_replace(chr(92),"/",$data);
		  	$data=str_replace("Librarium",COREDIR.'libs/librarium/libdb',$data);
			if (file_exists($data))
			{
				require_once($data);
			}
	}
	
	/**
	 * Load framework classes from file
	 * 
	 * @param String $data class definition
	 */
	public  function rdx($data)
	{
     	$data.=".php";
     	$data=str_replace(chr(92),"/",$data);
  		$data=str_replace(basename($data),strtolower(basename($data)),$data);
	   	$data=str_replace(array_keys($this->replace),array_values($this->replace),strtolower($data));
		$data=str_replace('//', '/', $data);		
		if (file_exists(LOGFILE)&&in_array('autoloader',LOGOPTION))
		{
			file_put_contents(LOGFILE, Date('d-m-Y h:i:s').' - AUTOLOADER : '.$data.' | '.file_exists($data).PHP_EOL , FILE_APPEND | LOCK_EX);
		}
		
		if (file_exists($data))
		{
			require_once($data);
		}
	}
	
	/**
	 * Load TWIG template engine classes from file
	 * 
	 * @param String $data class definition
	 */
	public function twig($data)
	{
		if (0 !== strpos($data, 'Twig')) 
		{ return; } 
		$file=COREDIR.'libs/'.str_replace('_','/', $data).'.php';
		$file=str_replace(chr(92),"/",$file);
			if(file_exists($file))
		{ require $file; }
	}

}
?>