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

class FileStorage implements  \SessionHandlerInterface
{
	/**
	 * @var String Path to session storage dir
	 */
	private $directory;
	
	/**
	 * File storage session handler class
	 * 
	 * @param String Path to session storage dir
	 */
	function __construct($directory)
	{
		if (!is_dir($directory))
		{
			throw new \Exception('Invalid session directory');
		}
		
		$this->directory=$directory;
	}
	
	/**
	 * Close the session
	 * 
	 * @return Boolean
 	 */
	public function close()
    {
        return true;
    }
	
	/**
	 * Initialize session
	 * 
	 * @param  String  $savePath The path where to store/retrieve the session
	 * @param  String  $sessionName The session name
	 * @return Boolean
 	 */
	public function open($savePath, $sessionName)
    {
        return true;
    }
	
	/**
	 * Read session data
	 * 
	 * @param  String $session_id The session id
	 * @return String|Boolean
 	 */
	public function read($session_id) 
	{
		$file = $this->directory . 'sess_' . basename($session_id);
		
		if (!is_file($file)) 
		{
			$this->write($session_id,[]);
		}
		
		return (string)@file_get_contents($file);

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
		$file = $this->directory . 'sess_' . basename($session_id);
		return file_put_contents($file, $data) === false ? false : true;
	}
	
	/**
	 * Destroy a session
	 * 
	 * @param  String $session_id The session id
	 * @return Boolean
 	 */
	public function destroy($session_id) {
		$file = $this->directory . 'sess_' . basename($session_id);

		if (is_file($file)) {
			unlink($file);
		}
	}
	
	/**
	 * Cleanup old sessions
	 * 
	 * @param  Int $maxlifetime Time after which session will expire 
	 * @return Boolean
 	 */
	public function gc($maxlifetime) 
	{
		if (ini_get('session.gc_divisor')) {
			$gc_divisor = ini_get('session.gc_divisor');
		} else {
			$gc_divisor = 1;
		}

		if (ini_get('session.gc_probability')) {
			$gc_probability = ini_get('session.gc_probability');
		} else {
			$gc_probability = 1;
		}

		if ((rand() % $gc_divisor) < $gc_probability) {
			$expire = time() - ini_get('session.gc_maxlifetime');

			$files = glob($this->directory . 'sess_*');

			foreach ($files as $file) {
				if (filemtime($file) < $maxlifetime) {
					unlink($file);
				}
			}
		}
	}
}
?>