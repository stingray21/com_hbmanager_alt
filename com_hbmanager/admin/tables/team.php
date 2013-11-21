<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableTeam extends JTable 
{
	var $Name = null;
	var $Link = null;
	var $Kuerzel = null;
	var $Liga = null;
	var $completeName = null;
	var $completeLiga = null;
	var $scheduleName = null;
	var $tableName = null;
	var $MW = null;
	var $JugendAktiv = null;
	var $HVWlink = null;
	var $updated = null;
	
	function TableTeam(&$db)
	{	
		parent::__construct('#__hbmanager', 'id', $db);
	}
}
