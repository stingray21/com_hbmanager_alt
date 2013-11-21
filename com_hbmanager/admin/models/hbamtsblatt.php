<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');


setlocale(LC_TIME, "de_DE.UTF-8");

// include the PHPdocx library
require_once '/www/htdocs/w00f0a0a/hb/libraries/phpdocx/classes/CreateDocx.inc';
// require_once JPATH_SITE.'/libraries/phpdocx/classes/CreateDocx.inc'; // wenn joomla in Hauptverzeichnis und nicht in "joomla"-Ordner

class HBmanagerModelHBamtsblatt extends JModel
{	
	private $recentGames = array();
	private $upcomingGames = array();
	private $berichte = array();
	private $vorberichte = array();

	private $dateStartRecent = "";
	private $dateEndRecent = "";
	private $dateStartUpcoming = "";
	private $dateEndUpcoming = "";
	
	function __construct() {
		parent::__construct();
		
		setlocale(LC_TIME, "de_DE");
		$datedefault = "last Saturday";
		self::setDateStartRecent(strftime("%Y-%m-%d", strtotime($datedefault)-432000));
		self::setDateEndRecent(strftime("%Y-%m-%d", strtotime($datedefault)+86400));
		self::setDateStartUpcoming(strftime("%Y-%m-%d", strtotime($datedefault)+172800));
		self::setDateEndUpcoming(strftime("%Y-%m-%d", strtotime($datedefault)+691200));
		
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


	function updateDates($dates)
	{
		//echo "<a>Post dates:</a><pre>"; print_r($dates); echo "</pre>";
		self::setDateStartRecent($dates['startdateRecent']);
		
		self::setDateEndRecent(strftime("%Y-%m-%d", strtotime($dates['startdateRecent'])+518400));
		if ($dates['withEndDateRecent'])
		{
			//echo 'mit Enddatum<br />';
			//echo $dates['startdateRecent'].': '.strtotime($dates['startdateRecent']).'<br />';
			//echo $dates['enddateRecent'].': '.strtotime($dates['enddateRecent']).'<br />';
	
			if ( strtotime($dates['startdateRecent']) <= strtotime($dates['enddateRecent']) )
			{
				self::setDateEndRecent($dates['enddateRecent']);
			}
		}
		
		self::setDateStartUpcoming($dates['startdateUpcoming']);
		
		self::setDateEndUpcoming(strftime("%Y-%m-%d", strtotime($dates['startdateUpcoming'])+518400));
		if ($dates['withEndDateUpcoming'])
		{
			//echo 'mit Enddatum<br />';
			//echo $dates['startdateUpcoming'].': '.strtotime($dates['startdateUpcoming']).'<br />';
			//echo $dates['enddateUpcoming'].': '.strtotime($dates['enddateUpcoming']).'<br />';
		
			if ( strtotime($dates['startdateUpcoming']) <= strtotime($dates['enddateUpcoming']) )
			{
				self::setDateEndUpcoming($dates['enddateUpcoming']);
			}
		}
	}
	
	
	// Datum Letzte Spiele
	
	function setDateStartRecent($date)
	{
		$this->dateStartRecent = strftime("%Y-%m-%d", strtotime($date));
		//echo strftime("%Y-%m-%d", strtotime($date));
	}
	
	function setDateEndRecent($date)
	{
		$this->dateEndRecent = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function getDateStartRecent()
	{
		return $this->dateStartRecent;
	}
	
	function getDateEndRecent()
	{
		return $this->dateEndRecent;
	}
	
	// Datum Kommende Spiele
	
	function setDateStartUpcoming($date)
	{
		$this->dateStartUpcoming = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function setDateEndUpcoming($date)
	{
		$this->dateEndUpcoming = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function getDateStartUpcoming()
	{
		return $this->dateStartUpcoming;
	}
	
	function getDateEndUpcoming()
	{
		return $this->dateEndUpcoming;
	}

	
	// -------------
	
	function getRecentGames()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartRecent).' AND '.$db->quote($this->dateEndRecent));
		$query->order($db->quoteName('reihenfolge').' ASC');
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo "<pre>"; print_r($games); echo "</pre>";
	
		return $this->recentGames = $games;
	}
	
	function getDates()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('DISTINCT datum, 
				DATE_FORMAT(datum, \'%a\' ) AS nametag, DATE_FORMAT(datum, \'%d\' ) AS tag, 
				DATE_FORMAT(datum, \'%m\' ) AS monat, DATE_FORMAT(datum, \'%y\' ) AS jahr ');
		$query->from('aaa_spiel');
		$query->order($db->quoteName('datum').' ASC');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartRecent).' AND '.$db->quote($this->dateEndRecent));
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$dates = $db->loadObjectList();
		//echo "<pre>"; print_r($dates); echo "</pre>";
		
		$i=0;
		for ($i = 0; $i < count($dates); $i++)
		{
			if (($dates[$i]->nametag == 'Sa' AND $dates[$i+1]->nametag == 'So') AND 
					(strftime("%j",strtotime($dates[$i]->datum))+1 == strftime("%j",strtotime($dates[$i+1]->datum))))
			{
				if ($dates[$i]->monat == $dates[$i+1]->monat)
				{
					$gameDates[] = 'Wochenende '.ltrim($dates[$i]->tag,'0').'./'.ltrim($dates[$i+1]->tag,'0').'.'.strftime("%b.",strtotime($dates[$i]->datum));
				}
				else
				{
					$gameDates[] = 'Wochenende '.ltrim($dates[$i]->tag,'0').'.'.strftime("%b.",strtotime($dates[$i]->datum)).'/'.ltrim($dates[$i+1]->tag,'0').'.'.strftime("%b.",strtotime($dates[$i+1]->datum));
				}
					
				$i++;
			}
			else $gameDates[] = $dates[$i]->nametag.' '.ltrim($dates[$i]->tag,'0').strftime(".%b.",strtotime($dates[$i]->datum));
			
		}
		
		//echo "<pre>"; print_r($gameDates); echo "</pre>";
		
		return $gameDates;
	}
	
	
	function getAbschnittLetzteSpiele($styles = array())
	{
		$data = '';
		$formerMannschaft = '';
		if (!empty($this->recentGames))
		{
			if (count(self::getDates()) == 1) $data['ueberschrift'] = 'Alle Spiele vom letzten Spieltag';
			else $data['ueberschrift'] = 'Alle Spiele von den letzten Spieltagen';
				
			$data['dates'] = implode(self::getDates(), ', ');
			
			foreach ($this->recentGames as $game)
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
	

	
	function getUpcomingGames()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartUpcoming).' AND '.$db->quote($this->dateEndUpcoming));
		$query->order($db->quoteName('datum').' ASC, '.$db->quoteName('uhrzeit').' ASC');
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo "<pre>"; print_r($games); echo "</pre>";
	
		return $this->upcomingGames = $games;
	}

	function getAbschnittKommendeSpiele($styles = array())
	{
		//echo "getAbschnittKommendeSpiele";
		$data = array();
		$formerMannschaft = '';
		$formerDate = '';
		//echo "<pre>"; print_r($this->upcomingGames); echo "</pre>";
		if (!empty($this->upcomingGames))
		{
			$data['ueberschrift'] = 'Alle Spiele vom nächsten Spieltag (chronologisch)';
			foreach ($this->upcomingGames as $game)
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
	
	
	function getBerichte()
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->leftJoin($db->quoteName('aaa_spielbericht').' USING ('.$db->quoteName('spielIDhvw').')');
		$query->where('('.$db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartRecent).' AND '.$db->quote($this->dateEndRecent).')'.
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
	
	function getVorberichte()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->leftJoin($db->quoteName('aaa_spielvorschau').' USING ('.$db->quoteName('spielIDhvw').')');
		$query->where('('.$db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStartUpcoming).' AND '.$db->quote($this->dateEndUpcoming).') AND ('.$db->quoteName('vorschau').' IS NOT NULL)');
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


