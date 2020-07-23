<?php
/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework
 * 
 *  Files manipulation helper class  
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core\Helpers;

class Files
{
	/**
	 * Deletes given directory
	 * 
	 * @param  String  $dir Path to directory
	 * @return Boolean
	 */
	static function deleteDirectory($dir) 
	{
    	if (!file_exists($dir)) 
    	{
        	return true;
    	}

    	if (!is_dir($dir)) 
    	{
        	return unlink($dir);
    	}

    	foreach (scandir($dir) as $item) 
    	{
        	if ($item == '.' || $item == '..') 
        	{
            	continue;
        	}
        	
        	if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) 
        	{
            	return false;
        	}
    	}
    	return rmdir($dir);
	}
	
	/**
	 * Returns list (array) of FileHandlers from dir
	 * 
	 * @param  String $dirPath Parh to dir
	 * @return Array
	 */
	static function getFileList($dirPath)
  	{
  		$dirPath=substr($dirPath,-1)==DIRECTORY_SEPARATOR ? $dirPath : $dirPath.DIRECTORY_SEPARATOR;
  		$result='';
    	if (is_dir($dirPath))
		{
			foreach(scandir($dirPath) as $file)
			{
				if (is_file($dirPath.$file))
				{
					$result[]=new FileHandler($dirPath.$file);
				}
			}
		}else
		{
			throw new \Exception("TRY TO LIST IVALID DIRECTORY");
		}
		return $result;
  	}
	
	/**
	 * Return given file content
	 * 
	 * @param  String $path Path to file
	 * @return mixed
	 */
	static function getFileBody($path)
	{
		if (file_exists($path))
		{
			return file_get_contents($path);
		}
		return false;
	}
	
	/**
	 * Write given data to file
	 * 
	 * @param String  $path   Path to file
	 * @param String  $body   Content to be added to file
	 * @param Boolean $append Optional determine if data will be added to file or will be override file content
	 * @param Boolean $lock   Optional determine if lock file before write data
	 */
	static function setFileBody($path,$body,$append=false,$lock=false)
	{
		if($append&&$lock)
		{
			return file_put_contents($path,$body,FILE_APPEND|LOCK_EX);
		}else
		if($append&&!$lock)
		{
			return file_put_contents($path,$body,FILE_APPEND);
		}else
		if(!$append&&$lock)
		{
			return file_put_contents($path,$body,LOCK_EX);
		}else
		{
			return file_put_contents($path,$body);
		}
	}
	
	/**
	 * Returns info about file
	 * 
	 * @param  String   $path Path to file
	 * @return stdClass       Info about file (ie size, extension)
	 */
	public static function FileInfo($path)
	{
		return (object)pathinfo($path);
	}
	
	/**
	 * Import file to memmory
	 * 
	 * @param String  $path Path to file
	 * @param Integer $type Optional mode which determine how file is imported (1-include once, 2-require, 3-require once, default include)
	 */
	public static function import($path,$type=null)
	{
		if (file_exists($path))
		{
			if ((int)$type==1)
			{
				include_once($path);
			}else
			if((int)$type==2)
			{
				require($path);
			}else
			if ((int)$type==3)
			{
				require_once($path);
			}else
			{
			  include($path);
			}
			return true;
		}
		return false;
	}
}
?>