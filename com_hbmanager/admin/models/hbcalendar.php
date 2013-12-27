<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class HBmanagerModelHbcalendar extends JModel
{
	

	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		return $teams = $db->loadObjectList();
	}
	
	function updateCal($teamkey = NULL)
	{
		if (is_null($teamkey)) {
			// no update
		}
		else {
			self::updateGamesInCal($teamkey);
		}
		return;
	}
	
	
	protected function updateGamesInCal($teamkey = NULL)
	{
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->where($db->qn('HVWlink').' IS NOT NULL');
		if ($teamkey != 'all')
		{
			// request only one team of DB
			$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		}
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		
		// beginning iCal file
		$calData = 
			'BEGIN:VCALENDAR'."\n".
			'VERSION:2.0'."\n".
			'PRODID:-//jEvents 2.0 for Joomla//EN'."\n".
			'CALSCALE:GREGORIAN'."\n".
			'METHOD:PUBLISH'."\n";
		
		$dates = array();
		foreach ($teams as $team)
		{
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('hb_spiel');
			$query->where($db->qn('kuerzel').' = '.$db->q($team->kuerzel));
			$query->leftJoin('hb_halle USING (hallenNummer)');
			$db->setQuery($query);
			//echo nl2br($query);//die; //see resulting query
			$dates = $db->loadObjectList();
			
			//echo '=> model->$dates <br><pre>"; print_r($dates); echo "</pre>';
			
			foreach ($dates as $date)
			{
				$start = strtotime ($date->Datum.' '.$date->Zeit);
				//$end = strtotime ($date->Datum.' '.23:59:59');
				$summary = $team->name." (".$team->liga.")";
				$desc = "Spiel: ".$date->heim." - ".$date->hast.'\n';
				$link = $team->hvwLink;
				$location = $date->hallenNummer." ".$date->kurzname." - ".
						$date->name." ".$date->plz." ".$date->stadt." ".
						$date->strasse." Tel: ".$date->telefon;
				//$location .= "Bemerkung: ".$date->Haftmittel;
				
				// offset, so date in jEvents is correct ???
				$startstamp = $start - 3600;
				$endstamp = $end - 3600;
				
				$time = date('Ymd\THis\Z', time());
				$startdate = date('Ymd\THis\Z', $startstamp);
				$enddate = date('Ymd\THis\Z', $endstamp);
				
				//$uid = md5(uniqid(rand(), true));
				$uid = md5($team->ligaKuerzel.$date->spielIDhvw);
				
				$calData .= 'BEGIN:VEVENT'."\n".
					'UID:'.$uid."\n".
					'CATEGORIES:'.$team->name."\n".
					'SUMMARY:'.$summary."\n".
					'DESCRIPTION;ENCODING=QUOTED-PRINTABLE:'.$desc.'\n'."\n".
					'CONTACT:'.$link."\n".
					'LOCATION:'.$location."\n".
					'DTSTAMP:'.$time."\n".
					'DTSTART:'.$startdate."\n".
					//'DTEND:'.$endstamp."\n". //no end date
					'SEQUENCE:0'."\n".
					'TRANSP:OPAQUE'."\n".
					'END:VEVENT'."\n";
			}
			
			
		}

		// ending iCal file
		$calData .= 'END:VCALENDAR';
		
		$file = 'calGames.ics';
		$fileName = JPATH_ROOT . DS . "cal" . DS . $file;
		
		if( JFile::write($fileName, $calData) )
		{
			echo '<p>Die Datei wurde geschrieben</p>';
		}
	}
}