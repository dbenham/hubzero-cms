<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

?>
<div id="plg-header">
<?php if ($this->project->isProvisioned()) { ?>
<h3 class="prov-header"><a href="<?php echo Route::url($this->route); ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION')); ?></h3>
<?php } else { ?>
<h3 class="publications c-header"><a href="<?php echo Route::url($this->route); ?>"><?php echo $this->title; ?></a> &raquo; <span class="indlist"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION')); ?></span></h3>
<?php } ?>
</div>
<?php if ($this->project->isProvisioned()) { ?>
<div class="grid">
	<div class="col span9">
<?php } ?>
<div class="welcome">
	<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEWPUB_WHAT'); ?></h3>
	<div id="suggestions" class="suggestions">
		<?php for ( $i = 0; $i < count($this->choices); $i++)
		{
			$current = $this->choices[$i];
			$action = 'publication';

		?>
		<div class="s-<?php echo $current->alias; ?>"><p><a href="<?php echo Route::url($this->route . '&action=' . $action . '&base=' . $current->alias); ?>"><?php echo $current->type; ?> <span class="block"><?php echo $current->description; ?></span></a></p></div>
		<?php } ?>
		<div class="clear"></div>
	</div>
</div>
<?php if ($this->project->isProvisioned()) { ?>
	</div><!-- / .subject -->
	<div class="col span3 omega">
		<div id="start-projectnote">
			<h4><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEED_PROJECT'); ?></h4>
			<p><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTRIB_START'); ?></p>
			<p class="getstarted-links"><a href="/members/myaccount/projects"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_YOUR_PROJECTS'); ?></a> | <a href="/projects/start" class="addnew"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PROJECT'); ?></a></p>
		</div>
	</div><!-- / .aside -->
</div>
<?php } ?>
