<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Editors Extd - Pagebreak plugin
 **/
class Migration20170831000000PlgEditorsextdPagebreak extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('editors-xtd', 'pagebreak');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('editors-xtd', 'pagebreak');
	}
}
