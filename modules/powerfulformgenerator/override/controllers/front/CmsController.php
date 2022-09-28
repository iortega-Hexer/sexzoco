<?php
/**
 * Powerful Form Generator
 *
 * This modules aims to provide for your customer any kind of form you want.
 *
 * If you find errors, bugs or if you want to share some improvments,
 * feel free to contact at contact@prestaddons.net ! :)
 * Si vous trouvez des erreurs, des bugs ou si vous souhaitez
 * tout simplement partager un conseil ou une amélioration,
 * n'hésitez pas à me contacter à contact@prestaddons.net
 *
 * @author    Cyril Nicodème <contact@prestaddons.net>
 * @copyright Copyright (C) April 2014 prestaddons.net <@email:contact@prestaddons.net>. All rights reserved.
 * @license   Nicodème Cyril
 * @package   modules
 * @since     2014-04-15
 * @version   2.7.9
 */

require_once(_PS_MODULE_DIR_.'powerfulformgenerator/classes/PFGRenderer.php');
class CmsController extends CmsControllerCore
{

    public function initContent()
    {
        parent::initContent();

        $parent_cat = new CMSCategory(1, $this->context->language->id);
        $this->context->smarty->assign('id_current_lang', $this->context->language->id);
        $this->context->smarty->assign('home_title', $parent_cat->name);
        $this->context->smarty->assign('cgv_id', Configuration::get('PS_CONDITIONS_CMS_ID'));

        if ($this->assignCase == 1) {
            if ($this->cms->indexation == 0) {
                $this->context->smarty->assign('nobots', true);
            }

            if (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '>=')) {
                $currentCms = $this->objectPresenter->present($this->cms);
                $currentCms['content'] = $this->returnContent($currentCms['content']);

                $this->context->smarty->assign(array(
                    'cms' => $currentCms,
                ));

                $this->setTemplate(
                    'cms/page',
                    array('entity' => 'cms', 'id' => $this->cms->id)
                );
            } else {
                if (isset($this->cms->id_cms_category) && $this->cms->id_cms_category) {
                    $path = Tools::getFullPath($this->cms->id_cms_category, $this->cms->meta_title, 'CMS');
                } elseif (isset($this->cms_category->meta_title)) {
                    $path = Tools::getFullPath(1, $this->cms_category->meta_title, 'CMS');
                }
                $this->cms->content = $this->returnContent($this->cms->content);

                $this->context->smarty->assign(array(
                    'cms' => $this->cms,
                    'content_only' => (int)Tools::getValue('content_only'),
                    'path' => $path,
                    'body_classes' => array($this->php_self.'-'.$this->cms->id, $this->php_self.'-'.$this->cms->link_rewrite)
                ));

                $this->setTemplate(_PS_THEME_DIR_.'cms.tpl');
            }
        } elseif ($this->assignCase == 2) {
            if (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '>=')) {
                $this->context->smarty->assign($this->getTemplateVarCategoryCms());
                $this->setTemplate('cms/category');
            } else {
                $this->context->smarty->assign(array(
                    'category' => $this->cms_category, //for backward compatibility
                    'cms_category' => $this->cms_category,
                    'sub_category' => $this->cms_category->getSubCategories($this->context->language->id),
                    'cms_pages' => CMS::getCMSPages($this->context->language->id, (int)$this->cms_category->id, true, (int)$this->context->shop->id),
                    'path' => ($this->cms_category->id !== 1) ? Tools::getPath($this->cms_category->id, $this->cms_category->name, false, 'CMS') : '',
                    'body_classes' => array($this->php_self.'-'.$this->cms_category->id, $this->php_self.'-'.$this->cms_category->link_rewrite)
                ));
                $this->setTemplate(_PS_THEME_DIR_.'cms.tpl');
            }
        }
    }

    private function generatePFG($id_pfg)
    {
        $renderer = new PFGRenderer($id_pfg);

        if (!$renderer->isAllowed(true)) {
            $redirect_url = $renderer->getForm()->unauth_redirect_url[Context::getContext()->language->id];
            if (!empty($redirect_url)) {
                Tools::redirect($redirect_url);
            } else {
                Controller::getController('PageNotFoundController')->run();
            }
            exit();
        }

        return $renderer->displayForm();
    }

    public function returnContent($contents)
    {
        preg_match_all('/\{powerfulform\:[(0-9\,)]+\}/i', $contents, $matches);
        foreach ($matches[0] as $match) {
            $explode = explode(":", $match);
            $contents = str_replace($match, $this->generatePFG(str_replace("}", "", $explode[1])), $contents);
        }

        return $contents;
    }
}
