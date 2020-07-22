<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2019 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$config            = EventbookingHelper::getConfig();
$controlGroupClass = $bootstrapHelper ? $bootstrapHelper->getClassMapping('control-group') : 'control-group';
$controlLabelClass = $bootstrapHelper ? $bootstrapHelper->getClassMapping('control-label') : 'control-label';
$controlsClass     = $bootstrapHelper ? $bootstrapHelper->getClassMapping('controls') : 'controls';
?>
<div class="form-group <?php echo $class ?>" <?php echo $controlGroupAttributes ?>>
		<?php
			echo $label;

			if ($config->get('display_field_description', 'use_tooltip') == 'under_field_label' && strlen($description) > 0)
			{
			?>
				<p class="eb-field-description"><?php echo $description; ?></p>
			<?php
			}
		?>
		<?php
			echo $input;
			if ($config->get('display_field_description', 'use_tooltip') == 'under_field_input' && strlen($description) > 0)
			{
			?>
				<p class="eb-field-description"><?php echo $description; ?></p>
			<?php
			}
		?>
</div>
