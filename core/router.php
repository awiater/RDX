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

use RDX\Core\Helpers\Strings as Str;
use RDX\Core\Helpers\Arrays as Arr;
use RDX\Core\Helpers\Collection as Factory;

class Router
{
	/**
	 * @var RDX\Core\Helpers\Collection Collection of routes
	 */
	private $routes;
	
	/**
	 * @var RDX\Core\Helpers\Collection Framework configuration
	 */
	private $config;
	
	/**
	 * @var Array Errors definitions
	 */
	private $errors=[
						0=>'Invalid route object',
						1=>'Invalid method'						
					];
	/**
	 * @var RDX\Core\Loader Framework elements loader (init class)
	 */
	private $loader;
	
	/**
	 * @var stdClass url parsers
	 */
	private $urlparser;
	
	/**
	 * Framework routing class
	 * 
	 * @param RDX\Core\Helpers\Collection Framework configuration
	 */
	function __construct($config)
	{
		$this->config=$config;
		$this->loader=new Loader($config->appclass);
		$this->routes=new Factory();
		if(isset($config->routes))
		{
			$this->addRoutes($config->routes);
		}
	}
	
	/**
	 * Return route with given name
	 * 
	 * @param  String $routeName Route unique name
	 * @return Array|Null
	 */
	public function get($routeName)
	{
		if ($this->routes->has($routeName))
		{
			return $this->routes->{$routeName};
		}
		return null;
	}
	
	/**
	 * Sets url parser
	 * 
	 * @param String $patern           Patern for url creation
	 * @param String $params_delimiter Character which delimits url elements
	 */
	public function setUrlParser($patern,$params_delimiter=null)
	{
		$this->urlparser=(object)['patern'=>$patern,'delimiter'=>$params_delimiter];
	}
	
	/**
	 * Add new route
	 * 
	 * @param String $name  Name of route
	 * @param mixed  $value Route definition
	 */
	public function addRoute($name,$value)
	{
		if ($this->valid($value))
		{
			$this->routes->add($name,$value);
		}else
		{
			$this->throwError(0);	
		}
	}
	
	/**
	 * Add multiple routes at once
	 * 
	 * @param array $routes Routes definitions
	 */
	public function addRoutes(array $routes)
	{
		var_dump($routes);exit;
		if (!$this->valid($routes))
		{
			foreach ($routes as $route) 
			{
				if (!$this->valid($route))
				{
					$this->throwError(0);	
				}
			}
		}
		$this->routes->add('.',$routes);	
	}
	
	/**
	 * Parse url to route object
	 * 
	 * @param  String         $url Url string
	 * @return Array|stdClass      Route object
	 */
	private function parse($url)
	{
		if (is_array($url))
		{
			$surl='';
			foreach($url as $key=>$value)
			{
				$surl.='&'.$key.'='.$value;
			}
			$url=substr($surl,1);
		}

		if (Str::contains($url,'?'))
		{
			$url=substr($url,strpos($url,'?')+1);
		}
		
		if ($this->routes->has($url))
		{
			return $this->routes->get($url); 
		}
		
		
		foreach($this->routes->ToArray() as $key=>$val)
		{
			$key=str_replace('/', '([a-zA-Z]*)', $key);
			$key=preg_replace('/\$[0-9]\*/','([^.]*)',$key); 
			$key=preg_replace('/\$\*/','([^.]*)',$key); 
			$key=preg_replace('/\$[0-9]/','([a-zA-Z0-9]*)' ,$key);
			$key=preg_replace('/\$[0-9]/','([a-zA-Z0-9]*)' ,$key);
			$key='/'.$key.'/';
			
			preg_match($key,$url,$match);
			if (is_array($match)&&count($match)>1)
			{
				 	array_shift($match);
					if (is_callable($val))
					{
						return call_user_func_array($val,$match) ;
					}
					
					$key=[];
					for($i=1;$i<=count($match);$i++)
					{
						$key[]='$'.$i;
					}
					
					$val=is_array($val)?$val:[$val];
					$arr=[];
					
					foreach($val as $k=>$v)
					{
						if (is_array($v))
						{
							if ($match[$v['ind']-1]!=null&&Str::contains($match[$v['ind']-1],$v['sep']))
							{
								$arr[$k]=explode($v['sep'], $match[$v['ind']-1]);
								$akeys=array_keys($arr[$k]);
								unset($arr[$k][$akeys[0]]);
								$arr[$k]=array_values($arr[$k]);
								if (isset($v['ksep'])&&Str::contains($match[$v['ind']-1],$v['ksep']))
								{
									$akeys=[];
									foreach ($arr[$k] as $svalue) 
									{
										$kk=explode($v['ksep'],$svalue);
										$akeys[$kk[0]]=$kk[1];
									}
									$arr[$k]=$akeys;
								}
							}
						}else
						{
							$arr[$k]=str_replace($key,$match,$v);
						}
					}
					return $arr;
			}
		}
		return null; 
	}
	
	/**
	 * Compile given route object
	 * 
	 * @param  Array|stdClass $route Route object
	 * @return mixed
	 */
	public function Compile($route)
	{
		$route=$this->parse($route); 
		
		if (!$this->valid($route))
		{
			return is_array($route)?(object)$route:$route;
		}
		
		$route=(object)$route; 
		
		$route->controller=$this->loader->Parse($route->controller,'controller');
		return $route;
	}
	
	/**
	 * Creates url (a href link)
	 * 
	 * @param  String  $controller Controller name
	 * @param  String  $action     Controller action name
	 * @param  Array   $params     Optional action arguments
	 * @param  Boolean $host       Optional determine if include host address in url
	 * @return String
	 */
	public function url($controller,$action,$params=null,$host=FALSE)
	{
		$host=$host==TRUE?$this->config->hostaddr.(CONFIG_SEOURL=='0'?$this->config->index:null):null;
		if ($this->urlparser==null)
		{
			throw new \Exception('Url parser not set', 1);
			
		}
		$url='';
		if (is_callable($this->urlparser->patern))
		{
			$url.=call_user_func_array($this->urlparser->patern, array($controller,$action,$params));
		}else
		if(is_array($this->urlparser->patern)&&count($this->urlparser->patern)>1)
		{
			if (is_object($this->urlparser->patern[0])&&is_string($this->urlparser->patern[1]))
			{
				$url.=call_user_func_array($this->urlparser->patern, array($controller,$action,$params));
			}else
			{
				$url=null;
			}
		}else
		if (is_string($this->urlparser->patern)&&Str::contains($this->urlparser->patern,':controller:')&&Str::contains($this->urlparser->patern,':action:')&&Str::contains($this->urlparser->patern,':args:'))
		{
			if (is_array($params))
			{
				$args=[];
				foreach ($params as $key => $value) 
				{
					if (is_numeric($key))
					{
						$args[]=$value;
					}else
					{
						$args[]=$key.$this->urlparser->delimiter.$value;
					}	
				}
				$args=implode($this->urlparser->delimiter, $args);
			}
			$url.=str_replace( [':controller:',':action:',':args:'], [$controller,$action,$args],$this->urlparser->patern);
		}else
		{
			$url=null;
		}
		if ($url==null)
		{
			throw new \Exception('Invalid url parser', 1);
			
		}
		$url=str_replace('://', ':#', $host.$url);
		$url=str_replace('//', '/', $url);
		$url=str_replace(':#', '://', $url);
		return $url;
	}
	
		
	/**
	 * Throw route exception
	 */
	function throwError(int $errorID)
	{
		throw new \Exception($this->errors[$errorID]);
	}
	
	/**
	 * Check if route is valid
	 * 
	 * @param  mixed   $route
	 * @return Boolean
	 */
	function valid($route)
	{
		if (is_callable($route))
		{
			return true;
		}
		if (is_array($route)&&array_key_exists('controller', $route)&&array_key_exists('action', $route))
		{
			return true;
		}
		if (is_array($route)&&!Arr::hasStringKeys($route))
		{
			return $this->valid(array_flip($route)); 
		}
		return false;		
	}
}
?>