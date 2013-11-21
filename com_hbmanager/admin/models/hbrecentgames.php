<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');


setlocale(LC_TIME, "de_DE");

class HBmanagerModelHBrecentGames extends JModel
{	
	private $recentGames = array();
	private $dateStart = "";
	private $dateEnd = "";
	
	function __construct() 
	{
		parent::__construct();
		
		setlocale(LC_TIME, "de_DE");
		//$datedefault = "last Saturday";
		//self::setDateStart($datedefault);
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
			//echo $todaydate = "2012-12-11";
			
			$query = "SELECT `datum` from `aaa_spiel` WHERE `datum` BETWEEN ".
				$db->q(strftime("%Y-%m-%d", strtotime('last Monday', strtotime('last friday', strtotime($todaydate))))).
				" AND " . $db->q($todaydate) .
				" ORDER BY `datum` ASC LIMIT 1";
			// Zur Kontrolle
			//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
			$db->setQuery($query);
			$result = $db->loadResult();
			if (!empty($result)) {
				$dates['startdateRecent'] = $result;
			}
			else {
				$query = "SELECT `datum` from `aaa_spiel` WHERE `datum` < ".
						$db->q(strftime("%Y-%m-%d", strtotime('last Monday', strtotime('last friday', strtotime($todaydate))))).
						" ORDER BY `datum` DESC LIMIT 1";
				// Zur Kontrolle
				//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
				$db->setQuery($query);
				$dates['startdateRecent'] = $db->loadResult();
			}
			$query = "SELECT `datum` from `aaa_spiel` WHERE `datum` BETWEEN ".
					$dates['startdateRecent']. " AND " . $db->q($todaydate) . 
					" ORDER BY `datum` DESC LIMIT 1";
			// Zur Kontrolle
			//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
			$db->setQuery($query);
			$dates['enddateRecent'] = $db->loadResult();
		}
			
		self::setDateStart($dates['startdateRecent']);
		self::setDateEnd($dates['enddateRecent']);
		//echo "<a>dates in recoverDates (output): </a><pre>"; print_r($dates); echo "</pre>";
	}
	
	function getDates()
	{
		$dates['startdateRecent'] = $this->dateStart;
		$dates['enddateRecent'] = $this->dateEnd;
		
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
		$query->select('`spielIDhvw`, `kuerzel`, `spielID`, `hallenNummer`, `datum`, `uhrzeit`, `heim`, `gast`, `toreHeim`, `toreGast`, `bemerkung`, `mannschaftID`, `reihenfolge`, `mannschaft`, `name`, `nameKurz`, `ligaKuerzel`, `liga`, `geschlecht`, `jugend`, `spielberichtID`, `bericht`, `spielerliste`, `zusatz`');
		$query->from('aaa_spiel');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->leftJoin($db->quoteName('aaa_spielbericht').' USING ('.$db->quoteName('spielIDhvw').')');
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
		
		return $this->recentGames = $sortedGames;
	}
	
	function getPreviousGamesArray($post = ''){
		for ($day = 0; $day <= 7; $day++) {
			$i = 0;
			
			while ($post["{$day}-{$i}_spielIDhvw"]) {
				
				$previousGames[$day][$i]['spielIDhvw'] = strip_tags(trim($post["{$day}-{$i}_spielIDhvw"]));
				$previousGames[$day][$i]['bericht'] = strip_tags(trim($post["{$day}-{$i}_bericht"]));
				$previousGames[$day][$i]['spielerliste'] = strip_tags(trim($post["{$day}-{$i}_spielerliste"]));
				
				$i++;
			}
		}
		
		// Zur Kontrolle
		// echo "<pre>PreviousGames";print_r($previousGames);echo "</pre>";
		
		return $previousGames;
	}
	
	function writeDB($previousGames = array())
	{
		if (empty($previousGames)) return;
		
		$db = $this->getDbo();
		
		//bei  und  whitespace entfernen (prüfen ob wirklich nicht leer)
		
		foreach ($previousGames as $day)
		{
			foreach ($day as $game)
			{
				$game['bericht'] = trim($game['bericht']);
				$game['spielerliste'] = trim($game['spielerliste']);
				
				if (!empty($game['bericht']) && !empty($game['spielerliste']))
				{
					
					$query = $db->getQuery(true);
					$query = "REPLACE INTO {$db->quoteName('aaa_spielbericht')} 
						({$db->quoteName('spielIDhvw')}, {$db->quoteName('bericht')}, {$db->quoteName('spielerliste')}) 
						VALUES ({$db->quote($game['spielIDhvw'])}, ";
						if (!empty($game['bericht'])) $query .= $db->quote($game['bericht']);
							else $query .= 'NULL';
							$query .= ', ';
						if (!empty($game['spielerliste'])) $query .= $db->quote($game['spielerliste']);
							else $query .= 'NULL';
						$query .= ");";
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
		$query->from('aaa_spielbericht');
		$query->leftJoin($db->quoteName('aaa_spiel').' USING ('.$db->quoteName('spielIDhvw').')');
		$query->leftJoin($db->quoteName('aaa_mannschaft').' USING ('.$db->quoteName('kuerzel').')');
		$query->where($db->quoteName('datum').' BETWEEN '.$db->quote($this->dateStart).' AND '.$db->quote($this->dateEnd));
		$query->order($db->quoteName('reihenfolge').' ASC');
		//echo $query;echo "<br />";
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo "<pre>Games \n"; print_r($games); echo "</pre>";
	
		// frühestes und spätestes betroffenes Datum
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
					$titledate = 'Wochenende '.ltrim(strftime("%d/", strtotime($dateframe->min)),'0').ltrim(strftime("%d. %b %Y", strtotime($dateframe->max)),'0');
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
		$content .= '<div class="newsspieltag">';
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
			$content .= '<table class="ergebnis">'.
							'<tbody>'.
								'<tr>'.
									'<td class="text">'.$game->heim.'</td><td>-</td><td class="text">'.$game->gast.'</td>'.
									'<td class="figure">'.$game->toreHeim.'</td><td>:</td><td class="figure">'.$game->toreGast.'</td>'.
								'</tr>'.
							'</tbody>'
						.'</table>';
			if (!empty($game->bericht))$content .= '<p class="spielbericht">'.$game->bericht.'</p>';
			if (!empty($game->bericht))$content .= '<p class="spielerliste">'.$game->spielerliste.'</p>';
			
			$content .= '</div>';
			
		}
		$content .= '</div>';
		//echo $content;
	
		$timestamp = time();
		$alias = date('Ymd-His', $timestamp).'-news-letztespiele';
	
		$table = JTable::getInstance('Content', 'JTable', array());
	
		$data = array(
				'alias' => $alias,
				'title' => 'Ergebnisse vom '.$titledate,
				//'title' => 'Vorschau für '.$titledateKW, //alternativ Anzeige KW statt Datum
				'introtext' => $content,
				//'fulltext' => '', //für Text der beim Klicken auf "Weiterlesen" erscheint
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
		$query->select($db->quoteName('id'));
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
	
		// in Frontpage DB table einfügen
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


