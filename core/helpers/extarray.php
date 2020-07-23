<?php
/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework
 * 
 *  Arrays manipulation helper class  
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core\Helpers;

class ExtArray implements \ArrayAccess
{
	/**
	 * Data container (Array)
	 */
	private $container = [];
	
	/**
	 * Arrays manipulation helper class 
	 * 
	 * @param Array $data Input array
	 */
	function __construct($data)
	{
		if (is_array($data))
		{
			$this->container=$data;	
		}else
		{
			$this->container[]=$data;	
		}
		
	}
	
	/**
	 * Returns qty of items in collection
	 */
	public function Count()
	{
		return count($this->container);
	}
	
	/**
	 * Returns first item from collection
	 * 
	 * @param  Boolean             $asObject Determine if result will as array or stdClass
	 * @return Array|stdClass|Null
	 */
	public function First($asObject=false)
	{
		if (is_numeric($this->container))
		{
			return $this->container;
		}
		$itemindex=array_keys($this->container)[0];
		if (is_array($this->container)&&$this->Count()>0)
		{
			return $asObject?(object)$this->container[$itemindex]:$this->container[$itemindex];
		}else
		if (is_int($this->container))
		{
			return $this->container;
		}else
		{
			return null;
		}
	}
	
	/**
	 * Returns last item from collection
	 * 
	 * @param  Boolean             $asObject Determine if result will as array or stdClass
	 * @return Array|stdClass|Null
	 */
	public function Last($asObject=false)
	{
		$itemindex=array_keys($this->container)[$this->Count()-1];
		return $this->Count()>0?($asObject?(object)$this->container[$itemindex]:$this->container[$itemindex]):null;	
	}
	
	/**
	 * Convert collection to stdClass object
	 * 
	 * @return stdClass
	 */
	public function ToObject()
	{
		return (object)$this->container;
	}
	
	/**
	 * Return collection as array
	 * 
	 * @return Array
	 */
	public function ToArray()
	{
		return $this->container;
	}
	
	/**
	 * Sets the value of item at the specified index to newval
	 * 
	 * @param mixed $offset The index being set.
	 * @param mixed $value  The new value for the index.
	 */
	public function offsetSet($offset, $value) 
	{
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
	
	/**
	 * Determin whether an offset exists in collection
	 * 
	 * @param  mixed   $offset An offset to check for
	 * @return Boolean
	 */
    public function offsetExists($offset) 
    {
        return isset($this->container[$offset]);
    }
	
	/**
	 * Unsets the value of item at the specified index
	 * 
	 * @param mixed $offset The index being unset
	 */
    public function offsetUnset($offset) 
    {
        unset($this->container[$offset]);
    }
	
	/**
	 * Returns the value of item at the specified index
	 * 
	 * @param  mixed aa The index with the value
	 * @return mixed
	 */
    public function offsetGet($offset) 
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}
}
