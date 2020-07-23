<?php
/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework  
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core\Engine;

use RDX\Core\Helpers\Strings as Strings;
use RDX\Core\Helpers\Collection as Factory;

class View
{
	/**
	 * Collection (RDX\Core\Helpers\Collection)
	 */	
	private $factory;
	
	/**
	 * View data (Array)
	 */
	public $data;
	
	/**
	 * View base class
	 */
	public function __construct()
	{
		$this->factory=new Factory();
		$this->data=[];
		$this->factory->add('file','');
		$this->factory->add('response',new \RDX\Core\Response());
	}
	
	/**
	 * View collection items getter
	 * 
	 * @param  String $key Item name
	 * @return Object      Value of item (if item exists) 
	 */
	function __get($key)
	{
		if ($this->factory->has($key))
		{
			return $this->factory->{$key};
		}else
		{
			throw new \Exception('Invalid class parameter');
		}
	}
	
	/**
	 * View collection items setter
	 * 
	 * @param String $key   Item name
	 * @param Object $value Item value
	 */
	function __set($key,$value)
	{
		if ($this->factory->has($key))
		{
			$this->factory->add($key,$value);
		}else
		{
			throw new \Exception($key.' is not valid class parameter');
		}
	}
	
	/**
	 * Returns view file path
	 * 
	 * @return String Path to view file
	 */
	public function getFile()
	{
		return $this->file;
	}
	
	/**
	 * Set view file path
	 * 
	 * @param String $file Path to view file (must be TWIG valid)
	 */
	public function setFile($file)
	{
		if(!Strings::endsWith($file,'.twig'))
		{
			$file.='.twig';
		}
		$this->file=$file;
	}
	
	/**
	 * Set view data
	 * 
	 * @param Array $data Array with view data
	 */
	public function setData(array $data)
	{
		$this->data=$data;
	}
	
	/**
	 * Merge view data with given array
	 * 
	 * @param Array $data Array with new view data
	 */
	public function mergeData(array $data)
	{
		if (!is_array($this->data))
		{
			$this->data=[];
		}
		$this->data=array_merge($this->data,$data);
	}
	
	
}
?>