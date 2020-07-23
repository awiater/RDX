<?php

/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework  
 * 
 *  Standard DB queries helper class
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core\ORM;

use RDX\Core\Helpers\Collection as Factory;

class Query
{
	/**
	 * @var Array Query fields collection
	 */
	public $Fields;
	
	/**
	 * @var Array Query filters collection
	 */
	public $Where;
	
	/**
	 * @var Array Query table names collection
	 */
	public $From;
	
	/**
	 * @var String Query type (select, distinct, update, insert, delete)
	 */
	public $Type;
	
	/**
	 * @var Array Tables relationship configuration
	 */
	public $Join;
	
	/**
	 * @var Array Special fields configuration
	 */
	public $Special;
	
	/**
	 * @var RDX\Core\ORM\EngineContract $engine DB engine
	 */
	protected $engine;
	
	/**
	 * Standard DB queries helper class
	 * 
	 * @param RDX\Core\ORM\EngineContract $engine DB engine class
	 */
	function __construct(EngineContract $engine)
	{
		$this->Fields=[];
		$this->Where=[];
		$this->Special=[];
		$this->From=new \stdClass;
		$this->engine=$engine;
	}
	
	/**
	 * Compile query using DB enngine
	 */
	public function Compile()
	{
		if (!$this->engine instanceof EngineContract)
		{
			throw new \Exception('Invalid engine class');
		}
		$result=new \RDX\Core\Helpers\ExtArray($this->engine->Compile($this));
		return $result;
	}
	
	/**
	 * Set query in read mode (select)
	 * 
	 * @param  String $table Table name
	 * @param  String $alias Optional table alias (must be set when using join)
	 * @return RDX\Core\ORM\Query
	 */
	public function selectData($table,$alias=null)
	{
		$this->addFrom($table,$alias,'select');
		return $this;
	}
	
	/**
	 * Set query in read mode for unique values (select distinct)
	 * 
	 * @param  String $table Table name
	 * @param  String $alias Optional table alias (must be set when using join)
	 * @return RDX\Core\ORM\Query
	 */
	public function uniqueData($table,$alias=null)
	{
		$this->addFrom($table,$alias,'distinct');
		return $this;
	}
	
	/**
	 * Set query in write mode for updating (update)
	 * 
	 * @param  String $table Table name
	 * @return RDX\Core\ORM\Query
	 */
	public function updateData($table)
	{
		$this->addFrom($table,null,'update');
		return $this;
	}
	
	/**
	 * Set query in write mode for inserting (insert)
	 * 
	 * @param  String $table Table name
	 * @return RDX\Core\ORM\Query
	 */
	public function addData($table)
	{
		$this->addFrom($table,null,'insert');
		return $this;
	}
	
	/**
	 * Add relationship configuration (Join left)
	 * 
	 * @param  Array $tableA  Array with table A information (name, alias)
	 * @param  Array $tableB  Array with table B information (name, alias)
	 * @param  String $colA   Name of column from table A
	 * @param  String $colB   Name of column from table B
	 * @return RDX\Core\ORM\Query
	 */
	public function joinData(array $tableA,array $tableB,$colA,$colB)
	{
		$this->Join=[
		'tableA'=>$tableA[0],
		'aliasA'=>$tableA[1],
		'tableB'=>$tableB[0],
		'aliasB'=>$tableB[1],
		'colA'=>$colA,
		'colB'=>$colB];
		return $this;
	}
	
	/**
	 * Set query in write mode for deleting (delete)
	 * 
	 * @param  String $table Table name
	 * @return RDX\Core\ORM\Query
	 */
	public function deleteData($table)
	{
		$this->addFrom($table,null,'delete');
		return $this;
	}
	
	/**
	 * Sets query type and table name
	 * 
	 * @param  String $table Table name
	 * @param  String $alias Optional table alias (must be set when using join)
	 * @return RDX\Core\ORM\Query
	 */
	protected function addFrom($table,$alias,$type)
	{
		$this->Type=$type;
		$this->From=(object)array('tableA'=>$table,'aliasA'=>$alias);
		$this->Fields=array();
		$this->Where=array();
		$this->Special=array();
	}
	
	/**
	 * Add field (column) to query fields collection
	 * 
	 * @param  String $name  Field name
	 * @param  String $alias Optional field alias
	 * @param  String $table Optional table name
	 * @return RDX\Core\ORM\Query
	 */
	public function addField($name,$alias=null,$table=null)
	{
		$table=$table==null?null:$table.".";
		$this->Fields[]=(object)array(
		'name'=>$name,
		'alias'=>$alias,
		'table'=>$table,
		'option'=>null);
		return $this;
	}
	
	/**
	 * Add count field (column) to query fields collection
	 * 
	 * @param  String $name  Field name
	 * @param  String $alias Optional field alias
	 * @param  String $table Optional table name
	 * @return RDX\Core\ORM\Query
	 */
	public function countField($name,$alias=null,$table=null)
	{
		$table=$table==null?null:$table.".";
		$this->Fields[]=(object)array(
		'name'=>$name,
		'alias'=>$alias,
		'table'=>$table,
		'option'=>'count');
		return $this;
	}
	
	/**
	 * Add insert field (column) to query fields collection
	 * 
	 * @param  String $name  Field name
	 * @param  mixed $value  Field value
	 * @param  String $alias Optional field alias
	 * @return RDX\Core\ORM\Query
	 */
	public function insertField($name,$value,$alias=null)
	{
		$this->Fields[]=(object)array(
		'name'=>$name,
		'value'=>is_array($value)||is_object($value)?json_encode($value):$value,
		'alias'=>$alias,
		'option'=>'insert');
		return $this;
	}
	
	/**
	 * Add update field (column) to query fields collection
	 * 
	 * @param  String $name  Field name
	 * @param  mixed $value  Field value
	 * @param  String $alias Optional field alias
	 * @return RDX\Core\ORM\Query
	 */
	public function updateField($name,$value,$alias=null)
	{
		$this->Fields[]=(object)array(
		'name'=>$name,
		'value'=>is_array($value)||is_object($value)?json_encode($value):$value,
		'alias'=>$alias,
		'option'=>'update');
		return $this;
	}
	
	/**
	 * Add special field (column) to limit results of read query
	 * 
	 * @param  mixed $limit  Limit config (Integer or Array)
	 * @return RDX\Core\ORM\Query
	 */
	public function addLimit($limit)
	{
		$this->Special[]=(object)array
		(
			'type'=>'limit',
			'value'=>$limit
		);
		return $this;
	}
	
	/**
	 * Add special field (column) to sort results of read query
	 * 
	 * @param  String $order  Field name used to sort
	 * @return RDX\Core\ORM\Query
	 */
	public function addOrderBy($order)
	{
		$this->Special[]=(object)array
		(
			'type'=>'orderby',
			'value'=>$order
		);
		return $this;
	}
	
	/**
	 * Add special field (column) to group results of read query
	 * 
	 * @param  mixed $order  Field name(s) used to group
	 * @return RDX\Core\ORM\Query
	 */
	public function addGroupBy($order)
	{
		$this->Special[]=(object)array
		(
			'type'=>'groupby',
			'value'=>$order
		);
		return $this;
	}
	
	/**
	 * Add equal fields filter (=)
	 * 
	 * @param  String $name       Field (Column) name
	 * @param  mixed  $value      Value to filter
	 * @param  String $alias  	  Optional alias for field name
	 * @param  String $AndOrNull  Optional filters joiner (AND or OR)
	 * @param  String $prefix     Optional prefix of filter
	 * @param  String $suffix     Optional suffix of filter
	 * @return RDX\Core\ORM\Query
	 */
	public function equalWhere($name,$value,$alias=null,$AndOrNull=null,$prefix=null,$suffix=null)
	{
		return $this->addWhere($name,'=',$value,$alias,$prefix,$suffix,$AndOrNull);
	}
	
	/**
	 * Add not equal fields filter (<>)
	 * 
	 * @param  String $name       Field (Column) name
	 * @param  mixed  $value      Value to filter
	 * @param  String $alias  	  Optional alias for field name
	 * @param  String $AndOrNull  Optional filters joiner (AND or OR)
	 * @param  String $prefix     Optional prefix of filter
	 * @param  String $suffix     Optional suffix of filter
	 * @return RDX\Core\ORM\Query
	 */
	public function notEqualWhere($name,$value,$alias=null,$AndOrNull=null,$prefix=null,$suffix=null)
	{
		return $this->addWhere($name,'<>',$value,$alias,$prefix,$suffix,$AndOrNull);
	}
	
	/**
	 * Add contains fields filter (LIKE)
	 * 
	 * @param  String $name       Field (Column) name
	 * @param  mixed  $value      Value to filter
	 * @param  String $alias  	  Optional alias for field name
	 * @param  String $AndOrNull  Optional filters joiner (AND or OR)
	 * @param  String $prefix     Optional prefix of filter
	 * @param  String $suffix     Optional suffix of filter
	 * @return RDX\Core\ORM\Query
	 */
	public function likeWhere($name,$value,$alias=null,$AndOrNull=null,$prefix=null,$suffix=null)
	{
		return $this->addWhere($name,'like','%'.$value.'%',$alias,$prefix,$suffix,$AndOrNull);
	}
	
	/**
	 * Add equal or higher fields filter (>=)
	 * 
	 * @param  String $name       Field (Column) name
	 * @param  mixed  $value      Value to filter
	 * @param  String $alias  	  Optional alias for field name
	 * @param  String $AndOrNull  Optional filters joiner (AND or OR)
	 * @param  String $prefix     Optional prefix of filter
	 * @param  String $suffix     Optional suffix of filter
	 * @return RDX\Core\ORM\Query
	 */
	public function equalOrHigherWhere($name,$value,$alias=null,$AndOrNull=null,$prefix=null,$suffix=null)
	{
		return $this->addWhere($name,'>=',$value,$alias,$prefix,$suffix,$AndOrNull);
	}
	
	/**
	 * Add equal or lower fields filter (<=)
	 * 
	 * @param  String $name       Field (Column) name
	 * @param  mixed  $value      Value to filter
	 * @param  String $alias  	  Optional alias for field name
	 * @param  String $AndOrNull  Optional filters joiner (AND or OR)
	 * @param  String $prefix     Optional prefix of filter
	 * @param  String $suffix     Optional suffix of filter
	 * @return RDX\Core\ORM\Query
	 */
	public function equalOrLowerWhere($name,$value,$alias=null,$AndOrNull=null,$prefix=null,$suffix=null)
	{
		return $this->addWhere($name,'<=',$value,$alias,$prefix,$suffix,$AndOrNull);
	}
	
	/**
	 * Add higher fields filter (>)
	 * 
	 * @param  String $name       Field (Column) name
	 * @param  mixed  $value      Value to filter
	 * @param  String $alias  	  Optional alias for field name
	 * @param  String $AndOrNull  Optional filters joiner (AND or OR)
	 * @param  String $prefix     Optional prefix of filter
	 * @param  String $suffix     Optional suffix of filter
	 * @return RDX\Core\ORM\Query
	 */
	public function higherWhere($name,$value,$alias=null,$AndOrNull=null,$prefix=null,$suffix=null)
	{
		return $this->addWhere($name,'>',$value,$alias,$prefix,$suffix,$AndOrNull);
	}
	
	/**
	 * Add lower fields filter (<)
	 * 
	 * @param  String $name       Field (Column) name
	 * @param  mixed  $value      Value to filter
	 * @param  String $alias  	  Optional alias for field name
	 * @param  String $AndOrNull  Optional filters joiner (AND or OR)
	 * @param  String $prefix     Optional prefix of filter
	 * @param  String $suffix     Optional suffix of filter
	 * @return RDX\Core\ORM\Query
	 */
	public function lowerWhere($name,$value,$alias=null,$AndOrNull=null,$prefix=null,$suffix=null)
	{
		return $this->addWhere($name,'<',$value,$alias,$prefix,$suffix,$AndOrNull);
	}
	
	/**
	 * Add fields filter
	 * 
	 * @param  String $name       Field (Column) name
	 * @param  mixed  $sign       Filter type (=, <>, LIKE, >=, <=, >, <)
	 * @param  mixed  $value      Value to filter
	 * @param  String $alias  	  Optional alias for field name
	 * @param  String $prefix     Optional prefix of filter
	 * @param  String $suffix     Optional suffix of filter
	 * @param  String $joiner     Optional filters joiner (AND or OR)
	 * @return RDX\Core\ORM\Query
	 */
	protected function addWhere($name,$sign,$value,$alias=null,$prefix=null,$suffix=null,$joiner=null)
	{
		$this->Where[]=(object)array(
		'name'=>$name,
		'value'=>$value,
		'alias'=>$alias,
		'sign'=>$sign,
		'joiner'=>$joiner,
		'prefix'=>$prefix,
		'suffix'=>$suffix);
		return $this;
	}
}
?>