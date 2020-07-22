<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Note that there are certain parts of this layout used only when there is exactly one tag.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$isSingleTag = count($this->item) === 1;

?>
<div class="blog card-blog tag-category<?php echo $this->pageclass_sfx; ?>">

	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="hero hero-section">
			<div class="container">
				<h1>
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				</h1>
			</div>
		</div>
	<?php endif; ?>

	<div class="container">
		<div class="page-content">

			<?php //Load Breadcrumb Module ?>
			<?php echo JHtml::_('content.prepare', '{loadposition breadcrumbs}'); ?>

			<?php if ($this->params->get('show_tag_title', 1)) : ?>
				<h2 class="tag-name">
					<?php echo JHtml::_('content.prepare', $this->tags_title, '', 'com_tag.tag'); ?>
				</h2>
			<?php endif; ?>

			<?php // We only show a tag description if there is a single tag. ?>
			<?php if (count($this->item) === 1 && ($this->params->get('tag_list_show_tag_image', 1) || $this->params->get('tag_list_show_tag_description', 1))) : ?>
				<?php $images = json_decode($this->item[0]->images); ?>
				<?php if ($this->params->get('tag_list_show_tag_image', 1) == 1 && !empty($images->image_fulltext)) : ?>
					<img src="<?php echo htmlspecialchars($images->image_fulltext, ENT_COMPAT, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>" />
				<?php endif; ?>

				<?php if ($this->params->get('tag_list_show_tag_description') == 1 && $this->item[0]->description) : ?>
					<?php echo JHtml::_('content.prepare', $this->item[0]->description, '', 'com_tags.tag'); ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php // If there are multiple tags and a description or image has been supplied use that. ?>
			<?php if ($this->params->get('tag_list_show_tag_description', 1) || $this->params->get('show_description_image', 1)) : ?>
				<?php if ($this->params->get('show_description_image', 1) == 1 && $this->params->get('tag_list_image')) : ?>
					<img src="<?php echo $this->params->get('tag_list_image'); ?>" />
				<?php endif; ?>

				<?php if ($this->params->get('tag_list_description', '') > '') : ?>
					<?php echo JHtml::_('content.prepare', $this->params->get('tag_list_description'), '', 'com_tags.tag'); ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php echo $this->loadTemplate('items'); ?>

			<?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
				<?php if ($this->params->def('show_pagination_results', 1)) : ?>
					<p class="counter">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
			<?php endif; ?>

			<?php //Load Go back to blog list Module ?>
			<div class="mt-5">
				<?php echo JHtml::_('content.prepare', '{loadposition blog-below}'); ?>
			</div>

		</div>
	</div>

</div>
