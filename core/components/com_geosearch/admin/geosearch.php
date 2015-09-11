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
 * @author    Brandon Beatty
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$option = 'com_geosearch';

if (!User::authorise('core.manage', $option))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
$controllerName = Request::getCmd('controller', 'categories');
if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'admin';
}

require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'GeosearchController' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

