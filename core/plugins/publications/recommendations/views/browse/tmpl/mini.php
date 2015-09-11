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

// No direct access
defined('_HZEXEC_') or die();

$this->css('assets/css/recommendations.css');

?>
<div id="recommendations">
	<h3><?php echo Lang::txt('PLG_PUBLICATION_RECOMMENDATIONS_HEADER'); ?></h3>
<?php if ($this->results) { ?>
	<ul>
<?php
	foreach ($this->results as $line)
	{
		// Get the SEF for the publication
		if ($line->alias) {
			$sef = Route::url('index.php?option=' . $this->option . '&alias=' . $line->alias . '&rec_ref=' . $this->publication->id);
		} else {
			$sef = Route::url('index.php?option=' . $this->option . '&id=' . $line->id . '&rec_ref=' . $this->publication->id);
		}
?>
		<li>
			<a href="<?php echo $sef; ?>"><?php echo stripslashes($line->title); ?></a>
		</li>
<?php } ?>
	</ul>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_PUBLICATION_RECOMMENDATIONS_NO_RESULTS_FOUND'); ?></p>
<?php } ?>
	<p id="credits"><a href="<?php echo Request::base(true); ?>/about/hubzero#recommendations"><?php echo Lang::txt('PLG_PUBLICATION_RECOMMENDATIONS_POWERED_BY'); ?></a></p>
</div>
