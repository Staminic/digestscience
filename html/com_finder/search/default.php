<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.core');
JHtml::_('formbehavior.chosen');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('stylesheet', 'com_finder/finder.css', array('version' => 'auto', 'relative' => true));

?>
<div class="finder item-page page<?php echo $this->pageclass_sfx; ?>">

	<div class="hero hero-page">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<div class="container">
				<h1>
					<?php if ($this->escape($this->params->get('page_heading'))) : ?>
						<?php echo $this->escape($this->params->get('page_heading')); ?>
					<?php else : ?>
						<?php echo $this->escape($this->params->get('page_title')); ?>
					<?php endif; ?>
				</h1>
			</div>
		<?php endif; ?>
	</div>

	<div class="container">
		<?php //Load Breadcrumb Module ?>
		<?php echo JHtml::_('content.prepare', '{loadposition breadcrumbs}'); ?>
		
		<div class="page-content" style="min-height: 15rem;">
			<?php if ($this->params->get('show_search_form', 1)) : ?>
				<div id="search-form">
					<?php echo $this->loadTemplate('form'); ?>
				</div>
			<?php endif; ?>

			<?php // Load the search results layout if we are performing a search. ?>
			<?php if ($this->query->search === true) : ?>
				<div id="search-results">
					<?php echo $this->loadTemplate('results'); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>
