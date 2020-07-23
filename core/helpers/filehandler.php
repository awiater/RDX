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

class FileHandler
{
	/**
	* @var String
	*/
	protected $Path;
	
	/**
	* @var Resource
	*/
	protected $File;
	
	/**
	*@var Array
	*/
	private $Info;
	
	/**
	* Files manipulation helper class
	*
	* @param String $path Path to file
	*/
	function __construct($path)
	{
		$this->Path=$path;
		$this->Info=(object)pathinfo($path);
	}
	
	/**
	* Destructor Function
	*/
	function __destruct()
	{
		if (is_resource($this->File))
		{
			fclose($this->File);
		}
	}

	/**
	* Return size of file in bytes
	*
	*@return Integer
	*/
	function sizeOf()
	{
		$result=filesize($this->Path);
		return $result>0 ? $result : 1;
	}
	
	/**
	* Open file and assign resource handler
	*
	* @param Integer $mode Optional open mode (0 - Read, 1 - Write)
	*/
	private function Open($mode=0)
	{
		switch($mode)
		{
			case 0: $mode="r";break;
			case 1: $mode="w";break;
		}
		$this->File= fopen($this->Path,$mode);
		return $this->File;
	}
	
	/** 
	* Writes data to file
	*
	* @param  String  $data Date to be written to file
	* @return Boolean
	*/
	private function Write($data)
	{
		$file=$this->Delete();
		if ($file)
		{
			if ($this->Lock(1))
			{
				$result=fwrite($this->File,$data);
				$this->Unlock();
			}else
			{
				$result=false;
			}
		}else
		{
			$result=false;
		}
		return $result;
	}
	
	/**
	* Saving file in new location
	*
	* @param  String  $path Path to file
	* @return Boolean
	*/
	function saveAs($path)
	{
		$data=$this->toString();
		$this->Path=$path;
		return $this->Write($data);
	}
	
	/** 
	* Lock file
	*
	*@param  Integer $mode Optional mode of lock (0 - Read Lock,1- Write Lock)
	*@return Boolean
	*/
	function Lock($mode=0)
	{
		$file=$this->Open($mode);
		switch($mode)
		{
			case 0: $mode=LOCK_SH;break;
			case 1: $mode=LOCK_EX;break;
		}
		
		return flock($file,$mode);
	}
	
	/**
	* Unlock File
	*
	* @return Boolean
	*/
	function Unlock()
	{
		return flock($this->File,LOCK_UN);
	}
	
	/**
	* Delete File
	*
	* @param Boolean $justContent Optional determine if file will be deleted (FALSE) or just content (TRUE)
	* @return Boolean
	*/
	function Delete($justContent=false)
	{
		
		return $justContent==true ? $this->Write("") : unlink($this->Path);
	}
	
	/**
	 * Make new copy of file in given path
	 * 
	 * @param  String  $path New location path
	 * @return Boolean
	 */
	 function copyTo($path)
	 {
	 	$current=$this->Path;
	 	$result=$this->saveAs($path);
	 	$this->Path=$current;
	 	return $result;
	 }
	
	/**
	* Add data to file content
	*
	* @param  String  $data     Data to be written to file
	* @param  Boolean $truncate Optional determine if data will be added or override file 
	* @return Boolean
	*/
	function Add($data,$truncate=false)
	{
		if (!$truncate)
		{
			$data.=$this->toString();
		}
		return $this->Write($data);
	}
	
	/**
	* Returning file extension
	*
	* @return String
	*/
	function getExtension()
	{
		return $this->Info["extension"];
	}
	
	/**
	* Return name of file
	*
	* @return String
	*/
	function getName()
	{
		return $this->Info["filename"];
	}
	
	/**
	 * Return File dir path
	 * 
	 * @return String
	 */
	function getDirPath()
	{
		return $this->Info["dirname"];
	}
	
	/**
	 * Return File base name (Filename with extension)
	 * 
	 * @return String
	 */
	function getBasename()
	{
		return $this->Info["basename"];
	}
	
	/**
	 * Return Full File path
	 * 
	 * @return String
	 */
	 function getFullPath()
	 {
	 	return $this->Path;
	 }
	
	/**
	* Return File content as String
	*
	* @return String
	*/
	 function toString()
	 {
		if ($this->Lock())
		{
			$result=fread($this->File,$this->sizeof());
			$this->Unlock();
		}else
		{
			$result=false;
		}
		return $result;
	 }
	
	/**
	* Return file resource handler
	*
	*@return Resource
	*/
	function toHandler()
	{
		return $this->File;
	}
	
	/**
	* Get content of file as Array
	*
	* @return Array
	*/
	function toArray()
	{
		return file($this->Path);
	}
	
	/**
	* Return file content as callable
	*
	* @return Callable
	*/
	function toCallable()
	{
		return include($this->Path);
	}
}