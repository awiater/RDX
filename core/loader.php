<?php
/**
 *  RDX Framework Package
 *   
 *   Class Loader
 * 
 *  @version: 1.1					
 *  @author Artur W. <arturwiater@gmail.com>				
 *  @copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace RDX\Core;

use RDX\Core\Helpers\Strings as Str;
use RDX\Core\Helpers\Collection as Factory;

class Loader
{
	/**
	 * \RDX\Core\Helpers\Collection object
	 */
	private $factory;
	
	function __construct($factory)
	{
		
		if (is_string($factory))
		{
			
			$this->factory=new Factory();
			$this->factory->add('config',new Factory());
			$this->factory->config->add('appclass',$factory);
		}else
		if ($factory instanceof Factory)
		{
			$this->factory=$factory;
		}
		$this->factory->add('_quickaccess',new Factory());
		
	}
	
	/**
	 * Parse name to valid class name
	 * 
	 * @param  String  $class  Class name
	 * @param  String  $type   Object type (Controller, Model)
	 * @return String          Returns valid class name
	 */
	function Parse($class,$type)
	{
		if (Str::startsWith($class,'/'))
		{
			return str_replace(' ','\\', ucwords (str_replace('/',' ',substr($class,1))));
		}
		
		if (!Str::contains($class,'/'))
		{
			$class=ucfirst($class);  
			$class.='/'.$class;
		}
		
		
		$file=substr(strrchr($class,'/'),1);	
		if ($type==null)
		{
			$type=str_replace(['Model','Controller'],'', $file);
			$type=str_replace($type, '', $file);
		}
		$type=ucfirst($type);
		$types=$type.'s';	
		
		$dir=substr($class,0,strpos($class,'/'.$file));	
		
		$dir=str_replace('$dir',$dir,$this->factory->config->appclass);
		$dir=str_replace('$type',$types,$dir);		
		$dir=str_replace('$file',$file,$dir);
		$dir=str_replace([$type.$type,$type.'s/',$types.'/'.$types],[$type,'',$types],$dir);
		$dir=str_replace('/','\\',$dir);
		if (Str::endsWith($dir,'s'))
		{
			$dir=substr($dir,0,strlen($dir)-1);
		}
		return $dir;
	}
	
	/**
	 * Set quick access class object
	 * 
	 * @param String  $controller  Controller class name
	 * @param String  $action      Optional action (method|function) name
	 * @param Array   $args        Optional arguments array for action
	 */
	public function setQuickAccess($controller,$action=null,$args=null)
	{
		$_=['controller'=>$controller];
		if ($action!=null)
		{
			$_['action']=$action;
		}
		if ($args!=null)
		{
			$_['args']=$args;
		}
	}
	
	/**
	 * Create object and return action result or object
	 * 
	 * @param  String|Array  $class  Name of class (String) or route object (Array)
	 * @return Object                Returns result of action or object
	 */
	public function Load($class)
	{
		
		if ($this->factory->get('_quickaccess')->has($class))
		{
			$class=$this->factory->get('_quickaccess')->get($class);
		} else
		if (is_string($class))
		{
			$class=['controller'=>$this->Parse($class,null)];
		}
		$class=is_array($class)?(object)$class:$class;
		
		if (!class_exists($class->controller))
		{
		 	throw new \RDX\Core\Exceptions\LoaderInvalidClass($class->controller);	
		}
		
		if (Str::endsWith($class->controller,'Controller'))
		{
			$class->controller=new $class->controller($this->factory);
		}else
		if (Str::endsWith($class->controller,'Model'));
		{
			$class->controller=new $class->controller($this->factory );
		}
		
		$action=null;
		
		if ($class->controller!=null&&property_exists($class, 'action')&& $class->action!=null&&method_exists($class->controller, $class->action)&&property_exists($class, 'args')&&$class->args!=null)
		{
			if (is_array($class->args))
			{
				$action= call_user_func_array([$class->controller,$class->action], $class->args);
			}else
			{
				$action= call_user_func_array([$class->controller,$class->action], [$class->args]);
			}
		}else
		if ($class->controller!=null&&property_exists($class, 'action')&& $class->action!=null&&method_exists($class->controller, $class->action))
		{

			$action= call_user_func([$class->controller,$class->action]);
		}
		return $action==null?$class->controller:$action;
	}
}
?>