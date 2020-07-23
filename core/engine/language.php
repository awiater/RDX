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
 use RDX\Core\Helpers\Strings as Str;
 
 class Language
 {
 	/**
	 * Collection (RDX\Core\Helpers\Collection)
	 */
 	private $factory;
	
	/**
	 * Current language name (String)
	 */
	private $curLng;
	
	/**
	 * Language parser
	 * 
	 * @param String $curLng Default language 2 characters code (ie en)
	 */
	function __construct($curLng)
	{
		$this->factory=new Factory();
		$this->curLng=$curLng;
	}
	
	/**
	 *  Set current language
	 * 
	 * @param String $lang Language 2 characters code (ie en)
	 */
	function Set($lang)
	{
		$this->curLng=$lang;
	}
	
	
	
	/**
	 * Load Language strings from file or Array
	 * 
	 * @param String       $lang    Language 2 characters code (ie en)
	 * @param String|Array $values  Path to language file (String) or array with language definitions
	 */
	public function Load($lang,$values)
	{
		if (is_array($values))
		{
			$this->factory->add($lang,new Factory($values));
		}else
		{	
			$file=$this->FileExists($values);
			if ($file==false)
			{
				throw new \Exception($file.' is not valid language file');
			}
		
			$_=Arr::FetchArrayFromFile($file,'_');
			
			if (!is_array($_))
			{
				throw new \Exception($file.' is not valid language file');
			}
			$this->factory->add($lang,new Factory($_));
		}
	}
	
	/**
	 * Get value of given language key or all values (if key is null)
	 *  
	 * @param  String          $key   Language key (definition) name. If value is array you can nest keys seperate them by "."
	 * @param  String | Array  $lang  Optional language 2 characters code (ie en)
	 * @return String | Array         Language definition value
	 */
	public function Get(string $key=null,$lang=null)
	{
		$lang=$lang==null?$this->curLng:$lang;
		
		if ($key==null)
		{
			$lang=is_array($lang)?$lang:[$lang];
			$_=new Factory();
			foreach ($lang as $value) 
			{
				if ($this->factory->has($value))
				{
					$_->addRange($this->factory->get($value)->toArray());
				}	
			}
			return $_->toArray();
		}
		$lang=is_array($lang)?$lang[0]:$lang;
		if (!$this->factory->has($lang))
		{
			return null;
		}
		
		$lang=$this->factory->get($lang);
		
		if (Str::contains($key,'.'))
		{
			$key=explode('.',$key);
			if ($lang->has($key[0]))
			{
				$key[0]=$lang->get($key[0]);
				$value=$key[0];
				if (!is_array($key[0]))
				{
					return null;
				}
				for ($i=1;$i<count($key);$i++) 
				{
					if (array_key_exists($key[$i], $value))
					{
						$value=$value[$key[$i]];
					}else
					{
						return null;
					}
				}
				return $value;
			}
		}else
		if ($lang->has($key))
		{
			return $lang->get($key);
		}else
		{
			return null;
		}
	}
	
	/**
	 * Check if given language file exists
	 * 
	 * @param  String         $file  File path
	 * @return String|Boolean        Path to file or FALSE if file not exists
	 */
	private function FileExists($file)
	{
		if (!Str::endsWith(strtolower($file),'.php'))
		{
			$file.='.php';
		}
		
		if (file_exists($file))
		{
			return $file;
		}else
		if (file_exists(LNGDIR.$file))
		{
			return LNGDIR.$file;
		}else
		if (file_exists(LNGDIR.$this->curLng.'_'.$file))
		{
			return LNGDIR.$this->curLng.'_'.$file;
		}else
		{
			return false;
		}
	}
 }
?>