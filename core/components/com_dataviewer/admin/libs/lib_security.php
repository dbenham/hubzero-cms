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

function check_rid()
{
	if (isset($_POST[DB_RID]) && $_POST[DB_RID] == DB_RID) {
		return true;
	}

	exit;
}
?>
