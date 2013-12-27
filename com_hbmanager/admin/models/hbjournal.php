<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');


setlocale(LC_TIME, "de_DE.UTF-8");


class HBmanagerModelHbjournal extends JModel
{	
	private $prevGames = array();
	private $nextGames = array();
	private $berichte = array();
	private $vorberichte = array();

	private $dateStartPrev = "";
	private $dateEndPrev = "";
	private $dateStartNext = "";
	private $dateEndNext = "";
	
	function __construct() {
		parent::__construct();
		
		setlocale(LC_TIME, "de_DE");
		// $datedefault = "last Saturday";
		// self::setDateStartPrev(strftime("%Y-%m-%d", strtotime($datedefault)-432000));
		// self::setDateEndPrev(strftime("%Y-%m-%d", strtotime($datedefault)+86400));
		// self::setDateStartNext(strftime("%Y-%m-%d", strtotime($datedefault)+172800));
		// self::setDateEndNext(strftime("%Y-%m-%d", strtotime($datedefault)+691200));
		self::setDates();
		
		$db = $this->getDbo();
		$db->setQuery("SET lc_time_names = 'de_DE'");
		try
		{
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		}
		catch (Exception $e) 
		{
			// catch any database errors.
		}
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		return $teams;
	}

	function setDates($dates = null)
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		$db = $this->getDbo();
		
		if (is_null($dates)){
			$todaydate = strftime("%Y-%m-%d", time());
			
			// previous games dates
			//echo $todaydate = "2012-12-11";
			
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
				$db->q(strftime("%Y-%m-%d", strtotime('last Monday', 
						strtotime('last friday', strtotime($todaydate))))).
				" AND " . $db->q($todaydate) .
				" ORDER BY `datum` ASC LIMIT 1";
			//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
			$db->setQuery($query);
			$result = $db->loadResult();
			if (!empty($result)) {
				$dates['startdatePrev'] = $result;
			}
			else {
				$query = "SELECT `datum` from `hb_spiel` WHERE `datum` < ".
						$db->q(strftime("%Y-%m-%d", strtotime('last Monday', 
							strtotime('last friday', strtotime($todaydate))))).
						" ORDER BY `datum` DESC LIMIT 1";
				//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
				$db->setQuery($query);
				$dates['startdatePrev'] = $db->loadResult();
			}
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
					$dates['startdatePrev']. " AND " . $db->q($todaydate) . 
					" ORDER BY `datum` DESC LIMIT 1";
			//echo __FILE__.'('.__LINE__.'):<pre>'.$query.'</pre>';
			$db->setQuery($query);
			$dates['enddatePrev'] = $db->loadResult();

			// next games dates
			//echo $todaydate = "2013-10-23";
			
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
				$db->q($todaydate). " AND " . 
				$db->q(strftime("%Y-%m-%d", strtotime('next Monday', 
							strtotime('next friday', strtotime($todaydate))))).
				" ORDER BY `datum` ASC LIMIT 1";
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$result = $db->loadResult();
			if (!empty($result)) {
				$dates['startdateNext'] = $result;
			}
			else {
				$query = "SELECT `datum` from `hb_spiel` WHERE `datum` > ".
						$db->q(strftime("%Y-%m-%d", strtotime('next Monday', 
							strtotime('next friday', strtotime($todaydate))))).
						" ORDER BY `datum` ASC LIMIT 1";
				//echo '=> model->$query <br><pre>".$query."</pre>';
				$db->setQuery($query);
				$dates['startdateNext'] = $db->loadResult();
			}
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` BETWEEN ".
					$db->q($dates['startdateNext']) . " AND " . 
					$db->q(strftime("%Y-%m-%d", strtotime('next friday', 
						strtotime($dates['startdateNext'])))).
					" ORDER BY `datum` DESC LIMIT 1";
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$dates['enddateNext'] = $db->loadResult();
		}
		
		self::setDateStartPrev($dates['startdatePrev']);
		self::setDateEndPrev($dates['enddatePrev']);
		
		self::setDateStartNext($dates['startdateNext']);
		self::setDateEndNext($dates['enddateNext']);
	}
	
	function updateDates($dates)
	{
		//echo "<a>Post dates:</a><pre>"; print_r($dates); echo "</pre>";
		self::setDateStartPrev($dates['startdatePrev']);
		
		self::setDateEndPrev(strftime("%Y-%m-%d", strtotime($dates['startdatePrev'])+518400));
		if ($dates['withEndDatePrev'])
		{
			//echo 'mit Enddatum<br />';
			//echo $dates['startdatePrev'].': '.strtotime($dates['startdatePrev']).'<br />';
			//echo $dates['enddatePrev'].': '.strtotime($dates['enddatePrev']).'<br />';
	
			if ( strtotime($dates['startdatePrev']) <= strtotime($dates['enddatePrev']) )
			{
				self::setDateEndPrev($dates['enddatePrev']);
			}
		}
		
		self::setDateStartNext($dates['startdateNext']);
		
		self::setDateEndNext(strftime("%Y-%m-%d", strtotime($dates['startdateNext'])+518400));
		if ($dates['withEndDateNext'])
		{
			//echo 'mit Enddatum<br />';
			//echo $dates['startdateNext'].': '.strtotime($dates['startdateNext']).'<br />';
			//echo $dates['enddateNext'].': '.strtotime($dates['enddateNext']).'<br />';
		
			if ( strtotime($dates['startdateNext']) <= strtotime($dates['enddateNext']) )
			{
				self::setDateEndNext($dates['enddateNext']);
			}
		}
	}
	
	
	// Datum Letzte Spiele
	
	function setDateStartPrev($date)
	{
		$this->dateStartPrev = strftime("%Y-%m-%d", strtotime($date));
		//echo strftime("%Y-%m-%d", strtotime($date));
	}
	
	function setDateEndPrev($date)
	{
		$this->dateEndPrev = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function getDateStartPrev()
	{
		return $this->dateStartPrev;
	}
	
	function getDateEndPrev()
	{
		return $this->dateEndPrev;
	}
	
	// Datum Kommende Spiele
	
	function setDateStartNext($date)
	{
		$this->dateStartNext = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function setDateEndNext($date)
	{
		$this->dateEndNext = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function getDateStartNext()
	{
		return $this->dateStartNext;
	}
	
	function getDateEndNext()
	{
		return $this->dateEndNext;
	}

	
	// -------------
	
	function getPrevGames()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartPrev).' AND '.$db->quote($this->dateEndPrev));
		$query->order($db->quoteName('reihenfolge').' ASC');
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo "<pre>"; print_r($games); echo "</pre>";
	
		return $this->prevGames = $games;
	}
	
	function getDates()
	{
		// $db = $this->getDbo();
	
		// $query = $db->getQuery(true);
		// $query->select('DISTINCT datum, 
				// DATE_FORMAT(datum, \'%a\' ) AS nametag, DATE_FORMAT(datum, \'%d\' ) AS tag, 
				// DATE_FORMAT(datum, \'%m\' ) AS monat, DATE_FORMAT(datum, \'%y\' ) AS jahr ');
		// $query->from('aaa_spiel');
		// $query->order($db->quoteName('datum').' ASC');
		// $query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartPrev).' AND '.$db->quote($this->dateEndPrev));
		// //echo $query;echo "<br />";
		// $db->setQuery($query);
		// $dates = $db->loadObjectList();
		// //echo "<pre>"; print_r($dates); echo "</pre>";
		
		// $i=0;
		// for ($i = 0; $i < count($dates); $i++)
		// {
			// if (($dates[$i]->nametag == 'Sa' AND $dates[$i+1]->nametag == 'So') AND 
					// (strftime("%j",strtotime($dates[$i]->datum))+1 == strftime("%j",strtotime($dates[$i+1]->datum))))
			// {
				// if ($dates[$i]->monat == $dates[$i+1]->monat)
				// {
					// $gameDates[] = 'Wochenende '.ltrim($dates[$i]->tag,'0').'./'.ltrim($dates[$i+1]->tag,'0').'.'.strftime("%b.",strtotime($dates[$i]->datum));
				// }
				// else
				// {
					// $gameDates[] = 'Wochenende '.ltrim($dates[$i]->tag,'0').'.'.strftime("%b.",strtotime($dates[$i]->datum)).'/'.ltrim($dates[$i+1]->tag,'0').'.'.strftime("%b.",strtotime($dates[$i+1]->datum));
				// }
					
				// $i++;
			// }
			// else $gameDates[] = $dates[$i]->nametag.' '.ltrim($dates[$i]->tag,'0').strftime(".%b.",strtotime($dates[$i]->datum));
			
		// }
		
		// //echo "<pre>"; print_r($gameDates); echo "</pre>";
		
		// return $gameDates;
	}
	
	
	function getAbschnittLetzteSpiele($styles = array())
	{
		$data = '';
		$formerMannschaft = '';
		if (!empty($this->prevGames))
		{
			if (count(self::getDates()) == 1) $data['ueberschrift'] = 'Alle Spiele vom letzten Spieltag';
			else $data['ueberschrift'] = 'Alle Spiele von den letzten Spieltagen';
				
			$data['dates'] = implode(self::getDates(), ', ');
			
			foreach ($this->prevGames as $game)
			{
				If ($formerMannschaft != $game->mannschaft) $data['spiele'] .= $game->mannschaft." ({$game->ligaKuerzel})\n";
				$formerMannschaft = $game->mannschaft;
				$data['spiele'] .= "{$game->heim} - {$game->gast}";
				$data['spiele'] .= "\t{$game->toreHeim}:{$game->toreGast}\n";
			}
			$data['spiele'] = str_replace(array(". Mannschaft","liche"),array("", "l."), $data['spiele']);
		}
		//echo "<pre>"; print_r($data); echo "</pre>";
		return $data;
	}
	

	
	function getNextGames()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartNext).' AND '.$db->quote($this->dateEndNext));
		$query->order($db->quoteName('datum').' ASC, '.$db->quoteName('uhrzeit').' ASC');
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo "<pre>"; print_r($games); echo "</pre>";
	
		return $this->nextGames = $games;
	}

	function getAbschnittKommendeSpiele($styles = array())
	{
		//echo "getAbschnittKommendeSpiele";
		$data = array();
		$formerMannschaft = '';
		$formerDate = '';
		//echo "<pre>"; print_r($this->nextGames); echo "</pre>";
		if (!empty($this->nextGames))
		{
			$data['ueberschrift'] = 'Alle Spiele vom nächsten Spieltag (chronologisch)';
			foreach ($this->nextGames as $game)
			{
				if ($formerDate != $game->datum) $data['spiele'] .= "\n".strftime("%A, %d.%m.%y",strtotime($game->datum))."\n";
				$formerDate = $game->datum;
				if ($formerMannschaft != $game->mannschaft) $data['spiele'] .= $game->mannschaft. " ({$game->ligaKuerzel})\n";
				$formerMannschaft = $game->mannschaft;
				$data['spiele'] .= substr($game->uhrzeit,0,5)." Uhr \t{$game->heim} - {$game->gast}\n";
			}
		}
		$data = str_replace(array(". Mannschaft","liche"),array("", "l."), $data);
		return $data;
	}
	
	
	function getReports()
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->leftJoin($db->quoteName('aaa_spielbericht').' USING ('.$db->quoteName('spielIDhvw').')');
		$query->where('('.$db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartPrev).' AND '.$db->quote($this->dateEndPrev).')'.
				' AND ('.$db->quoteName('bericht').' IS NOT NULL OR '.$db->quoteName('spielerliste').' IS NOT NULL)');
		$query->order($db->quoteName('reihenfolge').' DESC');
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo "<pre>"; print_r($games); echo "</pre>";
		
		return $this->berichte = $games;
	}
	
	function getAbschnittBerichte()
	{
		$data = array();
		
		if (!empty($this->berichte))
		{	
			foreach ($this->berichte as $bericht)
			{
				$ueberschrift = "{$bericht->mannschaft} - {$bericht->liga} ({$bericht->ligaKuerzel})";
				$ergebnis = "{$bericht->heim} - {$bericht->gast}"
						."\t{$bericht->toreHeim}:{$bericht->toreGast}";
				$text = $bericht->bericht;
				if (!empty($bericht->spielerliste)) $spieler = $bericht->spielerliste;
				
				$data[] = array('ueberschrift' => $ueberschrift, 'ergebnis' => $ergebnis, 'text' => $text, 'spieler' => $spieler);
			}
			//echo "<pre>"; print_r($data); echo "</pre>";
		}
		return $data;
	}
	
	function getForecasts()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->leftJoin($db->quoteName('aaa_spielvorschau').' USING ('.$db->quoteName('spielIDhvw').')');
		$query->where('('.$db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartNext).' AND '.$db->quote($this->dateEndNext).') AND ('.$db->quoteName('vorschau').' IS NOT NULL)');
		$query->order($db->quoteName('reihenfolge').' DESC');
		// echo $query;echo "<br />";
		$db->setQuery($query);
		$games = $db->loadObjectList();
		// echo "<pre>"; print_r($games); echo "</pre>";
	
		return $this->vorberichte = $games;
	}
	
	function getAbschnittVorberichte()
	{
		
		$data = array();
		
		if (!empty($this->vorberichte))
		{
			foreach ($this->vorberichte as $bericht)
			{
				$ueberschrift = $bericht->mannschaft.' - '.$bericht->liga.' ('.$bericht->ligaKuerzel.')';
				$spiel = substr($bericht->uhrzeit,0,5)." Uhr \t {$bericht->heim} - {$bericht->gast}";
				if (!empty($bericht->treffOrt) AND !empty($bericht->treffZeit))
				{
					$treff = "Treffpunkt: {$bericht->treffOrt} um {$bericht->treffZeit} Uhr";
				}
				$text = nl2br($bericht->vorschau);
				
				$data[] = array('ueberschrift' => $ueberschrift, 'spiel' => $spiel, 'treff' => $treff, 'text' => $text);
			}
		}
		//echo "<pre>"; print_r($data); echo "</pre>";
		return $data;
	}
	
	function getAbschnittAnfang($link = true)
	{
		$data['ueberschrift'] = 'Abt. Handball';
		
		if ($link)
		{
			$data['link'] = "Aktuellere und ausführlichere Informationen auf unserer Homepage: \n";
			$data['link'] .= "www.handball.tsv-geislingen.de";
		}
		
		return $data;
	}
		
	


}


