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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Short description for 'plgSearchWeightContributor'
 *
 * Long description (if any) ...
 */
class plgSearchWeightContributor extends \Hubzero\Plugin\Plugin
{
	/**
	 * Short description for 'onSearchWeightAll'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $terms Parameter description (if any) ...
	 * @param      object $res Parameter description (if any) ...
	 * @return     float Return description (if any) ...
	 */
	public static function onSearchWeightAll($terms, $res)
	{
		$pos_terms = $terms->get_positive_chunks();

		foreach (array_map('strtolower', $res->get_contributors()) as $contributor)
		{
			foreach ($pos_terms as $term)
			{
				if (strpos($contributor, $term) !== false)
				{
					return 1.0;
				}
			}
		}
		return 0.5;
	}
}

