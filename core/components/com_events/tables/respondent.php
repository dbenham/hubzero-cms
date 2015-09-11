<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

namespace Components\Events\Tables;

use Exception;

/**
 * Event respondent
 */
class Respondent extends \JTable
{
	/**
	 * Description for 'filters'
	 *
	 * @var mixed
	 */
	private $filters      = array();

	/**
	 * Description for 'order'
	 *
	 * @var string
	 */
	private $order        = NULL;

	/**
	 * Description for 'order_desc'
	 *
	 * @var string
	 */
	private $order_desc   = NULL;

	/**
	 * Description for 'search_terms'
	 *
	 * @var string
	 */
	private $search_terms = '';

	/**
	 * Description for 'limit'
	 *
	 * @var mixed
	 */
	private $limit        = 0;

	/**
	 * Description for 'offset'
	 *
	 * @var mixed
	 */
	private $offset       = 0;

	/**
	 * Short description for 'getRacialIdentification'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $resp_id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function getRacialIdentification($resp_id)
	{
		$dbh = \App::get('db');
		if (is_array($resp_id))
		{
			$dbh->setQuery('SELECT respondent_id, group_concat(concat(race, coalesce(concat(\'(\', tribal_affiliation, \')\'), \'\')) separator \', \') AS identification FROM #__events_respondent_race_rel WHERE respondent_id IN (' . implode(', ', array_map('intval', $resp_id)) . ') GROUP BY respondent_id');
			return $dbh->loadAssocList('respondent_id');
		}
		else
		{
			$dbh->setQuery('SELECT group_concat(concat(race, coalesce(concat(\'(\', tribal_affiliation, \')\'), \'\')) separator \', \') FROM #__events_respondent_race_rel WHERE respondent_id = ' . intval($resp_id) . ' GROUP BY respondent_id');
			return $dbh->loadResult();
		}
	}

	/**
	 * Short description for 'getSearchTerms'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function getSearchTerms()
	{
		return $this->search_terms;
	}

	/**
	 * Short description for 'getOrdering'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public function getOrdering()
	{
		return $this->order_desc;
	}

	/**
	 * Short description for 'getPaginator'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public function getPaginator()
	{
		return new \Hubzero\Pagination\Paginator($this->getCount(), $this->limit, $this->offset);
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @return     void
	 * @throws Exception  Exception description (if any) ...
	 * @throws Exception  Exception description (if any) ...
	 */
	public function __construct($filters)
	{
		parent::__construct('#__events_respondents', 'id', \App::get('db'));

		if (array_key_exists('sortby', $filters))
		{
			if (preg_match('/(registered|name|special|id)(?:\ (ASC|DESC))?/', $filters['sortby'], $match))
			{
				if ($match[1] == 'name')
				{
					$this->order_desc = 'name ' . $match[2];
					$this->order = ' ORDER BY last_name, first_name';
				}
				else if ($match[1] == 'special')
				{
					$this->order_desc = 'special ' . $match[2];
					$this->order = ' ORDER BY CASE WHEN disability_needs OR dietary_needs IS NOT NULL THEN 1 WHEN comment IS NOT NULL THEN 2 ELSE 3 END ' . $match[2];
				}
				else
				{
					$this->order_desc = $filters['sortby'];
					$this->order = ' ORDER BY ' . $filters['sortby'];
				}
				unset($filters['sortby']);
			}
			else
			{
				throw new Exception('Invalid sorting criterium: ' . $filters['sortby']);
			}
		}

		if (array_key_exists('limit', $filters))
		{
			$this->limit  = intval($filters['limit']);
			$this->offset = array_key_exists('offset', $filters) ? intval($filters['offset']) : 0;

			if (array_key_exists('offset', $filters))
			{
				unset($filters['offset']);
			}
			unset($filters['limit']);
		}

		foreach ($filters as $key=>$val)
		{
			switch ($key)
			{
				case 'id':
					$this->filters[] = (is_array($val))
						? 'event_id IN (' . implode(', ', array_map('intval', $val)) . ')'
						: 'event_id = ' . intval($val);
				break;
				case 'respondent_id':
					$this->filters[] = 'id = ' . intval($val);
				break;
				case 'search':
					if (!empty($val))
					{
						$this->filters[] = "concat(first_name, ' ', last_name) LIKE " . $this->_db->quote($val . '%');
						$this->searchTerms = htmlentities($val);
					}
				break;
				default:
					throw new Exception('Unhandled filter type ' . $key);
				break;
			}
		}
		$this->filters = implode(' AND ', array_values($this->filters));
	}

	/**
	 * Short description for 'fetch'
	 *
	 * Long description (if any) ...
	 *
	 * @param      boolean $bounded Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function fetch($bounded = true)
	{
		$this->_db->setQuery(
			'SELECT ' . ($bounded ? '*' : 'COUNT(*)') . " FROM $this->_tbl WHERE " . $this->filters . $this->order .
				($bounded && $this->limit != 0 ? ' LIMIT ' . $this->offset . ', ' . $this->limit : '')
		);
		return $bounded ? $this->_db->loadObjectList() : $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function getRecords()
	{
		return $this->fetch();
	}

	/**
	 * Short description for 'getCount'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function getCount()
	{
		return $this->fetch(false);
	}

	/**
	 * Short description for 'deleteRespondents'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $event_id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function deleteRespondents($event_id=NULL)
	{
		if ($event_id === NULL)
		{
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE event_id=" . intval($event_id));
		return $this->_db->query();
	}

	/**
	 * Check for unique registration per event
	 *
	 * @param  string $email
	 * @param  int $eventId
	 * @return int
	 */
	public static function checkUniqueEmailForEvent($email, $eventId)
	{
		$db = \App::get('db');
		$sql = "SELECT COUNT(*) FROM `#__events_respondents` WHERE `event_id`=" . $db->quote($eventId) . " AND `email`=" .$db->quote($email);
		$db->setQuery($sql);
		return $db->loadResult();
	}
}
