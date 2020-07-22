<?php
/**
 * @version        5.4.5
 * @package        Joomla
 * @subpackage     Joom Donation
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2009 - 2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
if ($this->config->use_https)
{
	$url = JRoute::_('index.php?option=com_jdonation&Itemid='.$this->Itemid, false, 1);
}
else
{
	$url = JRoute::_('index.php?option=com_jdonation&Itemid='.$this->Itemid, false);
}
JHtml::_('behavior.modal', 'a.jd-modal');
DonationHelperJquery::validateForm();
//Validation rule fo custom amount
$amountValidationRules = '';
$minDonationAmount = (int) $this->config->minimum_donation_amount;
$maxDonationAmount = (int) $this->config->maximum_donation_amount;
if ($minDonationAmount)
{
	$amountValidationRules .= ",min[$minDonationAmount]";
}
if ($maxDonationAmount)
{
	$amountValidationRules .= ",max[$maxDonationAmount]";
}
$selectedState = '';
$bootstrapHelper 	= $this->bootstrapHelper;
$rowFluidClass   	= $bootstrapHelper->getClassMapping('row-fluid');
$span12Class		= $bootstrapHelper->getClassMapping('span12');
$span3Class		    = $bootstrapHelper->getClassMapping('span3');
$span6Class		    = $bootstrapHelper->getClassMapping('span6');
$controlGroupClass 	= $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass 	= $bootstrapHelper->getClassMapping('input-group');
$addOnClass        	= $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass 	= $bootstrapHelper->getClassMapping('control-label');
$controlsClass     	= $bootstrapHelper->getClassMapping('controls');
$btnClass          	= $bootstrapHelper->getClassMapping('btn');
$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small');
$stripePaymentMethod = null;

$app    = JFactory::getApplication();
$params = $app->getParams();
$pageclass = $params->get('pageclass_sfx');
$tpath   = JURI::base(true).'/templates/'.$app->getTemplate().'/';

?>
<script type="text/javascript">
	<?php echo $this->recurringString ;?>
	var siteUrl	= "<?php echo DonationHelper::getSiteUrl(); ?>";
</script>
<script type="text/javascript" src="<?php echo DonationHelper::getSiteUrl().'media/com_jdonation/assets/js/jdonation.js'?>"></script>
<div id="errors"></div>
<div id="donation-form" class="<?php echo $rowFluidClass;?> jd-container">
    <div class="<?php echo $span12Class?>">
        <?php
        if($this->campaign->id > 0 && $this->campaign->showCampaignInformation)
        {
            echo $this->loadTemplate('campaign');
        }

        $allow_donation = true;
        $msg = "";
        if($this->campaign->id > 0)
        {
            $total_donated = DonationHelper::getTotalDonatedAmount($this->campaign->id);
            $total_donors  = DonationHelper::getTotalDonor($this->campaign->id);
            if (!$this->config->endable_donation_with_expired_campaigns && (($this->campaign->end_date != "" && $this->campaign->end_date != "0000-00-00 00:00:00") && (strtotime($this->campaign->end_date) < time())))
            {
                //already expired
                $allow_donation = false;
                $msg = JText::_('JD_EXPIRED_CAMPAIGN');
            }
            if($this->campaign->goal > 0 && $total_donated > $this->campaign->goal && ! $this->config->endable_donation_with_goal_achieved_campaigns && $allow_donation)
            {
                $allow_donation = false;
                $msg = JText::_('JD_GOAL_ACHIEVED');
            }
            if((int)$this->campaign->limit_donors > 0 && $total_donors > (int)$this->campaign->limit_donors && $allow_donation)
            {
                $allow_donation = false;
                $msg = JText::_('JD_NUMBER_DONORS_ACHIEVED');
            }
        }

        if($allow_donation)
        {
            if($this->campaign->id > 0 && $this->config->show_campaign == 1) {
            ?>
            <div class="<?php echo $rowFluidClass?>">
                <div class="<?php echo $span12Class;?> campaigndescription" id="donation_form">
                    <h3 class="jd-page-title"><?php echo JText::_('JD_DONATION'); ?></h3>
                    <?php
                    }
              ?>
            <?php
            //show campaign
            if($this->campaign->id > 0){
                $campaign_link = JUri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).JRoute::_(DonationHelperRoute::getDonationFormRoute($this->campaign->id,JFactory::getApplication()->input->getInt('Itemid',0)));
                ?>
                <div class="<?php echo $rowFluidClass;?>">
                    <div class="<?php echo $span12Class;?>">
                        <?php
                        $config=JFactory::getConfig();
                        if(JVERSION>=3.0)
                            $site_name=$config->get( 'sitename' );
                        else
                            $site_name=$config->getvalue( 'config.sitename' );

                        require_once(JPATH_SITE . "/components/com_jdonation/helper/integrations.php");
                        $doc = JFactory::getDocument();
                        $doc->addCustomTag( '<meta property="og:title" content="'.$this->campaign->title.'" />' );
                        if(($this->campaign->campaign_photo != "") && (file_exists(JPATH_ROOT.'/images/jdonation/'.$this->campaign->campaign_photo)))
                        {
                            $doc->addCustomTag( '<meta property="og:image" content="'.JUri::root().'images/jdonation/'.$this->campaign->campaign_photo.'" />' );
                        }
                        if($this->campaign->short_description != "")
                        {
                            $short_desc = strip_tags($this->campaign->short_description);
							$short_desc = str_replace("\n","",$short_desc);
							$short_desc = str_replace("\r","",$short_desc);
                            $short_desc = str_replace("\"","",$short_desc);
                            $short_desc = str_replace("'","",$short_desc);
                            if(strlen($short_desc) > 155)
                            {
                                $short_desc = substr($short_desc,0,155)."..";
                            }
                            $doc->addCustomTag( '<meta property="og:description" content="'.$short_desc.'" />' );
                        }
                        $doc->addCustomTag( '<meta property="og:url" content="'.$campaign_link.'" />' );
                        $doc->addCustomTag( '<meta property="og:site_name" content="'.$site_name.'" />' );
                        $doc->addCustomTag( '<meta property="og:type" content="article" />' );
                        if($this->config->social_sharing == 1 && $this->config->show_campaign == 0)
                        {
                            ?>
                            <script type="text/javascript" src="<?php echo DonationHelper::getSiteUrl().'media/com_jdonation/assets/js/fblike.js'?>"></script>
                            <?php
                            if($this->config->social_sharing_type == 0 && $this->config->show_campaign == 0)
                            {
                                $add_this_share='
                                <!-- AddThis Button BEGIN -->
                                <div class="addthis_toolbox addthis_default_style">
                                <a class="addthis_button_facebook_like" fb:like:layout="button_count" class="addthis_button" addthis:url="'.$campaign_link.'"></a>
                                <a class="addthis_button_google_plusone" g:plusone:size="medium" class="addthis_button" addthis:url="'.$campaign_link.'"></a>
                                <a class="addthis_button_tweet" class="addthis_button" addthis:url="'.$campaign_link.'"></a>
                                <a class="addthis_button_pinterest_pinit" class="addthis_button" addthis:url="'.$campaign_link.'"></a>
                                <a class="addthis_counter addthis_pill_style" class="addthis_button" addthis:url="'.$campaign_link.'"></a>
                                </div>
                                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid="'.$this->config->addthis_publisher.'"></script>
                                <!-- AddThis Button END -->' ;
                                $add_this_js='https://s7.addthis.com/js/300/addthis_widget.js';
                                $JdonationIntegrationsHelper=new JdontionIntegrationsHelper();
                                $JdonationIntegrationsHelper->loadScriptOnce($add_this_js);
                                //output all social sharing buttons
                                echo' <div id="rr" style="">
                                    <div class="social_share_container">
                                    <div class="social_share_container_inner">'.
                                        $add_this_share.
                                    '</div>
                                </div>
                                </div>
                                ';
                            }
                            else
                            {
                                echo '<div class="jd_horizontal_social_buttons">';
                                    echo '<div class="jd_float_left">
                                            <div class="fb-like" data-href="'.$campaign_link.'" data-send="true" data-layout="button_count" data-width="450" data-show-faces="true">
                                            </div>
                                        </div>';
                                    echo '

                                    <div class="jd_float_left">
                                            &nbsp; <div class="g-plus" data-action="share" data-annotation="bubble" data-href="'.$campaign_link.'">
                                                </div>
                                    </div>';
                                echo '<div class="jd_float_left">
                                        &nbsp; <a href="https://twitter.com/share" class="twitter-share-button"  data-url="'.$campaign_link.'" data-counturl="'.$campaign_link.'">Tweet</a>
                                    </div>';
                                echo '</div>
                                    <div class="clearfix"></div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if($this->campaign->donation_form_msg)
            {
                $message = $this->campaign->donation_form_msg;
            }
            else
            {
                $message = $this->config->donation_form_msg;
            }
            if (strlen($message))
            {
            ?>
                <div class="jd-message clearfix jd_width_100_percentage"><?php echo $message; ?></div>
            <?php
            }
            if (!$this->userId && ($this->config->registration_integration == 1 || $this->config->registration_integration == 2) && $this->config->show_login_box)
            {
                $actionUrl = JRoute::_('index.php?option=com_users&task=user.login');
                $validateLoginForm = 1;
                ?>
                <div class="registration_form jd_width_100_percentage">
                    <form method="post" action="<?php echo $actionUrl ; ?>" name="jd-login-form" id="jd-login-form" autocomplete="off" class="form form-horizontal">
                        <h4 class="jd-heading"><?php echo JText::_('JD_EXISTING_USER_LOGIN'); ?></h4>
                        <div class="<?php echo $controlGroupClass;?>">
                            <label class="<?php echo $controlLabelClass;?>" for="username">
                                <?php echo  JText::_('JD_USERNAME') ?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <input type="text" name="username" id="username" class="input-large validate[required]" value=""/>
                            </div>
                        </div>
                        <div class="<?php echo $controlGroupClass;?>">
                            <label class="<?php echo $controlLabelClass;?>" for="password">
                                <?php echo  JText::_('JD_PASSWORD') ?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <input type="password" id="password" name="password" class="input-large validate[required]" value="" />
                            </div>
                        </div>
                        <div class="<?php echo $controlGroupClass;?>">
                            <div class="<?php echo $controlsClass;?>">
                                <input type="submit" value="<?php echo JText::_('JD_LOGIN'); ?>" class="button btn btn-primary" />
                            </div>
                        </div>
                        <?php
                        if (JPluginHelper::isEnabled('system', 'remember'))
                        {
                        ?>
                            <input type="hidden" name="remember" value="1" />
                        <?php
                        }
                        ?>
                        <input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance()->toString()); ?>" />
                        <?php echo JHtml::_( 'form.token' ); ?>
                    </form>
                </div>
            <?php
            }
            else
            {
                $validateLoginForm = 0;
            }

            ?>
            <form method="post" name="os_form" id="os_form" action="<?php echo $url ; ?>" autocomplete="off" class="form form-horizontal" enctype="multipart/form-data">
                <?php
                if (!$this->userId && ($this->config->registration_integration == 1 || $this->config->registration_integration == 2) && ($this->allowUserRegistration))
                {
                $params = JComponentHelper::getParams('com_users');
                $minimumLength = $params->get('minimum_length', 4);
                ($minimumLength) ? $minSize = "minSize[4]" : $minSize = "";
                ?>
                <h4 class="eb-heading"><?php echo JText::_('JD_NEW_USER_REGISTER'); ?></h4>
                <div class="registration_form">
                <?php
                if (!$this->config->show_login_box)
                {
                ?>
                    <h4 class="eb-heading"><?php echo JText::_('JD_ACCOUNT_INFORMATION'); ?></h4>
                <?php
                }
                ?>

                    <div class="<?php echo $controlGroupClass;?>">
                        <label class="<?php echo $controlLabelClass;?>" for="username1">
                            <?php echo JText::_('JD_USERNAME') ?><span class="required">*</span>
                        </label>
                        <div class="<?php echo $controlsClass;?>">
                            <input type="text" name="username" id="username1" class="input-large validate[required,ajax[ajaxUserCall]]" value="<?php echo $this->input->get('username', '', 'string'); ?>" />
                        </div>
                    </div>
                    <div class="<?php echo $controlGroupClass;?>">
                        <label class="<?php echo $controlLabelClass;?>" for="password1">
                            <?php echo  JText::_('JD_PASSWORD') ?><span class="required">*</span>
                        </label>
                        <div class="<?php echo $controlsClass;?>">
                            <input type="password" name="password1" id="password1" class="input-large validate[required,<?php echo $minSize;?>]" value=""/>
                        </div>
                    </div>
                    <div class="<?php echo $controlGroupClass;?>">
                        <label class="<?php echo $controlLabelClass;?>" for="password2">
                            <?php echo  JText::_('JD_RETYPE_PASSWORD') ?><span class="required">*</span>
                        </label>
                        <div class="<?php echo $controlsClass;?>">
                            <input type="password" name="password2" id="password2" class="input-large validate[required,equals[password1]]" value="" />
                        </div>
                    </div>

                    </div>
                <?php
                    if($this->config->registration_integration == 2){
                        ?>
                        <h3 class="eb-heading">
                            <a href="javascript:void(0);" id="skip_registration"><?php echo JText::_("JD_SKIP_AUTHENTICATION_FORM"); ?></a>
                        </h3>
                        <script type="text/javascript">
                            jQuery(document).ready(function($){
                                $("#skip_registration").click(function(){
                                    if($(".registration_form").css('display') != 'none'){
                                       $("#skip_registration").text("<?php echo JText::_("JD_OPEN_AUTHENTICATION_FORM"); ?>");
                                    }else{
                                        $("#skip_registration").text("<?php echo JText::_("JD_SKIP_AUTHENTICATION_FORM"); ?>");
                                    }
                                    $(".registration_form").toggle("slow");
                                });
                            });
                        </script>
                    <?php
                    }
                ?>
                <?php
                }
                ?>

                <?php
                if ($this->config->use_campaign)
                {
                    if ($this->showCampaignSelection)
                    {
                        //Campaign has been selected from the module or from campaigns page, so just display campaign title
                    ?>
                        <div class="<?php echo $controlGroupClass;?>">
                            <label class="<?php echo $controlLabelClass;?>" for="campaign_id">
                                <?php echo JText::_('JD_CAMPAIGN');?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <?php   echo $this->lists['campaign_id'] ;?>
                            </div>
                        </div>
                    <?php
                    }
                    else
                    {
                    ?>
                        <div class="<?php echo $controlGroupClass;?>">
                            <label class="<?php echo $controlLabelClass;?>" for="campaign_id">
                                <?php echo JText::_('JD_CAMPAIGN');?>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <?php echo $this->campaign->title; ?>
                            </div>
                        </div>
                    <?php
                    }
                	}
									?>

								<?php // Donation amount begin ?>
                <h4 class="jd-heading"><?php echo JText::_('JD_DONATION_INFORMATION'); ?></h4>
								<fieldset>

								<?php
                if ($this->config->enable_recurring)
                {
                    if ($this->campaignId)
                    {
                        if ($this->campaign->donation_type == 0 && $this->method->getEnableRecurring())
                        {
                            $style = '';
                        }
                        else
                        {
                            $style = ' style="display:none;"' ;
                        }
                    }
                    else
                    {
                        if ($this->method->getEnableRecurring())
                        {
                            $style = '';
                        }
                        else
                        {
                            $style = ' style="display:none;"' ;
                        }
                    }
                ?>

									<div class="<?php echo $controlGroupClass;?>" id="donation_type" <?php echo $style; ?>>
	                    <label class="<?php echo $controlLabelClass;?>" for="donation_type">
	                        <?php echo JText::_('JD_DONATION_TYPE'); ?>
	                    </label>

	                    <?php
	                        if (version_compare(JVERSION, '3.0', 'lt'))
	                        {
	                        ?>
	                            <div class="<?php echo $controlsClass;?>">
	                                <?php echo $this->lists['donation_type']; ?>
	                            </div>
	                        <?php
	                        }
	                        else
	                        {
	                            echo $this->lists['donation_type'];
	                        }
	                    ?>
	                </div>

	                <?php
	                    if ($this->donationType == 'onetime' || !$this->method->getEnableRecurring())
	                    {
	                        $style = ' style="display:none" ';
	                    }
	                    else
	                    {
	                        $style = '';
	                    }
	                ?>

                	<div class="<?php echo $controlGroupClass;?>" id="tr_frequency" <?php echo $style; ?>>
                    <label class="<?php echo $controlLabelClass;?>" for="r_frequency">
                        <?php echo JText::_('JD_FREQUENCY') ; ?>
                    </label>
                    <div class="<?php echo $controlsClass;?>">
                        <?php
                            if (count($this->recurringFrequencies) > 1)
                            {
                                echo $this->lists['r_frequency'];
                            }
                            else
                            {
                                $frequency = $this->recurringFrequencies[0];
                                switch($frequency)
                                {
                                    case 'd':
                                        echo JText::_('JD_DAILY');
                                        break;
                                    case 'w':
                                        echo JText::_('JD_WEEKLY');
                                        break;
                                    case 'b':
                                        echo JText::_('JD_BI_WEEKLY');
                                        break;
                                    case 'm':
                                        echo JText::_('JD_MONTHLY');
                                        break;
                                    case 'q':
                                        echo JText::_('JD_QUARTERLY');
                                        break;
                                    case 's':
                                        echo JText::_('JD_SEMI_ANNUALLY');
                                        break;
                                    case 'a':
                                        echo JText::_('JD_ANNUALLY');
                                        break;
                                }
                                ?>
                                <input type="hidden" name="r_frequency" value="<?php echo $frequency; ?>" />
                                <?php
		                            }
		                        		?>
		                    	</div>
		                	</div>

			                <?php
			                    if ($this->config->show_r_times)
			                    {
			                    ?>
			                        <div class="<?php echo $controlGroupClass;?>" id="tr_number_donations" <?php echo $style; ?>>
			                            <label class="<?php echo $controlLabelClass;?>" for="r_times">
			                                <?php echo JText::_('JD_OCCURRENCES') ; ?>
			                            </label>
			                            <div class="<?php echo $controlsClass;?>">
			                                <input type="text" name="r_times" value="<?php echo $this->input->getInt('r_times', null); ?>" class="<?php echo $inputSmallClass;?>"/>
			                            </div>
			                        </div>
			                    <?php
			                    }
			                	}
			            		?>

                	<div class="<?php echo $controlGroupClass;?>">
                    <div class="<?php echo $controlsClass;?>" id="amount_container">
                        <?php
                            $amountSelected = false;
                            if ($this->config->donation_amounts)
                            {
                                $explanations = explode("\r\n", $this->config->donation_amounts_explanation) ;
                                $amounts = explode("\r\n", $this->config->donation_amounts);
																if($this->amount == 0){
																	$extraValidateClass = "validate[required]";
																}else{
																	$extraValidateClass = "";
																}
                                if ($this->config->amounts_format == 1)
                                {
																?>

																<div class="radio-list">
																<?php
                                    for ($i = 0 , $n = count($amounts) ; $i < $n ; $i++)
                                    {
                                        $amount = (float)$amounts[$i] ;
                                        if ($amount == $this->rdAmount)
                                        {
                                            $amountSelected = true;
                                            $checked = ' checked="checked" ' ;
                                        }
                                        else
                                        {
                                            $checked = '' ;
                                        }
                                    ?>
                                      <label class="don-<?php echo $amount; ?>">
                                        <input type="radio" name="rd_amount" class="<?php echo $extraValidateClass; ?>" value="<?php echo $amount; ?>" <?php echo $checked ; ?> onclick="clearTextbox();" data-errormessage="<?php echo JText::_('JD_AMOUNT_IS_REQUIRED'); ?>" /><span><?php echo ' '.DonationHelperHtml::formatAmount($this->config, $amount);?></span>
																				<img src="<?php echo $tpath;?>img/don-<?php echo $amount; ?>.svg" alt="Mon don" class="img-fluid" />
																				<?php
                                        if (isset($explanations[$i]) && trim($explanations[$i]) != "")
                                        {
                                          echo '<span class="amount_explaination">[ '.$explanations[$i].' ]</span>';
                                        }
                                        ?>
                                      </label>
																			<?php
																		} ?>
																	</div>

																<?php
																}
																else
																{
                                    $options = array() ;
                                    $options[] = JHtml::_('select.option', 0, JText::_('JD_AMOUNT')) ;
                                    for ($i = 0 , $n = count($amounts) ; $i < $n ; $i++)
                                    {
                                        $amount = (float)$amounts[$i] ;
                                        if ($amount == $this->rdAmount)
                                        {
                                            $amountSelected = true;
                                        }
                                        if (isset($explanations[$i]) && $explanations[$i])
                                        {
                                            $options[] = JHtml::_('select.option', $amount, DonationHelperHtml::formatAmount($this->config, $amount)." [$explanations[$i]]") ;
                                        }
                                        else
                                        {
                                            $options[] = JHtml::_('select.option', $amount, DonationHelperHtml::formatAmount($this->config, $amount)) ;
                                        }
                                    }
                                    echo  $this->config->currency_symbol.'  '.JHtml::_('select.genericlist', $options, 'rd_amount', ' class="'.$extraValidateClass.' input-large" onchange="clearTextbox();" ', 'value', 'text', $this->rdAmount).'<br /><br />';
                                }
                            }
                            if ($this->config->display_amount_textbox)
                            {
                                if ($this->config->donation_amounts)
                                {
                                    $placeHolder = JText::_('JD_OTHER_AMOUNT');
                                }
                                else
                                {
                                    $placeHolder = '';
                                }
                                if ($amountSelected)
                                {
                                    $amountCssClass = 'validate[custom[number]'.$amountValidationRules.'] '.$inputSmallClass;
                                }
                                else
                                {
                                    $amountCssClass = 'validate[required,custom[number]'.$amountValidationRules.'] '.$inputSmallClass;
                                }
                                if ($this->config->currency_position == 0)
                                {
                                    $addons = $this->config->currency_symbol;
                                    $input  = '<input type="number" placeholder="'.$placeHolder.'" class="'.$amountCssClass.'" name="amount" id="amount" value="'.$this->amount.'" onchange="deSelectRadio();" data-errormessage="'.JText::_('JD_AMOUNT_IS_REQUIRED').'" data-errormessage-range-underflow="'.JText::sprintf('JD_MIN_DONATION_AMOUNT_ALLOWED', $this->config->minimum_donation_amount).'" data-errormessage-range-overflow="'.JText::sprintf('JD_MAX_DONATION_AMOUNT_ALLOWED', $this->config->maximum_donation_amount).'" />';
                                    echo $bootstrapHelper->getPrependAddon($input,$addons);
                                }
                                else
                                {
                                    $addons = $this->config->currency_symbol;
                                    $input  = '<input type="number" placeholder="'.$placeHolder.'" class="'.$amountCssClass.'" name="amount" id="amount" value="'.$this->amount.'" onchange="deSelectRadio();" data-errormessage="'.JText::_('JD_AMOUNT_IS_REQUIRED').'" data-errormessage-range-underflow="'.JText::sprintf('JD_MIN_DONATION_AMOUNT_ALLOWED', $this->config->minimum_donation_amount).'" data-errormessage-range-overflow="'.JText::sprintf('JD_MAX_DONATION_AMOUNT_ALLOWED', $this->config->maximum_donation_amount).'" />';
                                    echo '<div class="col-lg-6"><label class="placeholder">' . $placeHolder . '</label>' . $bootstrapHelper->getAppendAddon($input,$addons) . '</div>';
                                }
                            }
                        ?>
	                    </div>
	                </div>
								</fieldset>
								<?php // Donation amount end	?>

								<?php // Custom fields begin
								$fields = $this->form->getFields();
								?>

								<h4 class="jd-heading"><?php echo JText::_('JD_CONTACT_INFO'); ?></h4>

								<fieldset>
								<?php
								if (isset($fields['state']))
                {
                    $selectedState = $fields['state']->value;
                }
                foreach ($fields as $field)
                {
                  if ($field->name =='email')
                  {
                    if ($this->userId || !$this->config->registration_integration || !$this->allowUserRegistration)
                    {
                      //We don't need to perform ajax email validate in this case, so just remove the rule
                      $cssClass = $field->getAttribute('class');
                      $cssClass = str_replace(',ajax[ajaxEmailCall]', '', $cssClass);
                      $field->setAttribute('class', $cssClass);
                    }
                  }
                  echo $field->getControlGroup(true, $bootstrapHelper);
                }
								?>
								</fieldset>
								<?php // Custom fields end ?>


								<?php
								if ($this->config->pay_payment_gateway_fee)
                {
                ?>
                    <div class="<?php echo $controlGroupClass;?>" id="pay_payment_gateway_fee_div">
                        <label class="<?php echo $controlLabelClass;?>" for="pay_payment_gateway_fee">
                            <?php echo  JText::_('JD_PAY_PAYMENT_GATEWAY_FEE'); ?>
                        </label>
                        <?php
                        if (version_compare(JVERSION, '3.0', 'lt'))
                        {
                        ?>
                            <div class="<?php echo $controlsClass;?>">
                                <?php echo $this->lists['pay_payment_gateway_fee']; ?>
                            </div>
                        <?php
                        }
                        else
                        {
                            echo $this->lists['pay_payment_gateway_fee'];
                        }
                        ?>
                    </div>
                <?php
                }
                if ($this->config->enable_hide_donor)
                {
                ?>
                    <div class="<?php echo $controlGroupClass;?>">
                        <label class="<?php echo $controlLabelClass;?>" for="hide_me">
                            <?php echo  JText::_('JD_HIDE_DONOR'); ?>
                        </label>
                        <div class="<?php echo $controlsClass;?>">
                            <input type="checkbox" class="input-large" name="hide_me" value="1" size="40" <?php if ($this->hideMe) echo ' checked="checked"' ; ?> />
                        </div>
                    </div>
                <?php
                }
                ?>
                <?php
                if($this->show_dedicate == 1)
                {
                    ?>
                    <div class="<?php echo $rowFluidClass?>">
                        <div class="<?php echo $span12Class?>">
                            <input type="checkbox" name="show_dedicate" id="show_dedicate" value="0" onclick="javascript:showDedicate();"/>
                            <?php echo JText::_('JD_HONOR_OF'); ?>
                        </div>
                    </div>
                    <div class="<?php echo $rowFluidClass?> nodisplay" id="honoreediv">
                        <div class="<?php echo $span12Class?>">
                            <div class="<?php echo $rowFluidClass?>">
                                <?php
                                for($i=1;$i<=4;$i++)
                                {
                                    ?>
                                    <div class="<?php echo $span3Class?>">
                                        <input type="radio" name="dedicate_type" value="<?php echo $i;?>" />
                                        &nbsp;
                                        <?php echo DonationHelper::getDedicateType($i);?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="<?php echo $rowFluidClass?>">
                                <div class="<?php echo $span12Class?>">
                                    <div class="<?php echo $controlGroupClass;?>">
                                        <label class="<?php echo $controlLabelClass;?>" for="campaign_id">
                                            <?php echo JText::_('JD_HONOREE_NAME');?>
                                        </label>
                                        <div class="<?php echo $controlsClass;?>">
                                            <input type="text" class="input-large" name="dedicate_name" value="" />
                                        </div>
                                    </div>
                                    <div class="<?php echo $controlGroupClass;?>">
                                        <label class="<?php echo $controlLabelClass;?>" for="campaign_id">
                                            <?php echo JText::_('JD_HONOREE_EMAIL');?>
                                        </label>
                                        <div class="<?php echo $controlsClass;?>">
                                            <input type="text" class="input-large" name="dedicate_email" value="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

								<?php // Credit card info begin	?>
								<fieldset>

                <?php
                    if ($this->config->currency_selection)
                    {
                    ?>
                        <div class="<?php echo $controlGroupClass;?>">
                            <label class="<?php echo $controlLabelClass;?>">
                                <?php echo JText::_('JD_CHOOSE_CURRENCY'); ?>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <?php echo $this->lists['currency_code']; ?>
                            </div>
                        </div>
                    <?php
                    }
										?>

										<?php
										if (count($this->methods) > 1)
                  	{
                  	?>
                      <div class="<?php echo $controlGroupClass;?>" id="jdpaymentmethods">
                          <label class="<?php echo $controlLabelClass;?>">
                              <?php echo JText::_('JD_PAYMENT_OPTION'); ?>
                              <span class="required">*</span>
                          </label>

													<div class="<?php echo $controlsClass;?>">
                              <?php
                                  $method = null ;
                                  for ($i = 0 , $n = count($this->methods); $i < $n; $i++)
                                  {
                                      $paymentMethod = $this->methods[$i];
                                      if ($paymentMethod->getName() == $this->paymentMethod)
                                      {
                                          $checked = ' checked="checked" ';
                                          $method = $paymentMethod ;
                                      }
                                      else
                                      {
                                          $checked = '';
                                      }
                                      if (strpos($paymentMethod->getName(), 'os_stripe') !== false)
                                      {
                                          $stripePaymentMethod = $paymentMethod;
                                      }
                                      ?>
                                      <label>
                                          <input onclick="changePaymentMethod();" type="radio" name="payment_method" value="<?php echo $paymentMethod->getName(); ?>" <?php echo $checked; ?> />&nbsp;<?php echo JText::_($paymentMethod->getTitle()); ?> <br />
                                      </label>
                                  <?php
                                  }
                              ?>
                          </div>

                      </div>
                    <?php
                    }
                    else
                    {
                      $method = $this->methods[0] ;
                      if (strpos($method->getName(), 'os_stripe') !== false)
                      {
                          $stripePaymentMethod = $method;
                      }
                      ?>
                      <div class="<?php echo $controlGroupClass;?>">
                          <label class="<?php echo $controlLabelClass;?>">
                              <?php echo JText::_('JD_PAYMENT_OPTION'); ?>
                          </label>
                          <div class="<?php echo $controlsClass;?>">
                              <?php echo JText::_($method->getTitle()); ?>
                          </div>
                      </div>
                      <?php
                    }

                    if ($method->getName() == 'os_squareup')
                    {
                        $style = '';
                    }
                    else
                    {
                        $style = 'style = "display:none"';
                    }
                    ?>

                    <div class="<?php echo $controlGroupClass;?> payment_information" id="sq_field_zipcode" <?php echo $style; ?>>
                      <label class="<?php echo $controlLabelClass;?>" for="sq_billing_zipcode">
                          <?php echo JText::_('JD_SQUAREUP_ZIPCODE'); ?><span class="required">*</span>
                      </label>

                      <div class="<?php echo $controlsClass;?>">
                          <div id="field_zip_input">
                              <input type="text" id="sq_billing_zipcode" name="sq_billing_zipcode" class="input-large" value="<?php echo $this->escape($this->input->getString('sq_billing_zipcode')); ?>" />
                          </div>
                      </div>
                    </div>
                    <?php
                    if ($method->getCreditCard())
                    {
                        $style = '' ;
                    }
                    else
                    {
                        $style = 'style = "display:none"';
                    }
                    ?>

										<div class="<?php echo $controlGroupClass;?>" id="tr_card_number" <?php echo $style; ?>>
                        <label class="<?php echo $controlLabelClass;?>"><?php echo  JText::_('AUTH_CARD_NUMBER'); ?><span class="required">*</span></label>
                        <div class="<?php echo $controlsClass;?>">
                            <div id="sq-card-number">
                                <input type="text" name="x_card_num" id="x_card_num" class="input-large validate[required,creditCard]" value="<?php echo $this->input->get('x_card_num', '', 'none'); ?>" size="20" />
                            </div>
                        </div>
                    </div>

										<div class="<?php echo $controlGroupClass;?>" id="tr_exp_date" <?php echo $style; ?>>
                        <label class="<?php echo $controlLabelClass;?>">
                            <?php echo JText::_('AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
                        </label>
                        <div class="<?php echo $controlsClass;?>">
                            <div id="sq-expiration-date">
                                <?php echo $this->lists['exp_month'] .'  /  '.$this->lists['exp_year'] ; ?>
                            </div>
                        </div>
                    </div>

										<div class="<?php echo $controlGroupClass;?>" id="tr_cvv_code" <?php echo $style; ?>>
                        <label class="<?php echo $controlLabelClass;?>">
                            <?php echo JText::_('AUTH_CVV_CODE'); ?><span class="required">*</span>
                        </label>
                        <div class="<?php echo $controlsClass;?>">
                            <div id="sq-cvv">
                                <input type="text" name="x_card_code" id="x_card_code" class="input-large validate[required,custom[number]]" value="<?php echo $this->input->get('x_card_code', '', 'none'); ?>" size="20" />
                            </div>
                        </div>
                    </div>

										<?php
                        if ($method->getCardType())
                        {
                            $style = '' ;
                        }
                        else
                        {
                            $style = ' style = "display:none;" ' ;
                        }
                    ?>
                      <div class="<?php echo $controlGroupClass;?>" id="tr_card_type" <?php echo $style; ?>>
                          <label class="<?php echo $controlLabelClass;?>">
                              <?php echo JText::_('JD_CARD_TYPE'); ?><span class="required">*</span>
                          </label>
                          <div class="<?php echo $controlsClass;?>">
                              <?php echo $this->lists['card_type'] ; ?>
                          </div>
                      </div>
                    <?php
                        if ($method->getCardHolderName())
                        {
                            $style = '' ;
                        }
                        else
                        {
                            $style = ' style = "display:none;" ' ;
                        }
                        ?>
                        <div class="<?php echo $controlGroupClass;?>" id="tr_card_holder_name" <?php echo $style; ?>>
                            <label class="<?php echo $controlLabelClass;?>">
                                <?php echo JText::_('JD_CARD_HOLDER_NAME'); ?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <input type="text" name="card_holder_name" id="card_holder_name" class="input-large validate[required]"  value="<?php echo $this->input->get('card_holder_name', '', 'none'); ?>" size="40" />
                            </div>
                        </div>

                        <?php
                        $sisowEnabled = os_payments::sisowEnabled();
                        if ($sisowEnabled) {
                            os_payments::getBankLists();
                        }
                        ?>
                    <?php
                    if (DonationHelper::isPaymentMethodEnabled('os_echeck'))
                    {
                        if ($method->getName() == 'os_echeck')
                        {
                            $style = '';
                        }
                        else
                        {
                            $style = ' style = "display:none;" ';
                        }
                        ?>
                        <div class="<?php echo $controlGroupClass;?>" id="tr_bank_rounting_number" <?php echo $style; ?>>
                            <label class="<?php echo $controlLabelClass;?>">
                                <?php echo JText::_('JD_BANK_ROUTING_NUMBER'); ?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <input type="text" name="x_bank_aba_code" class="input-large validate[required,custom[number]]" value="<?php echo $this->input->get('x_bank_aba_code', '', 'none'); ?>" size="40"/>
                            </div>
                        </div>
                        <div class="<?php echo $controlGroupClass;?>" id="tr_bank_account_number" <?php echo $style; ?>>
                            <label class="<?php echo $controlLabelClass;?>">
                                <?php echo JText::_('JD_BANK_ACCOUNT_NUMBER'); ?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <input type="text" name="x_bank_acct_num" class="input-large validate[required,custom[number]]" value="<?php echo $this->input->get('x_bank_acct_num', '', 'none');; ?>" size="40"/>
                            </div>
                        </div>
                        <div class="<?php echo $controlGroupClass;?>" id="tr_bank_account_type" <?php echo $style; ?>>
                            <label class="<?php echo $controlLabelClass;?>">
                                <?php echo JText::_('JD_BANK_ACCOUNT_TYPE'); ?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>"><?php echo $this->lists['x_bank_acct_type']; ?></div>
                        </div>
                        <div class="<?php echo $controlGroupClass;?>" id="tr_bank_name" <?php echo $style; ?>>
                            <label class="<?php echo $controlLabelClass;?>">
                                <?php echo JText::_('JD_BANK_NAME'); ?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <input type="text" name="x_bank_name" class="input-large validate[required]" value="<?php echo $this->input->get('x_bank_name', '', 'none'); ?>" size="40"/>
                            </div>
                        </div>
                        <div class="<?php echo $controlGroupClass;?>" id="tr_bank_account_holder" <?php echo $style; ?>>
                            <label class="<?php echo $controlLabelClass;?>">
                                <?php echo JText::_('JD_ACCOUNT_HOLDER_NAME'); ?><span class="required">*</span>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <input type="text" name="x_bank_acct_name" class="input-large validate[required]" value="<?php echo $this->input->get('x_bank_acct_name', '', 'none'); ?>" size="40"/>
                            </div>
                        </div>
                    <?php
                    }
                    if ($stripePaymentMethod !== null && method_exists($stripePaymentMethod, 'getParams'))
                    {
                        /* @var os_stripe $stripePaymentMethod */
                        $params = $stripePaymentMethod->getParams();
                        $useStripeCardElement = $params->get('use_stripe_card_element', 0);

                        if ($useStripeCardElement)
                        {
                            if ($method->getName() === 'os_stripe')
                            {
                                $style = '';
                            }
                            else
                            {
                                $style = ' style = "display:none;" ';
                            }
                            ?>
                            <div class="<?php echo $controlGroupClass;?> payment_information" <?php echo $style; ?> id="stripe-card-form">
                                <label class="<?php echo $controlLabelClass; ?>" for="stripe-card-element">
                                    <?php echo JText::_('JD_CREDIT_OR_DEBIT_CARD'); ?><span class="required">*</span>
                                </label>
                                <div class="<?php echo $controlsClass; ?>" id="stripe-card-element">

                                </div>
                            </div>
                            <?php
	                        }
	                    	}
												?>
											</fieldset>
											<?php // Credit card info end ?>

										<?php
                    if ($this->config->show_newsletter_subscription == 1 && DonationHelper::isNewsletterPluginEnabled()){
                        ?>
                        <div class="<?php echo $controlGroupClass;?>">
                            <label class="<?php echo $controlLabelClass;?>">
                                <?php echo JText::_('JD_SUBSCRIBE_TO_NEWSLETTER'); ?>
                            </label>
                            <div class="<?php echo $controlsClass;?>">
                                <input type="checkbox" name="newsletter_subscription" value="1" id="newsletter_subscription" />
                            </div>
                        </div>
                        <?php
                    }
                    if ($this->config->accept_term ==1 && $this->config->article_id > 0)
                    {
                        $articleId = $this->config->article_id;
                        $db =  JFactory::getDbo();
                        $query = $db->getQuery(true);
                        $query->select('id, catid')
                            ->from('#__content')
                            ->where('id = '. (int) $articleId);
                        $db->setQuery($query);
                        $article = $db->loadObject();
                        //Terms and Conditions
                        require_once JPATH_ROOT.'/components/com_content/helpers/route.php' ;
                        $termLink = ContentHelperRoute::getArticleRoute($article->id, $article->catid).'&tmpl=component&format=html' ;
                        $extra = ' class="jd-modal" ' ;
                        ?>
                        <div class="<?php echo $controlGroupClass;?>">
                            <label class="checkbox">
                                <input type="checkbox" name="accept_term" value="1" class="validate[required]" data-errormessage="<?php echo JText::_('JD_ACCEPT_TERMS');?>" />
                                <?php echo JText::_('JD_ACCEPT'); ?>&nbsp;
                                <?php
                                    echo "<a $extra href=\"".JRoute::_($termLink)."\">"."<strong>".JText::_('JD_TERM_AND_CONDITION')."</strong>"."</a>\n";
                                ?>
                            </label>
                        </div>
                        <?php
                    }

                    if ($this->config->show_privacy)
                    {
                        if ($this->config->privacy_policy_article_id > 0)
                        {
                            $privacyArticleId = $this->config->privacy_policy_article_id;

                            if (JLanguageMultilang::isEnabled())
                            {
                                $associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
                                $langCode     = JFactory::getLanguage()->getTag();
                                if (isset($associations[$langCode]))
                                {
                                    $privacyArticle = $associations[$langCode];
                                }
                            }

                            if (!isset($privacyArticle))
                            {
                                $db    = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select('id, catid')
                                    ->from('#__content')
                                    ->where('id = ' . (int) $privacyArticleId);
                                $db->setQuery($query);
                                $privacyArticle = $db->loadObject();
                            }

                            JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

                            $link = JRoute::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid).'&tmpl=component&format=html');
                        }
                        else
                        {
                            $link = '';
                        }
                        ?>
                        <div class="<?php echo $controlGroupClass ?>">
                            <div class="<?php echo $controlLabelClass; ?>">
                                <?php
                                if ($link)
                                {
                                    $extra = ' class="jd-modal" ' ;
                                ?>
                                    <a href="<?php echo $link; ?>" <?php echo $extra;?> class="eb-colorbox-privacy-policy"><?php echo JText::_('JD_PRIVACY_POLICY');?></a>
                                <?php
                                }
                                else
                                {
                                    echo JText::_('JD_PRIVACY_POLICY');
                                }
                                ?>
                            </div>
                            <div class="<?php echo $controlsClass; ?>">
                                <input type="checkbox" name="agree_privacy_policy" value="1" class="validate[required]" data-errormessage="<?php echo JText::_('JD_AGREE_PRIVACY_POLICY_ERROR');?>" />
                                <?php
                                $agreePrivacyPolicyMessage = JText::_('JD_AGREE_PRIVACY_POLICY_MESSAGE');

                                if (strlen($agreePrivacyPolicyMessage))
                                {
                                ?>
                                    <div class="eb-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }

					if($this->showCaptcha)
                    {
                        if(!$this->userId && $this->config->enable_captcha_with_public_user == 1){
                        ?>
                            <div class="<?php echo $controlGroupClass;?>">
                                <label class="<?php echo $controlLabelClass;?>">
                                    <?php echo JText::_('JD_CAPTCHA'); ?><span class="required">*</span>
                                </label>
                                <div class="<?php echo $controlsClass;?>">
                                    <?php echo $this->captcha; ?>
                                </div>
                            </div>
                        <?php
                        }elseif($this->config->enable_captcha_with_public_user == 0){
                            ?>
                            <div class="<?php echo $controlGroupClass;?>">
                                <label class="<?php echo $controlLabelClass;?>">
                                    <?php echo JText::_('JD_CAPTCHA'); ?><span class="required">*</span>
                                </label>
                                <div class="<?php echo $controlsClass;?>">
                                    <?php echo $this->captcha; ?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                ?>
                <div class="form-actions text-right">
                    <input type="submit" class="btn btn-lg btn-success" name="btnSubmit" id="btn-submit" value="<?php echo  JText::_('JD_PROCESS_DONATION') ;?>" />
                </div>
                <?php
                    if (count($this->methods) == 1)
                    {
                    ?>
                        <input type="hidden" name="payment_method" value="<?php echo $this->methods[0]->getName(); ?>" />
                    <?php
                    }
                    if (!$this->config->enable_recurring)
                    {
                    ?>
                        <input type="hidden" name="donation_type" value="onetime" />
                    <?php
                    }
                    if (!$this->showCampaignSelection)
                    {
                    ?>
                        <input type="hidden" id="campaign_id" name="campaign_id" value="<?php echo $this->campaignId; ?>" />
                    <?php
                    }
                ?>
                <input type="hidden" name="validate_form_login" value="<?php echo $validateLoginForm; ?>" />
                <input type="hidden" name="receive_user_id" value="<?php echo $this->input->getInt('receive_user_id'); ?>" />
                <input type="hidden" name="amounts_format" value="<?php echo $this->config->amounts_format; ?>" />
                <input type="hidden" name="field_campaign" value="<?php echo $this->config->field_campaign; ?>" />
                <input type="hidden" name="amount_by_campaign" value="<?php echo $this->config->amount_by_campaign; ?>" />
                <input type="hidden" name="enable_recurring" value="<?php echo $this->config->enable_recurring; ?>" />
                <input type="hidden" name="count_method" value="<?php echo count($this->methods); ?>" />
                <input type="hidden" name="current_campaign" value="<?php echo $this->campaignId; ?>" />
                <input type="hidden" name="donation_page_url" value="<?php echo $this->donationPageUrl; ?>" />
                <input type="hidden" id="card-nonce" name="nonce" />
                <input type="hidden" name="task" value="donation.process" />
				<input type="hidden" name="smallinput" id="smallinput" value="<?php echo $inputSmallClass; ?>" />
                <?php echo JHtml::_( 'form.token' ); ?>
                <script type="text/javascript">
                    var amountInputCssClasses = '<?php echo "validate[required,custom[number] $amountValidationRules ] ".$inputSmallClass; ?>';
                    <?php echo os_payments::writeJavascriptObjects() ; ?>
                    JD.jQuery(function($){
                        $(document).ready(function(){
                            $("#os_form").validationEngine('attach', {
                                onValidationComplete: function(form, status){
                                    if (status == true) {
                                        form.on('submit', function(e) {
                                            e.preventDefault();
                                        });

                                        form.find('#btn-submit').prop('disabled', true);


                                        if($('input:radio[name^=payment_method]').length)
                                        {
                                            var paymentMethod = $('input:radio[name^=payment_method]:checked').val();
                                        }
                                        else
                                        {
                                            var paymentMethod = $('input[name^=payment_method]').val();
                                        }

                                        if (typeof stripePublicKey !== 'undefined' && paymentMethod.indexOf('os_stripe') == 0 && $('#tr_card_number').is(':visible'))
                                        {
                                            Stripe.card.createToken({
                                                number: $('input[name^=x_card_num]').val(),
                                                cvc: $('input[name^=x_card_code]').val(),
                                                exp_month: $('select[name^=exp_month]').val(),
                                                exp_year: $('select[name^=exp_year]').val(),
                                                name: $('input[name^=card_holder_name]').val()
                                            }, stripeResponseHandler);

                                            return false;
                                        }

                                        // Stripe card element
                                        if (typeof stripe !== 'undefined' && paymentMethod.indexOf('os_stripe') == 0 && $('#stripe-card-form').is(":visible"))
                                        {
                                            stripe.createToken(card).then(function(result) {
                                                if (result.error) {
                                                    // Inform the customer that there was an error.
                                                    //var errorElement = document.getElementById('card-errors');
                                                    //errorElement.textContent = result.error.message;
                                                    alert(result.error.message);
													form.find('#btn-submit').prop('disabled', false);
                                                } else {
                                                    // Send the token to your server.
                                                    stripeTokenHandler(result.token);
                                                }
                                            });

                                            return false;
                                        }

                                        if (paymentMethod == 'os_squareup' && $('#tr_card_number').is(':visible'))
                                        {
                                            sqPaymentForm.requestCardNonce();

                                            return false;
                                        }

                                        return true;
                                    }
                                    return false;
                                }
                            });

                            if (typeof stripe !== 'undefined')
                            {
                                var style = {
                                    base: {
                                        // Add your base input styles here. For example:
                                        fontSize: '16px',
                                        color: "#32325d",
                                    }
                                };

                                // Create an instance of the card Element.
                                var card = elements.create('card', {style: style});

                                // Add an instance of the card Element into the `card-element` <div>.
                                card.mount('#stripe-card-element');
                            }

                            if($("[name*='validate_form_login']").val() == 1)
                            {
                                JDVALIDATEFORM("#jd-login-form");
                            }
                            <?php
                                if (isset($fields['state']) && \Joomla\String\StringHelper::strtolower($fields['state']->type) == 'state')
                                {
                                ?>
                                    buildStateField('state', 'country', '<?php echo $selectedState; ?>');
                                <?php
                                }
                            ?>
                        })
                    });
                </script>
            </form>
            <?php
            if($this->campaign->id > 0 && $this->config->show_campaign == 1) {
                ?>
                </div>
                </div>
                <?php
            }
    }
    else
    {
        ?>
        <div class="<?php echo $rowFluidClass?>">
            <div class="<?php echo $span12Class;?> campaigndescription" id="donation_form">
                <h3>
                    <?php echo JText::_('JD_DISABLE_DONATION');?>
                </h3>
                <?php
                echo JText::_('JD_REASON').": ".$msg;
                ?>
            </div>
        </div>
        <?php
    }
    ?>
    </div>
</div>
<?php
	if ($this->config->amount_by_campaign)
	{
		$rowCampaigns  = $this->rowCampaigns ;
		for ($j = 0 , $m = count($rowCampaigns) ; $j < $m ; $j++)
		{
            $rowCampaign = $rowCampaigns[$j] ;
            ?>
			<div id="campaign_<?php echo $rowCampaign->id; ?>" style="display: none;">
			<?php
			$explanations = explode("\r\n", $rowCampaign->amounts_explanation) ;
			$amounts = explode("\r\n", $rowCampaign->amounts);
			$amountSelected = false;
			if ($this->config->amounts_format == 1)
			{
				for ($i = 0 , $n = count($amounts) ; $i < $n ; $i++)
				{
					$amount = (float)$amounts[$i] ;
					if ($amount == $this->rdAmount)
					{
						$amountSelected = true;
						$checked = ' checked="checked" ' ;
					}
					else
					{
						$checked = '' ;
					}
				?>
                    <label>
                        <input type="radio" name="rd_amount" value="<?php echo $amount; ?>" <?php echo $checked ; ?> onclick="clearTextbox();" /><?php echo ' '.DonationHelperHtml::formatAmount($this->config, $amount) ;?>
                        <?php
                            if (isset($explanations[$i]) && $explanations[$i])
                            {
                                echo '   <span class="amount_explaination">[ '.$explanations[$i].' ]</span>  ' ;
                            }
                        ?>
                    </label>
				<?php
				}
			}
			else
			{
				$options = array() ;
				$options[] = JHtml::_('select.option', 0, JText::_('JD_DONATION_AMOUNT')) ;
				for ($i = 0 , $n = count($amounts) ; $i < $n ; $i++)
				{
					$amount = (float)$amounts[$i] ;
					if ($amount == $this->rdAmount)
					{
						$amountSelected = true;
					}
					if (isset($explanations[$i]) && $explanations[$i])
					{
						$options[] = JHtml::_('select.option', $amount, DonationHelperHtml::formatAmount($this->config, $amount)." [$explanations[$i]]");
					}
					else
					{
						$options[] = JHtml::_('select.option', $amount, DonationHelperHtml::formatAmount($this->config, $amount));
					}
				}
				echo  $this->config->currency_symbol.'  '.JHtml::_('select.genericlist', $options, 'rd_amount', ' class="input-large" onchange="clearTextbox();" ', 'value', 'text', $this->rdAmount).'<br /><br />';
			}
			if ($this->config->display_amount_textbox)
			{
				if ($amountSelected)
				{
					$amountCssClass = 'validate[custom[number]'.$amountValidationRules.'] '.$inputSmallClass;
				}
				else
				{
					$amountCssClass = 'validate[required,custom[number]'.$amountValidationRules.'] '.$inputSmallClass;
				}
				if ($rowCampaign->amounts)
				{
					$placeHolder = JText::_('JD_OTHER_AMOUNT');
				}
				else
				{
					$placeHolder = '';
				}

				if ($this->config->currency_position == 0)
				{
					$addons = $this->config->currency_symbol;
					$input  = '<input type="text" placeholder="'.$placeHolder.'" class="'.$amountCssClass.'" name="amount" id="amount" value="'.$this->amount.'" onchange="deSelectRadio();" data-errormessage="'.JText::_('JD_AMOUNT_IS_REQUIRED').'" data-errormessage-range-underflow="'.JText::sprintf('JD_MIN_DONATION_AMOUNT_ALLOWED', $this->config->minimum_donation_amount).'" data-errormessage-range-overflow="'.JText::sprintf('JD_MAX_DONATION_AMOUNT_ALLOWED', $this->config->maximum_donation_amount).'" />';
					echo  $bootstrapHelper->getPrependAddon($input,$addons);
				}
				else
				{
					$addons = $this->config->currency_symbol;
					$input  = '<input type="text" placeholder="'.$placeHolder.'" class="'.$amountCssClass.'" name="amount" id="amount" value="'.$this->amount.'" onchange="deSelectRadio();" data-errormessage="'.JText::_('JD_AMOUNT_IS_REQUIRED').'" data-errormessage-range-underflow="'.JText::sprintf('JD_MIN_DONATION_AMOUNT_ALLOWED', $this->config->minimum_donation_amount).'" data-errormessage-range-overflow="'.JText::sprintf('JD_MAX_DONATION_AMOUNT_ALLOWED', $this->config->maximum_donation_amount).'" />';
					echo $bootstrapHelper->getAppendAddon($input,$addons);
				}
			}
		?>
		</div>
		<?php
		}
	}
?>
