<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');

class HBmanagerModelHBmanagerCal extends JModel
{
	
	function getMannschaften()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_mannschaft');
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$mannschaften = $db->loadObjectList();
		return $mannschaften;
	}
	

	function updateCal($kuerzel = 'kein')
	{
		$updatedTeams = array();
	
		if ($kuerzel == 'kein')
		{
			// no update
		}
		else
		{
			self::updateGamesInCal($teamkey);
			//self::insertGamesInCal($teamkey);
		}
		return;
	}
	
	
	protected function updateGamesInCal($kuerzel = 'kein')
	{
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_mannschaft');
		$query->where($db->quoteName('HVWlink').' IS NOT NULL');
		if ($kuerzel != 'alle')
		{
			// request only one team of DB
			$query->where($db->quoteName('kuerzel').' = '.$db->quote($kuerzel));
		}
		$db->setQuery($query);
		$mannschaften = $db->loadObjectList();
		
		// beginning iCal file
		$calData = 
			'BEGIN:VCALENDAR'."\n".
			'VERSION:2.0'."\n".
			'PRODID:-//jEvents 2.0 for Joomla//EN'."\n".
			'CALSCALE:GREGORIAN'."\n".
			'METHOD:PUBLISH'."\n";
		
		$dates = array();
		foreach ($mannschaften as $mannschaft)
		{
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('aaa_spiel');
			$query->where($db->quoteName('kuerzel').' = '.$db->quote($mannschaft->kuerzel));
			$query->leftJoin('aaa_halle USING (hallenNummer)');
			$db->setQuery($query);
			//echo nl2br($query);//die; //see resulting query
			$dates = $db->loadObjectList();
			
			//echo "<pre>"; print_r($dates); echo "</pre>";
	
			
			foreach ($dates as $date)
			{
				$start = strtotime ($date->Datum.' '.$date->Zeit);
				//$end = strtotime ($date->Datum.' '.23:59:59');
				$summary = $mannschaft->name." (".$mannschaft->liga.")";
				$desc = "Spiel: ".$date->heim." - ".$date->hast.'\n';
				$link = $mannschaft->hvwLink;
				$location = "{$date->hallenNummer} {$date->kurzname} - {$date->name} {$date->plz} {$date->stadt} {$date->strasse} Tel: {$date->telefon} "; //Bemerkung: {$date->Haftmittel}";
				$startstamp = $start - 3600;// offset, so date in jEvents is correct ???
				$endstamp = $end - 3600;// offset, so date in jEvents is correct ???
				$time = date('Ymd\THis\Z', time());
				$startdate = date('Ymd\THis\Z', $startstamp);
				$enddate = date('Ymd\THis\Z', $endstamp);
				
				//$uid = md5(uniqid(rand(), true));
				$uid = md5($mannschaft->ligaKuerzel.$date->spielIDhvw);
				
				$calData .= 'BEGIN:VEVENT'."\n".
					'UID:'.$uid."\n".
					'CATEGORIES:'.$mannschaft->name."\n".
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
/*

	
	
	
	/*
	 * funktioniert zwar, dass die Eintr�ge in die DB table 
	 * #__jevents_vevent
	 * #__jevents_repetition
	 * #__jevents_rrule
	 * #__jevents_vevdetail
	 * gemacht werden, erscheinen aber nicht im Kalender/Jevents
	 */
	/*
	protected function insertGamesInCal($teamkey = 'noteam')
	{
		
		$timestamp = time();
		$uid = md5(uniqid(rand(), true));
		
		$eventSummery = "autotest";
		$eventDesc = "<p>Bsesch</p>";
		
		$startdate = strtotime ('2013-02-23 15:20:00');
		$enddate = strtotime ('2013-02-23 23:59:59');
		
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		// Insert columns.
		$columns = array('icsid', 'catid', 'uid', 'refreshed', 'created', 'created_by', 
						'created_by_alias', 'modified_by', 'rawdata', 'recurrence_id', 'detail_id', 
						'state', 'lockevent', 'author_notified', 'access');
		// Insert values.
		$value = array();
		//$value['ev_id'] = 10;
		$value['icsid'] = 1;
		$value['catid'] = 9;
		$value['uid'] = $db->quote($uid);
		$value['refreshed'] = $db->quote('0000-00-00 00:00:00');
		$value['created'] = $db->quote(date('Y-m-d H:i:s', $timestamp));
		$value['created_by'] = 96;
		$value['created_by_alias'] = $db->quote('');
		$value['modified_by'] = 96;
				
			$rawdata = 
				'a:18:{'.
					's:3:"UID";'.
					's:32:"'.$uid.'";'.
					's:11:"X-EXTRAINFO";'.
					's:0:"";'.
					's:8:"LOCATION";'.
					's:0:"";'.
					's:11:"allDayEvent";'.
					's:3:"off";'.
					's:7:"CONTACT";'.
					's:0:"";'.
					's:11:"DESCRIPTION";'.
					's:'.strlen($eventDesc).':"'.$eventDesc.'";'.
					's:12:"publish_down";'.
					's:10:"'.date('Y-m-d', $timestamp).'";'.
					's:10:"publish_up";'.
					's:10:"'.date('Y-m-d', $timestamp).'";'.
					's:7:"SUMMARY";'.
					's:'.strlen($eventSummery).':"'.$eventSummery.'";'.
					's:3:"URL";'.
					's:0:"";'.
					's:11:"X-CREATEDBY";'.
					's:2:"96";'.
					's:7:"DTSTART";'.
					'i:'.$startdate.';'.
					's:5:"DTEND";'.
					'i:'.$enddate.';'.
					's:5:"RRULE";'.
					'a:4:{'.
						's:4:"FREQ";'.
						's:4:"none";'.
						's:5:"COUNT";'.
						'i:1;'.
						's:8:"INTERVAL";'.
						's:1:"1";'.
						's:5:"BYDAY";'.
						's:2:"'.str_replace(array('1', '2', '3', '4', '5', '6', '7'),array('MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'),date('N', $timestamp)).'";'.
					'}'.
					's:8:"MULTIDAY";'.
					's:1:"1";'.
					's:9:"NOENDTIME";'.
					's:1:"1";'.
					's:7:"X-COLOR";'.
					's:0:"";'.
					's:9:"LOCKEVENT";'.
					's:1:"0";'.
				'}';
		$value['rawdata'] = $db->quote($rawdata);
		
		$value['recurrence_id'] = $db->quote('');
		$value['detail_id'] = 0;
		$value['state'] = 1;
		$value['lockevent'] = 0;
		$value['author_notified'] = 0;
		$value['access'] = 1;
		
		$values[0] = implode(',', $value);
		
		$query
			->insert($db->quoteName('#__jevents_vevent'))
			->columns($db->quoteName($columns))
			->values($values);
		echo nl2br($query);//die; //see resulting query
		
		$db->setQuery($query);
		
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		$event_id = $db->insertid();
		echo "ID-Nummer: ".$event_id;
		
		$query = $db->getQuery(true);
		$query->update('#__jevents_vevent');
		$query->set($db->quoteName('detail_id').' = '.$event_id);
		$query->where($db->quoteName('ev_id').' = '.$event_id);
		$db->setQuery($query);
		echo nl2br($query);//die; //see resulting query
		
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		//*************************************
		// #__jevents_vevdetail
		
		
		// Create a new query object.
		$query = $db->getQuery(true);
		// Insert columns.
		$columns = array('evdet_id', 'rawdata', 'dtstart', 'dtstartraw', 'duration', 'durationraw', 
				'dtend', 'dtendraw', 'dtstamp', 'class', 'categories', 'color', 'description', 'geolon', 
				'geolat', 'location', 'priority', 'status', 'summary', 'contact', 'organizer', 'url', 
				'extra_info', 'created', 'sequence', 'state', 'modified', 'multiday', 'hits', 'noendtime');
		// Insert values.
		$value = array();
		$value['evdet_id'] = $event_id;
		$value['rawdata'] = $db->quote('');
		$value['dtstart'] = $startdate;
		$value['dtstartraw'] = $db->quote('');
		$value['duration'] = 0;
		$value['durationraw'] = $db->quote('');
		$value['dtend'] = $enddate;
		$value['dtendraw'] = $db->quote('');
		$value['dtstamp'] = $db->quote('');
		$value['class'] = $db->quote('');
		$value['categories'] = $db->quote('');
		$value['color'] = $db->quote('');
		$value['description'] = $db->quote($eventDesc);
		$value['geolon'] = 0;
		$value['geolat'] = 0;
		$value['location'] = $db->quote('');
		$value['priority'] = 0;
		$value['status'] = $db->quote('');
		$value['summary'] = $db->quote($eventSummery);
		$value['contact'] = $db->quote('');
		$value['organizer'] = $db->quote('');
		$value['url'] = $db->quote('');
		$value['extra_info'] = $db->quote('');
		$value['created'] = $db->quote('');
		$value['sequence'] = 0;
		$value['state'] = 1;
		$value['modified'] = $db->quote(date('Y-m-d H:i:s', $timestamp));
		$value['multiday'] = 1;
		$value['hits'] = 0;
		$value['noendtime'] = 1;
		
		$values[0] = implode(',', $value);
		
		$query
		->insert($db->quoteName('#__jevents_vevdetail'))
		->columns($db->quoteName($columns))
		->values($values);
		echo nl2br($query);//die; //see resulting query
		
		$db->setQuery($query);
		
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		
		//*************************************
		// #__jevents_rrule
		
		// Create a new query object.
		$query = $db->getQuery(true);
		// Insert columns.
		$columns = array('rr_id', 'eventid', 'freq', 'until', 'untilraw', 'count', 'rinterval', 'bysecond', 'byminute', 'byhour', 'byday', 'bymonthday', 'byyearday', 'byweekno', 'bymonth', 'bysetpos', 'wkst');
		// Insert values.
		$value = array();
		$value['rr_id'] = $event_id;
		$value['eventid'] = $event_id;
		$value['freq'] = $db->quote('none');
		$value['until'] = 0;
		$value['untilraw'] = $db->quote('');
		$value['count'] = 1;
		$value['rinterval'] = 1;
		$value['bysecond'] = $db->quote('');
		$value['byminute'] = $db->quote('');
		$value['byhour'] = $db->quote('');
		$value['byday'] = $db->quote(str_replace(array('1', '2', '3', '4', '5', '6', '7'),array('MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'),date('N', $timestamp)));
		$value['bymonthday'] = $db->quote('');
		$value['byyearday'] = $db->quote('');
		$value['byweekno'] = $db->quote('');
		$value['bymonth'] = $db->quote('');
		$value['bysetpos'] = $db->quote('');
		$value['wkst'] = $db->quote('');
		
		$values[0] = implode(',', $value);
		
		$query
		->insert($db->quoteName('#__jevents_rrule'))
		->columns($db->quoteName($columns))
		->values($values);
		echo nl2br($query);//die; //see resulting query
		
		$db->setQuery($query);
		
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}
		
		//*************************************
		// #__jevents_repetition
		
		// Create a new query object.
		$query = $db->getQuery(true);
		// Insert columns.
		$columns = array('rp_id', 'eventid', 'eventdetail_id', 'duplicatecheck', 'startrepeat', 'endrepeat');
		// Insert values.
		$value = array();
		$value['rp_id'] = $event_id;
		$value['eventid'] = $event_id;
		$value['eventdetail_id'] = $event_id;
		$value['duplicatecheck'] = $db->quote(md5(uniqid(rand(), true)));
		$value['startrepeat'] = $db->quote(date('Y-m-d H:i:s', $startdate));
		$value['endrepeat'] = $db->quote(date('Y-m-d H:i:s', $enddate));
		
		$values[0] = implode(',', $value);
		
		$query
		->insert($db->quoteName('#__jevents_repetition'))
		->columns($db->quoteName($columns))
		->values($values);
		echo nl2br($query);//die; //see resulting query
		
		$db->setQuery($query);
		
		try {
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} catch (Exception $e) {
			// catch any database errors.
		}

	}
	*/
}


