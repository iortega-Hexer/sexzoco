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

include_once(dirname(__FILE__).'/../../classes/models/PFGSubmissionModel.php');
include_once(dirname(__FILE__).'/../../classes/models/PFGModel.php');
include_once(dirname(__FILE__).'/../../classes/models/PFGFieldModel.php');

class AdminPFGSubmissionsController extends AdminController
{
    private $pfg_model;
    private $fields;
    private $column_identifier = 'pdf_col_';

    /**
     * Initialize the HelperList for the submissions of the given ID form.
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'pfg_submissions';
        $this->className = 'PFGSubmissionModel';

        $this->identifier = 'id_submission';

        $this->allow_export = true;

        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->bulk_actions = array('delete' => array('text' => $this->_trans('Delete selected'), 'confirm' => $this->_trans('Delete selected items?')));

        if (!Tools::isSubmit('id_pfg')) {
            $link = new Link();
            Tools::redirectAdmin($link->getAdminLink('AdminPFG'));
        }

        $this->pfg_model = new PFGModel((int)Tools::getValue('id_pfg'));
        $this->fields = PFGFieldModel::findFields($this->pfg_model->id);

        $this->setSelectColumns();
        $this->_where = 'AND id_pfg = '.$this->pfg_model->id;
        $this->setFieldsList();

        parent::__construct();
        $this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminPFG'));
    }

    /**
     * Used for backward compatibility between 1.7 Prestashop version and older
     */
    private function _trans($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($class === null || $class == 'AdminTab') {
            $class = Tools::substr(get_class($this), 0, -10);
        } elseif (Tools::strtolower(Tools::substr($class, -10)) == 'controller') {
            /* classname has changed, from AdminXXX to AdminXXXController, so we remove 10 characters and we keep same keys */
            $class = Tools::substr($class, 0, -10);
        }

        if (version_compare(Tools::substr(_PS_VERSION_, 0, 7), '1.7.0.3', '>=')) {
            return Translate::getAdminTranslation($string, $class, $addslashes, $htmlentities);
        } else {
            return $this->l($string, $class, $addslashes, $htmlentities);
        }
    }

    /**
     * Initialize breadcrumbs for better UX
     * Used by the parent class
     *
     * @param int $tab_id
     * @param array $tabs
     *
     * @see AdminController::initBreadcrumbs
     */
    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        if (is_null($tab_id)) {
            parent::initBreadcrumbs();
        } else {
            parent::initBreadcrumbs($tab_id, $tabs);
        }

        $this->breadcrumbs[] = 'Powerful Form Generator';
        $this->breadcrumbs[] = $this->pfg_model->title[$this->context->language->id];
        $this->breadcrumbs[] = $this->_trans('Submissions');
    }

    /**
     * Initialize the process
     * Used by the parent class
     *
     * @see AdminController::initProcess
     */
    public function initProcess()
    {
        parent::initProcess();
        self::$currentIndex .= '&id_pfg='.(int)Tools::getValue('id_pfg');
    }

    /**
     * Set the selected columns
     */
    public function setSelectColumns()
    {
        if (count($this->fields) === 0) {
            return;
        }

        foreach ($this->fields as $field) {
            $this->_select .= '1 AS `'.$this->column_identifier.$field['name'].'`,';
        }

        $this->_select = Tools::substr($this->_select, 0, -1);
    }

    /**
     * Generate the list of fields do be displayed as columns
     * in the HelperList
     */
    public function setFieldsList()
    {
        $this->fields_list['id_submission'] = array (
            'title' => $this->_trans('ID'),
            'align' => 'center',
            'width' => 30
        );

        foreach ($this->fields as $i => $field) {
            if ($i > 5) { // Max number of columns to display
                break;
            }

            if ($field['required'] === '0') {
                continue;
            }

            $this->fields_list[$this->column_identifier.$field['name']] = array (
                'title' => $field['label'],
                'align' => 'center',
                'width' => 'auto',
                'type' => 'text',
                'orderby' => true,
                'search' => false,
                'callback' => 'get_'.$field['name']
            );
        }

        $this->fields_list['date_add'] = array (
            'title' => $this->_trans('Added'),
            'align' => 'center',
            'width' => 'auto',
            'type' => 'date',
            'orderby' => true,
            'search' => true
        );
    }

    /**
     * Magic method used to display the right value for the right column
     * Since it depends on the field structure of the form
     * the magic method gets all it's sense here :)
     *
     * @param string $name The name of the column
     * @param array $values Table of values that can be displayed
     *
     * @return string or null
     */
    public function __call($name, $values)
    {
        $entry = Tools::jsonDecode($values[1]['entry'], true);
        if (isset($entry[Tools::substr($name, 4)])) {
            if (is_array($entry[Tools::substr($name, 4)])) {
                return implode($entry[Tools::substr($name, 4)], ', ');
            } else {
                return $entry[Tools::substr($name, 4)];
            }
        }

        return null;
    }

    /**
     * Initialize the toolbar
     * Used by the parent class
     *
     * @see AdminController::initToolbar
     */
    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);

        if ($this->display === 'view') {
            $back_url = $this->context->link->getAdminLink('AdminPFGSubmissions').'&id_pfg='.Tools::getValue('id_pfg');
            $this->toolbar_btn['back'] = array(
                'short' => 'Back',
                'href' => $back_url,
                'desc' => $this->_trans('Back'),
            );
        } else {
            $back_url = $this->context->link->getAdminLink('AdminPFG');
            $this->toolbar_btn['back'] = array(
                'short' => 'Back',
                'href' => $back_url,
                'desc' => $this->_trans('Back to the forms'),
            );
        }

        if (Tools::substr(_PS_VERSION_, 0, 3) === '1.5') {
            $back_url = $this->context->link->getAdminLink('AdminPFGSubmissions').'&id_pfg='.Tools::getValue('id_pfg').'&export_csv=1';
            $this->toolbar_btn['export-csv-orders'] = array(
                'short' => 'Export',
                'href' => $back_url,
                'desc' => $this->_trans('Export (CSV)'),
            );

            if (Tools::isSubmit('export_csv')) {
                $this->processExport();
            }
        }
    }

    /**
     * Renders all the submitted entries using the HelperList
     *
     * @see AdminController::renderView
     */
    public function renderView()
    {
        $view = parent::renderView();

        $result = new PFGSubmissionModel(Tools::getValue('id_submission'));
        $entry = Tools::jsonDecode($result->entry, true);

        $view .= '<div style="background-color: #fff; padding: 20px; border-radius: 3px">';
        $view .= '<h1>'.sprintf($this->_trans('Submission of %s'), date($this->context->language->date_format_full, strtotime($result->date_add))).'</h1><hr />';
        $view .= '<ul style="margin-top: 40px; list-style-type: none">';
        foreach ($this->fields as $field) {
            if (!isset($entry[$field['name']])) {
                continue;
            }
            if ($field['type'] === 'separator' || $field['type'] === 'captcha') {
                continue;
            }

            $view .= '<li style="margin: 10px 0"><strong style="float: left">'.$field['label'].' :</strong><div style="margin-left: 150px;">';
            if (empty($entry[$field['name']])) {
                $view .= '&nbsp;';
            }

            if ($field['type'] === 'email') {
                $view .= '<a href="mailto:'.$entry[$field['name']].'" title="'.$this->_trans('Send an email to this person').'">'.$entry[$field['name']].'</a>';
            } elseif ($field['type'] === 'url' || $field['type'] === 'file') {
                if (is_array($entry[$field['name']])) {
                    $view .= '<ul style="list-style-type: none">';
                    foreach ($entry[$field['name']] as $element) {
                        $view .= '<li><a href="'.$element.'" title="'.$this->_trans('Click to open this link in a new window.').'" target="_blank">'.$element.'</a></li>';
                    }

                    $view .= '</ul>';
                } else {
                    $view .= '<a href="'.$entry[$field['name']].'" title="'.
                            $this->_trans('Click to open this link in a new window.').'" target="_blank">'.$entry[$field['name']].'</a>';
                }
            } else {
                if (is_array($entry[$field['name']])) {
                    $view .= '<ul style="list-style-type: none">';
                    $multi_values = array();
                    if ($field['type'] === 'multicheckbox') {
                        $multi_values = explode(',', $field['values']);
                    }

                    foreach ($entry[$field['name']] as $element) {
                        if ($element === 'true') {
                            $element = $this->_trans('Yes');
                        } elseif ($element === 'false') {
                            $element = $this->_trans('No');
                        }

                        if ($field['type'] === 'multicheckbox') {
                            $element = $multi_values[(int)$element];
                        }

                        $view .= '<li>'.$element.'</li>';
                    }
                    $view .= '</ul>';
                } else {
                    if ($entry[$field['name']] === 'true') {
                        $entry[$field['name']] = $this->_trans('Yes');
                    } elseif ($entry[$field['name']] === 'false') {
                        $entry[$field['name']] = $this->_trans('No');
                    }

                    $view .= $entry[$field['name']];
                }
            }

            $view .= '</div></li>';
        }

        if (isset($entry['_customer'])) {
            $view .= '<li style="margin: 10px 0"><strong style="float: left">'.$this->_trans('Customer').' :</strong><div style="margin-left: 150px;">';
            $view .= '<a href="'.Context::getContext()->link->getAdminLink('AdminCustomers').'&id_customer='.$entry['_customer']['id'].'&viewcustomer">'.$entry['_customer']['display'].'</a>';
            $view .= '</div></li>';
        }

        if (isset($entry['_product'])) {
            $view .= '<li style="margin: 10px 0"><strong style="float: left">'.$this->_trans('Product').' :</strong><div style="margin-left: 150px;">';
            $view .= '<a href="'.$entry['_product']['url'].'">'.$entry['_product']['display'].'</a>';
            $view .= '</div></li>';
        }

        $view .= '</ul><div style="clear: both"></div>';

        $view .= '<p style="margin-top: 20px; padding-left: 185px;"><a href="'.Context::getContext()->link->getAdminLink('AdminPFGSubmissions').'&id_pfg='.(int)Tools::getValue('id_pfg').'" title="'.$this->_trans('Go back to the list of submissions').'" class="btn btn-default">'.$this->_trans('Go back to the list of submissions').'</a></p>';

        $view .= '</div>';

        return $view;
    }

    /**
     * Renders the list of existing fields
     *
     * @see AdminController::renderList
     */
    public function renderList()
    {
        $this->initToolbar();

        return parent::renderList();
    }

    /**
     * Export all the submitted entries in a CSV file
     *
     * @param string $text_delimiter The text delimiter (default is ")
     */
    public function processExport($text_delimiter = '"')
    {
        // clean buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }

        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="submissions_'.date('Y-m-d_His').'.csv"');

        $headers = array ('id');
        foreach ($this->fields as $field) {
            $headers[] = Tools::htmlentitiesDecodeUTF8($field['name']);
        }

        $headers[] = 'created';

        $content = array();
        foreach ($this->_list as $i => $row) {
            $content[$i] = array ();
            $content[$i][] = $row[$this->identifier];

            $entry = Tools::jsonDecode($row['entry'], true);

            foreach ($this->fields as $field) {
                $multi_values = array();
                if ($field['type'] === 'multicheckbox') {
                    $multi_values = explode(',', $field['values']);
                }

                if (is_array($entry[$field['name']])) {
                    if ($field['type'] === 'multicheckbox') {
                        $values = $entry[$field['name']];
                        $results = array();
                        foreach ($values as $element) {
                            $results[] = $multi_values[(int)$element];
                        }

                        $entry[$field['name']] = $results;
                    }

                    $entry[$field['name']] = implode(', ', $entry[$field['name']]);
                }

                $content[$i][] = Tools::htmlentitiesDecodeUTF8($entry[$field['name']]);
            }

            $content[$i][] = $row['date_add'];
        }

        if (Tools::substr(_PS_VERSION_, 0, 3) === '1.5') {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers, ';', $text_delimiter);
            foreach ($content as $c) {
                fputcsv($out, $c, ';', $text_delimiter);
            }

            exit();
        }

        $this->context->smarty->assign(
            array(
                'export_precontent' => "\xEF\xBB\xBF",
                'export_headers' => $headers,
                'export_content' => $content,
                'text_delimiter' => $text_delimiter
            )
        );

        $this->layout = 'layout-export.tpl';
    }
}
