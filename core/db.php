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

use RDX\Core\Helpers\Arrays as Arr;

class  DB
{
	/**
	 * @var RDX\Core\ORM\EngineContract DB engine
	 */
	public $engine;
	
	/**
	 * @var Array Default config keys
	 */
	protected $configKeys=['engine'];
	
	/**
	 * Database manipulation class
	 * 
	 * @param mixed $config DB configurations
	 */
	function __construct($config)
	{
		$this->setEngine($config);
	}
	
	/**
	 * Returns RDX\Core\ORM\Query object for data records manipulation
	 * 
	 * @return RDX\Core\ORM\Query
	 */
	public function Query()
	{
		return new ORM\Query($this->engine);
	}
	
	/**
	 * Returns RDX\Core\ORM\SchemeQuery object for data collection manipulation (Create, Drop)
	 * 
	 * @return RDX\Core\ORM\SchemeQuery
	 */
	public function DDL()
	{
		return new ORM\DDLQuery($this->engine);
	}
	
	/**
	 * Sets database engine
	 * 
	 * @param mixed $config DB configurations
	 */
	public function setEngine($config)
	{
		if ($config['engine'] =='mysqli')
		{
			$this->engine=new ORM\MySqli($config);
		}
		
		if (!$this->engine instanceof ORM\ EngineContract)
		{
			throw new \Exception('Invalid engine class');
		}
	}
	
	
}
?>