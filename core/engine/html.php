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

class Html
{
	/**
	 * RDX\Core\Engine\Template Object
	 */
	private $template;
	
	/**
	 * HTML Document parser
	 * 
	 * @param RDX\Core\Engine\Template $template
	 */
	function __construct(Template $template)
	{
		$this->template=$template;
	}
	
	/**
	 * Return linkrel tag (for css)
	 * 
	 * @param  String $link Path to link file
	 * @return String       Link as HTML tag 
	 */
	function getRelLink(string $link)
	{
		return $this->template->RenderFile(COREDIR.'html/linkrel.twig', ['url'=>$link]);
	}
	
	/**
	 * Return script tag
	 * 
	 * @param  String  $link    Path to linked script
	 * @param  String  $source  Script body string
	 * @return String           Script as HTML tag
	 */
	function getScript($link,$source=null)
	{
		return $this->template->RenderFile(COREDIR.'html/script.twig',['url'=>$link,'src'=>$source]);
	}
}
?>