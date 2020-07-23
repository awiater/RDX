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

use RDX\Core\Helpers\Collection as Factory;
use RDX\Core\Helpers\Arrays as Arr;

class Document
{
	/**
	 * Factory (collection) object
	 */
	private $factory;
	
	/**
	 * HTML Document parser
	 * 
	 * @param RDX\Core\Engine\Template $template  Template parser object
	 * @param Array               $settings  Document settings array 
	 */
	function __construct(Template $template,$settings=null)
	{
		$this->factory=new Factory();
		$this->factory->add('title',null);
		$this->factory->add('description',null);
		$this->factory->add('keywords',null);
		$this->factory->add('refurls',[]);
		$this->factory->add('scripts',[]);
		$this->factory->add('type','html');
		$this->factory->add('body','');
		$this->factory->add('style','');
		$this->factory->add('response',null);
		$this->factory->add('template',$template);
		if (is_array($settings))
		{
			$this->addMany($settings);
		}
	}	
	
	/**
	 * Class properties getter override
	 * 
	 * @param  String  $param  Param name
	 * @return Object          Object from collection (if exists)
	 */
	function __get($param)	
	{
		if ($this->factory->has($param))		
		{
			return $this->factory->{$param};
		}
	}
	
	/**
	 * Class properties setter override
	 * 
	 * @param String $param Param name
	 * @param String $value Param value
	 */
	function __set($param,$value)	
	{
		if ($this->factory->has($param))		
		{
			return $this->factory->add($param,$value);
		}
	}
	
	/**
	 * Merge document properties with array
	 * 
	 * @param Array $settings Document properties as Array
	 */
	function Merge(array $settings)
	{
		$valid=['title','description','keywords','type','body'];
		foreach ($settings as $key => $value) 
		{
			if ($key=='refurls'||$key=='scripts')
			{
				if (is_array($value))
				{
					$this->factory->{$key}=array_merge($value,$this->factory->{$key});
				}else
				{
					$this->factory->{$key}[]=$value;
				}
			}else
			if ($key=='style')
			{
				$this->factory->style.=$value;
			}else
			if (in_array($key, $valid))
			{
				if ($value!=null)
				{
					$this->factory->{$key}=$value;
				}
			}
			
		}
	}
	
	/**
	 * Returns object properties as array
	 * 
	 * @return Array Document propertiers
	 */
	public function ToArray($parseToTags=false)
	{
		$arr=$this->factory->ToArray();
		if ($parseToTags)
		{
			$arr['refurls']=$this->getRefUrls();
			$arr['scripts']=$this->getScripts();
		}
		return $arr;
	}
	
	/**
	 * Add array as class properties
	 * 
	 * @param Array $settings Document properties as array
	 */
	public function addMany(array $settings)
	{
		$valid=['title','description','keywords','type','body','refurls','scripts','style'];
		foreach ($settings as $key => $value) 
		{
			if (in_array($key, $valid))
			{
				$this->factory->{$key}=$value;
			}else
			if ($key=='desc')
			{
				$this->factory->description=$value;
			}
			else
			if ($key=='keys')
			{
				$this->factory->keywords=$value;
			}
		}
	}
	
	/**
	 *  Set document body as HTML
	 * 
	 * @param  Object $body Document body
	 */
	public function setHtml($body)
	{
		$this->response=new \RDX\Core\Response();
		$this->type='html';
		$this->body=$body;
		$this->response->Set($body,$this->type);
	}
	
	/**
	 *  Set document body as JSON string
	 * 
	 * @param Object $body Document body
	 */
	public function setJson($body)
	{
		$this->response=new \RDX\Core\Response();
		$this->type='json';
		$this->body=$body;
		$this->response->Set($body,$this->type);
	}
	
	/**
	 * Add new link rel
	 * 
	 * @param String $url Path to link file
	 * @param String $rel Link type (ie. stylesheet)
	 */
	public function addRefUrl($url,$rel='stylesheet')
	{
		$_=$this->refurls;
		$_[]=['url'=>$url,'rel'=>$rel];
		$this->refurls=$_;
	}
	
	/**
	 * Return Ref urls as html tags
	 * 
	 * @param  String $tplFile Optional template file for parsing links
	 * @return String          Links collection as parsed template file (HTML)
	 */
	public function getRefUrls($tplFile=null)
	{
		$tplFile=$tplFile==null?COREDIR.'html/linkrel.twig':$tplFile;
		$res='';
		foreach ($this->refurls as $value) 
		{
			$res.=$this->template->RenderFile($tplFile, $value);
		}
		return $res;
	}
	
	/**
	 * Add new script to collection
	 * 
	 * @param String  $url      Path to script file
	 * @param String  $section  Optional section where script will be placed
	 * @param String  $type     Optional Type of script
	 * @param String  $src      Optional body of script 
	 */
	public function addScript($url,$section=null,$type=null,$src=null)
	{
		if ($section==null)
		{
			$_=$this->scripts;
			$_[]=['url'=>$url,'type'=>$type,'src'=>$src];
			$this->scripts=$_;
		}else
		{
			$_=$this->scripts;
			$_[$section]=['url'=>$url,'type'=>$type,'src'=>$src];
			$this->factory->scripts=$_;
		}
	}	
	
	/**
	 * Return scripts urls as html tags
	 * 
	 * @param  String $tplFile Optional path to template file
	 * @param  String $section Optional section where script will be placed
	 * @return String          Scripts collection parsed to HTML tags
	 */
	public function getScripts($tplFile=null,$section=null)
	{
		$tplFile=$tplFile==null?COREDIR.'html/script.twig':$tplFile;
		$res='';
		if ($section!=null&&array_key_exists($section, $this->scripts))
		{
			$res=$this->template->RenderFile($tplFile, $this->scripts[$section]);
		}else
		{
			foreach ($this->scripts as $value) 
			{
				$res.=$this->template->RenderFile($tplFile, $value);
			}
		}
		return $res;
	}
}
?>