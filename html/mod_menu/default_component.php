<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$menu = $app->getMenu();
$active = $app->getMenu()->getActive();


$attributes = array();

if ($item->anchor_title)
{
	$attributes['title'] = $item->anchor_title;
}

// $attributes['itemid'] = $active->id;
//
// $attributes['parentitemid'] = $active->parent_id;

$attributes['class'] = 'nav-link';

if (($item->level) >= '2')
{
	$attributes['class'] = 'dropdown-item';
}

if ((($item->level) >= '2') && (($active->parent_id == 429) || ($active->parent_id == 433) || ($active->parent_id == 1624) || ($active->parent_id == 1577) || ($active->parent_id == 1630) || ($active->parent_id == 1623)))
{
	$attributes['class'] = 'nav-link';
}

if ((($item->level) >= '2') && (($active->id == 429) || ($active->id == 433) || ($active->id == 1624)))
{
	$attributes['class'] = 'nav-link';
}

if ($item->anchor_css)
{
	$attributes['class'] .= ' ' . $item->anchor_css;
}

if ($item->anchor_rel)
{
	$attributes['rel'] = $item->anchor_rel;
}

$linktype = $item->title;

if ($item->menu_image)
{
	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
		$linktype = JHtml::_('image', $item->menu_image, $item->title, $image_attributes);
	}
	else
	{
		$linktype = JHtml::_('image', $item->menu_image, $item->title);
	}

	if ($item->params->get('menu_text', 1))
	{
		$linktype .= $item->title;
	}
}

$linktype = '<span>' . $linktype . '</span>';

if ($item->browserNav == 1)
{
	$attributes['target'] = '_blank';
}
elseif ($item->browserNav == 2)
{
	$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';

	$attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $options . "'); return false;";
}

echo JHtml::_('link', JFilterOutput::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
