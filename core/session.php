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

use RDX\Core\Helpers\Strings as Strings;
use RDX\Core\Helpers\Collection as Factory;

class Session
{
	/**
	 * @var RDX\Core\Helpers\Collection Config collection
	 */
	protected $factory;
	
	/**
	 * Session manipulation class
	 * 
	 * @param RDX\Core\DB $dbEngine Database object
	 * @param Array       $config   Session config
	 */
	function __construct(DB $dbEngine,$config)
	{
		$this->factory=new Factory();
		$this->factory->add('db',$dbEngine);
		if (is_dir($config))
		{	
			$config=new Session\FileStorage($config);
		}else
		if ($config!=null)
		{
			$config=new Session\DBStorage($dbEngine);	
		}
		
		if ($config!=null)
		{
			$this->factory->add('handler',$config);

			session_set_save_handler($config);
		}
	}
	
	/**
	 * Set session param
	 * 
	 * @param String $param Param name
	 * @param mixed  $value Param value
	 */
	public function set($param,$value)
	{
		if ($this->isStarted())
		{
			$_SESSION[$param]=$value;
		}else
		{
			throw new \Exception('Session not started');
		}
	}
	
	/**
	 * Get session param(s)
	 * 
	 * @param  String $param Optional param name, if null all params will be returned
	 * @return mixed
	 */
	public function get($param=null)
	{
		if ($param==null)
		{
			return $_SESSION;
		}
		if ($this->isStarted()&&array_key_exists($param, $_SESSION))
		{
			return	$_SESSION[$param];
		}
		return null;
	}
	
	/**
	 * Clear session or session param
	 * 
	 * @param Strimg $param Optional if set only given param will be removed from session, if not all session will be destroyed
	 */
	public function clear($param=null)
	{
		if ($this->isStarted()&&array_key_exists($param, $_SESSION))
		{
			unset($_SESSION[$param]);
		}else
		if ($this->isStarted())
		{
			session_unset();
		}
	}
	
	/**
	 * Start session
	 */
	public function start()
	{
		if (!$this->isStarted())
		{
			session_start();
		}
	}
	
	/**
	 *  Determine if given key exists in session array or if session array is not empty
	 * 
	 *  @param  String   $key Name of key to check if null whole array will be check
	 *  @return Boolean       True or False 
	 */
	public function is($key=null)
	{
		if (!$this->isStarted())
		{
			return false;
		}
		if ($key==null)
		{
			return count($_SESSION)>0;
		}else
		{
			return is_array($_SESSION)&&array_key_exists($key, $_SESSION);
		}
	}
	
	/**
	 * Determine if session is started or not
	 * 
	 * @return Boolean
	 */
	protected function isStarted()
	{
		return	session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
	}
	
	/**
	 * Return collection item value
	 * 
	 * @param  String $param Item name
	 * @return mixed         Item value
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
