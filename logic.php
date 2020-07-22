<?php defined( '_JEXEC' ) or die;

// variables
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$menu = $app->getMenu();
$active = $app->getMenu()->getActive();
$params = $app->getParams();
$pageclass = $params->get('pageclass_sfx');
$tpath = $this->baseurl.'/templates/'.$this->template;

// // category id
$input=$app->input;
if($input->getCmd('option')=='com_content' && $input->getCmd('view')=='article' ) {
  $db=JFactory::getDbo();
  $db->setQuery('select catid from #__content where id='.$input->getInt('id'));
  $catid=$db->loadResult();
}

// generator tag
$this->setGenerator(null);

// responsive meta tag (recommended in Bootstrap 4 doc)
$doc->setMetadata('viewport', 'width=device-width, initial-scale=1, shrink-to-fit=no');

// google fonts
$doc->addStyleSheet('https://fonts.googleapis.com/css?family=Barlow+Semi+Condensed:400,500,600,700|Barlow+Condensed');


JHtml::_('jquery.framework');
$doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', '', array('integrity' => 'sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1', 'crossorigin' => 'anonymous', 'defer' => 'defer'));
$doc->addScript('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', '', array('integrity' => 'sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM', 'crossorigin' => 'anonymous', 'defer' => 'defer'));

// unset
// unset($doc->_scripts[$this->baseurl .'/media/jui/js/jquery.min.js']);
unset($doc->_scripts[$this->baseurl .'/media/jui/js/jquery-noconflict.js']);
unset($doc->_scripts[$this->baseurl .'/media/jui/js/jquery-migrate.min.js']);
unset($doc->_scripts[$this->baseurl .'/media/jui/js/bootstrap.min.js']);
unset($doc->_scripts[$this->baseurl .'/media/system/js/caption.js']);
// unset($doc->_scripts[$this->baseurl .'/media/system/js/core.js']);
unset($doc->_scripts[$this->baseurl .'/media/system/js/tabs-state.js']);
unset($doc->_scripts[$this->baseurl .'/media/system/js/validate.js']);

if (isset($doc->_script['text/javascript']))
{
    $doc->_script['text/javascript'] = preg_replace('%jQuery\(window\)\.on\(\'load\'\,\s*function\(\)\s*\{\s*new\s*JCaption\(\'img.caption\'\);\s*}\s*\);\s*%', '', $doc->_script['text/javascript']);
    $doc->_script['text/javascript'] = preg_replace("%\s*jQuery\(document\)\.ready\(function\(\)\{\s*jQuery\('\.hasTooltip'\)\.tooltip\(\{\"html\":\s*true,\"container\":\s*\"body\"\}\);\s*\}\);\s*%", '', $doc->_script['text/javascript']);
    if (empty($doc->_script['text/javascript']))
    {
        unset($doc->_script['text/javascript']);
    }
}

// css
$doc->addStyleSheet($tpath.'/build/main.css');
