<?php
/**
 * Librarium JSON Flat File Database
 * 
 * Flat File Database Manipulation Script
 *  
 * @version: 1.0	Release Date: 03/2016					
 * @author Artur W. <arturwiater@gmail.com>				
 * @copyright Copyright (c) 2020 All Rights Reserved				
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Librarium
{
	/* Path to dir where database files will be stored*/
	private $dbDir;
	
	/* Array holding all documents data */
	private $docs=array();
	
	/* Last error description */
	public $LastError;
	
	/**
	 * Constructor class
	 * @param String $dbdir path to dir where database files will be stored
	 */
	function __construct($dbdir)
	{
		$this->dbDir=$dbdir;
		$this->getDB();
	}
	
	/**
	 * Search for records in document (table)
	 * @param String $name name of document (table)
	 * @param Array $fields optional array with values as tabs name to be fetch (column names) from document
	 * @param Array $where optional array contains logic to filter fetched data
	 * @return Array|null Return Array or null if failed
	 */
	function selectData($name,array $fields=null,array $where=null)
	{
		
		$name=$this->getDocument($name);
		if ($name==null)
		{
			$this->LastError='Invalid document object';
			return null;			
		}
		$fields=$fields==null?$name->tabs:$fields;
		$fields=count($fields)==0?$name->tabs:$fields;	
		
		$results=array();
		if (is_array($fields)||is_array($where))
		{
			foreach($name->items as $row)
			{
				
				if (is_array($where)&&count($where)>0)
				{
					if ($this->isWhere($row, $where))
					{
						$results[]=array_intersect_key ($row ,array_flip ($fields));
					}
				}else
				{
					$results[]=array_intersect_key ($row ,array_flip ($fields));
				}
			}
		}
		
		return count($results)>0?$results:null;
	}
	
	/**
	 * Update data in document (table)
	 * @param String $name name of document (table)
	 * @param Array $data array with data to be changed, keys as tab names (column names) and values as values
	 * @param Array $where array contains logic to filter fetched data
	 * @return Boolean Return true or false if failed
	 */
	function updateData($name,array $data,array $where)
	{
		$result=false;
		$doc=$this->getDocument($name);
		
		if ($doc==null)
		{
			$this->LastError='Invalid document object';
			return false;
		}
		
		$source=$doc->items;
		
		for($i=0;$i<count($source);$i++)
		{
			$row=$source[$i];
			
			if($this->isWhere($row,$where==null?[]:$where))
			{
				$source[$i]=array_replace($source[$i],$data); 
				$result=true;
			}
		}
		$doc->items=$source;
		$this->setDocument($name,$doc);
		return $result;
	}
	
	/**
	 * Delete data from document (table)
	 * @param String $name name of document (table)
	 * @param Array $where array contains logic to filter fetched data
	 * @return Boolean Return true or false if failed
	 */
	function deleteData($name,array $where)
	{
		$result=false;
		$doc=$this->getDocument($name);
		if ($doc==null)
		{
			$this->LastError='Invalid document object';
			return false;
		}
		
		$source=$doc->items;
		
		for($i=0;$i<count($source);$i++)
		{
			$row=$source[$i];
			
			if($this->isWhere($row,$where))
			{
				unset($source[$i]); 
				$result=true;
			}
		}
		$doc->items=$source;
		$this->setDocument($name,$doc);
		return $result;
	}
	
	/**
	 * Insert new data to document (table)
	 * @param String $name name of document (table)
	 * @param Array $data array with data to be changed, keys as tab names (column names) and values as values
	 * @return Boolean Return true or false if failed
	 */
	public function insertData($name,array $data)
	{
		$doc=$this->getDocument($name);
		if ($doc==null)
		{
			$this->LastError='Invalid document object';
			return false;
		}
		$this->LastError=null;
		if (count(array_diff(array_keys($data),$doc->tabs))>0)
		{
			throw new \Exception('Invalid tabs names');
		}
		
		if (!array_key_exists('id',$data))
		{
			$data['id']=date('Ymdhis');
		}
		$doc->items[]=$data;
		$this->setDocument($name,(array)$doc);
		return true;
	}
	
	/**
	 * Create new document (table)
	 * @param String $name name of document (table)
	 * @param Array $fields array with values as document tabs names (column names) used to validation
	 * @return Boolean Return true or false if failed
	 */
	function createDocument($name,array $fields)
	{
			if (array_key_exists($name,$this->docs))
			{
				$this->LastError='Document name already exists';
				return false;
			}
			if (!array_key_exists('id', array_flip($fields)))
			{
				$fields[]='id';
			}
			$data=array('tabs'=>$fields,'name'=>$name,'items'=>array());
			$this->docs[$name]=rand().'.json';
			$this->setDocument($name,$data);
			$this->setDB();
			return true;
	}
	
	/**
	 * Delete document (table) from database repository
	 * @param String $name name of document (table)
	 * @return Boolean Return true or false if failed
	 */
	function deleteDocument($name)
	{
			if (!array_key_exists($name,$this->docs))
			{
				$this->LastError='Document not exists in repository';
				return false;
			}
			unlink($this->dbDir.'/'.$this->docs[$name]);
			unset($this->docs[$name]);
			$this->setDB();
			return true;
	}
	
	/**
	 * Compile filter logic against given array
	 * @param Array $source source array to compile against
	 * @param Array $where array contains filter logic
	 * @return Boolean Return true or false if failed
	 */
	private function isWhere(array $source, array $where)
	{
		
		$result='';
		foreach($where as $key)
		{
			if (array_key_exists($key->name, $source))
			{
				if ($key->sign=='>'&&$source[$key->name]>$key->value)
				{
					$result.='1'.strtoupper($key->joiner);
				}else
				if ($key->sign=='<'&&$source[$key->name]<$key->value)
				{
					$result.='1'.strtoupper($key->joiner);
				}else
				if ($key->sign=='<='&&$source[$key->name]<=$key->value)
				{
					$result.='1'.strtoupper($key->joiner);
				}else
				if ($key->sign=='>='&&$source[$key->name]>=$key->value)
				{
					$result.='1'.strtoupper($key->joiner);
				}else
				if ($key->sign=='='&&$source[$key->name]==$key->value)
				{
					$result.='1'.strtoupper($key->joiner);
				}else
				if (strtolower($key->sign) === 'like' && preg_match('/'.$key->value.'/is',$source[$key->name]))
				{
					$result.='1'.strtoupper($key->joiner);
				}else
				{
					$result.='0'.strtoupper($key->joiner);
				}
				
			}else
			{
				return false;
			}			
		}	
		if (strpos($result,'0AND')!==false||strpos($result,'AND0')!==false)
		{
			return false;
		}
		return (int)$result>0;
	} 
	
	/**
	 * Load document (table) from file
	 * @param String $name name of document (table)
	 * @return StdClass|Boolean Return document or false if failed
	 */
	private function getDocument($name)
	{ 
		if (!array_key_exists($name,$this->docs))
			{
				$this->LastError='Invalid document name';
				return null;
			}
			$name=$this->dbDir.'/'.$this->docs[$name];
			if (!file_exists($name))
			{
				throw new \Exception('Invalid document file');
			}
			$file=fopen($name,'r');
			flock($file,LOCK_EX);
			$data=json_decode(file_get_contents($name));
			flock($file,LOCK_UN);
			$data->items=json_decode(json_encode($data->items),true);
			return $data;
	}
	
	/**
	 * Save document (table) to file
	 * @param String $name name of document (table)
	 * @return Boolean Return true or false if failed
	 */
	private function setDocument($name,$data)
	{
			if (!array_key_exists($name,$this->docs))
			{
				$this->LastError='Invalid document name';
				return false;
			}
			$name=$this->dbDir.'/'.$this->docs[$name];
			$file=fopen($name,'w');
			flock($file,LOCK_EX);
			file_put_contents($name,json_encode($data));
			flock($file,LOCK_UN);
			return true;
	}
	
	/**
	 * Load databse config from file
	 * @return StdClass|Boolean Return database config or false if failed
	 */
	private function getDB()
	{
		$name=$this->dbDir.'database.json';
		if (file_exists($name))
		{
			$this->docs=json_decode(file_get_contents($name),true);
		}
	}
	
	/**
	 * Save databse config to file
	 */
	private function setDB()
	{
		$name=$this->dbDir.'database.json';
		file_put_contents($name,json_encode($this->docs));
	}
}
?>