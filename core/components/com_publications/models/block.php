<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Components\Publications\Models;

use Hubzero\Base\Object;

/**
 * Publications block base class
 */
class Block extends Object
{
	/**
	* Element name
	*
	* This has to be set in the final
	* renderer classes.
	*
	* @var string
	*/
	protected $_name = null;

	/**
	* Block manifest
	*
	* This has to be set in the final
	* renderer classes.
	*
	* @var string
	*/
	protected $_manifest = null;

	/**
	* Reference to the object that instantiated the element
	*
	* @var object
	*/
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @access protected
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	* Get the block name
	*
	* @access public
	* @return string type of the parameter
	*/
	public function getName()
	{
		return $this->_name;
	}

	/**
	* Get property
	*
	* @access public
	* @return string type of the parameter
	*/
	public function getProperty($name)
	{
		if (isset($this->$name))
		{
			return $this->$name;
		}

		return false;
	}
}