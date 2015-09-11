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

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Form Field Place class.
 */
class JFormFieldClient extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Client';

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$onchange = $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$options = array();
		foreach ($this->element->children() as $option)
		{
			$options[] = Html::select('option', $option->attributes('value'), Lang::txt(trim($option->data())));
		}
		$options[] = Html::select('option', '0', Lang::txt('JSITE'));
		$options[] = Html::select('option', '1', Lang::txt('JADMINISTRATOR'));
		$return = Html::select('genericlist', $options, $this->name, $onchange, 'value', 'text', $this->value, $this->id);
		return $return;
	}
}
