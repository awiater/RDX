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

use RDX\Core\Helpers\Collection as Factory;
use RDX\Core\Helpers\Strings as Str;

class Response
{
	/**
	 * @var String Default header
	 */
	private $header;
	
	/**
	 * @var RDX\Core\Helpers\Collection Collection of predefined headers
	 */
	private $headersArr;
	
	/**
	 * @var mixed Response data
	 */
	private $response;
	
	/**
	 * HTML response manipulation helper class
	 */
	function __construct()
	{
		$this->headersArr=new Factory();
		$this->headersArr->add('json','Content-Type: application/json');
		$this->headersArr->add('html','Conent-Type: text/html');
		$this->setHeader('html');
	}
	
	/**
	 * Returns predefined header by name
	 * 
	 * @param  String $header Header name
	 * @return String 
	 */
	public function getHeader($header)
	{
		if ($this->headersArr->has($header))
		{
			return $this->headersArr->{$header};
		}
		return null;
	}
	
	/**
	 * Check if given header or header name is valid
	 * 
	 * @param  String $header Header name or header definition
	 * @return Boolean
	 */
	public function validHeader($header)
	{
		return in_array($header, $this->headersArr->toArray())||$this->headersArr->has($header);
	}
	
	/**
	 * Send all registered headers to server
	 */
	public function sendHeader()
	{
		if (!headers_sent())
		{
			header($this->header,true);
		}
	}
	
	/**
	 * Set default header
	 * 
	 * @param String $value Header name or header definition
	 */
	public function setHeader($value)
	{
		if ($this->headersArr->has($value))
		{
			$this->header=$this->headersArr->{$value};	
		}else
		if ($this->validHeader($value))
		{
			$this->header=$value;
		}
		
	}
	
	/**
	 * Determine if default header is JSON header
	 * 
	 * @return Boolean
	 */
	public function isJson()
	{
		return Str::contains(strtolower($this->header),'json');
	}
	
	/**
	 * Redirect current content to new url
	 * 
	 * @param String $url Redirection url
	 * 
	 */
	public function redirect($url) 
	{		
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url), true, 302);		
		exit();	
	}
	
	/**
	 * Set response data
	 * 
	 * @param mixed  $response Response body data
	 * @param String $header   Optional header name or definition
	 */
	public function Set($response,$header='html')
	{
		if (!$this->validHeader($header))
		{
			throw new \RDX\Core\Exceptions\ResponseInvalidHeader();
		}
		
		$this->header=$header;
		$this->response=$response;
	}
	
	/**
	 *  Get response body and send headers if needed
	 *  
	 *  @param  Boolean $sendheader
	 *  @return Object 
	 */
	public function Get($sendheader=TRUE)
	{
		if ($sendheader&&$this->validHeader($this->header))
		{
			$this->sendHeader();
		}
		return $this->response;
	}
 }
?>