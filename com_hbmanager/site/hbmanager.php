<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
require_once( JPATH_COMPONENT.DS.'controller.php');

//Execute the task
$controller = JController::getInstance('HBmanager');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
