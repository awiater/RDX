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

use RDX\Core\Helpers\Collection as Factory;

class Model
{
	/**
	 * Collection (RDX\Core\Helpers\Collection)
	 */
	protected $factory;
	
	/**
	 * Model base class
	 * 
	 * @param RDX\Core\Helpers\Collection $factory Collection of class config
	 */	
	function __construct(Factory $factory)
	{
		$this->setConfig($factory);
	}
	
	/**
	 * Set model collection
	 * 
	 * @param RDX\Core\Helpers\Collection $factory Model config collection
	 */
	public function setConfig(Factory $factory)
	{
		$this->factory=$factory;
		$db=new \RDX\Core\DB($this->config->db);
		$this->factory->add('db',$db);
		$this->factory->add('query',$db->query());
		$this->factory->add('ddl',$db->query(true));
		$this->factory->add('prefix',$this->config->table_prefix);
	}
	
	/**
	 * Model collection items getter
	 * 
	 * @param  String $param  Item name
	 * @return String     Item value
	 */
	function __get($param)
	{
		if ($this->factory!=null&&$this->factory->has($param))
		{
			return $this->factory->{$param};
		}
	}
}
?>