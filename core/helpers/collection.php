<?php
/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework  
 * 
 *  Collection helper class
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core\Helpers;

use Strings as Str;

class Collection
{
	/**
	 * Data collection (Array)
	 */
	private $data=[];
	
	/**
	 * Collection helper class
	 * 
	 * @param Array $array Optional array with collection data 
	 */
	function __construct($array=null)
	{
		if (is_array($array))
		{
			$this->data=$array;
		}
	}
	
	/**
	 * Determine if given key exists in collection
	 * 
	 * @param  String  $key Key name
	 * @return Boolean      TRUE if exsists or FALSE if not
	 */
	public function has($key)
	{
		if(!is_numeric($key)&&!is_string($key))
		{
			return false;
		}
		return array_key_exists($key,$this->data);
	}
	
	/**
	 * Collection item value getter
	 * 
	 * @param  String $key Item name
	 * @return Object      Item value
	 */
	public function __get($key)
	{
		$key=$this->has($key)?$this->data[$key]:null;
		return $key;
	}
	
	/**
	 * Collection item value setter
	 * 
	 * @param String $key   Item name
	 * @param Object $value Item value
	 */
	public function __set($key,$value)
	{
		$this->add($key,$value);
	}
	
	/**
	 * Returns collection item value
	 * 
	 * @param  String $key Item name
	 * @return Object      Item value
	 */
	public function get($key)
	{
		if ($this->has($key))
		{
			return $this->data[$key];
		}else
		{
			throw new \Exception($key.' not exists in collection');
		}
	}
	
	/**
	 * Add new item to collection
	 * 
	 * @param String $key   Item name
	 * @param Object $value Item value
	 */
	public function add($key,$value)
	{
			$this->data[$key]=$value;
	}
	
	/**
	 * Add new items to collection
	 * 
	 * @param Array $value Array with new items
	 */
	public function addRange(array $value)
	{
			$this->data=array_merge($this->data,$value);
	}
	
	/**
	 * Convert items collection (Array) to object
	 * 
	 * @return stdClass
	 */
	public function toObject()
	{
		return (object)$this->data;
	}
	
	/**
	 * Returns items collection
	 * 
	 * @return Array
	 */
	public function toArray()
	{
		return $this->data;
	}
	
	/**
	 * Convert items collection (Array) to JSON string
	 * 
	 * @return String
	 */
	public function toJson()
	{
		return json_encode($this->data);
	}
}
?>