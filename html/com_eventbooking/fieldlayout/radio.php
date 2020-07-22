<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2019 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$size          = (int) $row->size ?: 1;
$span          = intval(12 / $size);
$i             = 0;
$numberOptions = count($options);
$rowFluid      = $bootstrapHelper ? $bootstrapHelper->getClassMapping('row-fluid') : 'row-fluid';
$spanClass     = $bootstrapHelper ? $bootstrapHelper->getClassMapping('span' . $span) : 'span' . $span;
$clearFixClass = $bootstrapHelper ? $bootstrapHelper->getClassMapping('clearfix') : 'clearfix';
?>
<fieldset id="<?php echo $name; ?>">
	<?php
	foreach ($options as $optionValue => $optionText)
	{
    $i++;
    $checked = ($optionValue == $value) ? 'checked' : '';
	?>
  <div class="custom-control custom-radio">
    <input type="radio" id="<?php echo $name.$i; ?>"
           name="<?php echo $name; ?>"
           value="<?php echo htmlspecialchars($optionValue, ENT_COMPAT, 'UTF-8') ?>"
        <?php echo $checked.$attributes; ?>
    />
    <label class="custom-control-label" for="<?php echo $name.$i; ?>">
        <?php echo $optionText; ?>
    </label>
  </div>

	<?php
	}
	?>
</fieldset>
