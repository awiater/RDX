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
 
namespace RDX\Core\Session;

final class DBStorage implements  \SessionHandlerInterface
{
	/**
	 * @var Integer Time after which cache will expire 
	 */
	public $expire = 3600;
	
	/**
	 * @var \RDX\Core\Storage Storage object
	 */
	private $db;
	
	/**
	 * Database session handler class
	 * 
	 * @param  \RDX\Core\Storage $db Storage object
	 */
	public function __construct(\RDX\Core\Storage $db) 
	{
		$this->db = $db;
		
		$this->expire = ini_get('session.gc_maxlifetime');
	}
	
	/**
	 * Read session data
	 * 
	 * @param  String $session_id The session id
	 * @return String|Boolean
 	 */
	public function read($session_id) 
	{
		$query =$this->db->Query()
		->selectFrom('session')
		->addColumn('data')
		->equalWhere('session_id',$session_id,null,'AND')
		->higherWhere('expire',date('YmdHis'))
		->Compile();
		if ($query->Count()>0) {
			return json_decode($query->First()['data'], true);
		} else {
			return false;
		}
	}
	
	/**
	 * Write session data
	 * 
	 * @param  String $session_id The session id
	 * @param  String $data       The encoded session data
	 * @return Boolean
 	 */
	public function write($session_id, $data) 
	{
		if ($session_id) 
		{
			$data=$data?json_encode($data):'';
			$this->db->Query()
			->updateFrom('session')
			->addUpdateField('session_id',aaa)
			->addUpdateField('expire',date('YmdHis', time() + $this->expire))
			->addUpdateField('data',$data)
			->Compile();
		}
		
		return true;
	}
	
	/**
	 * Destroy a session
	 * 
	 * @param  String $session_id The session id
	 * @return Boolean
 	 */
	public function destroy($session_id) 
	{
		$this->db->Query()
		->deleteFrom('session')
		->equalWhere('session_id',$session_id)
		->Compile();
		return true;
	}
	
	/**
	 * Cleanup old sessions
	 * 
	 * @param  Int $expire Time after which session will expire 
	 * @return Boolean
 	 */
	public function gc($expire) 
	{
		if (ini_get('session.gc_divisor')) 
		{
			$gc_divisor = ini_get('session.gc_divisor');
		} else {
			$gc_divisor = 1;
		}

		if (ini_get('session.gc_probability')) 
		{
			$gc_probability = ini_get('session.gc_probability');
		} else {
			$gc_probability = 1;
		}

		if ((rand() % $gc_divisor) < $gc_probability) 
		{
			$this->db->Query()
			->deleteFrom('session')
			->lowerWhere('expire',date('YmdHis', time()))
			->Compile();
		}

		return true;
	}
}