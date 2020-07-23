<?php
/**
 *  RDX Framework Package
 *   
 *   Main Framework File
 * 
 * 
 *  @version: 1.1					
 *  @author Artur W. <arturwiater@gmail.com>				
 *  @copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core;

use RDX\Core\Helpers\Collection as Factory;
use RDX\Core\Helpers\Files as Files;
use RDX\Core\Helpers\Arrays as Arr;
use RDX\Core\Storage as Storage;
use RDX\Core\Helpers\Strings as Str;
use RDX\Core\Response;
use RDX\Core\Request;


define('QUERY_VARCHAR','varchar');
define('QUERY_INT','int');
define('QUERY_PRIMARY','primary');
define('QUERY_UNIQUE','unique');


class Core
{	
	private $factory;
	
	static function init($config)
	{
		return new self($config);
	}
	
	function  __construct($config)
	{
		require_once(COREDIR.'autoloader.php');
		require_once(COREDIR.'helpers/collection.php');
		$this->factory=new Factory();
		$this->setConfig($config);
		
		Autoloader::init($this->config->has('loader')?$this->config->loader:[]);
		
		$this->initFactory();
	}
	
	function __get($param)
	{
		if ($this->factory->has($param))
		{
			return $this->factory->{$param};
		}	
	}
	
	function __set($param,$value)
	{
		if ($this->factory!=null && $this->factory->has($param))
		{
			$this->factory->add($param,$value);
			
			if ($param=='config')
			{
				$this->initFactory(); 
			}
		}	
	}
	
	private function initFactory()
	{
		$this->factory->add('template',new Engine\Template($this->config));
		$this->factory->add('html',new Engine\Html($this->template));
		$this->factory->add('request',new Request());
		$this->factory->add('router',new Router($this->config));
		$this->factory->add('response',new Response());
		$this->factory->add('db',new db($this->config->db));
		$this->factory->add('session',new Session($this->db,$this->config->session));
		$this->factory->add('lang',new Engine\Language($this->config->lang));
		$this->factory->add('loader',new Loader($this->factory));
	}
	
	/**
	 *  Add custom value to app factory
	 * 
	 *  @param String $name Name of variable
	 *  @param Object $value Value of variable
	 */
	public function addFactoryVar(string $name,$value)
	{
		$this->factory->add($name,$value);	
	}
	
	public function registerLoader($loader)
	{
		spl_autoload_register($loader);
	}
	
	public function setConfig($cfg,$value=null)
	{
		if (!$this->factory->has('config'))
		{
			$this->factory->add('config',new Factory());
		}
		if ($this->config->has($cfg))
		{
			$this->config->add($cfg,$value);
			$this->initFactory();
		}else
		if (is_array($cfg))
		{
			$this->factory->config->addRange($cfg);
		}else
		if (file_exists($cfg))
		{
			require($cfg);
			$this->factory->config->addRange($cfg);
		}	
	}
	
	
	public function registerExceptionHandler( $excFunc)
	{
		set_exception_handler($excFunc);
	}
	
	public function Run()
	{
		if ($this->config!=null)
		{
			$this->session->start();
			$request=new Request();  
			$cRoute=$this->router->compile($request->get());
			$cRoute=$this->compileAction($cRoute);
			$cRoute->response->sendHeader();
			if($cRoute-> response->isJson())
			{
				echo json_encode($cRoute-> view->data);
			}else
			{
				echo $this->template->Render($cRoute->view);
			}
			
		}else
		{
			throw new \Exception('Config not set');
		}
	}
	
	/**
	 *  Compile given route object (string, object, array)
	 * 
	 *  @param Object|String|Array $route
	 *  @param Boolean $isadmin
	 *  @return Object
	 */
	function compileRoute($route,$isadmin=false)
	{
		if (is_array($route)||is_object($route))
		{
			$route=is_array($route)?(object)$route:$route;
			if (!Str::contains($route->controller,'\\'))
			{
				$controller=ucwords($route->controller);
				$route->controller=strtoupper($this->config->appname).'\\'.$controller.'\\'.($isadmin?'Admin\\':'').'Controllers\\'.$controller.'Controller';
			}
			return $route;
		}else
		if (is_string($route))
		{
			return $this->router->Compile($route);
		}
	}
	/**
	 * 
	 */
	
}
?>