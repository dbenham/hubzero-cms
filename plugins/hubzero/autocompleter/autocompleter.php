<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Short description for 'plgHubzeroAutocompleter'
 * 
 * Long description (if any) ...
 */
class plgHubzeroAutocompleter extends JPlugin
{

	/**
	 * Description for '_pushscripts'
	 * 
	 * @var boolean
	 */
	private $_pushscripts = true;

	/**
	 * Short description for 'plgHubzeroAutocompleter'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'hubzero', 'autocompleter' );
		$this->loadLanguage();
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$this->params = new JParameter($this->_plugin->params);
		}
	}

	/**
	 * Short description for 'onGetAutocompleter'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $atts Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function onGetAutocompleter( $atts )
	{
		// Ensure we have an array
		if (!is_array($atts)) {
			$atts = array();
		}
		
		//var to hold scripts
		$scripts = "";

		// Set some parameters
		$opt   = (isset($atts[0])) ? $atts[0] : 'tags';  // The component to call
		$name  = (isset($atts[1])) ? $atts[1] : 'tags';  // Name of the input field
		$id    = (isset($atts[2])) ? $atts[2] : 'act';   // ID of the input field
		$class = (isset($atts[3])) ? 'autocomplete '.$atts[3] : 'autocomplete';  // CSS class(es) for the input field
		$value = (isset($atts[4])) ? $atts[4] : '';      // The value of the input field
		$size  = (isset($atts[5])) ? $atts[5] : '';      // The size of the input field
		$wsel  = (isset($atts[6])) ? $atts[6] : '';      // AC autopopulates a select list based on choice?
		$type  = (isset($atts[7])) ? $atts[7] : 'multi'; // Allow single or multiple entries
		$dsabl = (isset($atts[8])) ? $atts[8] : '';      // Readonly input

		// Push some needed scripts and stylings to the template but ensure we do it only once
		if ($this->_pushscripts) {
			$document = JFactory::getDocument();
			if (JPluginHelper::isEnabled('system', 'jquery'))
			{
				$document->addScript(DS.'plugins'.DS.'hubzero'.DS.'autocompleter'.DS.'autocompleter.jquery.js');
			}
			else 
			{
				$document->addScript(DS.'plugins'.DS.'hubzero'.DS.'autocompleter'.DS.'textboxlist.js');
				$document->addScript(DS.'plugins'.DS.'hubzero'.DS.'autocompleter'.DS.'observer.js');
				$document->addScript(DS.'plugins'.DS.'hubzero'.DS.'autocompleter'.DS.'autocompleter.js');
			}
			//$document->addStyleSheet(DS.'plugins'.DS.'hubzero'.DS.'autocompleter'.DS.'autocompleter.css');

			$this->_pushscripts = false;
		}

		// Build the input tag
		$html  = '<input type="text" name="'.$name.'" rel="'.$opt.','.$type.','.$wsel.'"';
		$html .= ($id)    ? ' id="'.$id.'"'       : '';
		$html .= ($class) ? ' class="'.trim($class).'"' : '';
		$html .= ($size)  ? ' size="'.$size.'"'   : '';
		$html .= ($dsabl) ? ' readonly="readonly"'   : '';
		$html .= ' value="'. htmlentities($value, ENT_QUOTES) .'" autocomplete="off" />';
		
		/*$json = '';
		if ($value)
		{
			$items = array();
			$data = explode(',', $value);
			$data = array_map('trim', $data);
			foreach ($data as $item)
			{
				if ($type != 'tags')
				{
					if (preg_match('/(.*)\((.*)\)/U', $item, $matched)) { 
						$itemId = htmlentities(stripslashes($matched[2]), ENT_COMPAT, 'UTF-8');
						$itemName = htmlentities(stripslashes($matched[1]), ENT_COMPAT, 'UTF-8');
					} else { 
						$itemId = htmlentities(stripslashes($item), ENT_COMPAT, 'UTF-8');
						$itemName = $itemId;
					}
				}
				$items[] = "{'id':'".$itemId."','name':'".$itemName."'}";
			}
			$json = '['.implode(',',$items).']';
		}
		
		$html .= '<input type="hidden" name="pre-'.$name.'" rel="'.$id.'"';
		$html .= ($id)    ? ' id="pre-'.$id.'"'       : '';
		$html .= ($class) ? ' class="pre-'.trim($class).'"' : '';
		$html .= ' value="'. $json .'" />';*/

		// Return the Input tag
		return $html;
	}

	/**
	 * Short description for 'onGetMultiEntry'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $atts Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onGetMultiEntry( $atts )
	{
		if (!is_array($atts)) {
			$atts = array();
		}
		$params = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'tags';
		$params[] = (isset($atts[1])) ? $atts[1] : 'tags';
		$params[] = (isset($atts[2])) ? $atts[2] : 'act';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = '';
		$params[] = 'multi';
		$params[] = (isset($atts[6])) ? $atts[6] : '';

		return $this->onGetAutocompleter( $params );
	}

	/**
	 * Short description for 'onGetSingleEntry'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $atts Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onGetSingleEntry( $atts )
	{
		if (!is_array($atts)) {
			$atts = array();
		}
		$params = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'tags';
		$params[] = (isset($atts[1])) ? $atts[1] : 'tags';
		$params[] = (isset($atts[2])) ? $atts[2] : 'act';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = '';
		$params[] = 'single';
		$params[] = (isset($atts[6])) ? $atts[6] : '';

		return $this->onGetAutocompleter( $params );
	}

	/**
	 * Short description for 'onGetSingleEntryWithSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $atts Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onGetSingleEntryWithSelect( $atts )
	{
		if (!is_array($atts)) {
			$atts = array();
		}
		$params = array();
		$params[] = (isset($atts[0])) ? $atts[0] : 'groups';
		$params[] = (isset($atts[1])) ? $atts[1] : 'groups';
		$params[] = (isset($atts[2])) ? $atts[2] : 'acg';
		$params[] = (isset($atts[3])) ? $atts[3] : '';
		$params[] = (isset($atts[4])) ? $atts[4] : '';
		$params[] = (isset($atts[5])) ? $atts[5] : '';
		$params[] = (isset($atts[6])) ? $atts[6] : 'ticketowner';
		$params[] = 'single';
		$params[] = (isset($atts[7])) ? $atts[7] : '';

		return $this->onGetAutocompleter( $params );
	}
}
