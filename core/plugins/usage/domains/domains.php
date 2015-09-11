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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Usage plugin class for domains
 */
class plgUsageDomains extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the name of the area this plugin retrieves records for
	 *
	 * @return     array
	 */
	public function onUsageAreas()
	{
		return array(
			'domains' => Lang::txt('PLG_USAGE_DOMAINS')
		);
	}

	/**
	 * Event call for displaying usage data
	 *
	 * @param      string $option        Component name
	 * @param      string $task          Component task
	 * @param      object $db            JDatabase
	 * @param      array  $months        Month names (Jan -> Dec)
	 * @param      array  $monthsReverse Month names in reverse (Dec -> Jan)
	 * @param      string $enddate       Time period
	 * @return     string HTML
	 */
	public function onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)
	{
		// Check if our task is the area we want to return results for
		if ($task)
		{
			if (!in_array($task, $this->onUsageAreas())
			 && !in_array($task, array_keys($this->onUsageAreas())))
			{
				return '';
			}
		}

		// Set some vars
		$thisyear = date("Y");

		$o = \Components\Usage\Helpers\Helper::options($db, $enddate, $thisyear, $monthsReverse, 'check_for_regiondata');

		// Build HTML
		$html  = '<form method="post" action="'. Route::url('index.php?option=' . $option . '&task=' . $task) . '">' . "\n";
		$html .= "\t" . '<fieldset class="filters">' . "\n";
		$html .= "\t\t" . '<label>' . "\n";
		$html .= "\t\t\t" . Lang::txt('PLG_USAGE_SHOW_DATA_FOR') . ': ' . "\n";
		$html .= "\t\t\t" . '<select name="selectedPeriod" id="selectedPeriod">' . "\n";
		$html .= $o;
		$html .= "\t\t\t" . '</select>' . "\n";
		$html .= "\t\t" . '</label> <input type="submit" value="' . Lang::txt('PLG_USAGE_VIEW') . '" />' . "\n";
		$html .= "\t" . '</fieldset>' . "\n";
		$html .= '</form>' . "\n";
		$html .= \Components\Usage\Helpers\Helper::toplist($db, 10, 1, $enddate);
		$html .= \Components\Usage\Helpers\Helper::toplist($db, 17, 2, $enddate);
		$html .= \Components\Usage\Helpers\Helper::toplist($db, 11, 3, $enddate);
		$html .= \Components\Usage\Helpers\Helper::toplist($db,  9, 4, $enddate);
		$html .= \Components\Usage\Helpers\Helper::toplist($db, 12, 5, $enddate);
		$html .= \Components\Usage\Helpers\Helper::toplist($db, 19, 6, $enddate);
		$html .= \Components\Usage\Helpers\Helper::toplist($db, 18, 7, $enddate);
		$html .= \Components\Usage\Helpers\Helper::toplist($db,  7, 8, $enddate);

		// Return HTML
		return $html;
	}
}
