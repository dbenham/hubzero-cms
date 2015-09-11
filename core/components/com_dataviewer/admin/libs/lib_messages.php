<?php
/**
 * @package     hubzero.cms.admin
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

if (!isset($_SESSION['databases']['notifications'])) {
	$_SESSION['databases']['notifications'] = array();
}

function db_msg($msg, $type = 'error')
{
	$_SESSION['databases']['notifications'][] = array('message' => $msg, 'type' => $type);
}

function db_show_msg()
{
	foreach ($_SESSION['databases']['notifications'] as $notification) {
		print "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}

	$_SESSION['databases']['notifications'] = array();
}
?>
