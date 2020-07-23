<?php
namespace RCMS\Exceptions;

class NullRouteException extends \Exception
{
	
	public function __construct() 
	{
		parent::__construct('Route is invalid or null', 0, null);
   	}
}
?>
