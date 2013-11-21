<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');


setlocale(LC_TIME, "de_DE");

class HBmanagerModelHBupcomingGames extends JModel
{	
	private $upcomingGames = array();
	private $dateStart = "";
	private $dateEnd = "";
	
	function __construct() 
	{
		parent::__construct();
	
		setlocale(LC_TIME, "de_DE");
		$datedefault = "next Saturday";
		self::setDateStart($datedefault);
		//$this->dateStart = strftime("%Y-%m-%d", strtotime($datedefault)-432000);
		//$this->dateEnd = strftime("%Y-%m-%d", strtotime($datedefault)+518400);
		
		$db = $this->getDbo();
		$db->setQuery("SET lc_time_names = 'de_DE'");
		try
		{
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		}
		catch (Exception $e) {
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
	
	function setDates($dates)
	{
		//echo "<a>dates in setDates (input): </a><pre>"; print_r($dates); echo "</pre>";
		$db = $this->getDbo();
		
		if (empty($dates)){
			$todaydate = strftime("%Y-%m-%d", time());
			//echo $todaydate = "2013-10-23";
			
			$query = "SELECT `datum` from `aaa_spiel` WHERE `datum` BETWEEN ".
				$db->q($todaydate). " AND " . 
				$db->q(strftime("%Y-%m-%d", strtotime('next Monday', strtotime('next friday', strtotime($todaydate))))).
				" ORDER BY `datum` ASC LIMIT 1";
			// Zur Kontrolle
			//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
			$db->setQuery($query);
			$result = $db->loadResult();
			if (!empty($result)) {
				$dates['startdateUpcoming'] = $result;
			}
			else {
				$query = "SELECT `datum` from `aaa_spiel` WHERE `datum` < ".
						$db->q(strftime("%Y-%m-%d", strtotime('next Monday', strtotime('next friday', strtotime($todaydate))))).
						" ORDER BY `datum` DESC LIMIT 1";
				// Zur Kontrolle
				//echo "<a>ModelHB->query (else): </a><pre>"; echo $query; echo "</pre>";
				$db->setQuery($query);
				$dates['startdateUpcoming'] = $db->loadResult();
			}
			$query = "SELECT `datum` from `aaa_spiel` WHERE `datum` BETWEEN ".
					$db->q($dates['startdateUpcoming']) . " AND " . 
					$db->q(strftime("%Y-%m-%d", strtotime('next friday', strtotime($dates['startdateUpcoming'])))).
					" ORDER BY `datum` DESC LIMIT 1";
			// Zur Kontrolle
			//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
			$db->setQuery($query);
			$dates['enddateUpcoming'] = $db->loadResult();
		}
			
		self::setDateStart($dates['startdateUpcoming']);
		self::setDateEnd($dates['enddateUpcoming']);
		//echo "<a>dates in recoverDates (output): </a><pre>"; print_r($dates); echo "</pre>";
	}
		
	function getDates()
	{
		$dates['startdateUpcoming'] = $this->dateStart;
		$dates['enddateUpcoming'] = $this->dateEnd;
	
		return $dates;
	}
	
	function setDateStart($date)
	{
		$this->dateStart = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function setDateEnd($date)
	{
		//echo $date .' and '.$this->dateStart.'<br>';
		if ($date > $this->dateStart) {
			$this->dateEnd = strftime("%Y-%m-%d", strtotime($date));
		}
		else {
			$db = $this->getDbo();
			$query = "SELECT `datum` from `aaa_spiel` WHERE `datum` < ".
					$db->q(strftime("%Y-%m-%d", strtotime('+1 week', strtotime($date)))).
					" ORDER BY `datum` DESC LIMIT 1";
			// Zur Kontrolle
			//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
			$db->setQuery($query);
			$this->dateEnd = $db->loadResult();
		}
	}
	
	function getDateStart()
	{
		return $this->dateStart;
	}
	
	function getDateEnd()
	{
		return $this->dateEnd;
	}
	
	function getGames()
	{
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('`spielIDhvw`, `kuerzel`, `spielID`, `hallenNummer`, `datum`, `uhrzeit`,'.
				' `heim`, `gast`, `toreHeim`, `toreGast`, `bemerkung`, `mannschaftID`, `reihenfolge`, '.
				'`mannschaft`, `name`, `nameKurz`, `ligaKuerzel`, `liga`, `geschlecht`, `jugend`, '.
				'`spielvorschauID`, `vorschau`, `treffOrt`, `treffZeit`');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->leftJoin($db->quoteName('aaa_spielvorschau').' USING ('.$db->quoteName('spielIDhvw').')');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStart).' AND '.$db->quote($this->dateEnd));
		$query->order($db->quoteName('datum').' ASC, '.$db->quoteName('uhrzeit').' ASC');
		$db->setQuery($query);
		//echo $query;echo "<br />";
		$games = $db->loadObjectList();
		//echo "<pre>"; print_r($games); echo "</pre>";
	
		foreach ($games as $game){
			$sortedGames[$game->datum][] = $game;
		}
		//echo "<pre>"; print_r($sortedGames); echo "</pre>";
	
		return $this->upcomingGames = $sortedGames;
	}
	
	function getUpcomingGamesArray($post = ''){
		
		for ($day = 0; $day <= 7; $day++) {
			$i = 0;
			
			while ($post["{$day}-{$i}_spielIDhvw"]) {
				$upcomingGames[$day][$i]['spielIDhvw'] = strip_tags(trim($post["{$day}-{$i}_spielIDhvw"]));
				$upcomingGames[$day][$i]['vorbericht'] = strip_tags(trim($post["{$day}-{$i}_vorbericht"]));
				$upcomingGames[$day][$i]['treffOrt'] = strip_tags(trim($post["{$day}-{$i}_treffOrt"]));
				$upcomingGames[$day][$i]['treffZeit'] = strip_tags(trim($post["{$day}-{$i}_treffZeit"]));
				
				$i++;
			}
		}
		// echo "<pre>"; print_r($upcomingGames); echo "</pre>";
		
		return $upcomingGames;
	}
	
	function updateDB($upcomingGames = array())
	{
		if (empty($upcomingGames)) return;
		
		$db = $this->getDbo();
		
		foreach ($upcomingGames as $day)
		{
			foreach ($day as $game)
			{
				if (!empty($game['vorbericht']) OR !empty($game['treffOrt']) OR !empty($game['treffZeit']))
				{
					
					$query = $db->getQuery(true);
					$query->select('teamkey');
					$query->from('hb_teams');
					$query->where($db->quoteName('league').' = '.$db->quote($game['klasse']));
					$db->setQuery($query);
					$teamkey = $db->loadResult();
					
					$query = $db->getQuery(true);
					$query = "REPLACE INTO {$db->quoteName('aaa_spielvorschau')} 
						({$db->quoteName('SpielIDhvw')}, {$db->quoteName('vorschau')}, {$db->quoteName('treffOrt')}, {$db->quoteName('treffZeit')}) 
						VALUES (".$db->quote($game['spielIDhvw']).', ';
						if (!empty($game['vorbericht'])) $query .= $db->quote($game['vorbericht']);
							else $query .= 'NULL';
							$query .= ', ';
						if (!empty($game['treffOrt'])) $query .= $db->quote($game['treffOrt']);
							else $query .= 'NULL';
							$query .= ', ';
						if (!empty($game['treffZeit'])) $query .= $db->quote($game['treffZeit']);
							else $query .= 'NULL';
						$query .=');';
					//echo $query; echo '<br />';
					$db->setQuery($query);
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
			}
		}
		
	}
	
	function writeNews()
	{
		// Inhalt Artikel
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_spielvorschau');
		$query->leftJoin($db->quoteName('aaa_spiel').' USING ('.$db->quoteName('spielIDhvw').')');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStart).' AND '.$db->quote($this->dateEnd));
		$query->order($db->quoteName('reihenfolge').' ASC');
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo "<pre>Games \n"; print_r($games); echo "</pre>";
		
		// fr�hestes und sp�testes betroffenes Datum
		$query = $db->getQuery(true);
		$query->select('MIN('.$db->quoteName('datum').') AS min, MAX('.$db->quoteName('datum').') AS max');
		$query->from('aaa_spielvorschau');
		$query->leftJoin($db->quoteName('aaa_spiel').' USING ('.$db->quoteName('spielIDhvw').')');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStart).' AND '.$db->quote($this->dateEnd));
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$dateframe = $db->loadObject();
		//echo "<pre>Datum: \n"; print_r($dateframe); echo "</pre>";
		
		
		// Darstellung Datum
		if ($dateframe->min == $dateframe->max)
		{
			$titledate = strftime("%a, ", strtotime($dateframe->min)).ltrim(strftime("%d. %b %Y", strtotime($dateframe->min)),'0');
			$titledateKW = 'KW'.ltrim(strftime("%V", strtotime($dateframe->min)),'0');
		}
		else
		{
			if (strftime("%u", strtotime($dateframe->min)) == 6 AND strftime("%u", strtotime($dateframe->max)) == 7)
			{
				if (strftime("%m", strtotime($dateframe->min)) == strftime("%m", strtotime($dateframe->max)))
				{
					$titledate = 'Wochenende '.ltrim(strftime("%d./", strtotime($dateframe->min)),'0').ltrim(strftime("%d. %b %Y", strtotime($dateframe->max)),'0');
				}
				else
				{
					$titledate = 'Wochenende '.ltrim(strftime("%d. %b / ", strtotime($dateframe->min)),'0').ltrim(strftime("%d. %b %Y", strtotime($dateframe->max)),'0');
				}
			}
			else
			{
				$titledate = ltrim(strftime("%d. %b %Y", strtotime($dateframe->min)),'0').' bis '.ltrim(strftime("%d. %b %Y", strtotime($dateframe->max)),'0');
			}
				
			if (strftime("%V", strtotime($dateframe->min)) == strftime("%V", strtotime($dateframe->max)))
			{
	
				$titledateKW = 'KW'.ltrim(strftime("%V", strtotime($dateframe->min)),'0');
			}
			else
			{
				$titledateKW = 'KW'.ltrim(strftime("%V", strtotime($dateframe->min)),'0').' bis KW'.ltrim(strftime("%V", strtotime($dateframe->max)),'0');
			}
		}
		
		$formerMannschaft = '';
		$content = '<div class="newsspieltag">';
		foreach ($games as $game)
		{
				
			if ($formerMannschaft != $game->mannschaft)
			{
				$content .= '<h4>'.
						'<a href="'.JURI::Root().'index.php/'.strtolower($game->kuerzel).'-home">'.
						$game->mannschaft.' - '.$game->liga.' ('.$game->ligaKuerzel.')</a>'.
						'</h4>';
			}
			$formerMannschaft = $game->mannschaft;
				
			$content .= '<div>';
			$content .= '<dl class="vorbericht">'.
					'<dt>Spiel</dt><dd>'.$game->heim.' - '.$game->gast.'</td>'.
					'<dd>'.strftime("%A, %d.%m.%Y", strtotime($game->datum)).' um '.strftime("%H:%M Uhr", strtotime($game->uhrzeit)).'</dd>';
			if (!empty($game->treffOrt) OR !empty($game->treffZeit))
			{
				$content .= '<dt>Treffpunkt';
				if ($game->hallenNummer != '7014') $content.= '/Abfahrt';
				$content .= '</dt>';
				$content .= '<dd>'.$game->treffOrt;
				if (!empty($game->treffZeit)) $content .= ' um '.strftime("%H:%M Uhr", strtotime($game->treffZeit));
				$content .= '</dd>';
			}
			$content .= '</dl>';
			if (!empty($game->vorschau))$content .= '<p class="vorbericht">'.$game->vorschau.'</p>';
		
			$content .= '</div>';
				
		}
		$content .= '</div>';
		//echo $content;
		
		$timestamp = time();
		$alias = date('Ymd-His', $timestamp).'-news-vorschau'; 
		
		$table = JTable::getInstance('Content', 'JTable', array());
		
		$data = array(
				'alias' => $alias,
				'title' => 'Vorschau f�r '.$titledate, 
				//'title' => 'Vorschau f�r '.$titledateKW, //alternativ Anzeige KW statt Datum
				'introtext' => $content,
				//'fulltext' => '', //f�r Text der beim Klicken auf "Weiterlesen" erscheint
				'state' => 1,
				'catid' => 8,
				'featured' => 1
		);
		
		// Bind data
		if (!$table->bind($data))
		{
			$this->setError($table->getError());
			return false;
		}
		
		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());
			return false;
		}
		
		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());
			return false;
		}
		
		//To reorder the category
		//$table->reorder('catid = '.(int) $table->catid.' AND state >= 0');
		
		
		
		
		
		// auf frontpage setzen
		
		// content_ID ermitteln
		$query = $db->getQuery(true);
		$query->select($db->quoteName('ID'));
		$query->from($db->quoteName('#__content'));
		$query->where($db->quoteName('alias').' = '.$db->quote($alias));
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$contentID = $db->loadResult();
		//echo "<pre>ID (Content): \n"; print_r($contentID); echo "</pre>";
		
		// Reihenfolge bereits auf frontpage dargestellter Artikel inkrementieren
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__content_frontpage'));
		$query->set($db->quoteName('ordering').' = '.$db->quoteName('ordering').'+1');
		//echo $query;echo "<br />";
		$db->setQuery($query);
		try 
		{
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		} 
			catch (Exception $e) {
			// catch any database errors.
		}
		
		// in Frontpage DB table einf�gen
		$columns = array('content_id', 'ordering');
		$values = array($db->quote($contentID), 1);
		$query = $db->getQuery(true);
		$query->insert($db->quoteName('#__content_frontpage'));
		$query->columns($db->quoteName($columns));
		$query->values(implode(',', $values));
		//echo $query;echo "<br />";
		$db->setQuery($query);
		try
		{
			// Execute the query in Joomla 2.5.
			$result = $db->query();
		}
		catch (Exception $e) {
			// catch any database errors.
		}
		
	}
	

	// general function hbmanager
	function formatInput ($input, $i)
	{
		$input = preg_replace('/name=\"([\S]{1,})\[([\S]{1,})\]/', "name=\"$1[".$i."][$2]", $input);
		$input = preg_replace('/id=\"([\S]{1,})_([\S]{1,})/', "id=\"$1_".$i."_$2", $input);
		echo $input;
	}
}


