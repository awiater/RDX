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

use RDX\Core\Helpers\Strings as Str;
use RDX\Core\Helpers\Collection as Factory;

class Controller
{
		/**
		 * Collection (RDX\Core\Helpers\Collection)
		 */
		protected $factory;
		
		/**
		 *  View object (\RDX\Core\Engine\View)
		 */
		protected $view;
		
		
		/**
		 * Avaliable items names (Array)
		 */
		private $validators=['model','controller'];
		
		/**
		 * Controller base class
		 * 
		 * @param \RDX\Core\Helpers\Collection $factory Controller settings collection
		 */
		function __construct($factory=null)
		{
			$this->factory=new Factory();
			if ($factory instanceof Factory)
			{
				$this->setFactory($factory);
			}else
			{
				$this->setFactory($this->factory);
			}
			
			$this->view=new View();
			
		}
		
		/**
		 * Set internal objects collection factory
		 * 
		 * @param RDX\Core\Helpers\Collection  $factory  Collection helper object
		 */
		public function setFactory(Factory $factory)	
		{
			$this->factory=$factory;
			$this->factory->add('document',new \RDX\Core\Engine\Document($this->template));
			$this->factory->add('view',new View());	
		}	
		
		/**
		 *  Register new view in controller
		 * 
		 *  @param String  $viewname  Name of view
		 */
		public function registerView($viewname)	
		{
			$this->view=new View();		
			$this->factory->add('document',new \RDX\Core\Engine\Document($this->template));
			$this->view->setFile(str_replace($this->config->appname, APPDIR, $this->parseName($viewname,'views')));		
		}	
		
		/**
		 * Add language file to pool
		 * 
		 *  @param String $lngfile Path to language file
		 */
		protected function registerLang($lngfile)
		{
			$lngfile=$this->parseName($lngfile,'lang');
			
			if ($this->view instanceof View)
			{
				$lngfile=new \RDX\Core\Engine\Language($lngfile,$this->config->lang);
				$this->view->data['strings']=$lngfile->Get();
			}else
			{
				throw new \Exception('View not set please use this function after view register');
			}
		} 
		
		/**
		 * Parse given name to path
		 *  
		 * @param  String   $name  Object name (View, Model, Controller)
		 * @param  String   $type  Type of object (View, Model, Controller)
		 * @return String          Path (class) of object
		 * 
		 */
		private function parseName($name,$type)
		{
			if (Str::startsWith($name,'@/'))
			{
				$name=APPDIR.substr($name,1);
			}else
			if (Str::startsWith($name,'/'))		
			{
				$name=BASEDIR.substr($name,1);		
			}else	
			{
				if ($type=='views')
				{
					$bname=str_replace('Controllers','views',get_class($this));			
					$bname=str_replace(strrchr($bname,'\\'), '/', $bname);			
					$bname=str_replace('\\','/',$bname);			
					$bname=strtolower($bname);			
					$name=$bname.$name;
				}else
				if ($type=='lang')
				{
					$name=LNGDIR.$this->config->lang.'/'.$name.'.php';
				}		
			}
			
			return $name;
		}
		
		/**
		 * Register items with class
		 * 
		 * @param String $class Item class
		 * @param String $type  Item type (see validators property)
		 */
		private function register($class,$type)
		{
			
			$class=$this->loader->Parse($class,$type);
			$name=str_replace($type,'',substr(strrchr($class,'\\'),1));
			$name=strtolower($name);
			$type=strtolower($type).(Str::endsWith($type,'s')?'':'s');
			
			if (!$this->factory->has($type))
			{
				$this->factory->add($type,new Factory());
			}
			
			if (!$this->factory->{$type}->has($name))
			{
				$class=new $class($this->factory);
				$this->factory->{$type}->add($name,$class);
			}
		}
		
		/**
		 *  Functions calls override
		 * 
		 *  @param String $function Function name
		 *  @param Array  $args     Array with function arguments values
		 */
		function __call($function, $args)
		{
			if (Str::startsWith($function,'register'))
			{
				$function=str_replace('register', '', $function);
				if (in_array(strtolower($function), $this->validators) && count($args)==1)
				{
					$this->register($args[0],$function);
				}
			}
		}
		
		/**
		 *  Class property access override
		 * 
		 *  @param String $param Property name
		 */
		function __get($param)	
		{
			if ($this->factory->has($param))		
			{
				$param= $this->factory->{$param};
				if (is_string($param)&&class_exists($param))
				{
					return new $param($this->factory);
				}
				return $param;	
			}
		}	
			
}
?>