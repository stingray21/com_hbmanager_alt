<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * HB Manager Component Controller
 */
class hbmanagerController extends JController
{

	function display($cachable=false, $urlparams = false)
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'hbmanager'));

		parent::display($cachable);
		// Set the submenu
		hbhelper::addSubmenu('');
	}
	
	function showTeams()
	{
		$model = $this->getModel('hbteams');

		$post = JRequest::get('post');
		//echo "=> contoller->post<br><pre>"; print_r($post); echo "</pre>";
		if (isset($post['updateTeams_button'])) {
			$model->updateTeams($post['hbteam']);
		}
		if (isset($post['addTeams_button'])) {
			$model->addNewTeams($post['hbAddTeam']);
		}
		if (isset($post['deleteTeams_button'])) {
			$model->deleteTeams($post['hbDeleteTeam']);
		}
		
		$view = $this->getView('hbteams','html');
 		$view->setModel($model);
		
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbteams');
	}
	function addTeams()
	{
		$model = $this->getModel('hbteams');
		
		$jinput = JFactory::getApplication()->input;
		$updateHvw = $jinput->get('getHvwData', false);
		
		if ($updateHvw)
		{
			$year = strftime('%Y');
			if (strftime('%m') < 9) $year = $year-1;
			$leagueArray = $model->getLeagueArrayFromHVW(
					'http://www.hvw-online.org/index.php'.
					'?id=39&orgID=11&A=g_org&nm=0&do='.
					$year.'-10-01');
			$model->updateLeaguesInDB($leagueArray);
		}
		
		$view = $this->getView('hbteams','html');
		$view->setModel($model);
		$view->setLayout('addteams');
	
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbteams');
	}
	
	function deleteTeams()
	{
		$model = $this->getModel('hbteams');
	
		$view = $this->getView('hbteams','html');
		$view->setModel($model);	
		$view->setLayout('deleteteams');	
	
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbteams');
	}
	
	function showData()
	{
		$model = $this->getModel('hbdata');
		
		$view = $this->getView('hbdata','html');
		$view->setModel($model);
		
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbdata');
	}
	
	function updateData()
	{
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey', 'none');
		
		$model = $this->getModel('hbdata');
		$model->updateDB($teamkey);
		
		$view = $this->getView('hbdata','html');
		$view->setModel($model);
		$view->display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbdata');
	}
	
	function showPrevGames()
	{
		$model = $this->getModel('hbprevgames');
		
		$post = JRequest::get('post');
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($post);echo'</pre>';
		
		$dates = null;
		if (isset($post['hbdates'])) $dates = $post['hbdates'];
		$model->setDates($dates);
		
		if (isset($post['hbprevgames'])) $prevGames = $post['hbprevgames'];
		else $prevGames = null;
		
		if (isset($post['update_button'])) {
			//echo "=> Update button<br>";
			$model->updateDB($prevGames);
		} 
		elseif (isset($post['article_button'])) {
			//echo "=> Article button<br>";
			$model->updateDB($prevGames);
			$model->writeNews();
		} 
		else {
			//no button pressed
		}
		
		$view = $this->getView('hbprevgames','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbprevgames');
	}
	
	function showNextGames()
	{
		$model = $this->getModel('hbnextgames');

		$post = JRequest::get('post');
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($post);echo'</pre>';
		
		$dates = null;
		if (isset($post['hbdates'])) $dates = $post['hbdates'];
		$model->setDates($dates);
		
		if (isset($post['hbnextgames'])) $nextGames = $post['hbnextgames'];
		else $nextGames = null;
		
		if (isset($post['update_button'])) {
			//echo "=> update button<br>";
			$model->updateDB($nextGames);
		}
		elseif (isset($post['article_button'])) {
			//echo "=> article button<br>";
			$model->updateDB($nextGames);
			$model->writeNews();
		}
		else {
			//no button pressed
		}		
		
		$view = $this->getView('hbnextgames','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbnextgames');
	}
	
	function showJournal()
	{
		$model = $this->getModel('hbjournal');
		
		$post = JRequest::get('post');
		//echo "=> contoller->post<br><pre>"; print_r($post); echo "</pre>";
		
		$dates = null;
		if (isset($post['hbdates'])) $dates = $post['hbdates'];
		$model->setDates($dates);
		

		
		$view = $this->getView('hbjournal','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbjournal');
	}

	function showDatabase()
	{
		$model = $this->getModel('hbdatabase');
	
		$view = $this->getView('hbdatabase','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbdatabase');
	}
	
	function createDbTables()
	{
		$jinput = JFactory::getApplication()->input;
		$dbOption = $jinput->get('dbOption', '');
	
		$model = $this->getModel('hbdatabase');
	
		$model->createDBtables($dbOption);
	
		$view = $this->getView('hbdatabase','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbdatabase');
	}
	
	function showCalendar()
	{
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey', 'kein');
	
		$model = $this->getModel('hbcalendar');
		
		$model->updateCal($teamkey);
	
		$view = $this->getView('hbcalendar','html');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbcalendar');
	}
	
	function showJournalWord()
	{
		$model = $this->getModel('hbjournal');
	
		$view = $this->getView('hbJournalWord','docx');
		$view->setModel($model, true);
		$view->display();
		//self::display();
		
		// Set the submenu
		hbhelper::addSubmenu('hbjournal');
	}
	
} 