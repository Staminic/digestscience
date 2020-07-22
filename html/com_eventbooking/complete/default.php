<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2019 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die;
?>
<div id="eb-registration-complete-page" class="eb-container">
	<div class="container">
			<div class="page-content">
				<div><?php echo JHtml::_('content.prepare', $this->message); ?></div>
				<p>Les données personnelles collectées à travers le formulaire ci-dessus sont nécessaires au traitement de votre demande et seront traitées selon <a href="index.php?option=com_content&view=article&layout=digestscience:hero-page&id=1130&Itemid=1443" target="_blank">notre politique de confidentialité</a>.</p>
			</div>
	</div>
</div>
<?php
	echo $this->conversionTrackingCode;
?>
