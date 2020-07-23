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

class ResponseInvalidHeader extends \Exception
{
	
	/**
	 * Invalid response header exception
	 */
	public function __construct() 
	{
		parent::__construct('Invalid reponse header', 0, null);
   	}
}
?>
