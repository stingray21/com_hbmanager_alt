<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * HB Manager component helper.
 */

abstract class HbHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) 
	{
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_TEAMS_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showTeams',
				$submenu == 'hbteams');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_DATA_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showData',
				$submenu == 'hbdata');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_PREVGAMES_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showPrevGames',
				$submenu == 'hbprevgames');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_NEXTGAMES_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showNextGames',
				$submenu == 'hbnextgames');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_JOURNAL_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showJournal',
				$submenu == 'hbjournal');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_DATABASE_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showDatabase',
				$submenu == 'hbdatabase');
		JSubMenuHelper::addEntry(JText::_('COM_HBMANAGER_CALENDAR_SUBMENU'), 
				'index.php?option=com_hbmanager&task=showCalendar',
				$submenu == 'hbcalendar');
	}
	
	public static function formatInput($input, $i)
	{
		$formatedInput = preg_replace('/name="([\S]{1,})\[([\S]{1,})\]/',
					"name=\"$1[".$i."][$2]", $input);
		$formatedInput = preg_replace('/id="([\S]{1,})_([\S]{1,})/',
					"id=\"$1_".$i."_$2", $formatedInput);
		return $formatedInput;
	}
}
