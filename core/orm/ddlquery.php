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

namespace RDX\Core\ORM;

use RDX\Core\Helpers\Strings as Str;

class SchemeQuery
{
	/**
	 * @var RDX\Core\ORM\EngineContract DB engine
	 */
	protected $engine;
	
	/**
	 * @var Array Fields collection
	 */
	public $Fields=[];
	
	/**
	 * @var Array Tables names collection
	 */
	public $From=[];
	
	/**
	 * @var String Query type (drop, create)
	 */
	public $Type;
	
	/**
	 * Schema creation query helper class
	 * 
	 * @param RDX\Core\ORM\EngineContract $engine DB engine
	 */
	function __construct(EngineContract $engine)
	{
		$this->engine=$engine;
	}
	
	/**
	 * Add table column
	 * 
	 * @param  String $name  Name of column
	 * @param  String $type  Type of column
	 * @param  String $size  Size of column
	 * @param  String $flags Optional column type flags
	 * @return RDX\Core\ORM\SchemeQuery
	 */
	public function addColumn($name,$type,$size,$flags='')
	{
		$this->Fields[]=(object)array
		(
			'name'=>$name,
			'type'=>$type,
			'size'=>$size,
			'primary'=>$flags!=null&&Str::contains($flags,QUERY_PRIMARY),
			'unique'=>$flags!=null&&Str::contains($flags,QUERY_UNIQUE)
		);
		return $this;
	}
	
	/**
	 * Create table delete query
	 * 
	 * @param  String $table Table name
	 * @return RDX\Core\ORM\SchemeQuery
	 */
	public function dropTable($table)
	{
		$this->Type='drop';
		$this->From=(object)array('tableA'=>$table);
		return $this;
	}
	
	/**
	 * Create table creation query
	 * 
	 * @param  String $table Table name
	 * @return RDX\Core\ORM\SchemeQuery
	 */
	public function createTable($table)
	{
		$this->Type='create';
		$this->From=(object)array('tableA'=>$table);
		return $this;
	}
	
	/**
	 * Compile query in engine
	 */
	public function Compile()
	{
		if (!$this->engine instanceof EngineContract)
		{
			throw new \Exception('Invalid engine class');
		}
		return $this->engine->Compile($this);
	}
}
?>