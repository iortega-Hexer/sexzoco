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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

class Advancedemailguard extends Module
{
    /**
     * Prestashop Addons marketplace link.
     *
     * @var string
     */
    const PS_ADDONS_LINK = 'https://addons.prestashop.com/';

    /**
     * Prestashop Addons module ID.
     *
     * @var int
     */
    const PS_ADDONS_ID = 30941;

    /**
     * Prestashop Addons module author ID.
     *
     * @var int
     */
    const PS_ADDONS_AUTHOR_ID = 747147;

    /**
     * Module docs link.
     *
     * @var string
     */
    const DOCS_LINK = 'https://drive.google.com/drive/folders/10Wj9hGBWgE2r-FG8FQyre6qiW7th3qj7';

    /**
     * The reCAPTCHA API.
     *
     * @var string
     */
    const REC_API = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Email logs table name.
     *
     * @var string
     */
    const TABLE_EMAIL_LOGS = 'adveg_email_logs';

    /**
     * Message logs table name.
     *
     * @var string
     */
    const TABLE_MESSAGE_LOGS = 'adveg_message_logs';

    /**
     * Recaptcha logs table name.
     *
     * @var string
     */
    const TABLE_RECAPTCHA_LOGS = 'adveg_recaptcha_logs';

    /**
     * Unique database instance.
     *
     * @var \Db
     */
    public $db;

    /**
     * Admin controllers configuration.
     *
     * @var array
     */
    protected $adminControllers = array(
        array(
            'name' => 'Advanced Anti Spam Google reCAPTCHA',
            'class_name' => 'AdminAdvancedEmailGuard',
        ),
    );

    /**
     * Configuration keys and default values.
     *
     * @var array
     */
    protected $configs = array(
        'ADVEG_REC_TYPE' => 'v2_cbx',

        'ADVEG_REC_V2_CBX_KEY' => '',
        'ADVEG_REC_V2_CBX_SECRET' => '',
        'ADVEG_REC_V2_INV_KEY' => '',
        'ADVEG_REC_V2_INV_SECRET' => '',
        'ADVEG_REC_V3_KEY' => '',
        'ADVEG_REC_V3_SECRET' => '',

        'ADVEG_REC_LANGUAGE' => 'shop',
        'ADVEG_REC_THEME' => 'light',
        'ADVEG_REC_POSITION' => 'bottomright',
        'ADVEG_REC_SCORE_THRESHOLD' => '0.5',
        'ADVEG_REC_LEGAL_LINKS' => false,
        'ADVEG_REC_LOAD_ON_DEMAND' => false,

        'ADVEG_CHECK_EMAIL_DISPOSABLE' => true,
        'ADVEG_FORBIDDEN_EMAILS' => '[]',
        'ADVEG_FORBIDDEN_EMAIL_DOMAINS' => '[]',
        'ADVEG_FORBIDDEN_EMAIL_PATTERNS' => '[]',
        'ADVEG_FORBIDDEN_TEXTS' => '[]',

        'ADVEG_PREVIEW_MODE' => false,
        'ADVEG_PREVIEW_MODE_IPS' => '[]',

        'ADVEG_CONTACT_US_OPTS' => '{"recaptchaAlign":"offset","recaptchaOffset":3}',
        'ADVEG_REGISTER_OPTS' => '{"recaptchaAlign":"center"}',
        'ADVEG_LOGIN_OPTS' => '{"recaptchaAlign":"center"}',
        'ADVEG_RESET_PASSWORD_OPTS' => '{"recaptchaAlign":"center"}',
        'ADVEG_QUICK_ORDER_OPTS' => '{"recaptchaAlign":"center"}',
        'ADVEG_NEWSLETTER_OPTS' => '{"recaptchaAlign":"left"}',
        'ADVEG_WRITE_REVIEW_OPTS' => '{"recaptchaAlign":"left"}',
        'ADVEG_NOTIFY_WHEN_IN_STOCK_OPTS' => '{"recaptchaAlign":"left"}',
        'ADVEG_SEND_TO_FRIEND_OPTS' => '{"recaptchaAlign":"left"}',

        'ADVEG_LOGS_MODE' => 'all',
    );

    /**
     * Global configuration keys and default values.
     *
     * @var array
     */
    protected $globals = array(
        'ADVEG_INSTALL_DATE' => '',
        'ADVEG_DISPLAY_RATING' => true,
        'ADVEG_LOGS_TOKEN' => '',
    );

    /**
     * Array type configuration keys.
     *
     * @var array
     */
    protected $arrayKeys = array(
        'ADVEG_PREVIEW_MODE_IPS',
        'ADVEG_FORBIDDEN_EMAILS', 'ADVEG_FORBIDDEN_EMAIL_DOMAINS',
        'ADVEG_FORBIDDEN_EMAIL_PATTERNS', 'ADVEG_FORBIDDEN_TEXTS',
        'ADVEG_CONTACT_US_OPTS', 'ADVEG_REGISTER_OPTS',
        'ADVEG_LOGIN_OPTS', 'ADVEG_RESET_PASSWORD_OPTS',
        'ADVEG_QUICK_ORDER_OPTS', 'ADVEG_NEWSLETTER_OPTS',
        'ADVEG_WRITE_REVIEW_OPTS', 'ADVEG_NOTIFY_WHEN_IN_STOCK_OPTS',
        'ADVEG_SEND_TO_FRIEND_OPTS',
    );

    /**
     * Boolean type configuration keys.
     *
     * @var array
     */
    protected $boolKeys = array(
        'ADVEG_PREVIEW_MODE', 'ADVEG_CHECK_EMAIL_DISPOSABLE',
        'ADVEG_REC_LEGAL_LINKS', 'ADVEG_REC_LOAD_ON_DEMAND',
    );

    /**
     * Integer type configuration keys.
     *
     * @var array
     */
    protected $intKeys = array();

    /**
     * Multi-language configuration keys.
     *
     * @var array
     */
    protected $multiLangKeys = array();

    /**
     * Hooks used by the module.
     *
     * @var array
     */
    protected $hooks = array(
        // Display hooks.
        'displayBackOfficeHeader',
        'header',
        'displayFooter',
        array('name' => 'displayFooterAfter', 'min' => '1.7'),
        array('name' => 'displayBeforeBodyClosingTag', 'min' => '1.7'),

        // Event hooks.
        'actionDispatcher',
        // Other hooks.
        'moduleRoutes',
    );

    /**
     * Supported forms for validation.
     *
     * @var array
     */
    protected $forms = array(
        'contact_us', 'register', 'login', 'reset_password',
        'quick_order', 'newsletter', 'write_review',
        'notify_when_in_stock', 'send_to_friend',
    );

    /**
     * Unconventional forms that cannot be tracked in the dispatcher hook.
     *
     * @var array
     */
    protected $unconvForms = array('send_to_friend');

    /**
     * Supported forms for email validation.
     *
     * @var array
     */
    protected $emailForms = array(
        'contact_us', 'register', 'quick_order',
        'newsletter', 'notify_when_in_stock', 'send_to_friend',
    );

    /**
     * Supported forms for message validation.
     *
     * @var array
     */
    protected $messageForms = array('contact_us', 'write_review');

    /**
     * Validations log table names.
     *
     * @var array
     */
    protected $logTables = array(
        'recaptcha' => self::TABLE_RECAPTCHA_LOGS,
        'email'     => self::TABLE_EMAIL_LOGS,
        'message'   => self::TABLE_MESSAGE_LOGS,
    );

    /**
     * Flag to determine if the main reCAPTCHA container was displayed.
     *
     * @var bool
     */
    protected $recDisplayed = false;

    /**
     * Create a new module instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->name = 'advancedemailguard';
        $this->tab = 'front_office_features';
        $this->author = 'ReduxWeb';
        $this->version = '4.2.2';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '48558626675f8755eaa6cd391dafd8d1';

        parent::__construct();

        $this->displayName = $this->l('Advanced Anti-Spam');
        $this->description = $this->l(
            'Protect your shop against spam and bots using anti-spam filters and Google reCAPTCHA.'
        );
        $this->ps_version_compliancy = array('min' => '1.5.0', 'max' => _PS_VERSION_);

        $this->db = Db::getInstance();
    }

    /**
     * Install the module.
     *
     * @return bool
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install() &&
            $this->registerHooks() &&
            $this->addConfigs() &&
            $this->createAdminTabs() &&
            $this->runMigrations();
    }

    /**
     * Uninstall the module.
     *
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() &&
            $this->unregisterHooks() &&
            $this->removeConfigs() &&
            $this->deleteAdminTabs() &&
            $this->dropMigrations();
    }

    /**
     * Register the module hooks.
     *
     * @return bool
     */
    protected function registerHooks()
    {
        foreach ($this->getHooks() as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Unregister the module hooks.
     *
     * @return bool
     */
    protected function unregisterHooks()
    {
        foreach ($this->getHooks() as $hook) {
            if (!$this->unregisterHook($hook)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get hook names based on shop version.
     *
     * @return array
     */
    protected function getHooks()
    {
        $v = _PS_VERSION_;
        $hooks = array();
        foreach ($this->hooks as $hook) {
            if (!is_array($hook)) {
                $hooks[] = $hook;
                continue;
            }

            if (Tools::version_compare($v, $hook['min'], '>=')) {
                if (!array_key_exists('max', $hook)) {
                    $hooks[] = $hook['name'];
                    continue;
                }

                $exp = explode('.', $hook['max']);
                $last = array_pop($exp);
                $max = implode('.', $exp) . '.' . ((int) $last + 1);
                if (Tools::version_compare($v, $max, '<')) {
                    $hooks[] = $hook['name'];
                }
            }
        }
        return $hooks;
    }

    /**
     * Add custom configuration.
     *
     * @return bool
     */
    protected function addConfigs()
    {
        foreach ($this->configs as $config => $value) {
            if (is_bool($value)) {
                $value = (int) $value;
            }

            if (!Configuration::updateValue($config, $value)) {
                return false;
            }
        }

        foreach ($this->globals as $config => $value) {
            if ($config === 'ADVEG_INSTALL_DATE') {
                $value = date('Y-m-d H:i:s');
            } elseif ($config === 'ADVEG_LOGS_TOKEN') {
                $value = Tools::passwdGen(12);
            }
            if (is_bool($value)) {
                $value = (int) $value;
            }

            if (!Configuration::updateGlobalValue($config, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove custom configuration.
     *
     * @return bool
     */
    protected function removeConfigs()
    {
        foreach (array_keys($this->configs) as $config) {
            if (!Configuration::deleteByName($config)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Run the required database migrations.
     *
     * @return bool
     */
    protected function runMigrations()
    {
        $sql = $this->getMigrationQueriesArray();
        if (empty($sql)) {
            return false;
        }

        foreach ($sql as $query) {
            if (!$this->db->execute($query)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Drop the database migrations.
     *
     * @return bool
     */
    protected function dropMigrations()
    {
        $sql = $this->getMigrationQueries();
        if ($sql === null) {
            return false;
        }

        $results = array();
        preg_match_all('/create\s+table\s+\`?(\w+)`?/i', $sql, $results);
        if (!$results) {
            return false;
        }

        foreach ($results[1] as $table) {
            if (!$this->db->execute("drop table if exists `{$table}`")) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return the database migration SQL queries.
     *
     * @param string $sqlFile
     * @return string|null
     */
    public function getMigrationQueries($sqlFile = 'db.sql')
    {
        $sqlFile = $this->getLocalPath() . $sqlFile;
        if (!file_exists($sqlFile) || !$sql = Tools::file_get_contents($sqlFile)) {
            return null;
        }

        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        // Remove SQL comments.
        return preg_replace('/\/\*.*?\*\/|--.*?\n/s', '', $sql);
    }

    /**
     * Return the database migration SQL queries as an array.
     *
     * @param string $sqlFile
     * @return array
     */
    public function getMigrationQueriesArray($sqlFile = 'db.sql')
    {
        $sql = $this->getMigrationQueries($sqlFile);
        if ($sql === null) {
            return array();
        }
        // Split each query and return an array.
        return array_filter(array_map('trim', preg_split('/;\s*[\r\n]+/', $sql)));
    }

    /**
     * Create new admin controller tabs.
     *
     * @return bool
     */
    protected function createAdminTabs()
    {
        // Delete tabs from previous install attempts.
        $this->deleteAdminTabs();

        $useNewIcons = Tools::version_compare(_PS_VERSION_, '1.7', '>=');
        foreach ($this->adminControllers as $controller) {
            $tab = $this->newAdminTab();
            foreach ($controller as $property => $value) {
                if ($property == 'name') {
                    $tab->$property = $this->getMultiLangArray($value);
                } elseif ($property == 'id_parent' && is_string($value)) {
                    $tab->$property = (int) Tab::getIdFromClassName($value);
                } elseif ($property == 'icons') {
                    $tab->icon = $useNewIcons ? $value['md'] : $value['fa'];
                } else {
                    $tab->$property = $value;
                }
            }

            if (!$tab->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete admin tab controllers.
     *
     * @return bool
     */
    protected function deleteAdminTabs()
    {
        if (!$tabs = Tab::getCollectionFromModule($this->name)) {
            return true;
        }

        foreach ($tabs as $tab) {
            if (!$tab->delete()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get new module admin controller tab object.
     *
     * @return \Tab
     */
    public function newAdminTab()
    {
        $tab = new Tab();
        $tab->position = 0;
        $tab->id_parent = 0;
        $tab->active = false;
        $tab->module = $this->name;
        return $tab;
    }

    /**
     * Get multi-language array values.
     *
     * @param string $value
     * @return array
     */
    public function getMultiLangArray($value = '')
    {
        $idLangs = array_map('intval', array_column(Language::getLanguages(false), 'id_lang'));
        $totalLangs = count($idLangs);
        return array_combine($idLangs, array_fill(0, $totalLangs, $value));
    }

    /**
     * Set namespaced cookie value for the current context.
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function setCookie($key, $value)
    {
        $name = $this->name . '.' . $key;
        try {
            $this->context->cookie->$name = $value;
        } catch (Exception $e) {
            unset($e);
            return false;
        }
        return true;
    }

    /**
     * Set many namespaced cookies for the current context.
     *
     * @param array $cookies
     * @return bool
     */
    public function setCookies(array $cookies)
    {
        foreach ($cookies as $key => $value) {
            if (!$this->setCookie($key, $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get namespaced cookie value from the current context.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getCookie($key, $default = null)
    {
        $name = $this->name . '.' . $key;
        if (!isset($this->context->cookie->$name)) {
            return $default;
        }
        return $this->context->cookie->$name;
    }

    /**
     * Get namespaced cookie value once from the current context.
     * Delete it after its value has been retrieved.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getCookieOnce($key, $default = null)
    {
        $value = $this->getCookie($key, $default);
        $this->deleteCookie($key);
        return $value;
    }

    /**
     * Unset namespaced cookie from the current context.
     *
     * @param string|array $key
     * @return void
     */
    public function deleteCookie($key)
    {
        $key = (array) $key;
        foreach ($key as $k) {
            $name = $this->name . '.' . $k;
            unset($this->context->cookie->$name);
        }
    }

    /**
     * Get the module configuration values.
     *
     * @return array
     */
    public function getConfigValues()
    {
        $values = array();

        foreach ($this->configs as $key => $default) {
            if (in_array($key, $this->multiLangKeys)) {
                $value = Configuration::getInt($key);
            } else {
                $value = Configuration::get($key);
            }

            if (in_array($key, $this->arrayKeys)) {
                $values[$key] = json_decode($value, true) ?: json_decode($default, true);
            } elseif (in_array($key, $this->boolKeys)) {
                $values[$key] = $value !== false ? (bool) $value : $default;
            } elseif (in_array($key, $this->intKeys)) {
                $values[$key] = (int) $value ?: $default;
            } else {
                $values[$key] = $value !== false ? $value : $default;
            }
        }

        return $values;
    }

    /**
     * Get the module installation date.
     *
     * @return string
     */
    public function getInstallDate()
    {
        return Configuration::getGlobalValue('ADVEG_INSTALL_DATE');
    }

    /**
     * Get all common translations.
     *
     * @return array
     */
    public function getAllTrans()
    {
        static $trans;
        if ($trans !== null) {
            return $trans;
        }

        $trans = array(
            // Yes-no options (switches).
            'yes' => $this->l('Yes'),
            'no'  => $this->l('No'),

            // CRUD operations.
            'save'   => $this->l('Save'),
            'delete' => $this->l('Delete'),
            'deleteAll' => $this->l('Delete all'),

            // Form controls.
            'sepByComma' => $this->l('Separate values by pressing the comma key.'),
            'sepByEnter' => $this->l('Separate values by pressing the enter key.'),
            'days' => $this->l('Days'),

            // Refresh/Cancel operations.
            'reset'   => $this->l('Reset'),
            'refresh' => $this->l('Refresh'),

            // Search & filter.
            'apply'   => $this->l('Apply'),
            'filters' => $this->l('Filters'),
            'all'     => $this->l('All'),

            // Pagination.
            'perPage'   => $this->l('Per page'),
            'noRecords' => $this->l('No records found.'),

            // Confirmations.
            'deleteConf' => $this->l('Delete ?'),
            'warning' => $this->l('Warning'),
            'cancel' => $this->l('Cancel'),
            'continue' => $this->l('Continue'),

            // Copy to clipboard.
            'copyToClipboard'   => $this->l('Copy to clipboard'),
            'copiedToClipboard' => $this->l('Copied to clipboard'),

            // Feedback messages.
            'settingsSaved'  => $this->l('Settings saved.'),
            'serverError'    => $this->l('Server error.'),
            'recordsDeleted' => $this->l('Records deleted.'),

            // Validation messages.
            'emailError'     => $this->l('Forbidden email address, please use another email address.'),
            'messageError'   => $this->l('Forbidden message, please use another message.'),
            'recaptchaError' => $this->l('reCAPTCHA validation failed, please try again.'),

            // Demo mode.
            'demo' => $this->l('Demo'),
            'disabledForDemo' => $this->l('Disabled for demo'),
        );
        return $trans;
    }

    /**
     * Get common translation by key.
     *
     * @param string $key
     * @return string
     */
    public function getTrans($key)
    {
        $trans = $this->getAllTrans();
        return array_key_exists($key, $trans) ? $trans[$key] : $key;
    }

    /**
     * Check if module is in demo mode.
     *
     * @return bool
     */
    public function isDemo()
    {
        static $check;
        if (isset($check)) {
            return $check;
        }

        if (isset($this->context->employee) && $this->context->employee->isSuperAdmin()) {
            return $check = false;
        }
        return $check = is_file($this->getLocalPath() . 'demo');
    }

    /**
     * Check if admin page is the configure module page.
     *
     * @return bool
     */
    protected function isConfigModulePage()
    {
        return $this->context->controller instanceof AdminModulesController
            && Tools::getValue('configure') === $this->name;
    }

    /**
     * Get the module full path URI.
     *
     * @return string
     */
    public function getFullPathUri()
    {
        return _PS_MODULE_DIR_ . basename(dirname(__FILE__)) . '/';
    }

    /**
     * Get the module base link.
     *
     * @return string
     */
    public function getBaseLink()
    {
        return _PS_BASE_URL_SSL_ . $this->getPathUri();
    }

    /**
     * Get the module configure link.
     *
     * @return string
     */
    public function getConfigLink()
    {
        return $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name;
    }

    /**
     * Get the module main admin controller link.
     *
     * @return string
     */
    public function getMainAdminLink()
    {
        return $this->context->link->getAdminLink($this->adminControllers[0]['class_name']);
    }

    /**
     * Get the documentation link in the current language.
     *
     * @return string
     */
    public function getDocsLink()
    {
        return static::DOCS_LINK;
    }

    /**
     * Get Prestashop Addons info/help links.
     *
     * @return array
     */
    public function getPsAddonsLinks()
    {
        $baseLink = self::PS_ADDONS_LINK . $this->getPsAddonsIsoCode();
        $contactUs = $this->getPsAddonsContactUsUri();

        return array(
            'contact' => $baseLink . '/' . $contactUs . '?id_product=' . self::PS_ADDONS_ID,
            'profile' => $baseLink . '/2_community-developer?contributor=' . self::PS_ADDONS_AUTHOR_ID,
            'ratings' => $baseLink . '/ratings.php',
        );
    }

    /**
     * Get valid Prestashop Addons language iso code.
     *
     * @return string
     */
    protected function getPsAddonsIsoCode()
    {
        static $codes = array('en', 'fr', 'es', 'de', 'it', 'nl', 'pl', 'pt', 'ru');
        $code = $this->context->language->iso_code;

        if (in_array($code, $codes)) {
            return $code;
        }
        return 'en';
    }

    /**
     * Get valid PrestaShop Addons "contact us" URI.
     *
     * @return string
     */
    protected function getPsAddonsContactUsUri()
    {
        static $uris = array(
            'en' => 'contact-us',
            'fr' => 'contactez-nous',
            'es' => 'contacte-con-nosotros',
        );
        $code = $this->getPsAddonsIsoCode();

        if (array_key_exists($code, $uris)) {
            return $uris[$code];
        }
        return $uris['en'];
    }

    /**
     * Get rating request message.
     *
     * @return string|null
     */
    public function getRatingMessage()
    {
        if ($this->isDemo() || !$this->isRatingDisplayable()) {
            return null;
        }
        return sprintf(
            $this->l('Hello %s! We\'d love to see a rating from you on the PrestaShop marketplace.'),
            '<b>' . $this->context->employee->firstname . '</b>'
        );
    }

    /**
     * Check if rating message can be displayed.
     *
     * @return bool
     */
    protected function isRatingDisplayable()
    {
        if (!(bool) Configuration::getGlobalValue('ADVEG_DISPLAY_RATING')) {
            return false;
        }
        $now = time();
        $delay = strtotime($this->getInstallDate() . ' +2 days');
        return $now > $delay;
    }

    /**
     * Dismiss rating message.
     *
     * @return void
     */
    public function dismissRatingMessage()
    {
        Configuration::updateGlobalValue('ADVEG_DISPLAY_RATING', 0);
    }

    /**
     * Determine back-office CSS file based on shop version.
     *
     * @return string
     */
    protected function determineBackOfficeCSS()
    {
        $css = 'admin.proxy';
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            // 1.7.4 introduces significantly different nav style.
            if (Tools::version_compare(_PS_VERSION_, '1.7.4', '<')) {
                $css .= '.lt.174';
            } elseif (Tools::version_compare(_PS_VERSION_, '1.7.8', '<')) {
                // 1.7.8 introduces changes to old style.
                $css .= '.lt.178';
            }
        } elseif (Tools::version_compare(_PS_VERSION_, '1.6', '>=')) {
            // 1.6.1 theme introduces new branding and back-office theme.
            $css .= Tools::version_compare(_PS_VERSION_, '1.6.1', '>=') ? '.161' : '.160';
        } else {
            $css .= '.15';
        }
        return $css . '.css';
    }

    /**
     * Get JavaScript app vars.
     *
     * @return array
     */
    protected function getJavaScriptVars()
    {
        return array(
            'meta' => $this->getJavaScriptMeta(),
            'settings' => $this->getJavaScriptSettings(),
            'context' => $this->getJavaScriptContext(),
            'trans' => $this->getJavaScriptTrans(),
        );
    }

    /**
     * Get JavaScript app meta data.
     *
     * @return array
     */
    protected function getJavaScriptMeta()
    {
        $errorType = $this->getCookieOnce('validationError');
        return array(
            'isGDPREnabled' => $this->isGDPREnabled(),
            'isLegacyOPCEnabled' => $this->isLegacyOPCEnabled(),
            'isLegacyMAModuleEnabled' => $this->isLegacyMAModuleEnabled(),
            'validationError' => $errorType !== null ? $this->getValidationError($errorType) : null,
        );
    }

    /**
     * Get JavaScript app settings.
     *
     * @return array
     */
    protected function getJavaScriptSettings()
    {
        $type = $this->getRecaptchaType();
        return array(
            'recaptcha' => array(
                'type'  => $type,
                'key'   => $this->getRecaptchaKey(),
                'forms' => $this->getRecaptchaForms(),
                'language' => Configuration::get('ADVEG_REC_LANGUAGE'),
                'theme'    => Configuration::get('ADVEG_REC_THEME'),
                'position' => Configuration::get('ADVEG_REC_POSITION'),
                'hidden'   => $type !== 'v2_cbx' && (bool) Configuration::get('ADVEG_REC_LEGAL_LINKS'),
                'deferred' => (bool) Configuration::get('ADVEG_REC_LOAD_ON_DEMAND'),
            ),
        );
    }

    /**
     * Get JavaScript app context data.
     *
     * @return array
     */
    protected function getJavaScriptContext()
    {
        $v = _PS_VERSION_;
        $v17 = Tools::version_compare($v, '1.7', '>=');
        // Version that brought product comments back.
        $v17pc = $v17 && Tools::version_compare($v, '1.7.6', '>=');
        // Version that brought the "Prevent rage clicking on submit button" checkout changes.
        $v17ch = $v17 && Tools::version_compare($v, '1.7.7', '>=');
        $v16 = !$v17 && Tools::version_compare($v, '1.6', '>=');
        $v161 = $v16 && Tools::version_compare($v, '1.6.1', '>=');
        $v15 = !$v17 && !$v16;

        $pageName = trim(Tools::getValue('controller'));
        if (!empty($this->context->controller->php_self)) {
            $pageName = $this->context->controller->php_self;
        }
        return array(
            'ps' => compact('v17', 'v17pc', 'v17ch', 'v16', 'v161', 'v15'),
            'languageCode' => $this->context->language->iso_code,
            'pageName'     => $pageName,
        );
    }

    /**
     * Get JavaScript app translation texts.
     *
     * @return array
     */
    protected function getJavaScriptTrans()
    {
        return array(
            'genericError' => $this->l('An error occurred, please try again.'),
        );
    }

    /**
     * Check if the module's main functionality is available for the current client.
     *
     * @return bool
     */
    protected function isEnabledForClient()
    {
        static $check;
        if ($check === null) {
            if (!(bool) Configuration::get('ADVEG_PREVIEW_MODE')) {
                $check = true;
            } else {
                $ips = json_decode(Configuration::get('ADVEG_PREVIEW_MODE_IPS')) ?: array();
                $check = in_array(Tools::getRemoteAddr(), $ips);
            }
        }

        return $check;
    }

    /**
     * Get the module content when accessing "Configure".
     *
     * @return string
     */
    public function getContent()
    {
        if (Tools::getValue('conf') !== false) {
            return '';
        }
        return $this->context->smarty
            ->assign('reduxAdminLink', $this->getMainAdminLink())
            ->fetch($this->getLocalPath() . 'views/templates/admin/proxy.tpl');
    }

    /**
     * Add additional CSS/JS files when accessing the admin section.
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayBackOfficeHeader($params)
    {
        // Only run on the module configure page.
        if (!$this->isConfigModulePage()) {
            return '';
        }

        if (Tools::getValue('conf') !== false) {
            return $this->context->smarty
                ->assign('reduxAdminRedirect', $this->getConfigLink())
                ->fetch($this->getLocalPath() . 'views/templates/admin/redirect.tpl');
        }

        $css = $this->determineBackOfficeCSS();
        $this->context->controller->addCSS(array($this->getPathUri() . 'views/css/' . $css));
        $this->context->controller->addJS(array($this->getPathUri() . 'views/js/admin.proxy.js'));
        return '';
    }

    /**
     * Render content in the front-office's <head> section.
     * Used to perform "before page is loaded" actions.
     *
     * @param array $params
     * @return string
     */
    public function hookHeader($params)
    {
        $this->context->controller->addCSS(array($this->getPathUri() . 'views/css/front.css'));
        $this->context->controller->addJS(array($this->getPathUri() . 'views/js/front.js'));

        $content = '';
        if (Tools::version_compare(_PS_VERSION_, '1.6', '>=')) {
            Media::addJsDef(array('AdvancedEmailGuardData' => $this->getJavascriptVars()));
        } else {
            $content .= $this->context->smarty
                ->assign('AdvancedEmailGuardData', json_encode($this->getJavascriptVars()))
                ->fetch($this->getLocalPath() . 'views/templates/hook/jsdef.tpl');
        }
        return $content;
    }

    /**
     * Display additional footer content.
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayFooter($params)
    {
        if ($this->recDisplayed) {
            return '';
        }
        $this->recDisplayed = true;

        return $this->context->smarty
            ->assign(array(
                'recaptchaType'  => $this->getRecaptchaType(),
                'recaptchaLegal' => $this->getRecaptchaLinks(),
                'recaptchaPos'   => Configuration::get('ADVEG_REC_POSITION'),
            ))
            ->fetch($this->getLocalPath() . 'views/templates/hook/recaptcha.tpl');
    }

    /**
     * Display content after the footer.
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayFooterAfter($params)
    {
        return $this->hookDisplayFooter($params);
    }

    /**
     * Display content before the closing of the body tag.
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayBeforeBodyClosingTag($params)
    {
        return $this->hookDisplayFooter($params);
    }

    /**
     * Fire event right after dispatcher has resolved the request.
     *
     * @param array $params
     * @return void
     */
    public function hookActionDispatcher($params)
    {
        if (!isset($this->context->controller) || !$this->context->controller instanceof FrontController) {
            return;
        }
        if (!$this->isEnabledForClient()) {
            return;
        }

        $skip = $this->unconvForms;
        $checkList = array_filter($this->forms, function ($form) use ($skip) {
            return !in_array($form, $skip);
        });
        $form = $this->getSubmittedForm($checkList);
        if ($form !== null) {
            $this->validateForm($form);
        }
        $this->saveReferrerURL();
    }

    /**
     * Register the module routes.
     *
     * @param array $params
     * @return array
     */
    public function hookModuleRoutes($params)
    {
        if (!isset($this->context->controller) || !$this->context->controller instanceof FrontController) {
            return array();
        }
        if (!$this->isEnabledForClient()) {
            return array();
        }

        $form = $this->getSubmittedForm($this->unconvForms);
        if ($form !== null) {
            $this->validateForm($form);
        }
        return array();
    }

    /**
     * Check if legacy one-page-checkout mode is enabled.
     *
     * @return bool
     */
    public function isLegacyOPCEnabled()
    {
        return Tools::version_compare(_PS_VERSION_, '1.7', '<') && $this->isOPCEnabled();
    }

    /**
     * Check if one-page-checkout mode is enabled without version check.
     *
     * @return bool
     */
    public function isOPCEnabled()
    {
        return (bool) Configuration::get('PS_ORDER_PROCESS_TYPE');
    }

    /**
     * Check if legacy "Mail Alerts" module is enabled.
     *
     * @return bool
     */
    public function isLegacyMAModuleEnabled()
    {
        return Module::isEnabled('mailalerts');
    }

    /**
     * Check if the official PS GDPR module is enabled.
     *
     * @return bool
     */
    public function isGDPREnabled()
    {
        return Module::isEnabled('psgdpr');
    }

    /**
     * Get front-office forms meta data.
     *
     * @return array
     */
    public function getFormsMeta()
    {
        $forms = array();
        foreach ($this->forms as $form) {
            $forms[$form] = array(
                'settings' => $this->getFormSettings($form),
                'display' => $this->getFormDisplayDetails($form),
                'isEmailForm' => in_array($form, $this->emailForms),
                'isMessageForm' => in_array($form, $this->messageForms),
            );
        }
        return $forms;
    }

    /**
     * Get admin defined form settings.
     *
     * @param string $form
     * @return array
     */
    protected function getFormSettings($form)
    {
        static $defaults = array(
            'emailValidation' => false,
            'messageValidation' => false,
            'recaptchaValidation' => false,
            'recaptchaSize' => 'normal',
            'recaptchaAlign' => 'left',
            'recaptchaOffset' => 1,
        );

        $settings = json_decode(Configuration::get('ADVEG_' . Tools::strtoupper($form) . '_OPTS'), true) ?: array();
        return array_merge($defaults, $settings);
    }

    /**
     * Get form name, icon and compatibility description.
     *
     * @param string $form
     * @return array
     */
    protected function getFormDisplayDetails($form)
    {
        $is17 = Tools::version_compare(_PS_VERSION_, '1.7', '>=');
        switch ($form) {
            case 'contact_us':
                return array(
                    'icon'   => 'email',
                    'color'  => '#007bff',
                    'name'   => $this->l('Contact us'),
                    'desc'   => $this->l('Compatible with the native contact form.'),
                    'custom' => false,
                );
            case 'register':
                return array(
                    'icon'   => 'how_to_reg',
                    'color'  => '#6610f2',
                    'name'   => $this->l('Register'),
                    'desc'   => $this->l('Compatible with the native register form.'),
                    'custom' => false,
                );
            case 'login':
                return array(
                    'icon'   => 'login',
                    'color'  => '#6f42c1',
                    'name'   => $this->l('Login'),
                    'desc'   => $this->l('Compatible with the native login form.'),
                    'custom' => false,
                );
            case 'reset_password':
                return array(
                    'icon'   => 'help_outline',
                    'color'  => '#e83e8c',
                    'name'   => $this->l('Forgot your password'),
                    'desc'   => $this->l('Compatible with the native reset password form.'),
                    'custom' => false,
                );
            case 'quick_order':
                $name = sprintf('%s ➜ %s', $this->l('Order checkout'), $this->l('Order as guest'));
                $desc = $this->l('Compatible with the native order checkout form for guests.');
                if ($is17) {
                    $name = sprintf(
                        '%s ➜ %s & %s',
                        $this->l('Order checkout'),
                        $this->l('Register'),
                        $this->l('Order as guest')
                    );
                    $desc = $this->l('Compatible with the native order checkout forms.');
                }
                return array(
                    'icon'   => 'shopping_basket',
                    'color'  => '#fd7e14',
                    'name'   => $name,
                    'desc'   => $desc,
                    'custom' => false,
                );
            case 'newsletter':
                return array(
                    'icon'   => 'send',
                    'color'  => '#20c997',
                    'name'   => $this->l('Newsletter'),
                    'desc'   => $this->l('Compatible with the native newsletter form.'),
                    'custom' => false,
                );
            case 'write_review':
                return array(
                    'icon'   => 'chat',
                    'color'  => '#17a2b8',
                    'name'   => sprintf('%s ➜ %s', $this->l('Product'), $this->l('Write review')),
                    'desc'   => $this->l('Compatible with the “Product Comments” module form.'),
                    'custom' => false,
                );
            case 'notify_when_in_stock':
                return array(
                    'icon'   => 'notifications',
                    'color'  => '#28a745',
                    'name'   => sprintf('%s ➜ %s', $this->l('Product'), $this->l('Notify me when it\'s available')),
                    'desc'   => $this->l('Compatible with the (old and new) “Email alerts” module form.'),
                    'custom' => false,
                );
            case 'send_to_friend':
                return array(
                    'icon'   => 'share',
                    'color'  => '#343a40',
                    'name'   => sprintf('%s ➜ %s', $this->l('Product'), $this->l('Send to a friend')),
                    'desc'   => $this->l('Compatible with the “Send to a friend” module form.'),
                    'custom' => false,
                );
        }

        return array(
            'icon'   => 'settings',
            'color'  => '#868e96',
            'name'   => str_replace('_', ' ', Tools::ucfirst($form)),
            'desc'   => '',
            'custom' => true,
        );
    }

    /**
     * Get the form by validation type.
     *
     * @param string $type
     * @return array
     */
    protected function getFormsByType($type)
    {
        if ($type === 'email') {
            return $this->emailForms;
        }
        if ($type === 'message') {
            return $this->messageForms;
        }
        return $this->forms;
    }

    /**
     * Get form select options based on validation type.
     *
     * @param string $type
     * @return array
     */
    public function getFormOptionsByType($type)
    {
        $forms = $this->getFormsByType($type);
        $opts = array();
        foreach ($forms as $form) {
            $opts[$form] = $this->getFormDisplayDetails($form);
        }
        return $opts;
    }

    /**
     * Get the submitted form instance.
     *
     * @param array $checkList
     * @return \ReduxWeb\AdvancedEmailGuard\Forms\Form|null
     */
    protected function getSubmittedForm(array $checkList)
    {
        foreach ($checkList as $name) {
            $class = '\\ReduxWeb\\AdvancedEmailGuard\\Forms\\Types\\' .
                \ReduxWeb\AdvancedEmailGuard\Utils::toStudly($name);
            if (!class_exists($class)) {
                continue;
            }
            /** @var \ReduxWeb\AdvancedEmailGuard\Forms\Form */
            $form = new $class($name);
            if ($form->isSubmitted()) {
                return $form;
            }
        }
        return null;
    }

    /**
     * Execute the validations for the specified form.
     *
     * @param \ReduxWeb\AdvancedEmailGuard\Forms\Form $form
     * @return void
     */
    protected function validateForm($form)
    {
        if (
            $form instanceof \ReduxWeb\AdvancedEmailGuard\Forms\EmailForm &&
            $this->isEmailValidationEnabled($form->getName())
        ) {
            $this->validateFormEmail($form);
        }
        if (
            $form instanceof \ReduxWeb\AdvancedEmailGuard\Forms\MessageForm &&
            $this->isMessageValidationEnabled($form->getName())
        ) {
            $this->validateFormMessage($form);
        }
        if ($this->isRecaptchaValidationEnabled($form->getName())) {
            $this->validateFormRecaptcha($form);
        }
    }

    /**
     * Check if email validation is enabled for the specified form.
     *
     * @param string $form
     * @return bool
     */
    protected function isEmailValidationEnabled($form)
    {
        return in_array($form, $this->emailForms) && $this->getFormSettings($form)['emailValidation'];
    }

    /**
     * Check if message validation is enabled for the specified form.
     *
     * @param string $form
     * @return bool
     */
    protected function isMessageValidationEnabled($form)
    {
        return in_array($form, $this->messageForms) && $this->getFormSettings($form)['messageValidation'];
    }

    /**
     * Check if recaptcha validation is enabled for the specified form.
     *
     * @param string $form
     * @return bool
     */
    protected function isRecaptchaValidationEnabled($form)
    {
        return $this->getFormSettings($form)['recaptchaValidation'];
    }

    /**
     * Validate the form email input.
     *
     * @param \ReduxWeb\AdvancedEmailGuard\Forms\Form&\ReduxWeb\AdvancedEmailGuard\Forms\EmailForm $form
     * @return void
     */
    protected function validateFormEmail($form)
    {
        $email = $form->getEmail();
        // If email is not valid then allow the current controller to handler the validation.
        if (!Validate::isEmail($email)) {
            return;
        }

        $success = $this->validateEmail($email);
        $this->logEmailValidation($form->getName(), $email, $success);
        if (!$success) {
            $form->sendResponseWithError('email');
        }
    }

    /**
     * Check if email is allowed based on admin defined settings.
     *
     * @param string $email
     * @return bool
     */
    protected function validateEmail($email)
    {
        $email = Tools::strtolower($email);
        if (in_array($email, $this->getForbiddenEmails())) {
            return false;
        }
        try {
            list($local, $domain) = \ReduxWeb\AdvancedEmailGuard\Utils::parseEmailAddress($email);
            unset($local);
        } catch (InvalidArgumentException $e) {
            unset($e);
            return true;
        }

        if (in_array($domain, $this->getForbiddenEmailDomains())) {
            return false;
        }
        if (\ReduxWeb\AdvancedEmailGuard\Utils::strMatches($this->getForbiddenEmailPatterns(), $email)) {
            return false;
        }
        return true;
    }

    /**
     * Get forbidden email addresses list.
     *
     * @return array
     */
    protected function getForbiddenEmails()
    {
        $forbidden = json_decode(Configuration::get('ADVEG_FORBIDDEN_EMAILS'), true) ?: array();
        return array_map(array('Tools', 'strtolower'), $forbidden);
    }

    /**
     * Get forbidden email address domains.
     *
     * @return array
     */
    protected function getForbiddenEmailDomains()
    {
        $forbidden = json_decode(Configuration::get('ADVEG_FORBIDDEN_EMAIL_DOMAINS'), true) ?: array();
        $forbidden = array_map(array('Tools', 'strtolower'), $forbidden);
        if (!(bool) Configuration::get('ADVEG_CHECK_EMAIL_DISPOSABLE')) {
            return $forbidden;
        }

        // Already converted to lowercase by parseLines().
        $disposable = \ReduxWeb\AdvancedEmailGuard\Utils::parseLines(
            Tools::file_get_contents($this->getLocalPath() . 'data/disposable.txt')
        );
        return array_merge($disposable, $forbidden);
    }

    /**
     * Get forbidden email address patterns.
     *
     * @return array
     */
    protected function getForbiddenEmailPatterns()
    {
        $forbidden = json_decode(Configuration::get('ADVEG_FORBIDDEN_EMAIL_PATTERNS'), true) ?: array();
        return array_map(array('Tools', 'strtolower'), $forbidden);
    }

    /**
     * Validate the form message input.
     *
     * @param \ReduxWeb\AdvancedEmailGuard\Forms\Form&\ReduxWeb\AdvancedEmailGuard\Forms\MessageForm $form
     * @return void
     */
    protected function validateFormMessage($form)
    {
        $message = $form->getMessage();
        // If message is (almost) empty then allow the current controller to handler the validation.
        if (empty($message) || Tools::strlen($message) < 3) {
            return;
        }

        $text = $this->getForbiddenTextFromMessage($message);
        $this->logMessageValidation($form->getName(), $message, $text);
        if ($text !== null) {
            $form->sendResponseWithError('message');
        }
    }

    /**
     * Get forbidden text from the message.
     *
     * @param string $message
     * @return string|null
     */
    protected function getForbiddenTextFromMessage($message)
    {
        $message = Tools::strtolower($message);
        foreach ($this->getForbiddenTexts() as $text) {
            if (\ReduxWeb\AdvancedEmailGuard\Utils::strContains($message, $text)) {
                return $text;
            }
        }
        return null;
    }

    /**
     * Get forbidden texts.
     *
     * @return array
     */
    protected function getForbiddenTexts()
    {
        $forbidden = json_decode(Configuration::get('ADVEG_FORBIDDEN_TEXTS'), true) ?: array();
        return array_map(array('Tools', 'strtolower'), $forbidden);
    }

    /**
     * Validate the form reCAPTCHA token.
     *
     * @param \ReduxWeb\AdvancedEmailGuard\Forms\Form $form
     * @return void
     */
    protected function validateFormRecaptcha($form)
    {
        $token = $form->getInput('g-recaptcha-response');
        if (empty($token)) {
            $this->logNoRecaptchaToken($form->getName());
            $form->sendResponseWithError('recaptcha');
            return;
        }

        $resp = $this->makeRecaptchaAPICall($token);
        // Bad API response.
        if ($resp === null) {
            return;
        }

        $success = (bool) $resp['success'];
        $score = null;
        if ($success && array_key_exists('score', $resp)) {
            $score = (float) $resp['score'];
            $success = $score >= $this->getRecaptchaScoreThreshold();
        }

        $this->logRecaptchaValidation($form->getName(), $resp, $score, $success);
        if (!$success) {
            $form->sendResponseWithError('recaptcha');
        }
    }

    /**
     * Make reCAPTCHA API call and return the response.
     *
     * Example JSON response:
     * ```js
     * {
     *   "success": true|false,
     *   "score": number,            // the score for this request (0.0 - 1.0) (v3 only)
     *   "action": string,           // the action name for this request (important to verify) (v3 only)
     *   "challenge_ts": timestamp,  // timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
     *   "hostname": string,         // the hostname of the site where the reCAPTCHA was solved
     *   "error-codes": [...]        // optional
     * }
     * ```
     *
     * @param string $token
     * @return array|null
     */
    protected function makeRecaptchaAPICall($token)
    {
        $req = http_build_query(array(
            'secret' => $this->getRecaptchaSecret(),
            'response' => $token,
            'remoteip' => Tools::getRemoteAddr(),
        ));

        $opts = array('http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $req,
        ));

        $ctx = stream_context_create($opts);
        $resp = Tools::file_get_contents(self::REC_API, false, $ctx);
        return json_decode($resp, true);
    }

    /**
     * Get reCAPTCHA public (site) key.
     *
     * @return string
     */
    protected function getRecaptchaKey()
    {
        $type = Tools::strtoupper($this->getRecaptchaType());
        return Configuration::get("ADVEG_REC_{$type}_KEY");
    }

    /**
     * Get reCAPTCHA secret key.
     *
     * @return string
     */
    protected function getRecaptchaSecret()
    {
        $type = Tools::strtoupper($this->getRecaptchaType());
        return Configuration::get("ADVEG_REC_{$type}_SECRET");
    }

    /**
     * Get reCAPTCHA type.
     *
     * @return string
     */
    protected function getRecaptchaType()
    {
        return Configuration::get('ADVEG_REC_TYPE');
    }

    /**
     * Get reCAPTCHA legal links.
     *
     * @return array|null
     */
    protected function getRecaptchaLinks()
    {
        if (!(bool) Configuration::get('ADVEG_REC_LEGAL_LINKS') || $this->getRecaptchaType() === 'v2_cbx') {
            return null;
        }
        return array(
            'privacy' => 'https://policies.google.com/privacy?hl=' . $this->context->language->iso_code,
            'terms'   => 'https://policies.google.com/terms?hl=' . $this->context->language->iso_code,
        );
    }

    /**
     * Get reCAPTCHA v3 score threshold.
     *
     * @return float
     */
    protected function getRecaptchaScoreThreshold()
    {
        return (float) Configuration::get('ADVEG_REC_SCORE_THRESHOLD');
    }

    /**
     * Get forms enabled for reCAPTCHA validation and their settings.
     *
     * @return array
     */
    protected function getRecaptchaForms()
    {
        $forms = array();
        if (!$this->isEnabledForClient()) {
            return $forms;
        }

        foreach ($this->forms as $form) {
            if (!$this->isRecaptchaValidationEnabled($form)) {
                continue;
            }

            $settings = $this->getFormSettings($form);
            $forms[$form] = array(
                'size'   => $settings['recaptchaSize'],
                'align'  => $settings['recaptchaAlign'],
                'offset' => $settings['recaptchaOffset'],
            );
        }
        return $forms;
    }

    /**
     * Log email validation.
     *
     * @param string $form
     * @param string $email
     * @param bool $success
     * @return void
     */
    protected function logEmailValidation($form, $email, $success)
    {
        $mode = $this->getLogsMode();
        if ($mode === 'none' || ($mode === 'failed' && $success)) {
            return;
        }

        $data = array(
            'form'    => pSQL($form),
            'email'   => pSQL($email),
            'success' => $success,
        );
        $this->db->insert(self::TABLE_EMAIL_LOGS, array_merge($data, $this->getCommonLogData()), true);
    }

    /**
     * Log message validation.
     *
     * @param string $form
     * @param string $message
     * @param string|null $text
     * @return void
     */
    protected function logMessageValidation($form, $message, $text)
    {
        $success = $text === null;
        $mode = $this->getLogsMode();
        if ($mode === 'none' || ($mode === 'failed' && $success)) {
            return;
        }

        $data = array(
            'form'    => pSQL($form),
            'message' => pSQL($message),
            'text'    => $success ? null : pSQL($text),
            'success' => $success,
        );
        $this->db->insert(self::TABLE_MESSAGE_LOGS, array_merge($data, $this->getCommonLogData()), true);
    }

    /**
     * Log reCAPTCHA validation.
     *
     * @param string $form
     * @param array $resp
     * @param float|null $score
     * @param bool $success
     * @return void
     */
    protected function logRecaptchaValidation($form, array $resp, $score, $success)
    {
        $mode = $this->getLogsMode();
        if ($mode === 'none' || ($mode === 'failed' && $success)) {
            return;
        }

        $data = array(
            'form'     => pSQL($form),
            'response' => json_encode($resp),
            'score'    => $score,
            'success'  => $success,
        );
        $this->db->insert(self::TABLE_RECAPTCHA_LOGS, array_merge($data, $this->getCommonLogData()), true);
    }

    /**
     * Log no reCAPTCHA token provided.
     *
     * @param string $form
     * @return void
     */
    protected function logNoRecaptchaToken($form)
    {
        if ($this->getLogsMode() === 'none') {
            return;
        }

        $data = array(
            'form'     => pSQL($form),
            'response' => null,
            'score'    => null,
            'success'  => false,
        );
        $this->db->insert(self::TABLE_RECAPTCHA_LOGS, array_merge($data, $this->getCommonLogData()), true);
    }

    /**
     * Get common validation log data.
     *
     * @return array
     */
    protected function getCommonLogData()
    {
        return array(
            'id_shop'    => (int) $this->context->shop->id,
            'ip_address' => pSQL(Tools::getRemoteAddr()),
            'user_agent' => pSQL($_SERVER['HTTP_USER_AGENT']),
            'date_add'   => date('Y-m-d H:i:s'),
        );
    }

    /**
     * Get logs mode setting.
     *
     * @return string
     */
    protected function getLogsMode()
    {
        return Configuration::get('ADVEG_LOGS_MODE');
    }

    /**
     * Save the current URL as the referrer.
     *
     * @return void
     */
    protected function saveReferrerURL()
    {
        // Bail on controllers utilized for AJAX requests.
        if ($this->context->controller->ajax || $this->context->controller->isXmlHttpRequest()) {
            return;
        }

        $url = $this->getCurrentPageUrl();
        // Bail if current URL is a theme asset.
        if (\ReduxWeb\AdvancedEmailGuard\Utils::strContains($url, _THEME_DIR_)) {
            return;
        }
        $this->setCookie('referrer', $url);
    }

    /**
     * Get the URL of the current page.
     *
     * @return string
     */
    protected function getCurrentPageUrl()
    {
        return Tools::getHttpHost(true) . $_SERVER['REQUEST_URI'];
    }

    /**
     * Set validation error that will be displayed once to the user.
     *
     * @param string $type
     * @return void
     */
    public function setValidationError($type)
    {
        $this->setCookie('validationError', $type);
    }

    /**
     * Get validation error message.
     *
     * @param string $type
     * @return string
     */
    public function getValidationError($type)
    {
        return $this->getTrans($type . 'Error');
    }

    /**
     * Redirect the user back to the previous (referrer) page stored in the cookies.
     *
     * @return void
     */
    public function redirectBack()
    {
        $referrer = $this->getCookie('referrer');
        if (!$this->isSafeURL($referrer)) {
            Tools::redirect('index.php');
            return;
        }
        Tools::redirect($referrer);
    }

    /**
     * Check if the URL belongs to the shop.
     *
     * @param string $url
     * @return boolean
     */
    public function isSafeURL($url)
    {
        if (!is_string($url)) {
            return false;
        }

        $parsed = parse_url($url);
        if (!is_array($parsed)) {
            return false;
        }

        if (!array_key_exists('host', $parsed) || $parsed['host'] !== Tools::getServerName()) {
            return false;
        }
        return true;
    }

    /**
     * Immediately send a json response to the client.
     *
     * @param array $data
     * @param bool $legacyNoContent
     * @return void
     */
    public function sendJson(array $data, $legacyNoContent = false)
    {
        if (!$legacyNoContent) {
            header('Content-Type: application/json');
        }
        die(json_encode($data));
    }

    /**
     * Get validation log types.
     *
     * @return array
     */
    public function getLogTypes()
    {
        return array_keys($this->logTables);
    }

    /**
     * Get validation log table names.
     *
     * @return array
     */
    public function getLogTables()
    {
        return $this->logTables;
    }

    /**
     * Get delete logs token.
     *
     * @return string
     */
    public function getLogsToken()
    {
        return Configuration::getGlobalValue('ADVEG_LOGS_TOKEN');
    }

    /**
     * Get delete logs link.
     *
     * @return string
     */
    public function getDeleteLogsLink()
    {
        $token = $this->getLogsToken();
        return $this->context->link->getModuleLink($this->name, 'deletelogs', compact('token'));
    }

    /**
     * Get the logs summary grouped by type.
     *
     * @return array
     */
    public function getLogsSummary()
    {
        $query = new DbQuery();
        $query->select(
            '`form`, count(`id_log`) as `total`, sum(`success`) as `passed`,'
                . ' sum(if(`success` != 1, 1, 0)) as `failed`,'
                . ' max(`date_add`) as `latest`'
        )->where('`id_shop` = ' . (int) $this->context->shop->id)
            ->groupBy('form')->orderBy('form');

        $logs = array();
        foreach ($this->logTables as $type => $table) {
            $cQuery = clone $query;
            $results = $this->db->executeS($cQuery->from($table)) ?: array();

            $forms = $this->getFormsByType($type);
            $grouped = array();
            foreach ($forms as $form) {
                $display = $this->getFormDisplayDetails($form);

                foreach ($results as $data) {
                    if ($data['form'] === $form) {
                        $grouped[$form] = array(
                            'total'   => (int) $data['total'],
                            'passed'  => (int) $data['passed'],
                            'failed'  => (int) $data['failed'],
                            'latest'  => $data['latest'],
                            'display' => $display,
                        );
                        continue 2;
                    }
                }

                $grouped[$form] = array(
                    'total' => 0, 'passed' => 0, 'failed' => 0, 'latest' => '', 'display' => $display,
                );
            }

            $logs[$type] = array('forms' => $grouped, 'count' => array_sum(array_column($grouped, 'total')));
        }

        return $logs;
    }

    /**
     * Get the validation logs by type.
     *
     * @param string $type
     * @param int $page
     * @param int $perPage
     * @param array $params
     * @return (array|int|bool)[]
     */
    public function getLogsByType($type, $page = 1, $perPage = 20, array $params = array())
    {
        static $defaults = array(
            'form' => '',
            'success' => null,
        );

        if (!in_array($type, $this->getLogTypes())) {
            return array(array(), 0);
        }
        if ($page < 1) {
            $page = 1;
        }
        if ($perPage < 1) {
            $perPage = 20;
        }
        $start = ($page - 1) * $perPage;
        $params = array_merge($defaults, $params);
        $filtered = false;

        $query = new DbQuery();
        $query->from($this->logTables[$type]);
        $query->where('`id_shop` = ' . (int) $this->context->shop->id);
        if ($params['form'] !== '') {
            $query->where('`form` = "' . pSQL($params['form']) . '"');
            $filtered = true;
        }
        if ($params['success'] !== null) {
            $query->where('`success` = ' . ($params['success'] ? 1 : 0));
            $filtered = true;
        }
        $cQuery = clone $query;
        $cQuery->select('count(`id_log`) as `count`');

        $query->orderBy('`date_add` desc');
        $results = $this->db->executeS($query->build() . " limit {$start}, {$perPage}");
        if (!$results) {
            return array(array(), 0, $filtered);
        }
        $count = (int) $this->db->getValue($cQuery);

        $formatter = 'formatLog';
        if ($type === 'recaptcha') {
            $formatter = 'formatRecaptchaLog';
        } elseif ($type === 'message') {
            $formatter = 'formatMessageLog';
        }
        $results = array_map(array($this, $formatter), $results);
        return array($results, $count, $filtered);
    }

    /**
     * Format log entity accordingly.
     *
     * @param array $log
     * @return array
     */
    protected function formatLog(array $log)
    {
        $log['id_log'] = (int) $log['id_log'];
        $log['success'] = (bool) $log['success'];
        $log['display'] = $this->getFormDisplayDetails($log['form']);

        $log['browser'] = 'unknown';
        $log['platform'] = 'unknown';

        if (is_string($log['user_agent']) && $log['user_agent'] !== '') {
            $browser = new \ReduxWeb\AdvancedEmailGuard\Browser($log['user_agent']);
            $log['browser'] = $browser->getBrowser();
            $log['platform'] = $browser->getPlatform();
        }
        return $log;
    }

    /**
     * Format message log entity accordingly.
     *
     * @param array $log
     * @return array
     */
    protected function formatMessageLog(array $log)
    {
        $log = $this->formatLog($log);
        $log['message'] = htmlspecialchars($log['message']);

        if (!$log['success'] && $log['text'] !== null) {
            $p = '/(' . $log['text'] . ')/i';
            $log['message'] = preg_replace($p, '<span class=\'bg-danger text-white\'>${1}</span>', $log['message'], 1);
        }

        $log['message'] = nl2br($log['message']);
        return $log;
    }

    /**
     * Format RECAPTCHA log entity accordingly.
     *
     * @param array $log
     * @return array
     */
    protected function formatRecaptchaLog(array $log)
    {
        $log = $this->formatLog($log);
        if ($log['score'] !== null) {
            $log['score'] = (float) $log['score'];
        }
        if ($log['response'] !== null) {
            $log['response'] = json_encode(json_decode($log['response']), 128); // JSON_PRETTY_PRINT
        }
        return $log;
    }

    /**
     * Delete all logs older than the specified number of days.
     *
     * @param int $days
     * @return bool
     */
    public function deleteLogs($days = 0)
    {
        if ($days < 0) {
            $days = 0;
        }

        $threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        foreach ($this->logTables as $table) {
            if (!(bool) $this->db->delete($table, "`date_add` <= '{$threshold}'")) {
                return false;
            }
        }
        return true;
    }

    /**
     * Delete log entry by type.
     *
     * @param string $type
     * @param int $id
     * @return bool
     */
    public function deleteLogByType($type, $id)
    {
        if (!in_array($type, $this->getLogTypes())) {
            return true;
        }
        $id = (int) $id;
        if ($id === 0) {
            return true;
        }
        return (bool) $this->db->delete($this->logTables[$type], '`id_log` = ' . $id);
    }

    /**
     * Delete all log entries by type.
     *
     * @param string $type
     * @return bool
     */
    public function deleteAllLogsByType($type)
    {
        if (!in_array($type, $this->getLogTypes())) {
            return true;
        }
        return (bool) $this->db->delete($this->logTables[$type], '`id_shop` = ' . (int) $this->context->shop->id);
    }
}
