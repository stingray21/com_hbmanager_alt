<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');


setlocale(LC_TIME, "de_DE");

class hbmanagerModelHbprevgames extends JModel
{	
	private $prevGames = array();
	private $dateStart = "";
	private $dateEnd = "";
	
	function __construct() 
	{
		parent::__construct();
		
		setlocale(LC_TIME, "de_DE");
		
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
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$mannschaften = $db->loadObjectList();
		return $mannschaften;
	}
	
	function setDates($dates = null)
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		$db = $this->getDbo();
		
		if (is_null($dates)){
			$todaydate = strftime("%Y-%m-%d", time());
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
		}
			
		self::setDateStart($dates['startdatePrev']);
		self::setDateEnd($dates['enddatePrev']);
	}
	
	function getDates()
	{
		$dates['startdatePrev'] = $this->dateStart;
		$dates['enddatePrev'] = $this->dateEnd;
		
		return $dates;
	}
	
	function setDateStart($date)
	{
		$this->dateStart = strftime("%Y-%m-%d", strtotime($date));
	}
	
	function setDateEnd($date)
	{
		if ($date > $this->dateStart) {
			$this->dateEnd = strftime("%Y-%m-%d", strtotime($date));
		}
		else {
			$db = $this->getDbo();
			$query = "SELECT `datum` from `hb_spiel` WHERE `datum` < ".
				$db->q(strftime("%Y-%m-%d", 
						strtotime('+1 week', strtotime($date)))).
				" ORDER BY `datum` DESC LIMIT 1";
			//echo '=> model->$query <br><pre>".$query."</pre>';
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
		$query->select('`spielIDhvw`, `kuerzel`, `spielID`, `hallenNummer`, '.
				'`datum`, `uhrzeit`, `heim`, `gast`, `toreHeim`, `toreGast`, '.
				'`bemerkung`, `mannschaftID`, `reihenfolge`, `mannschaft`, '.
				'`name`, `nameKurz`, `ligaKuerzel`, `liga`, `geschlecht`, '.
				'`jugend`, `spielberichtID`, `bericht`, `spielerliste`, '.
				'`zusatz`, `halbzeitstand`, `spielverlauf`');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_mannschaft').' USING ('.
				$db->qn('kuerzel').')');
		$query->leftJoin($db->qn('hb_spielbericht').' USING ('.
				$db->qn('spielIDhvw').')');
		$query->where($db->qn('datum').' BETWEEN '.
				$db->q($this->dateStart).' AND '.$db->q($this->dateEnd));
		$query->order($db->qn('datum').' ASC, '.$db->qn('uhrzeit').' ASC');
		$db->setQuery($query);
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$games = $db->loadObjectList();
		//echo '=> model->$games <br><pre>".$games."</pre>';
		
		// sort games by date
		$sortedGames = array();
		foreach ($games as $game){
			$sortedGames[$game->datum][] = $game;
		}
		//echo '=> model->$sortedGames <br><pre>".$sortedGames."</pre>';
		
		return $this->prevGames = $sortedGames;
	}
	
	function updateDB($previousGames = array())
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($previousGames);echo'</pre>';
		if (empty($previousGames)) return;
		
		$db = $this->getDbo();
		foreach ($previousGames as $game)
		{
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($game);echo'</pre>';
			foreach ($game as $key => $value)
			{
				$game[$key] = trim($value);
			}
			
			if (count(array_filter($game)) > 1)
			{
				$query = $db->getQuery(true);
				$query = "REPLACE INTO ".$db->qn('hb_spielbericht')."(".
						$db->qn('spielIDhvw').", ".$db->qn('bericht').", ".
						$db->qn('spielerliste').", ".$db->qn('zusatz').", ".
						$db->qn('spielverlauf').", ".$db->qn('halbzeitstand')
						.")".
						"VALUES (".$db->q($game['spielIDhvw']).", ";
					if (empty($game['bericht'])) $query .= 'NULL, ';
						else $query .= $db->q($game['bericht']).", ";
					if (empty($game['spielerliste'])) $query .= 'NULL, ';
						else $query .= $db->q($game['spielerliste']);
					if (empty($game['zusatz'])) $query .= 'NULL, ';
						else $query .= $db->q($game['zusatz']);
					if (empty($game['spielverlauf'])) $query .= 'NULL, ';
						else $query .= $db->q($game['spielverlauf']);
					if (empty($game['halbzeitstand'])) $query .= 'NULL ';
						else $query .= $db->q($game['halbzeitstand']);
					$query .= ");";
				//echo __FILE__.'('.__LINE__.'):<pre>';print_r($query);echo'</pre>';
				$db->setQuery($query);
				try {
					// Execute the query in Joomla 2.5.
					$result = $db->query();
				}
				catch (Exception $e) {
					// catch any database errors.
				}
			}
		}
	}
	
	
	function writeNews()
	{
		// content article
		$db = $this->getDbo();
	
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_spiel');
		$query->leftJoin($db->qn('hb_spielbericht').
				' USING ('.$db->qn('spielIDhvw').')');
		$query->leftJoin($db->qn('hb_mannschaft').
				' USING ('.$db->qn('kuerzel').')');
		$query->where($db->qn('datum').' BETWEEN '.
				$db->q($this->dateStart).' AND '.$db->q($this->dateEnd));
		$query->order($db->qn('reihenfolge').' ASC');
		//echo '=> model->$query <br><pre>".$query."</pre>';
		$db->setQuery($query);
		$games = $db->loadObjectList();
		//echo '=> model->$games <br><pre>".$games."</pre>';
		
		if (!empty($games))
		{
			// earliest and latest included date 
			$query = $db->getQuery(true);
			$query->select('MIN('.$db->qn('datum').') AS min, MAX('.
					$db->qn('datum').') AS max');
			$query->from('hb_spiel');
			$query->leftJoin($db->qn('hb_spielbericht').' USING ('.
					$db->qn('spielIDhvw').')');
			$query->where($db->qn('datum').' BETWEEN '.
					$db->q($this->dateStart).' AND '.$db->q($this->dateEnd));
			//echo '=> model->$query <br><pre>".$query."</pre>';
				$db->setQuery($query);
			$dateframe = $db->loadObject();
			//echo '=> model->$dateframe <br><pre>".$dateframe."</pre>';


						// format date
			$minDate = strtotime($dateframe->min);
			$maxDate = strtotime($dateframe->max);
			if ($minDate === $maxDate)
			{
				$titledate = strftime("%A, ", $minDate).
						ltrim(strftime("%d. %b %Y", $minDate),'0');
				$titledateKW = 'KW'.ltrim(strftime("%V", $minDate),'0');
			}
			else
			{
				if (strftime("%u", $minDate) == 6 AND 
						strftime("%u", $maxDate) == 7)
				{
					if (strftime("%m", $minDate) == strftime("%m", $maxDate))
					{
						$titledate = 'Wochenende '.
							ltrim(strftime("%d/", $minDate),'0').
							ltrim(strftime("%d. %b %Y", $maxDate),'0');
					}
					else
					{
						$titledate = 'Wochenende '.
							ltrim(strftime("%d. %b / ", $minDate),'0').
							ltrim(strftime("%d. %b %Y", $maxDate),'0');
					}
				}
				else
				{
					$titledate = ltrim(strftime("%d. %b %Y", $minDate),'0').
							' bis '.ltrim(strftime("%d. %b %Y", $maxDate),'0');
				}

				if (strftime("%V", $minDate) == strftime("%V", $maxDate))
				{

					$titledateKW = 'KW'.ltrim(strftime("%V", $minDate),'0');
				}
				else
				{
					$titledateKW = 'KW'.ltrim(strftime("%V", $minDate),'0').
							' bis KW'.ltrim(strftime("%V", $maxDate),'0');
				}
			}

			$prevTeam = NULL;
			$content .= '<div class="newsspieltag">';
			foreach ($games as $game)
			{	
				if ($prevTeam !== $game->mannschaft)
				{
					$content .= '<h4>'.
							'<a href="'.JURI::Root().'index.php/'.
							strtolower($game->kuerzel).'-home">'.
							$game->mannschaft.' - '.$game->liga
							.' ('.$game->ligaKuerzel.')</a>'.
							'</h4>';
				}
				$prevTeam = $game->mannschaft;

				$content .= '<div>';
				$content .= '<table class="ergebnis">'.
								'<tbody>'.
									'<tr>'.
										'<td class="text">'.$game->heim.'</td>'.
										'<td>-</td>'.
										'<td class="text">'.$game->gast.'</td>'.
										'<td class="figure">'.$game->toreHeim.
										'</td><td>:</td>'.
										'<td class="figure">'.$game->toreGast.
										'</td>'.
									'</tr>'.
								'</tbody>'.
							'</table>';
				if (!empty($game->bericht))
					$content .= '<p class="spielbericht">'.$game->bericht.'</p>';
				if (!empty($game->bericht))
					$content .= '<p class="spielerliste">'.
						'<span>Es spielten:</span><br />'.
						$game->spielerliste.'</p>';
				$content .= '</div>';
			}
			$content .= '</div>';
			//echo '=> model->$content <br><pre>".$content."</pre>';

			$timestamp = time();
			$alias = date('Ymd-His', $timestamp).'-news-letztespiele';

			$table = JTable::getInstance('Content', 'JTable', array());

			$data = array(
					'alias' => $alias,
					'title' => 'Ergebnisse vom '.$titledate,
					'introtext' => $content,
					// for text that appears by clicking on 'more'
					//'fulltext' => '',
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

			// put article on the frontpage

			// get content_ID
			$query = $db->getQuery(true);
			$query->select($db->qn('id'));
			$query->from($db->qn('#__content'));
			$query->where($db->qn('alias').' = '.$db->q($alias));
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			$contentID = $db->loadResult();
			//echo '=> model->$contentID<br><pre>'; 
			//print_r($contentID); echo "</pre>";

			// increment the order of the articles that are already on the frontpage
			$query = $db->getQuery(true);
			$query->update($db->qn('#__content_frontpage'));
			$query->set($db->qn('ordering').' = '.$db->qn('ordering').'+1');
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				$result = $db->query();
			}
			catch (Exception $e) {
				// catch any database errors.
			}

			// insert in frontpage DB table
			$columns = array('content_id', 'ordering');
			$values = array($db->q($contentID), 1);
			$query = $db->getQuery(true);
			$query->insert($db->qn('#__content_frontpage'));
			$query->columns($db->qn($columns));
			$query->values(implode(',', $values));
			//echo '=> model->$query <br><pre>".$query."</pre>';
			$db->setQuery($query);
			try {
				// Execute the query in Joomla 2.5.
				$result = $db->query();
			}
			catch (Exception $e) {
				// catch any database errors.
			}
		}
	}
	
}