<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * HB Manager Component Controller
 */
class HBmanagerController extends JController
{

	function display($cachable=false)
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'hbmanager'));

		parent::display($cachable);
		// Set the submenu
		hbmanagerHelper::addSubmenu('');
	}
	
	function zeigeHVWDBTables()
	{
		$model = $this->getModel('hbmanagerhvw');
		
		$view = $this->getView('hbmanagerhvw','html');
		$view->setModel($model);
		
		$view->display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbmanagerhvw');
	}
	
	function updatehvw()
	{
		$jinput = JFactory::getApplication()->input;
		$kuerzel = $jinput->get('kuerzel', 'kein');
		
		$model = $this->getModel('hbmanagerhvw');
		$model->updateDB($kuerzel);
		
		$view = $this->getView('hbmanagerhvw','html');
		$view->setModel($model);
		$view->display();
	
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbmanagerhvw');
	}
	
	function manageMannschaften()
	{
		$model = $this->getModel('hbmanagerteams');
		
		
		$post = JRequest::get('post');
		//echo "<pre>"; print_r($post); echo "</pre>";
		if ($post['teamsUpdated']) {
			$model->updateMannschaften($post['hbmannschaft']);
		}
		if ($post['teamsAdded']) {
			$model->addNewTeams2Db($post['hbmannschaftNeu']);
		}
		if ($post['teamsDeleted']) {
			$model->deleteMannschaften($post['hbmannschaftdelete']);
		}
		
		$view = $this->getView('hbmanagerteams','html');
 		$view->setModel($model);
		
		$view->display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbmanagerteams');
	}
	
	function addHvwMannschaften()
	{
		$model = $this->getModel('hbmanagerteams');
	
		$view = $this->getView('hbmanagerteams','html');
		$view->setModel($model);
		$view->setLayout('addteams');
	
		$view->display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbmanagerteams');
	}
	
	function deleteMannschaften()
	{
		$model = $this->getModel('hbmanagerteams');
	
		$view = $this->getView('hbmanagerteams','html');
		$view->setModel($model);
		$view->setLayout('deleteteams');		
	
		$view->display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbmanagerteams');
	}
	
	function manageDBtables()
	{
		$jinput = JFactory::getApplication()->input;
	
		$model = $this->getModel('hbmanagerdb');
	
		$view = $this->getView('hbmanagerdb','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbmanagerteams');
	}
	

	function createDBtables()
	{
		$jinput = JFactory::getApplication()->input;
		$createTables = $jinput->get('createTables', '');
	
		$model = $this->getModel('hbmanagerdb');
	
		$model->createDBtables($createTables);
	
		$view = $this->getView('hbmanagerdb','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbmanagerdb');
	}
	
	function updateCal()
	{
		$jinput = JFactory::getApplication()->input;
		$kuerzel = $jinput->get('kuerzel', 'kein');
	
		$model = $this->getModel('hbmanagercal');
		
		$model->updateCal($kuerzel);
	
		$view = $this->getView('hbmanagercal','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbmanagercal');
	}
	/*
	function manageDBtables()
	{
		$jinput = JFactory::getApplication()->input;
		
		$model = $this->getModel('hbdbmanager');
	
		$view = $this->getView('hbdbmanager','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
	}
	*/
	function updateRecentGames()
	{
		$jinput = JFactory::getApplication()->input;
	
		$model = $this->getModel('hbrecentgames');
		
		$post = JRequest::get('post');
		//echo "<p>POST in controller</p><pre>"; print_r($post); echo "</pre>";
		
		$model->setDates($post['hbmanagerdates']);
		
// 		if (isset($post['date_button'])) {
// 			//echo "<p>Date button</p>";
// 			$model->setDates($post['hbmanagerdates']);
// 		}

		$previousGames = $post['hbrecentgames'];
		if (isset($post['update_button'])) {
			//echo "<p>Update button</p>";
			$model->writeDB($previousGames);
		} 
		else if (isset($post['article_button'])) {
			//echo "<p>Article button</p>";
			$model->writeDB($previousGames);
			$model->writeNews();
		} 
		else {
			//no button pressed
		}
		
		$view = $this->getView('hbrecentgames','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbrecentgames');
	}
	
	function updateUpcomingGames()
	{
		$jinput = JFactory::getApplication()->input;
		
		$model = $this->getModel('hbupcominggames');

		$post = JRequest::get('post');
		//echo "<p>POST in controller</p><pre>"; print_r($post); echo "</pre>";
		
		$model->setDates($post['hbmanagerdates']);
		
		// 		if (isset($post['date_button'])) {
		// 			//echo "<p>Date button</p>";
		// 			$model->setDates($post['hbmanagerdates']);
		// 		}
		
		$upcomingGames = $post['hbupcominggames'];
		if (isset($post['update_button'])) {
			//echo "<p>Update button</p>";
			$model->updateDB($upcomingGames);
		}
		else if (isset($post['article_button'])) {
			//echo "<p>Article button</p>";
			$model->updateDB($upcomingGames);
			$model->writeNews();
		}
		else {
			//no button pressed
		}
		
		if (isset($post['sent']))
		{
			if ($post['sent'])
			{
				$upcomingGames = $model->getUpcomingGamesArray($post);
				//echo "<pre>"; print_r($upcomingGames); echo "</pre>";
					
				$model->updateDB($upcomingGames);
				$model->writeNews();
			}
		}
		
		
		$view = $this->getView('hbupcominggames','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbupcominggames');
	}
	
	function showAmtsblatt()
	{
		$jinput = JFactory::getApplication()->input;
		
		$model = $this->getModel('hbamtsblatt');
		
		$post = JRequest::get('post');
		//echo "<pre>"; print_r($post); echo "</pre>";
		
		if ($post['dateChanged'])
		{
			//echo "Datum geändert <br />";
			$model->updateDates($post['hbmanagerfields']);
		}
		//$model->writeAmtsblatt();
		
		$view = $this->getView('hbamtsblatt','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbamtsblatt');
	}

	function showAmtsblattWord()
	{
		$jinput = JFactory::getApplication()->input;
	
		$model = $this->getModel('hbamtsblatt');
	
	
		//$model->writeAmtsblatt();
	
		$view = $this->getView('hbamtsblattArtikel','docx');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbmanagerHelper::addSubmenu('hbamtsblatt');
	}


} 