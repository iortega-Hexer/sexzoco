<?php
/**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2022 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

class AdminAdvancedEmailGuardController extends ModuleAdminController
{
    /**
     * Module instance.
     *
     * @var \Advancedemailguard
     */
    public $module;

    /**
     * HTTP request method.
     *
     * @var string
     */
    protected $method;

    /**
     * HTTP request action name.
     *
     * @var string
     */
    protected $action;

    /**
     * The current page tab.
     *
     * @var string
     */
    protected $tab;

    /**
     * Alert feedback messages.
     *
     * @var array
     */
    protected $alerts = array(
        'success' => array(),
        'warning' => array(),
        'error'   => array(),
    );

    /**
     * Request data bag.
     *
     * @var array
     */
    protected $request = array();

    /**
     * Config keys grouped by form type.
     *
     * @var array
     */
    protected $configs = array(
        'settings.recaptcha' => array(
            'ADVEG_REC_TYPE',
            'ADVEG_REC_V2_CBX_KEY', 'ADVEG_REC_V2_CBX_SECRET',
            'ADVEG_REC_V2_INV_KEY', 'ADVEG_REC_V2_INV_SECRET',
            'ADVEG_REC_V3_KEY', 'ADVEG_REC_V3_SECRET',
            'ADVEG_REC_LEGAL_LINKS', 'ADVEG_REC_LOAD_ON_DEMAND',

            'ADVEG_REC_LANGUAGE', 'ADVEG_REC_THEME',
            'ADVEG_REC_POSITION', 'ADVEG_REC_SCORE_THRESHOLD',
        ),
        'settings.email' => array(
            'ADVEG_CHECK_EMAIL_DISPOSABLE',
            'ADVEG_FORBIDDEN_EMAILS', 'ADVEG_FORBIDDEN_EMAIL_DOMAINS',
            'ADVEG_FORBIDDEN_EMAIL_PATTERNS',
        ),
        'settings.message' => array(
            'ADVEG_FORBIDDEN_TEXTS',
        ),
        'forms.previewMode' => array(
            'ADVEG_PREVIEW_MODE', 'ADVEG_PREVIEW_MODE_IPS',
        ),
        'logs.settings' => array(
            'ADVEG_LOGS_MODE',
        ),
    );

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->method = Tools::strtoupper($_SERVER['REQUEST_METHOD']) === 'GET' ? 'GET' : 'POST';
        $this->action = Tools::getValue('_action') ?: '';
        $this->tab = Tools::getValue('tab') ?: '';
    }

    /**
     * {@inheritDoc}
     */
    public function viewAccess($disable = false)
    {
        unset($disable);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if (!$this->checkConfigPermission()) {
            $this->displayUnauthorizedAdminPage();
        } else {
            $this->displayCustomAdminPage();
        }
        parent::display();
    }

    /**
     * Check employee configure permission.
     *
     * @return bool
     */
    protected function checkConfigPermission()
    {
        if (!isset($this->context->employee)) {
            return false;
        }

        $canEdit = false;
        $employee = $this->context->employee;
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $canEdit = $employee->can('edit', 'AdminModulessf');
        } else {
            $actions = Profile::getProfileAccess($employee->id_profile, Tab::getIdFromClassName('AdminModules'));
            $canEdit = (int) $actions['edit'] === 1;
        }

        return $canEdit && $this->module->getPermission('configure', $employee);
    }

    /**
     * Display unauthorized admin page.
     *
     * @return void
     */
    protected function displayUnauthorizedAdminPage()
    {
        $this->addError($this->module->l('You do not have permission to configure this module.'));

        $this->layout = $this->module->getLocalPath() . 'views/templates/admin/page.tpl';
        $this->context->smarty->assign($this->getCommonVars());
        $this->context->smarty->assign(array(
            'canConfig' => false,
            'alerts' => $this->alerts,
        ));
    }

    /**
     * Display custom admin page.
     *
     * @return void
     */
    protected function displayCustomAdminPage()
    {
        $this->layout = $this->module->getLocalPath() . 'views/templates/admin/page.tpl';
        $this->context->smarty->assign($this->getCommonVars());

        $this->checkPrerequisites();
        $this->processRequest();

        $this->context->smarty->assign(array(
            'canConfig' => true,
            'alerts' => $this->alerts,
            'config' => $this->module->getConfigValues(),
            'forms' => $this->module->getFormsMeta(),
            'logsType' => $this->getLogsType(),
            'logs' => $this->getLogsMeta(),
        ));
    }

    /**
     * Get common template vars.
     *
     * @return array
     */
    protected function getCommonVars()
    {
        $shared = array(
            'url' => $this->module->getMainAdminLink(),
            'isDemo' => $this->module->isDemo(),
            'trans' => $this->module->getAllTrans(),
            'basePath' => $this->module->getBaseLink(),
            'deleteLogsLink' => $this->module->getDeleteLogsLink(),
            'ip' => Tools::getRemoteAddr(),
        );

        $vars = array_merge($shared, array(
            'appName' => $this->module->displayName,
            'appVersion' => $this->module->version,
            'tplPath' => $this->module->getFullPathUri() . 'views/templates',
            'cssPath' => $this->module->getBaseLink() . 'views/css/',
            'jsPath' => $this->module->getBaseLink() . 'views/js/',
            'lang' => $this->context->language->getFields(),
            'languages' => Language::getLanguages(),
            'docsLink' => $this->module->getDocsLink(),
            'psAddonsLinks' => $this->module->getPsAddonsLinks(),
            'ratingMessage' => $this->module->getRatingMessage(),
            'tab' => $this->tab,
            'jsVars' => json_encode($shared),
        ));

        return $vars;
    }

    /**
     * Add success alert message.
     *
     * @param string $message
     * @return void
     */
    protected function addSuccess($message)
    {
        $this->alerts['success'][] = $message;
    }

    /**
     * Add warning alert message.
     *
     * @param string $message
     * @return void
     */
    protected function addWarning($message)
    {
        $this->alerts['warning'][] = $message;
    }

    /**
     * Add error alert message.
     *
     * @param string $message
     * @return void
     */
    protected function addError($message)
    {
        $this->alerts['error'][] = $message;
    }

    /**
     * Check the module's prerequisites and display warnings to the admin if needed.
     *
     * @return void
     */
    protected function checkPrerequisites()
    {
        if (!filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
            $this->addWarning(
                $this->module->l('The PHP option allow_url_fopen must be enabled for reCAPTCHA validations to work.') .
                    ' ' . $this->module->l('Please edit your server\'s PHP configuration or contact your hosting provider.')
            );
        }
    }

    /**
     * Process the request.
     *
     * @return void
     */
    protected function processRequest()
    {
        if ($this->action === '') {
            return;
        }

        if ($this->method === 'GET') {
            $this->processGetRequest();
        } else {
            $this->processPostRequest();
        }
    }

    /**
     * Process the GET request.
     *
     * @return void
     */
    protected function processGetRequest()
    {
        switch ($this->action) {
            case 'rating.dismiss':
                $this->processRatingDismissRequest();
                break;
        }
    }

    /**
     * Process the POST request.
     *
     * @return void
     */
    protected function processPostRequest()
    {
        switch ($this->action) {
            case 'settings.recaptcha':
                $this->processSettingsRecaptchaRequest();
                break;
            case 'settings.email':
                $this->processSettingsEmailRequest();
                break;
            case 'settings.message':
                $this->processSettingsMessageRequest();
                break;
            case 'forms.settings':
                $this->processFormsSettingsRequest();
                break;
            case 'forms.previewMode':
                $this->processFormsPreviewModeRequest();
                break;
            case 'logs.settings':
                $this->processLogsSettingsRequest();
                break;
            case 'logs.page.step':
                $this->processLogsPageStepRequest();
                break;
            case 'logs.delete':
                $this->processLogsDeleteRequest();
                break;
        }
    }

    /**
     * Process rating message dismiss ajax request.
     *
     * @return void
     */
    protected function processRatingDismissRequest()
    {
        $this->module->dismissRatingMessage();
        die;
    }

    /**
     * Process settings reCAPTCHA form request.
     *
     * @return void
     */
    protected function processSettingsRecaptchaRequest()
    {
        $this->setSettingsRecaptchaRequestData();

        if (!$this->module->isDemo()) {
            $type = Tools::strtoupper($this->request['ADVEG_REC_TYPE']);
            $key = $this->request["ADVEG_REC_{$type}_KEY"];
            $secret = $this->request["ADVEG_REC_{$type}_SECRET"];

            if (empty($key) || empty($secret)) {
                $this->addError($this->module->l('The reCAPTCHA API keys are required.'));
                return;
            }
            if ($key === $secret) {
                $this->addError($this->module->l('The reCAPTCHA API keys cannot be the same.'));
                return;
            }
        }

        foreach ($this->request as $key => $value) {
            Configuration::updateValue($key, $value);
        }
        $this->addSuccess($this->module->getTrans('settingsSaved'));
    }

    /**
     * Set settings reCAPTCHA request data.
     *
     * @return void
     */
    protected function setSettingsRecaptchaRequestData()
    {
        $keys = array(
            'ADVEG_REC_V2_CBX_KEY', 'ADVEG_REC_V2_CBX_SECRET',
            'ADVEG_REC_V2_INV_KEY', 'ADVEG_REC_V2_INV_SECRET',
            'ADVEG_REC_V3_KEY', 'ADVEG_REC_V3_SECRET',
            'ADVEG_REC_SCORE_THRESHOLD', 'ADVEG_REC_LEGAL_LINKS',
            'ADVEG_REC_LOAD_ON_DEMAND',
        );

        if ($this->module->isDemo()) {
            $this->configs['settings.recaptcha'] = array_filter(
                $this->configs['settings.recaptcha'],
                function ($name) use ($keys) {
                    return !in_array($name, $keys);
                }
            );
        }

        $opts = array(
            'ADVEG_REC_TYPE'  => array('v2_cbx', 'v2_inv', 'v3'),
            'ADVEG_REC_THEME' => array('light', 'dark'),
            'ADVEG_REC_LANGUAGE' => array('shop', 'browser'),
            'ADVEG_REC_POSITION' => array('bottomright', 'bottomleft', 'inline'),
        );

        foreach ($this->configs['settings.recaptcha'] as $key) {
            $value = Tools::getValue($key);

            if (in_array($key, $keys)) {
                $value = trim($value);
            } elseif (in_array($key, array_keys($opts))) {
                if (!in_array($value, $opts[$key])) {
                    $value = $opts[$key][0];
                }
            } elseif ($key === 'ADVEG_REC_SCORE_THRESHOLD') {
                $value = (float) $value;
                $value = $value < 0 || $value > 1 ? '0.5' : number_format($value, 1);
            } else {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }

            $this->request[$key] = $value;
        }
    }

    /**
     * Process settings email form request.
     *
     * @return void
     */
    protected function processSettingsEmailRequest()
    {
        $this->setSettingsEmailRequestData();

        foreach ($this->request['ADVEG_FORBIDDEN_EMAILS'] as $key => $value) {
            if (!Validate::isEmail($value)) {
                $this->addWarning(sprintf($this->module->l('Invalid email address “%s”'), $value));
                unset($this->request['ADVEG_FORBIDDEN_EMAILS'][$key]);
            }
        }

        foreach ($this->request as $key => $value) {
            Configuration::updateValue($key, is_array($value) ? json_encode($value) : $value);
        }
        $this->addSuccess($this->module->getTrans('settingsSaved'));
    }

    /**
     * Set validations email request data.
     *
     * @return void
     */
    protected function setSettingsEmailRequestData()
    {
        if ($this->module->isDemo()) {
            $omit = array('ADVEG_CHECK_EMAIL_DISPOSABLE');
            $this->configs['settings.email'] = array_filter(
                $this->configs['settings.email'],
                function ($name) use ($omit) {
                    return !in_array($name, $omit);
                }
            );
        }

        $arr = array('ADVEG_FORBIDDEN_EMAILS', 'ADVEG_FORBIDDEN_EMAIL_DOMAINS', 'ADVEG_FORBIDDEN_EMAIL_PATTERNS');
        foreach ($this->configs['settings.email'] as $key) {
            $value = Tools::getValue($key);
            if (in_array($key, $arr)) {
                $this->request[$key] = array_filter(array_map('trim', (array) $value));
            } else {
                $this->request[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }
    }

    /**
     * Process settings message form request.
     *
     * @return void
     */
    protected function processSettingsMessageRequest()
    {
        $this->setSettingsMessageRequestData();

        foreach ($this->request as $key => $value) {
            Configuration::updateValue($key, is_array($value) ? json_encode($value) : $value);
        }
        $this->addSuccess($this->module->getTrans('settingsSaved'));
    }

    /**
     * Set settings message request data.
     *
     * @return void
     */
    protected function setSettingsMessageRequestData()
    {
        foreach ($this->configs['settings.message'] as $key) {
            $value = Tools::getValue($key);
            if ($key === 'ADVEG_FORBIDDEN_TEXTS') {
                $this->request[$key] = array_filter(array_map('trim', (array) $value), function ($value) {
                    return Tools::strlen($value) > 2;
                });
            } else {
                $this->request[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }
    }

    /**
     * Process forms settings form request.
     *
     * @return void
     */
    protected function processFormsSettingsRequest()
    {
        if ($this->module->isDemo()) {
            return;
        }

        $this->setFormsSettingsRequestData();

        foreach ($this->request as $key => $value) {
            Configuration::updateValue($key, is_array($value) ? json_encode($value) : $value);
        }
        $this->addSuccess($this->module->getTrans('settingsSaved'));
    }

    /**
     * Process forms preview mode form request.
     *
     * @return void
     */
    protected function processFormsPreviewModeRequest()
    {
        if ($this->module->isDemo()) {
            return;
        }

        $this->setFormsPreviewModeRequestData();

        foreach ($this->request as $key => $value) {
            Configuration::updateValue($key, is_array($value) ? json_encode($value) : $value);
        }
        $this->addSuccess($this->module->getTrans('settingsSaved'));
    }

    /**
     * Process logs settings form request.
     *
     * @return void
     */
    protected function processLogsSettingsRequest()
    {
        if ($this->module->isDemo()) {
            return;
        }

        $this->setLogsSettingsRequestData();
        foreach ($this->request as $key => $value) {
            Configuration::updateValue($key, $value);
        }
        $this->addSuccess($this->module->getTrans('settingsSaved'));
    }

    /**
     * Process logs settings request data.
     *
     * @return void
     */
    protected function setLogsSettingsRequestData()
    {
        $logModes = array('all', 'failed', 'none');
        foreach ($this->configs['logs.settings'] as $key) {
            $value = Tools::getValue($key);
            $this->request[$key] = in_array($value, $logModes) ? $value : $logModes[0];
        }
    }

    /**
     * Process logs page step request.
     *
     * @return void
     */
    protected function processLogsPageStepRequest()
    {
        $this->module->setCookie('logsPerPage', trim(Tools::getValue('perPage')));
    }

    /**
     * Process logs delete request.
     *
     * @return void
     */
    protected function processLogsDeleteRequest()
    {
        // Note: "type" already taken by logs filter.
        if (Tools::getValue('_type') === false) {
            return;
        }
        $type = trim(Tools::getValue('_type'));

        if (Tools::getValue('id') !== false) {
            $this->module->deleteLogByType($type, (int) Tools::getValue('id'));
            die;
        }

        if ($this->module->isDemo()) {
            return;
        }

        $this->module->deleteAllLogsByType($type);
        $this->addSuccess($this->module->getTrans('recordsDeleted'));
    }

    /**
     * Process forms settings request data.
     *
     * @return void
     */
    protected function setFormsSettingsRequestData()
    {
        $opts = array(
            'recaptchaSize'  => array('normal', 'compact'),
            'recaptchaAlign' => array('left', 'center', 'right', 'offset'),
        );

        foreach ($this->module->getFormsMeta() as $formName => $form) {
            $name = 'ADVEG_' . Tools::strtoupper($formName) . '_OPTS';
            $values = Tools::getValue($name);
            if (!is_array($values)) {
                continue;
            }

            $data = array();
            foreach (array_keys($form['settings']) as $key) {
                $value = '';
                if (array_key_exists($key, $values)) {
                    $value = $values[$key];
                }

                if ($key === 'recaptchaValidation') {
                    $data[$key] = (bool) $value;
                } elseif ($key === 'emailValidation') {
                    $data[$key] = $form['isEmailForm'] && (bool) $value;
                } elseif ($key === 'messageValidation') {
                    $data[$key] = $form['isMessageForm'] && (bool) $value;
                } elseif ($key === 'recaptchaOffset') {
                    $data[$key] = (int) $value > 0 && (int) $value <= 4 ? (int) $value : 1;
                } elseif (in_array($key, array_keys($opts))) {
                    $data[$key] = in_array($value, $opts[$key]) ? $value : $opts[$key][0];
                }
            }

            $this->request[$name] = $data;
        }
    }

    /**
     * Process forms preview mode request data.
     *
     * @return void
     */
    protected function setFormsPreviewModeRequestData()
    {
        foreach ($this->configs['forms.previewMode'] as $key) {
            $value = Tools::getValue($key);
            if ($key === 'ADVEG_PREVIEW_MODE_IPS') {
                $this->request[$key] = array_filter(array_map('trim', (array) $value), function ($ip) {
                    return filter_var($ip, FILTER_VALIDATE_IP);
                });
            } else {
                $this->request[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }
    }

    /**
     * Get the logs type for the validation section.
     *
     * @return string|null
     */
    protected function getLogsType()
    {
        if ($this->tab !== 'logs') {
            return null;
        }

        $type = trim(Tools::getValue('type'));
        if (!in_array($type, $this->module->getLogTypes())) {
            return null;
        }
        return $type;
    }

    /**
     * Get logs meta data for the validations section.
     *
     * @return array
     */
    protected function getLogsMeta()
    {
        $type = $this->getLogsType();
        if ($type === null) {
            return $this->module->getLogsSummary();
        }

        $page = 1;
        if (($pag = (int) Tools::getValue('page')) > 0) {
            $page = $pag;
        }
        $perPage = (int) $this->module->getCookie('logsPerPage', 20);

        $filters = array('form' => '', 'success' => null);
        if (Tools::getValue('form') !== false && ($form = trim(Tools::getValue('form'))) !== '') {
            $filters['form'] = $form;
        }
        if (Tools::getValue('success') !== false && ($success = trim(Tools::getValue('success'))) !== '') {
            $filters['success'] = (bool) $success;
        }

        list($logs, $count, $filtered) = $this->module->getLogsByType($type, $page, $perPage, $filters);

        $urlParams = http_build_query(array_merge(array('type' => $type), array_filter($filters)));
        $url = $this->module->getMainAdminLink() . '&tab=logs&' . $urlParams . '&page=(:num)';
        $paginator = new \JasonGrimes\Paginator($count, $perPage, $page, $url);

        $forms = $this->module->getFormOptionsByType($type);
        return compact(
            'logs',
            'count',
            'paginator',
            'type',
            'page',
            'perPage',
            'urlParams',
            'filtered',
            'filters',
            'forms'
        );
    }
}
