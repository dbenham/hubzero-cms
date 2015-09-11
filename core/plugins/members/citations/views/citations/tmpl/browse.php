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
 * @author	Shawn Rice <zooley@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('citations.css')
	 ->js();

$base = 'index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=citations';

if (isset($this->messages))
{
	foreach ($this->messages as $message)
	{
		echo "<p class=\"{$message['type']}\">" . $message['message'] . "</p>";
	}
}

?>

<div id="content-header-extra"><!-- Citation management buttons -->
	<?php if ($this->isAdmin) : ?>
		<a class="btn icon-add" href="<?php echo Route::url($base. '&action=add'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION'); ?>
		</a>
		<a class="btn icon-upload" href="<?php echo Route::url($base. '&action=import'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION'); ?>
		</a>
		<a class="btn icon-settings" href="<?php echo Route::url($base. '&action=settings'); ?>">
			<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT'); ?>
		</a>
	<?php endif; ?>
</div><!-- / Citations management buttons -->

<div class="frm" id="browsebox"><!-- .frm #browsebox -->
	<form action="<?php echo Route::url(Request::current()); ?>" id="citeform" method="GET" class="withBatchDownload">
		<section class="main section"> <!-- .main .section -->
			<div class="subject">
				<div class="container data-entry"> <!-- citation search box -->
					<!-- @TODO replace with hubgraph -->
					<input class="entry-search-submit" type="submit" value="Search" /> <!-- search button -->
					<fieldset class="entry-search"> <!-- text box container -->
						<legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SEARCH_CITATIONS'); ?></legend>
						<input type="text" name="filters[search]" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SEARCH_CITATIONS_PLACEHOLDER'); ?>" />
					</fieldset>
				</div><!-- /.container .data-entry -->

				<div class="container"> <!-- .container for citation type (aff, nonaff, all) -->
				<div class="clearfix"></div>
				<?php if ($this->citations->count() > 0) : ?>
					<table class="citations entries">
						<thead>
							<tr>
								<th class="batch">
									<input type="checkbox" class="checkall-download" />
								</th>
								<th colspan="6"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS'); ?></th>
							</tr>
							<?php if ($this->isAdmin) : ?>
								<tr class="hidden">
									<div class="admin">
										<a class="btn icon-window-publish bulk" data-link="<?php echo Route::url($base. '&action=publish&bulk=true'); ?>">
											<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISH_SELECTED'); ?>
										</a>
										<a class="btn icon-delete bulk" data-protected="true" data-link="<?php echo Route::url($base. '&action=delete&bulk=true'); ?>">
											<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_DELETE_SELECTED'); ?>
										</a>
										</td>
									</div>
								</tr>
							<?php endif; ?>
						</thead>
						<tbody>
							<?php $x = 0; ?>
							<?php foreach ($this->citations as $cite) : ?>
								<tr class="citation-row <?php echo ($cite->published == $cite::STATE_UNPUBLISHED ? 'unpublished' : ''); ?>">
									<td class="batch">
										<input type="checkbox" class="download-marker" name="download_marker[]" value="<?php echo $cite->id; ?>" />
									</td>
									<?php if ($this->label != "none") : ?>
										<td class="citation-label <?php echo $this->citations_label_class; ?>">
											<?php
												/**
												 * @TODO replace with Relational
												 **/
												$type = "";
												foreach ($this->types as $t) {
													if ($t->id == $cite->type) {
														$type = $t->type_title;
													}
												}
												$type = ($type != "") ? $type : "Generic";

												switch ($this->label)
												{
													case "number":
														echo "<span class=\"number\">{$cite->id}.</span>";
														break;
													case "type":
														echo "<span class=\"type\">{$type}</span>";
														break;
													case "both":
														echo "<span class=\"number\">{$cite->id}. </span>";
														echo "<span class=\"type\">{$type}</span>";
														break;
												}
											?>
										</td>
									<?php endif; ?>
									<td class="citation-container">
										<?php
											$formatted = $cite->formatted($this->config->toArray(), $this->filters['search']);

											if ($cite->doi)
											{
												$formatted = str_replace(
													'doi:' . $cite->doi,
													'<a href="' . $cite->url . '" rel="external">' . 'doi:' . $cite->doi . '</a>',
													$formatted
												);
											}

											echo $formatted;

											//get this citations rollover param
											//$params = new \Hubzero\Config\Registry($cite->params);
											$citation_rollover = 0;
										?>
										<?php if ($citation_rollover && $cite->abstract != "") : ?>
											<div class="citation-notes">
												<?php
													$cs = new \Components\Citations\Tables\Sponsor($this->database);
													$sponsors = $cs->getCitationSponsor($cite->id);
													$final = "";
													if ($sponsors)
													{
														foreach ($sponsors as $s)
														{
															$sp = $cs->getSponsor($s);
															if ($sp)
															{
																$final .= '<a rel="external" href="'.$sp[0]['link'].'">'.$sp[0]['sponsor'].'</a>, ';
															}
														}
													}
												?>
												<?php if ($final != '' && $this->config->get("citation_sponsors", "yes") == 'yes') : ?>
													<?php $final = substr($final, 0, -2); ?>
													<p class="sponsor"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_ABSTRACT_BY'); ?> <?php echo $final; ?></p>
												<?php endif; ?>
												<p><?php echo nl2br($cite->abstract); ?></p>
											</div>
										<?php endif; ?>
									</td>
									<?php if ($this->isAdmin === true) : ?>
										<td class="col-edit"><a class="icon-edit edit individual" href="<?php echo Route::url($base. '&action=edit&cid=' .$cite->id ); ?>"></span>
											<span><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_EDIT'); ?></span>
										</a></td>
										<td class="col-delete"><a class="icon-delete delete individual protected" href="<?php echo Route::url($base. '&action=delete&cid=' . $cite->id); ?>">
											<span><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_DELETE'); ?></span>
										</a></td>
										<td class="col-publish"><a class="icon-window-publish individual publish" href="<?php echo Route::url($base. '&action=publish&cid=' . $cite->id); ?>">
											<span><?php echo ($cite->published == $cite::STATE_PUBLISHED ? Lang::txt('PLG_MEMBERS_CITATIONS_UNPUBLISH') : '<strong>' . Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISH') . '</strong>'); ?></span>
										</a></td>
									<?php endif; ?>
								</tr>
								<tr>
									<td <?php if ($this->label == "none") { echo 'colspan="5"'; } else { echo 'colspan="6"'; } ?> class="citation-details <?php echo ($cite->published == $cite::STATE_UNPUBLISHED ? 'unpublished-details' : ''); ?>">
										<?php	echo $cite->citationDetails($this->openurl); ?>
										<?php if ($this->config->get('citations_show_badges', 'yes') == "yes"): ?>
										 	<?php echo $cite->badgeCloud(); ?>
										 <?php endif; ?>
										<?php if ($this->config->get('citations_show_tags', 'yes') == "yes"): ?>
											<?php echo $cite->tagCloud(); ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php else : ?>
						<p class="warning"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
					<?php endif; ?>
					<?php echo $this->citations->pagination; ?>
					<div class="clearfix"></div>
				</div><!-- /.container -->
			</div><!-- /.subject -->
			<div class="aside">
				<fieldset id="download-batch">
					<strong><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_EXPORT_MULTIPLE'); ?></strong>
					<p><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_EXPORT_MULTIPLE_DESC'); ?></p>

					<input type="submit" name="download" class="download" id="download-endnote" value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_ENDNOTE'); ?>" />
					|
					<input type="submit" name="download" class="download" id="download-bibtex" value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_BIBTEX'); ?>" />
				<!-- for serving up the file download -->
					<iframe id="download-frame"></iframe>
				<!-- end file serving -->
				</fieldset>
				<fieldset>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_TYPE'); ?>
						<select name="filters[type]" id="type">
							<option value=""><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_ALL'); ?></option>
							<?php foreach ($this->types as $t) : ?>
								<?php $sel = ($this->filters['type'] == $t->id) ? "selected=\"selected\"" : ""; ?>
								<option <?php echo $sel; ?> value="<?php echo $t->id; ?>"><?php echo $t->type_title; ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_TAGS'); ?>:
						<?php
							$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'filters[tag]', 'actags', '', $this->filters['tag'])));  // type, field name, field id, class, value
							if (count($tf) > 0) : ?>
								<?php echo $tf[0]; ?>
							<?php else: ?>
								<input type="text" name="filters[tag]" value="<?php echo $this->filters['tag']; ?>" />
							<?php endif; ?>
					</label>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_AUTHORED_BY'); ?>
						<input type="text" name="filters[author]" value="<?php echo $this->filters['author']; ?>" />
					</label>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISHED_IN'); ?>
						<input type="text" name="filters[publishedin]" value="<?php echo $this->filters['publishedin']; ?>" />
					</label>
					<label for="year_start">
						<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_YEAR'); ?><br />
						<input type="text" name="filters[year_start]" class="half" value="<?php echo $this->filters['year_start']; ?>" />
						to
						<input type="text" name="filters[year_end]" class="half" value="<?php echo $this->filters['year_end']; ?>" />
					</label>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_SORT_BY'); ?>
						<select name="filters[sort]" id="sort" class="">
							<?php foreach ($this->sorts as $k => $v) : ?>
								<option value="<?php echo $k; ?>" <?php echo (trim($this->filters['sort']) == $k ? 'selected' : ''); ?>>
									<?php echo $v; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</label>
					<input type="hidden" name="idlist" value="<?php echo $this->filters['idlist']; ?>"/>
					<input type="hidden" name="referer" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" />
					<input type="hidden" name="action" value="browse" />

					<div class="btn-cluster">
						<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_FILTER'); ?>" />
						<a href="<?php echo Route::url('index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=citations'); ?>" class="btn">Reset</a>
					</div>
				</fieldset>
			</div><!-- /.aside -->
		</section>
	</form>
</div><!-- /.frm /#browsebox -->
