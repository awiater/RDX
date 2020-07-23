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
 
namespace RDX\Core\ORM;

use RDX\Core\Helpers\Arrays as Arrays;

class MySqli implements EngineContract
{
	/**
	 * @var String MYSQL engine type
	 */
	protected $dbEngine='InnoDB';
	
	/**
	 * @var String MYSQL DB charset type
	 */
	protected $dbCharset='utf8';
	
	/**
	 * @var String Path to log file
	 */
	 protected $logFile;
	 
	 /**
	  * @var mysqli
	  */
	 protected $db;
	
	/**
	 * MYSQLi db engine class
	 * 
	 * @param Array $config Array with db configuration (ie. host, username)
	 */
	function __construct(array $config)
	{
		$this->db=new \mysqli($config['host'],$config['user'],$config['pass'],$config['database']);
		if (isset($config['dbengine']))
		{
			$this->dbEngine=$config['dbengine'];
		}
		if (isset($config['charset']))
		{
			$this->$dbCharset=$config['charset'];
		}
		$this->logFile=__DIR__.'/mysql.log';
	}
	
	/**
	 * Free resources
	 */
	function __destruct()
	{
		$this->db->close();
	}
	
	/**
	 * Compile query object to sql string
	 * 
	 * @param RDX\Core\ORM\Query|RDX\Core\ORM\SchemeQuery $query Query helper object
	 * @return Array|Integer
	 */
	public function Compile($query)
	{
		if (method_exists($this, $query->Type))
		{
			return call_user_func_array(array($this,$query->Type),array($query));
		}
	}
	
	/**
	 * Execute sql query
	 * 
	 * @param  String  $sql      SQL query as string
	 * @param  Array   $params   Optional array with query parameters
	 * @param  Boolean $israw    Optional determine if query returns results or not (raw)
	 * @param  Boolean $isscheme Optional determine if query is schema creation query
	 * @return Array|Integer
	 */
	protected function execute($sql,array $params=null,$israw=FALSE,$isscheme=false)
	{
		$this->Log($sql.Arrays::Flatten($params==null?[]:$params));
		if ($isscheme)
		{
			return $this->db->query($sql)===TRUE?TRUE:$this->db->error;
		}	
			
		$query=$this->db->prepare($sql);
		if ($query==false)
		{
			throw new \Exception('MYSQLI PREPARED FAILED');
		}
		
		if (is_array($params)&&count($params)>0)
		{
			$dbparams=[];
			$types=implode('',Arrays::Fill($params,'s'));
			$dbparams[]=&$types;
			foreach ($params as $key => $value) 
			{
				$dbparams[$key]=&$params[$key];
			}
			
			call_user_func_array(array($query,'bind_param'),$dbparams);
		}
		
		$query->execute();
		if ($israw)
		{
			return $this->db->affected_rows;
		}
		$array=$query->result_metadata();
		$query->store_result();
		$fields=array();
		if ($array!==false)
		{
			$array=$array->fetch_fields();
			foreach($array as $key=>$value)
			{
				$nkey=$value->name;
				$fields[$nkey]=&$array[$key]->name;
			}
		}else
		{
			return $query->affected_rows;
		}
		
		call_user_func_array(array($query,"bind_result"),$fields);
		
		//fetch result*/
		$result=array();
		$i=0;
		while($query->fetch())
		{
			foreach($fields as $key=>$value)
			{
				$result[$i][$key]=$value;
			}
			$i++;
		}
		$query->close();
		unset($array);
		unset($fields);
		
		return $result;
	}
	
	/**
	 * Delete given table
	 * 
	 * @param  String $table Table name
	 * @return Integer
	 */
	protected function drop($table)
	{
		$sql='DROP TABLE IF EXISTS `'.$table.'`';
		return $this->execute(sql,null,true);
	}
	
	/**
	 * Create new table
	 * 
	 * @param  mixed   $query Query object
	 * @return Integer
	 */
	protected function create($query)
	{
		$sql='CREATE TABLE `'.$query->From->tableA.'` (';
		$primary='';
		$unique=[];
		$auto=null;
		foreach($query->Fields as $field)
		{
			$sql.='`'.$field->name.'` '.$field->type.'('.$field->size.') NOT NULL';
			if ($field->primary)
			{
				if ($field->type=='int')
				{
					$auto='CHANGE `'.$field->name.'` `'.$field->name.'` '.$field->type.'('.$field->size.') NOT NULL AUTO_INCREMENT,';
				}
				$primary=$field->name;
			}
			if ($field->unique)
			{
				$unique[]=$field->name;
			}
			$sql.=',';
		}
		$sql=substr($sql, 0,strlen($sql)-1);
		$sql.=') ENGINE='.$this->dbEngine.' DEFAULT CHARSET='.$this->dbCharset.';';
		$result=$this->execute($sql,null,false,true);
		$sql='ALTER TABLE `'.$query->From->tableA.'` '.$auto;
		$sql.='ADD PRIMARY KEY (`'.$primary.'`)';
		foreach ($unique as $value) 
		{
			$sql.=',ADD UNIQUE KEY `'.$value.'` (`'.$value.'`)';	
		}
		
		$sql.=';';
		
		return $result&&$this->execute($sql,null,false,true);
	}
	
	/**
	 * Insert new record to table
	 * 
	 * @param  mixed   $query Query object
	 * @return Integer
	 */
	protected function insert($query)
	{
		$sql='INSERT INTO `'.$query->From->tableA.'` (	';
		$inserts=[];
		$values=[];
		$params=[];
		foreach($query->Fields as $item)
		{
			$inserts[]='`'.$item->name.'`';
			$values[]='?';
			$params[$item->name]=$item->value;
		}
		$sql.=implode(',',$inserts).') VALUES (';
		$sql.=implode(',',$values).');';
		return $this->execute($sql,$params,true);
	}
	
	/**
	 * Update record from table
	 * 
	 * @param  mixed   $query Query object
	 * @return Integer
	 */
	protected function update($query)
	{
		$sql='UPDATE `'.$query->From->tableA.'` SET ';
		$values=[];
		$params=[];
		foreach($query->Fields as $item)
		{
			$values[]='`'.$item->name.'` = ?';
			$params[$item->name]=$item->value;
		}
		$sql.=implode(',',$values).' ';
		if (is_array($query->Where)&&count($query->Where)>0)
		{
			$where=$this->getWhere($query->Where);
			$sql.=$where['sql'];
			$params=array_merge($params,$where['params']);
		}
		return $this->execute($sql,$params,true);
	}
	
	/**
	 * Delete record from table
	 * 
	 * @param  mixed   $query Query object
	 * @return Integer
	 */
	protected function delete($query)
	{
		$sql='DELETE FROM `'.$query->From->tableA.'`';
		$params=[];
		if (is_array($query->Where)&&count($query->Where)>0)
		{
			$where=$this->getWhere($query->Where);
			$sql.=$where['sql'];
			$params=$where['params'];
		}
		return $this->execute($sql,$params,true);
	}
	
	/**
	 * Select records from table
	 * 
	 * @param  mixed   $query Query object
	 * @return Array
	 */
	protected function select($query)
	{
		$sql='SELECT ';
		foreach($query->Fields as $field)
		{
			if ($field->option=='count')
			{
				$sql.='count(`'.$field->name.'`)';
			}else
			{
				$sql.='`'.$field->name.'`';
			}
			if (isset($field->alias))
			{
				$sql.=' as `'.$field->alias.'`';
			}
			$sql.=',';
		}
		if (count($query->Fields)<1)
		{
			$sql.=' * ';
		}
		
		$sql=substr($sql, 0,strlen($sql)-1);
		$sql.=' FROM `'.$query->From->tableA.'` ';
		if (isset($query->From->aliasA)&&$query->From->aliasA!=null)
		{
			$sql.=' as `'.$query->From->aliasA.'`';
		}	
		
		$params=[];
		if (is_array($query->Where)&&count($query->Where)>0)
		{
			$where=$this->getWhere($query->Where);
			$sql.=' '.$where['sql'];
			$params=$where['params'];
		}
		
		if ($this->isLimit($query))
		{
			$sql.=$this->getLimit($query);
		}
		
		if ($this->isOrder($query))
		{
			$sql.=$this->getOrder($query);
		}
		
		if ($this->isGrouped($query))
		{
			$sql.=$this->getGroup($query);
		}
		
		return $this->execute($sql,$params);
	}
	
	/**
	 * Convert where statements (filters) from Query object to SQL string and params
	 * 
	 * @param  Array $where Where statements array
	 * @return Array
	 */
	private function getWhere(array $where)
	{
		$sql='WHERE ';
		$params=[];
		foreach ($where as $item) 
		{
			
			$sql.=$item->prefix.'`'.$item->name.'`';
			$sql.=' '.$item->sign.' ?';
			$sql.=' '.$item->joiner.' '.$item->suffix;
			
			$item->alias=$item->alias==null?$item->name:$item->alias;
			$params[$item->alias]=$item->value;
		}
		return ['sql'=>$sql,'params'=>$params];
	}
	
	/**
	 * Create FROM statement
	 * 
	 * @param  String $table Table name
	 * @param  String $alias Table name alias
	 * @return String
	 */
	private function getTable($table,$alias)
	{
		$sql=' FROM `'.$table.'`';
		if (isset($alias)&&$$alias!=null)
		{
			$sql.=' as `'.$alias.'`';
		}	
		return $sql;
	}
	
	/**
	 * Convert limit statements (special) from Query object to SQL string
	 * 
	 * @param  RDX\Core\ORM\Query $query Query helper object
	 * @return String
	 */
	private function getLimit($query)
	{
		foreach ($query->Special as $key) 
		{
			if ($key->type=='limit')
			{
				$limit=$key->value;
			}
		}
		
		$sql='LIMIT ';
		
		if (is_array($limit))
		{
			$sql.=' '.$this->escape($limit[0]).','.$this->escape($limit[1]);
		}else
		{
			$sql.=' '.$this->escape($limit);
		}
		return $sql;
	}
	
	/**
	 * Convert order by statements (special) from Query object to SQL string
	 * 
	 * @param  RDX\Core\ORM\Query $query Query helper object
	 * @return String
	 */
	private function getOrder($query)
	{
		foreach ($query->Special as $key) 
		{
			if ($key->type=='orderby')
			{
				$value=$key->value;
			}
		}
		
		$sql=' ORDER BY ';
		if (!is_array($value))
		{
			$value=[$value];
		}
		foreach ($value as $key) 
		{
			$sql.='`'.$key.'`,';	
		}
		return substr($sql, 0,strlen($sql)-1);
	}
	
	/**
	 * Convert group by statements (special) from Query object to SQL string
	 * 
	 * @param  RDX\Core\ORM\Query $query Query helper object
	 * @return String
	 */
	private function getGroup($query)
	{
		foreach ($query->Special as $key) 
		{
			if ($key->type=='groupby')
			{
				$value=$key->value;
			}
		}
		
		$sql=' GROUP BY ';
		if (!is_array($value))
		{
			$value=[$value];
		}
		foreach ($value as $key) 
		{
			$sql.='`'.$key.'`,';	
		}
		return substr($sql, 0,strlen($sql)-1);
	}
	
	/**
	 * Escapes special characters in a string for use in an SQL statement
	 * 
	 * @param  String $value The string to be escaped
	 * @return String
	 */
	protected function escape($value)
	{
		return $this->db->real_escape_string($value);
	}
	
	private function isLimit($query)
	{
		foreach ($query->Special as $key) {
			if ($key->type=='limit'){return true;}
		}
		return false;
	}
	
	/**
	 * Determine if Query object have order statements
	 * 
	 * @param  RDX\Core\ORM\Query $query Query helper object
	 * @return Boolean
	 */
	private function isOrder($query)
	{
		foreach ($query->Special as $key) {
			if ($key->type=='orderby'){return true;}
		}
		return false;
	}
	
	/**
	 * Determine if Query object have group by statements
	 * 
	 * @param  RDX\Core\ORM\Query $query Query helper object
	 * @return Boolean
	 */
	private function isGrouped($query)
	{
		foreach ($query->Special as $key) {
			if ($key->type=='groupby'){return true;}
		}
		return false;
	}
	
	/**
	 * Add log to log file
	 * 
	 * @param string $logData Log data to add
	 */
	private function Log($logData)
	{
		if (file_exists(LOGFILE)&&in_array('mysqli', LOGOPTION))
		{
			$logData=Date('d-m-Y h:i:s').' - MYSQLI :'.$logData; 
			file_put_contents(LOGFILE, $logData.PHP_EOL , FILE_APPEND | LOCK_EX);
		}
	}
	
}
?>