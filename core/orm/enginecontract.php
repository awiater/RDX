<?php

/*
 *  RDX Framework
 * 
 *  This file is part of RDX Framework  
 * 
 *  DB engine interface 
 * 
 *  @version: 1.1					
 *	@author Artur W				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
namespace RDX\Core\ORM;

interface EngineContract
{
	/**
	 * Compile (Run) query in engine
	 * 
	 * @param mixed $query RDX\Core\ORM\SchemeQuery or RDX\Core\ORM\Query query object
	 */
	public function Compile($query);
}
?>