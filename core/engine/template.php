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

use RDX\Core\Helpers\Files as Files;
use RDX\Core\Helpers\Collection as Factory;
use RDX\Core\Helpers\Strings as Str;

class Template
{
	/**
	 * Collection (RDX\Core\Helpers\Collection)
	 */
	private $factory;
	
	/**
	 * Templates collection (Array)
	 */
	private $templates=[];
	
	/**
	 * Template parser
	 * 
	 * @param Array|RDX\Core\Helpers\Collection $config Template config collection
	 */
	function __construct($config)
	{
		$this->factory=new Factory();
		$this->appDir=str_replace('//','/',$config->AppDir.'/');
		$cache=$config->cacheDir;
		$this->factory->add('config',[
		'autoescape' => false,
		'debug' => false,
		'auto_reload' => true,	
		'cache' =>is_dir($cache) ?$cache:false	]);
		$this->factory->add('html',new Html($this));
		if ($config->has('templates')&&is_array($config->templates))
		{
			foreach ($config->templates as $key => $value) 
			{
				$this->templates[$key]=Files::getFileBody($value);
			}
		}
	}
	
	/**
	 * Template collection items getter
	 * 
	 * @param  String $param Item name
	 * @return Object        Item value
	 */
	function __get($param)
	{
		if ($this->factory!=null&&$this->factory->has($param))
		{
			return $this->factory->{$param};
		}
	}
	
	/**
	 * Template collection items setter
	 * 
	 * @param String $param Item name
	 * @param String $value Item value
	 */
	function __set($param,$value)
	{
		if ($this->factory!=null&&$this->factory->has($param))
		{
			$this->factory->add($param,$value);
		}
	}
	
	/**
	 * Render view object to pure HTML
	 * 
	 * @param  RDX\Core\Engine\View $view View object
	 * @return String          Parsed view as HTML   
	 */
	function Render(View $view)
	{
		$file=$view->getFile();
		$tpl=Files::getFileBody($file);
		if ($tpl!=false)
		{
			return $this->RenderString($view->file,$tpl,$view->data);
		}else
		{
			throw new \Exception('Invalid Template File');
		}
	}
	
	/**
	 * Render view file to pure HTML
	 * 
	 * @param  String $file  Path to view file (TWIG file)
	 * @param  Array  $data  View data array
	 * @return String        Parsed view file as HTML
	 */
	function RenderFile($file,array $data)
	{
		return $this->RenderString($file,Files::getFileBody($file),$data);
	}
	
	/**
	 * Render string to pure HTML
	 * 
	 * @param  String $name  Name of view file (object)
	 * @param  String $body  Body of view file
	 * @param  Array  $data  View data array
	 * @return String $name  Parsed string as HTML
	 */
	function RenderString($name,$body,array $data)
	{
		$templates=$this->templates;
		
		if (!array_key_exists($name, $templates))
		{
			$templates[$name]=$body;
		}
		
				
		$loader = new \Twig\Loader\ArrayLoader($templates);
		$twig = new \Twig\Environment($loader, $this->config);
		return $twig->render($name, $data);
	}
	
	
	
}
?>