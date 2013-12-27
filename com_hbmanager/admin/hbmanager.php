<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 

// require helper file
JLoader::register('hbhelper', 
		dirname(__FILE__).DS.'helpers'.DS.'hbhelper.php');
//echo dirname(__FILE__) . DS . 'helpers' . DS . 'hbmanager.php';


// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by HBmanager
$controller = JController::getInstance('HBmanager');
//Execute the task
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();


