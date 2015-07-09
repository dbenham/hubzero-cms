<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Hubzero\Utility\String;

/**
 * Request handler replaces the default PHP global variables 
 * and functions by an object-oriented layer.
 */
class Request extends BaseRequest
{
	/**
	 * Array of filters
	 * 
	 * @var  array
	 */
	static $filters = array(
		'int'   => '/-?[0-9]+/',
		'float' => '/-?[0-9]+(\.[0-9]+)?/',
		'cmd'   => '/[^A-Z0-9_\.-]/i',
		'word'  => '/[^A-Z_]/i'
	);

	/**
	 * Set a variable in one of the request variables.
	 *
	 * @param   string   $name       Name
	 * @param   string   $value      Value
	 * @param   string   $hash       Hash
	 * @param   boolean  $overwrite  Boolean
	 * @return  string   Previous value
	 */
	public function setVar($name, $value = null, $hash = 'method', $overwrite = true)
	{
		// If overwrite is true, makes sure the variable hasn't been set yet
		if (!$overwrite && $this->has($name))
		{
			return $this->getVar($name);
		}

		// Get the request hash value
		$hash = strtolower($hash);
		if ($hash === 'method')
		{
			$hash = strtolower($this->getMethod());
		}

		switch ($hash)
		{
			case 'server':
				$hash = 'server';
			break;

			case 'cookie':
			case 'cookies':
				$hash = 'cookies';
			break;

			case 'file':
			case 'files':
				$hash = 'files';
			break;

			case 'post':
			case 'request':
				$hash = 'request';
				//$this->request->set($name, $value);
			break;

			case 'get':
			case 'query':
				$hash = 'query';
				//$this->request->set($name, $value);
			break;

			case 'header':
			case 'headers':
				$hash = 'headers';
			break;

			case 'put':
				$hash = 'request';
			break;

			default:
			break;
		}

		return $this->$hash->set($name, $value);
	}

	/**
	 * Get var
	 * 
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @param   string   $type     Return type for the variable. [!] Deprecated. Joomla legacy support.
	 * @param   string   $mask     Filter mask for the variable. [!] Deprecated. Joomla legacy support.
	 * @return  itneger  Request variable
	 */
	public function getVar($key, $default = null, $hash = 'input', $type = 'none', $mask = 0)
	{
		$hash = strtolower($hash);

		switch ($hash)
		{
			case 'server':
				return $this->server($key, $default);
			break;

			case 'cookie':
				return $this->cookie($key, $default);
			break;

			case 'files':
				$result = $this->file($key, $default);
				if ($type == 'array')
				{
					$res = array(
						'name'      => null,
						'tmp_name'  => null,
						'mime_type' => null,
						'extension' => null,
						'size'      => null
					);
					if ($result)
					{
						$res = array(
							'name'      => $result->getClientOriginalName(),
							'tmp_name'  => $result->getPathName(),
							'mime_type' => $result->getClientMimeType(),
							'extension' => $result->getExtension(),
							'size'      => $result->getClientSize()
						);
					}
					$result = $res;
				}
				return ($result !== null ? $result : $default);
			break;

			case 'post':
				return $this->request($key, $default);
			break;

			case 'get':
				return $this->query($key, $default);
			break;

			default:
				return $this->input($key, $default);
			break;
		}
	}

	/**
	 * Get integer
	 * 
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  itneger  Request variable
	 */
	public function getInt($key, $default = 0, $hash = 'input')
	{
		preg_match('/-?[0-9]+/', (string) $this->getVar($key, $default, $hash), $matches);
		$result = @ $matches[0];
		return (!is_null($result) ? (int) $result : $default);
	}

	/**
	 * Get unsigned integer
	 * 
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  integer  Request variable
	 */
	public function getUInt($name, $default = 0, $hash = 'input')
	{
		$result = $this->getInt($name, $default, $hash);
		return (!is_null($result) ? abs($result) : $default);
	}

	/**
	 * Get float
	 * 
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  integer  Request variable
	 */
	public function getFloat($name, $default = 0.0, $hash = 'input')
	{
		return preg_replace(static::$filters['float'], '', $this->getVar($key, $default, $hash));
	}

	/**
	 * Get boolean
	 *
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  boolean  Request variable
	 */
	public function getBool($key = null, $default = null, $hash = 'input')
	{
		return (bool) $this->getVar($key, $default, $hash);
	}

	/**
	 * Get word
	 * 
	 * @param   string  $key      Request key
	 * @param   mixed   $default  Default value
	 * @param   string  $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  string  Request variable
	 */
	public function getWord($key, $default = null, $hash = 'input')
	{
		return preg_replace(static::$filters['word'], '', $this->getVar($key, $default, $hash));
	}

	/**
	 * Get cmd
	 *
	 * @param   string  $key      Request key
	 * @param   mixed   $default  Default value
	 * @param   string  $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  string  Request variable
	 */
	public function getCmd($key = null, $default = null, $hash = 'input')
	{
		$result = (string) preg_replace(static::$filters['cmd'], '', $this->getVar($key, $default, $hash));
		return ltrim($result, '.');
	}

	/**
	 * Fetches and returns a given variable as an array.
	 *
	 * @param   string  $key      Request key
	 * @param   mixed   $default  Default value
	 * @param   string  $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  array   Request variable
	 */
	public function getArray($key = null, $default = array(), $hash = 'input')
	{
		return (array) $this->getVar($key, $default, $hash);
	}

	/**
	 * Fetches and returns a given variable as a string.
	 *
	 * @param   string  $key      Request key
	 * @param   mixed   $default  Default value
	 * @param   string  $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  string  Request variable
	 */
	public function getString($name, $default = null, $hash = 'input')
	{
		return (string) $this->getVar($name, $default, $hash);
	}

	/**
	 * Return the Request instance.
	 *
	 * @return  object
	 */
	public function instance()
	{
		return $this;
	}

	/**
	 * Get the request method.
	 *
	 * @return  string
	 */
	public function method()
	{
		return $this->getMethod();
	}

	/**
	 * Get the root URL for the application.
	 *
	 * @param   boolean  $pathonly  If false, prepend the scheme, host and port information. Default is false.
	 * @return  string
	 */
	public function root($pathonly = false)
	{
		$root = rtrim(($pathonly ? '' : $this->getSchemeAndHttpHost()) . $this->getBasePath(), '/');
		$root = explode('/', $root);
		if (in_array(end($root), array('administrator', 'api')))
		{
			array_pop($root);
		}

		return implode('/', $root) . '/';
	}

	/**
	 * Get the URL (no query string) for the request.
	 *
	 * @return  string
	 */
	public function current($query = false)
	{
		return ($query ? $this->getUri() : rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/'));
	}

	/**
	 * Get the full URL for the request.
	 *
	 * @return  string
	 */
	/*public function fullUrl()
	{
		$query = $this->getQueryString();

		return $query ? $this->current() . '?' . $query : $this->current();
	}*/

	/**
	 * Get the current path info for the request.
	 *
	 * @return  string
	 */
	public function path()
	{
		$pattern = trim($this->getPathInfo(), '/');

		return $pattern == '' ? '/' : '/' . $pattern;
	}

	/**
	 * Get the current path info for the request.
	 *
	 * @param   boolean  $pathonly  If false, prepend the scheme, host and port information. Default is false.
	 * @return  string
	 */
	public function base($pathonly = false)
	{
		$path = $this->getBasePath();

		if ($pathonly) return $path;

		return $this->root() . ($path ? trim($path, '/') . '/' : '');
	}

	/**
	 * Get a segment from the URI (1 based index).
	 *
	 * @param   string  $index
	 * @param   mixed   $default
	 * @return  string
	 */
	public function segment($index, $default = null)
	{
		$segments = $this->segments();

		return isset($segments[$index - 1]) ? $segments[$index - 1] : $default;
	}

	/**
	 * Get all of the segments for the request path.
	 *
	 * @return  array
	 */
	public function segments()
	{
		$segments = explode('/', $this->path());

		return array_values(array_filter($segments, function($v) { return $v != ''; }));
	}

	/**
	 * Determine if the request is the result of an AJAX call.
	 *
	 * @return  bool
	 */
	public function ajax()
	{
		return $this->isXmlHttpRequest();
	}

	/**
	 * Determine if the request is over HTTPS.
	 *
	 * @return  bool
	 */
	public function secure()
	{
		return $this->isSecure();
	}

	/**
	 * Get the IP address of the client.
	 *
	 * @return  string
	 */
	public function ip()
	{
		return $this->getClientIp();
	}

	/**
	 * Get the request scheme.
	 *
	 * @return  string
	 */
	public function scheme()
	{
		return $this->getScheme();
	}

	/**
	 * Get the HTTP host.
	 *
	 * @return  string
	 */
	public function host()
	{
		return $this->getHost();
	}

	/**
	 * Determine if the request contains a given input item.
	 *
	 * @param   mixed  $key  string|array
	 * @return  bool
	 */
	public function has($key)
	{
		if (count(func_get_args()) > 1)
		{
			foreach (func_get_args() as $value)
			{
				if ( ! $this->has($value)) return false;
			}

			return true;
		}

		if (is_bool($this->input($key)) || is_array($this->input($key)))
		{
			return true;
		}

		return trim((string) $this->input($key)) !== '';
	}

	/**
	 * Retrieve an input item from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function input($key = null, $default = null)
	{
		$input = $this->getInputSource()->all() + $this->query->all();

		return isset($input[$key]) ? $input[$key] : $default;
	}

	/**
	 * Get the input source for the request.
	 *
	 * @return  object
	 */
	protected function getInputSource()
	{
		return $this->getMethod() == 'GET' ? $this->query : $this->request;
	}

	/**
	 * Retrieve a post item from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function request($key = null, $default = null)
	{
		return $this->retrieveItem('request', $key, $default);
	}

	/**
	 * Retrieve a query string item from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function query($key = null, $default = null)
	{
		return $this->retrieveItem('query', $key, $default);
	}

	/**
	 * Retrieve a cookie from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function cookie($key = null, $default = null)
	{
		return $this->retrieveItem('cookies', $key, $default);
	}

	/**
	 * Retrieve a file from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public function file($key = null, $default = null)
	{
		$array = $this->files->all();

		if (is_null($key)) return $array;

		if (isset($array[$key])) return $array[$key];

		return $default;
	}

	/**
	 * Retrieve a header from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function header($key = null, $default = null)
	{
		return $this->retrieveItem('headers', $key, $default);
	}

	/**
	 * Retrieve a server variable from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function server($key = null, $default = null)
	{
		return $this->retrieveItem('server', $key, $default);
	}

	/**
	 * Retrieve a parameter item from a given source.
	 *
	 * @param   string  $source
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	protected function retrieveItem($source, $key, $default)
	{
		if (is_null($key))
		{
			return $this->$source->all();
		}

		return $this->$source->get($key, $default, true);
	}
}
