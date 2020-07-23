<?php
/**
 *  RCMS
 * 
 *  
 *  @version: 1.1					
 *	@author Artur W. <arturwiater@gmail.com>				
 *	@copyright Copyright (c) 2020 All Rights Reserved				
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Helpers registering
 */ 
use RDX\Core\Helpers\Arrays as Arr;
use RDX\Core\Helpers\Strings as Str;
use RDX\Core\Helpers\Collection as Factory;

/**
 *  Define main constants
 */
define('COREDIR',__DIR__.'/core/');
define('TPLDIR',__DIR__.'/templates/');
define('LNGDIR',__DIR__.'/language/');
define('BASEDIR',__DIR__.'/');
define('APPDIR',__DIR__.'');
define('LOGFILE',__DIR__.'/storage/log.txt');
define('LOGOPTION',['mysqli','autoloader']);
define('ERROR_SHOW','all');

/**
 * Framework initialization
 */
require(__DIR__.'/core/rdxcore.php');
require(__DIR__.'/config.php');
$cfg['appclass']='RDX\\Modules\\$dir\\$type\\$file$type';
$cfg['templates']=
		[
		'html_yesno'=>TPLDIR.'html/tpls/yesno.twig',
		'html_accessedit'=>TPLDIR.'html/tpls/accesslevelsedit.twig',
		'html_formtoken'=>TPLDIR.'html/tpls/input_formtoken.twig',
		'html_loginform'=>APPDIR.'/modules/users/views/login.twig',
		'base_scripts'=>TPLDIR.'html/tpls/basescripts.twig'
		];
$rdx=RDX\Core\Core::init($cfg);
$cfg=null;
	
/**
 * Set error handling
 */
set_error_handler (
    function($errno, $errstr, $errfile, $errline) 
    {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);     
    }
);

 
 
/**
 * Set global variables
 */
$globals=new Factory();
$globals->add('model_home',$rdx->loader->Load('Home/HomeModel'));
$globals->add('logeduser',$rdx->loader->Load('Users/AuthController'));
$globals->add('settings',$rdx->loader->Load('Settings/SettingsModel'));
$rdx->addFactoryVar('env',(object)($globals->ToArray()));

$lngfront=$globals->settings->get('general.lngfront');
$lngadm=$globals->settings->get('general.lngadm');
$rdx->lang->Load($lngadm,LNGDIR.$lngfront.'.php');
if ($lngadm!=$lngfront)
{
	$rdx->lang->Load($lngadm,LNGDIR.$lngadm.'.php');
}
$globals->add('lang',$rdx->lang->Get(null,[$lngfront,$lngadm]));

$globals->add('tpl',$globals->settings->get('template.template'));
$globals->add('webmode',$globals->settings->get('general.webmode'));

$globals->add('view',new RDX\Core\Engine\View());
$globals->add('document',new \RDX\Core\Engine\Document($rdx->template,$globals->settings->getWebsiteSettings()));
$globals->document->addRefUrl($rdx->config->hostaddr.'vendor/fontawesome-free/css/all.css');
$globals->document->addRefUrl($rdx->config->hostaddr.'vendor/bootstrap/css/bootstrap.min.css');
$globals->document->addRefUrl($rdx->config->hostaddr.'templates/admin/default.css');
$globals->document->addScript($rdx->config->hostaddr.'vendor/jquery/jquery.min.js');
$globals->document->addScript($rdx->config->hostaddr.'vendor/jquery/popper.js');
$globals->document->addScript($rdx->config->hostaddr.'vendor/bootstrap/js/bootstrap.bundle.min.js');
$globals->document->addScript($rdx->config->hostaddr.'vendor/tinymce/tinymce.min.js');

$globals->view->data['strings']=$globals->lang;
$globals->view->data['config']=$rdx->config->ToArray();
$globals->view->data['template']=$rdx->loader->Load('Template/TemplateModel')->getCurTplVars();


define('CONFIG_SEOURL',$globals->settings->get('general.seourl'));

/**
 * Default routes
 */
 $rdx->router->addRoute(
	'module=auth&action=login',
	[
		'controller'=>'Users/Auth',
		'action'=>'parseLoginForm'
	] 
);

$rdx->router->addRoute(
	'/auth_login.html',
	[
		'controller'=>'Users/Auth',
		'action'=>'parseLoginForm'
	] 
);

$rdx->router->addRoute(
	'/auth_logoff.html',
	[
		'controller'=>'Users/Auth',
		'action'=>'logoff'
	] 
);

$rdx->router->addRoute(
	'module=auth&action=logoff',
	[
		'controller'=>'Users/Auth',
		'action'=>'logoff'
	] 
);

$rdx->router->addRoute(
	'module=$1_admin&action=$2$3*',
	[
		'controller'=>'/RDX/Modules/$1/Controllers/$1AdminController',
		'action'=>'$2'
	] 
);

$rdx->router->addRoute(
	'module=$1&action=$2$3*',
	[
		'controller'=>'/RDX/Modules/$1/Controllers/$1Controller',
		'action'=>'$2'
	] 
);

$rdx->router->addRoute(
	'$1_admin_$2$3*.html',
	[
		'controller'=>'/RDX/Modules/$1/Controllers/$1AdminController',
		'action'=>'$2',
		'args'=>['ind'=>3,'sep'=>'_','ksep'=>'-']
	] 
);

$rdx->router->addRoute(
	'$1_$2$3*.html',
	[
		'controller'=>'/RDX/Modules/$1/Controllers/$1Controller',
		'action'=>'$2',
		'args'=>['ind'=>3,'sep'=>'_','ksep'=>'-']
	] 
); 

/**
 * Assign urls parsers
 */
$rdx->router->setUrlParser(function($controller,$action,$args)
{
	$url=null;
	if ($controller==null||$controller=='/')
	{
		return ' ';
	}
		if (is_array($args))
		{
			$url='';
			foreach ($args as $key => $value) 
			{
				$url.='&'.$key.'='.$value;
			}
		}
		
	if (CONFIG_SEOURL=='0')
	{
		return '?module='.$controller.'&action='.$action.$url;
	}else
	{
		if (is_array($args))
		{
			$url='';
			foreach ($args as $key => $value) 
			{
				$url.='_'.$key.'-'.$value;
			}
		}
		if ($controller=='auth')
		{
			return $controller.'_'.$action.'.html';
		}
		$url=$controller.'_'.$action.$url.'.html';
		return $url;
	}
	
});

$globals->view->data['auth']['loginurl']=$rdx->router->url('users','loginform',null,true);

/**
 * Start New Session
 */
$rdx->session->start();

/**
 * Init routing
 */
 
$route=null;
 
if ($rdx->request->isSetGet('route'))
{
	$route='/'.$rdx->request->get('route');
}else
{
	$route=$rdx->request->server('REQUEST_URI');
}


if ($route==null)
{
	throw new RCMS\Exceptions\NullRouteException();
}


$route=$rdx->compileRoute($route);

if ($globals->webmode=='offline')
{
	$globals->view->setFile(TPLDIR.'frontend/'.$globals->tpl.'/offline.twig');
	
	if (!file_exists($globals->view->file))
	{
		$globals->view->setFile(TPLDIR.'html/tpls/htmlskeleton.twig');
	}
	$globals->view->data['content']=$globals->settings->get('general.offlinetext');
}else
if($globals->webmode==null||$globals->webmode=='error')	
{
	error404:
	$view=new \RDX\Core\Engine\View();
	$view->setFile(TPLDIR.'frontend/'.$globals->tpl.'/404.twig');
	$message=defined(ERROR_SHOW)? $globals->model_home->getMessage():'';
	$message=strlen($message)>1?$message:$rdx->lang->Get('msg.error.invalid_route');
	if (!file_exists($view->file))
	{
		$globals->view->setFile(TPLDIR.'frontend/'.$globals->tpl.'/index.twig');
		$globals->view->data['content']=$rdx->template->RenderFile(TPLDIR.'html/tpls/exception.twig',['message'=>$message]);
	}else
	{
		$view->data['message']=$message;
		$globals->view->data['content']=$rdx->template->Render($view);
	}		
}else
{
	$globals->add('auth',$globals->webmode=='private');
	$globals->view->setFile(TPLDIR.'frontend/'.$globals->tpl.'/index.twig');
	$globals->view->data['user']['logoff']=$globals->logeduser->getLogOffUrl();
	$globals->view->data['sections']=$rdx->loader->Load('/RDX/Modules/Template/Models/TemplateModel')-> getSetSections( );
	
	if ($route!=null&&$globals->logeduser->isValidRouteObject($route))
	{
		$rdx->loader->Load($route);
	}
	auth:
	if ($globals->auth && !$globals->logeduser->isLoged())
	{
		$globals->view->setFile(TPLDIR.'frontend/'.$globals->tpl.'/private.twig');
		if (!file_exists($globals->view->file))
		{
			$globals->view=$globals->logeduser->getLoginForm();
		}else
		{
			$globals->model_home->generateFormToken($globals->view);
		}
		$globals->view->data['error_msg']=$globals->model_home->getMessage();	
		$globals->view->data['refurl']=substr($rdx->request->server('REQUEST_URI'),1);	
	}else
	{
		
		$controller=null;
		$controller=$rdx->loader->Load($route);
		/*try
		{
			$controller=$rdx->loader->Load($route);
			
		}catch(Exception $e)
		{
			if ($e instanceof \RDX\Core\Exceptions\LoaderInvalidClass )
			{
				if (defined(ERROR_SHOW)&&ERROR_SHOW=='all')
				{
					$globals->model_home->setMessage(0,$e->getMessage());	
				}
				goto error404;
			}else
			{
				$globals->view->data['content']=$globals->model_home->generateExceptionPanel($e);
			}
		}
		*/
		if ($controller==null)
		{
			
			if (defined(ERROR_SHOW)&&ERROR_SHOW=='all')
			{
				$globals->model_home->setMessage(0,'Invalid Controller');	
			}
			goto error404;
		} 
	
		if ($controller->document->response->isJson())
		{
			echo json_encode($controller->response->Get());
			exit;
		}
		$view=$controller->document->response->Get(FALSE);
		$view->data['strings']=$globals->lang;
		$globals->document->Merge($controller->document->ToArray());
		if (!$view instanceof \RDX\Core\Engine\View)
		{
			if (defined(ERROR_SHOW)&&ERROR_SHOW=='all')
			{
				$globals->model_home->setMessage(0,'Invalid View');	
			}
			
			goto error404;
		}
		
		try
		{
			$globals->view->data['content']=$rdx->template->Render($view);
		}catch(\Exception $e)
		{
			if (defined(ERROR_SHOW)&&ERROR_SHOW=='all')
			{
				$globals->model_home->setMessage(0,$e->getMessage());	
			}
			goto error404;
		}
	}
}

if ($globals->logeduser->isLoged())
{
	$globals->view->data['user']['logoffurl']=$globals->logeduser->getLogOffUrl();
	$globals->view->data['user']['logeduser']=$globals->logeduser->getLogedUserInfo();
}

$globals->view->data['document']=$globals->document->ToArray(TRUE);
$globals->view->data['document']['url']=$rdx->request->server('REQUEST_URI');
$globals->view->data['user']['ismobile']=$globals->model_home->isMobile();

echo $rdx->template->Render($globals->view);
?> 