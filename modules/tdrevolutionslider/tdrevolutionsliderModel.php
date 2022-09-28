<?php

class tdrevolutionsliderModel extends ObjectModel {

    public static function createTables() {
        return (
                tdrevolutionsliderModel::createtdhomeadvertisingTable() &&
                tdrevolutionsliderModel::createtdhomeadvertisingLangTable()
                && tdrevolutionsliderModel::createtdDefaultData()
                );
    }

    public static function dropTables() {
        return Db::getInstance()->execute('DROP TABLE
			`' . _DB_PREFIX_ . 'tdrevolutionslider`,
			`' . _DB_PREFIX_ . 'tdrevolutionslider_lang`');
    }

    public static function createtdDefaultData() {
    $context = Context::getContext();
    $id_shop = $context->shop->id;
    $tdmodurl=_PS_BASE_URL_.__PS_BASE_URI__.'modules/tdrevolutionslider/banner/';
        $sql= Db::getInstance()->Execute('
		INSERT INTO `' . _DB_PREFIX_ . 'tdrevolutionslider`(`slider_link`,`active`, `position`,`id_shop`) VALUES("#",1,0,'.$id_shop.')');

        $sql .= Db::getInstance()->Execute('
		INSERT INTO `' . _DB_PREFIX_ . 'tdrevolutionslider`(`slider_link`,`active`, `position`,`id_shop`) VALUES("#",1,1,'.$id_shop.')');

        $sql .= Db::getInstance()->Execute('
		INSERT INTO `' . _DB_PREFIX_ . 'tdrevolutionslider`(`slider_link`,`active`, `position`,`id_shop`) VALUES("#",1,2,'.$id_shop.')');

        
        
        $languages = Language::getLanguages(false);
        for ($i = 1; $i <= 3; $i++) {
            if ($i == 1):
                $title = 'Fluid Grid Layout';
                $content='<div class="caption sft custom1"  data-x="90" data-y="150" data-speed="300" data-start="800" data-easing="easeOutExpo" data-endeasing="easeOutExpo">RESPONSIVE</div>
<div class="caption sfr small_text"  data-x="90" data-y="200" data-speed="300" data-start="1100" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><span>Customize theme as per your store requirement.</span></div>
<div class="caption sfr small_text"  data-x="90" data-y="220" data-speed="300" data-start="1100" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><span style> Fully customizable from admin panel</span></div>
<div class="caption lfl"  data-x="858" data-y="124" data-speed="1000" data-start="2000" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'imac.png" alt="Image 4" style="width: 280px; height: 215px;"></div>
<div class="caption lft"  data-x="658" data-y="65" data-speed="1000" data-start="2500" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'mac.png" alt="Image 5" style="width: 350px; height: 314px;"></div>
<div class="caption lfr"  data-x="543" data-y="141" data-speed="1000" data-start="3000" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'ipad.png" alt="Image 6" style="width: 175px; height: 224px;"></div>
<div class="caption lfb"  data-x="651" data-y="190" data-speed="1000" data-start="3500" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'iphone.png" alt="Image 6" style=" height: 159px;"></div>							
		';
            elseif ($i == 2):
                $title =  'Fluid Grid Layout';
                $content='<div class="caption sfr small_text"  data-x="90" data-y="200" data-speed="300" data-start="1100" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><span style="color:#696969;">Customize theme as per your store requirement.</span></div>
<div class="caption sfr small_text"  data-x="90" data-y="220" data-speed="300" data-start="1100" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><span style="color:#696969;"> Fully customizable from admin panel</span></div>
<div class="caption lfr"  data-x="478" data-y="58" data-speed="1000" data-start="2000" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'img3.png" alt="Image 4" style="width: 386px; height: 274px;"></div>
<div class="caption lfr"  data-x="836" data-y="45" data-speed="1000" data-start="2500" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'img2.png" alt="Image 6" style="width: 386px; height: 308px;"></div>
<div class="caption lfr"  data-x="573" data-y="15" data-speed="1000" data-start="3000" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'img1.png" alt="Image 5" style="width: 412px; height: 350px;"></div>
			';
                        elseif ($i ==3):
                $title =  'Fluid Grid Layout';
                $content='<div class="caption sft custom1"  data-x="690" data-y="150" data-speed="300" data-start="800" data-easing="easeOutExpo" data-endeasing="easeOutExpo" style="color:#FF4629;">CREATIVE DESIGN</div>
<div class="caption sfb small_text"  data-x="690" data-y="210" data-speed="300" data-start="1100" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><span style="font-size:22px;font-family:oswaldbold;">CUSTOMIZE THEME AS PER YOUR STORE REQUIREMENT</span></div>
<div class="caption sfr small_text"  data-x="690" data-y="245" data-speed="300" data-start="1100" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><span style="font-size:22px;font-family:oswaldbold;"> FULLY CUSTOMIZABLE FROM ADMIN PANEL</span></div>
<div class="caption lft"  data-x="0" data-y="90" data-speed="1000" data-start="1000" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'img5.png" alt="Image 4" style="width: 591px; height: 335px;"></div>
<div class="caption lfb"  data-x="45" data-y="0" data-speed="1000" data-start="2500" data-easing="easeOutExpo" data-endeasing="easeOutExpo"><img src="'.$tdmodurl.'img4.png" alt="Image 5" style="width: 591px; height: 335px;"></div>							
				';
            endif;
            
            $tdmodurlbg='modules/tdrevolutionslider/banner/';
            
            foreach ($languages as $language) {
                $sql .=Db::getInstance()->Execute('
                        INSERT INTO `' . _DB_PREFIX_ . 'tdrevolutionslider_lang`(`id_tdrevolutionslider`, `id_lang`, `image_title`, `slider_content`,`image_url`) 
                        VALUES(' . $i . ', ' . (int) $language['id_lang'] . ', 
                        "' . htmlspecialchars($title) . '"," '.htmlspecialchars($content).' ","'.$tdmodurlbg.'bg' . $i . '.jpg")');
            }
        }
        return $sql;
    }

    public static function createtdhomeadvertisingTable() {
        return (Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tdrevolutionslider` (
	        `id_tdrevolutionslider` int(10) unsigned NOT NULL auto_increment,
                `slider_link` varchar(255) NOT NULL,
                `active` int(11) unsigned NOT NULL,
                `position` int(11) unsigned NOT NULL default \'0\',
                `id_shop` int(10) unsigned NOT NULL,
		PRIMARY KEY (`id_tdrevolutionslider`))
		ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'));
    }

    public static function createtdhomeadvertisingLangTable() {
        return (Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tdrevolutionslider_lang` (
		`id_tdrevolutionslider` int(10) unsigned NOT NULL,
		`id_lang` int(10) unsigned NOT NULL,
                `image_title` varchar(255) NOT NULL,
                `slider_content` text NOT NULL,
                `image_url` varchar(255) NOT NULL,
		PRIMARY KEY (`id_tdrevolutionslider`, `id_lang`))
		ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8'));
    }

    public static function getAllSlider() {
        global $cookie;
           $context = Context::getContext();
		$id_shop = $context->shop->id;
		$id_lang = $context->language->id;
        return Db::getInstance()->ExecuteS('
            SELECT td.`id_tdrevolutionslider`,td.`slider_link`, td.`active`, td.`position`, td1.`image_url`, td1.id_lang, td1.image_title, td1.slider_content
            FROM `' . _DB_PREFIX_ . 'tdrevolutionslider` td
            INNER JOIN `' . _DB_PREFIX_ . 'tdrevolutionslider_lang` td1 ON (td.`id_tdrevolutionslider` = td1.`id_tdrevolutionslider`)
            WHERE td.`id_shop`= '.(int)$id_shop.' AND td1.`id_lang` = ' . (int) $cookie->id_lang . '
            ORDER BY td.`position`');
    }

    public static function getSliderByID($id_tdrevolutionslider) {
$context = Context::getContext();
		$id_shop = $context->shop->id;
		$id_lang = $context->language->id;
        $getslider = Db::getInstance()->ExecuteS('
            SELECT td.`id_tdrevolutionslider`, td.`slider_link`, td.`active`, td.`position`, td1.`image_url`, td1.id_lang, td1.image_title, td1.`slider_content`
            FROM `' . _DB_PREFIX_ . 'tdrevolutionslider` td
            INNER JOIN `' . _DB_PREFIX_ . 'tdrevolutionslider_lang` td1 ON (td.`id_tdrevolutionslider` = td1.`id_tdrevolutionslider`)
            WHERE td.`id_tdrevolutionslider` = ' . (int) $id_tdrevolutionslider);


        $store_display_update = array(0, $size = count($getslider));
        foreach ($getslider AS $sliderbyid) {
            $getslider['image_title'][(int) $sliderbyid['id_lang']] = $sliderbyid['image_title'];
            if ($store_display_update['0'] < $store_display_update['1'])
                ++$store_display_update['0'];
        }
        foreach ($getslider AS $imagecaption) {
            $getslider['slider_content'][(int) $imagecaption['id_lang']] = $imagecaption['slider_content'];
            if ($store_display_update['0'] < $store_display_update['1'])
                ++$store_display_update['0'];
        }
        foreach ($getslider AS $sliderimage) {
            $getslider['image_url'][(int) $sliderimage['id_lang']] = $sliderimage['image_url'];
            if ($store_display_update['0'] < $store_display_update['1'])
                ++$store_display_update['0'];
        }
        return $getslider;
    }

}