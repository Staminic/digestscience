<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (JLanguageAssociations::isEnabled() && $params->get('show_associations'));
JHtml::_('behavior.caption');

// Custom fields
foreach ($this->item->jcfields as $jcfield)
   {
    $this->item->jcFields[$jcfield->name] = $jcfield;
   }
?>

<div class="item-page page section<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Article">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />

	<div class="hero hero-section">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<div class="container">
				<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
			</div>
		<?php endif; ?>
	</div>

	<div class="container">
		<div class="page-content">
			<?php if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) {
				echo $this->item->pagination;
			} ?>

			<?php //Load Breadcrumb Module ?>
			<?php echo JHtml::_('content.prepare', '{loadposition breadcrumbs}'); ?>

			<?php // Todo Not that elegant would be nice to group the params ?>
			<?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
			|| $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam); ?>

			<?php if (!$useDefList && $this->print) : ?>
				<div id="pop-print" class="btn hidden-print">
					<?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
				</div>
				<div class="clearfix"> </div>
			<?php endif; ?>

			<?php if ($params->get('show_title') || $params->get('show_author')) : ?>
				<?php if ($params->get('show_title')) : ?>
					<?php if ($this->params->get('show_page_heading')) : ?>
						<h2 itemprop="headline">
							<?php echo $this->escape($this->item->title); ?>
						</h2>
					<?php else : ?>
						<h1 itemprop="headline">
							<?php echo $this->escape($this->item->title); ?>
						</h1>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($this->item->state == 0) : ?>
					<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
				<?php endif; ?>
				<?php if (strtotime($this->item->publish_up) > strtotime(JFactory::getDate())) : ?>
					<span class="label label-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></span>
				<?php endif; ?>
				<?php if ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != JFactory::getDbo()->getNullDate()) : ?>
					<span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (!$this->print) : ?>
				<?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
					<?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
				<?php endif; ?>
			<?php else : ?>
				<?php if ($useDefList) : ?>
					<div id="pop-print" class="btn hidden-print">
						<?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php // Content is generated by content plugin event "onContentAfterTitle" ?>
			<?php echo $this->item->event->afterDisplayTitle; ?>

			<?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
				<?php // Todo: for Joomla4 joomla.content.info_block.block can be changed to joomla.content.info_block ?>
				<?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
			<?php endif; ?>

			<?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
				<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>

				<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
			<?php endif; ?>

			<?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
			<?php echo $this->item->event->beforeDisplayContent; ?>

			<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position)))
				|| (empty($urls->urls_position) && (!$params->get('urls_position')))) : ?>
			<?php echo $this->loadTemplate('links'); ?>
			<?php endif; ?>

			<?php if ($params->get('access-view')) : ?>

				<?php
				if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative) :
					echo $this->item->pagination;
				endif;
				?>

				<?php if (isset ($this->item->toc)) :
					echo $this->item->toc;
				endif; ?>

				<div itemprop="articleBody">
          <?php if ($this->item->jcFields['sous-titre']->rawvalue) : ?>
            <?php echo $this->item->jcFields['sous-titre']->rawvalue; ?>
          <?php endif; ?>
          <?php if ($this->item->jcFields['intro']->rawvalue) : ?>
						<?php if ($this->item->jcFields['accroche']->rawvalue) : ?>
							<div class="row mb-5 intro-section">
								<div class="col-lg-8">
									<div class="lead">
										<?php echo $this->item->jcFields['intro']->rawvalue; ?>
									</div>
								</div>
								<div class="col-lg-3 offset-lg-1">
									<div class="accroche mt-4 mt-lg-0">
                    <?php echo $this->item->jcFields['accroche']->rawvalue; ?>
                  </div>
								</div>
							</div>
						<?php else : ?>
							<div class="lead mb-5">
								<?php echo $this->item->jcFields['intro']->rawvalue; ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php echo $this->item->text; ?>

          <?php if ($this->item->jcFields['outro']->rawvalue) : ?>
					  <div class="outro clearfix">
							<?php echo $this->item->jcFields['outro']->rawvalue; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ($info == 1 || $info == 2) : ?>
					<?php if ($useDefList) : ?>
							<?php // Todo: for Joomla4 joomla.content.info_block.block can be changed to joomla.content.info_block ?>
						<?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
					<?php endif; ?>
					<?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
						<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
						<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
					<?php endif; ?>
				<?php endif; ?>

				<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative) {
					echo $this->item->pagination;
				} ?>

				<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) : ?>
				<?php echo $this->loadTemplate('links'); ?>
				<?php endif; ?>

			<?php // Optional teaser intro text for guests ?>
			<?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>

				<?php echo JLayoutHelper::render('joomla.content.intro_image', $this->item); ?>
				<?php echo JHtml::_('content.prepare', $this->item->introtext); ?>

				<?php // Optional link to let them register to see the whole article. ?>
				<?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
					<?php $menu = JFactory::getApplication()->getMenu(); ?>
					<?php $active = $menu->getActive(); ?>
					<?php $itemId = $active->id; ?>
					<?php $link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
					<?php $link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language))); ?>
					<p class="readmore">
						<a href="<?php echo $link; ?>" class="register">
						<?php $attribs = json_decode($this->item->attribs); ?>
						<?php
						if ($attribs->alternative_readmore == null) :
							echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
						elseif ($readmore = $attribs->alternative_readmore) :
							echo $readmore;
							if ($params->get('show_readmore_title', 0) != 0) :
								echo JHtml::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
							endif;
						elseif ($params->get('show_readmore_title', 0) == 0) :
							echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
						else :
							echo JText::_('COM_CONTENT_READ_MORE');
							echo JHtml::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
						endif; ?>
						</a>
					</p>
				<?php endif; ?>

			<?php endif; ?>

			<?php	if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative) {
				echo $this->item->pagination;
			} ?>

			<?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
			<?php echo $this->item->event->afterDisplayContent; ?>

		</div>
	</div>
</div>
