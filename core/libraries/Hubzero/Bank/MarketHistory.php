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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Bank;

/**
 * Market History class:
 * Logs batch transactions, royalty distributions and other big transactions
 */
class MarketHistory extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__market_history', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->itemid = intval($this->itemid);
		if (!$this->itemid)
		{
			$this->setError(\Lang::txt('Entry must have an item ID.'));
		}

		$this->category = trim($this->category);
		if (!$this->category)
		{
			$this->setError(\Lang::txt('Entry must have a category.'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->date)
		{
			$this->date = \Date::toSql();
		}

		return true;
	}

	/**
	 * Get the ID of a record matching the data passed
	 *
	 * @param   mixed    $itemid    Integer
	 * @param   string   $action    Transaction type
	 * @param   string   $category  Transaction category
	 * @param   string   $created   Transaction date
	 * @param   string   $log       Transaction log
	 * @return  integer
	 */
	public function getRecord($itemid=0, $action='', $category='', $created='', $log = '')
	{
		if ($itemid === NULL)
		{
			$itemid = $this->itemid;
		}
		if ($action === NULL)
		{
			$action = $this->action;
		}
		if ($category === NULL)
		{
			$category = $this->category;
		}

		$sql = "SELECT id FROM $this->_tbl";

		$where = array();
		if ($itemid)
		{
			$where[] = "itemid=" . $this->_db->quote($itemid);
		}
		if ($action)
		{
			$where[] = "action=" . $this->_db->quote($action);
		}
		if ($category)
		{
			$where[] = "category=" . $this->_db->quote($category);
		}
		if ($created)
		{
			$where[] = "`date` LIKE '" . $created . "%'";
		}
		if ($log)
		{
			$where[] = "log=" . $this->_db->quote($log);
		}
		if (count($where) > 0)
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		$sql .= " LIMIT 1";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}

