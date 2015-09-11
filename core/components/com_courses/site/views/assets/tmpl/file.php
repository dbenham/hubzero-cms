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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

// Incoming
$d = Request::getVar('d', 'inline');

//make sure we have a proper disposition
if ($d != "inline" && $d != "attachment")
{
	$d = "inline";
}

// File path
$path = $this->model->path($this->course->get('id'));

// Ensure we have a path
if (empty($path))
{
	App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND'));
	return;
}

// Add PATH_APP
$filename = PATH_APP . $path;

// Ensure the file exist
if (!file_exists($filename))
{
	App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND') . ' ' . $filename);
	return;
}

// Force certain extensions to the 'attachment' disposition
$ext = strtolower(Filesystem::extension($filename));
if (!in_array($ext, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'pdf', 'htm', 'html', 'txt', 'json', 'xml')))
{
	$d = 'attachment';
}

// Initiate a new content server and serve up the file
$xserver = new \Hubzero\Content\Server();
$xserver->filename($filename);
//$xserver->saveas($this->model->get('title') . '.' . $ext);
$xserver->disposition($d);
$xserver->acceptranges(false); // @TODO fix byte range support

if (!$xserver->serve())
{
	// Should only get here on error
	App::abort(500, Lang::txt('COM_COURSES_SERVER_ERROR'));
}
else
{
	// Just exit (i.e. no template)
	exit;
}