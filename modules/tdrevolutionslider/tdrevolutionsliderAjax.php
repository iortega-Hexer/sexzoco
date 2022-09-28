<?php
include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('tdrevolutionslider.php');
$tdconslider = new tdrevolutionslider();
$consliderdata = Tools::getValue('tdrevolutionsliderdata');

if (!Tools::isSubmit('secure_key') OR Tools::getValue('secure_key') != $tdconslider->secure_key OR !Tools::isSubmit('action'))
	die(1);
if (Tools::getValue('action') == 'dnd')
{
	if (isset($consliderdata))
	{
		$positon = 0;
		foreach ($consliderdata as $key =>$id_slide)
		{
			$sliderid = explode('_', $id_slide);
        
			Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'tdrevolutionslider` 
			SET `position` = '.(int)$positon.' 
			WHERE `id_tdrevolutionslider` = '.(int)$sliderid[2]);
			$positon++;
		}
	}
}