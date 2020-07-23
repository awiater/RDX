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

namespace RDX\Core\Exceptions;

class LoaderInvalidClass extends \Exception
{
	
	/**
	 * Invlaid class exception used by loader
	 */
	public function __construct($class) 
	{
		parent::__construct($class.' is not valid class definition', 0, null);
   	}
}
?>
