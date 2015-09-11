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

namespace Hubzero\Debug;

use Hubzero\Debug\Dumper\Renderable;
use Hubzero\Debug\Dumper\RendererNotFoundException;
use InvalidArgumentException;

class Dumper
{
	/**
	 * Renderable
	 *
	 * @var  object
	 */
	protected $_renderer = null;

	/**
	 * Messages
	 *
	 * @var  array
	 */
	protected $_messages = array();

	/**
	 * Map of characters to be replaced through strtr
	 *
	 * @var  array
	 */
	protected $_nameReplacements = array(
		'-'  => '',
		'_'  => '',
		' '  => '',
		'\\' => '',
		'/'  => ''
	);

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->clear();
	}

	/**
	 * Returns a reference to a global object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return  object
	 */
	public static function &getInstance()
	{
		static $instance;

		if (!$instance)
		{
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Returns the renderer
	 *
	 * @return  object  Renderer\Renderable
	 */
	public function getRenderer()
	{
		return $this->_renderer;
	}

	/**
	 * Sets the renderer
	 *
	 * @param   mixed  $renderer  string or Renderable
	 * @return  object
	 */
	public function setRenderer($renderer)
	{
		if (is_string($renderer))
		{
			$cName = $this->scrub($renderer);

			$invokable = __NAMESPACE__ . '\\Dumper\\' . ucfirst($cName);

			if (!class_exists($invokable))
			{
				throw new RendererNotFoundException(sprintf(
					'%s: failed retrieving renderer via invokable class "%s"; class does not exist',
					__CLASS__ . '::' . __FUNCTION__,
					$invokable
				));
			}
			$renderer = new $invokable;
		}

		if (!($renderer instanceof Renderable))
		{
			throw new InvalidArgumentException(sprintf(
				'%s was unable to fetch renderer or renderer was not an instance of %s',
				get_class($this) . '::' . __FUNCTION__,
				__NAMESPACE__ . '\\Dumper\\Renderable'
			));
		}

		$this->_renderer = $renderer;

		return $this;
	}

	/**
	 * Canonicalize name
	 *
	 * @param   string  $name
	 * @return  string
	 */
	protected function scrub($name)
	{
		// this is just for performance instead of using str_replace
		return strtolower(strtr($name, $this->_nameReplacements));
	}

	/**
	 * Adds a message
	 *
	 * @param   mixed   $message
	 * @param   string  $label
	 * @return  object
	 */
	public function addVar($var)
	{
		$varc = (is_object($var) ? clone $var : $var);
		$this->_messages[] = array(
			'var'  => $varc,
			'time' => microtime(true)
		);

		return $this;
	}

	/**
	 * Get a list of messages
	 *
	 * @return  array
	 */
	public function messages()
	{
		$messages = $this->_messages;

		// sort messages by their timestamp
		usort($messages, function($a, $b)
		{
			if ($a['time'] === $b['time'])
			{
				return 0;
			}
			return $a['time'] < $b['time'] ? -1 : 1;
		});

		return $messages;
	}

	/**
	 * Deletes all messages
	 *
	 * @return  void
	 */
	public function clear()
	{
		$this->_messages = array();
	}

	/**
	 * Get a count and list of messages
	 *
	 * @return  array
	 */
	public function collect()
	{
		$messages = $this->messages();

		return array(
			'count'    => count($messages),
			'messages' => $messages
		);
	}

	/**
	 * Does the log have any messages?
	 *
	 * @return  integer
	 */
	public function hasMessages()
	{
		return count($this->messages());
	}

	/**
	 * Render
	 *
	 * @param   object  $renderer
	 * @return  string
	 */
	public function render($renderer=null)
	{
		if (!$renderer)
		{
			$renderer = php_sapi_name() === 'cli' ? 'console' : 'html';
		}

		return $this->setRenderer($renderer)
					->getRenderer()
					->render($this->messages());
	}

	/**
	 * Adds a message
	 *
	 * @param   mixed  $var
	 * @return  void
	 */
	public static function log($var)
	{
		$console = self::getInstance();
		$console->addVar($var);
	}

	/**
	 * Dumps a var
	 *
	 * @param   mixed   $var
	 * @param   string  $to
	 * @return  void
	 */
	public static function dump($var, $to=null)
	{
		$console = self::getInstance();
		$console->addVar($var);

		echo $console->render($to);
		$console->clear();
	}

	/**
	 * Dumps a var and dies();
	 *
	 * @param   mixed   $var
	 * @param   string  $to
	 * @return  void
	 */
	public static function stop($var, $to=null)
	{
		self::dump($var, $to);
		die();
	}
}
