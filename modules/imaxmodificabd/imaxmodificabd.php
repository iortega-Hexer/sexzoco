<?php

class ImaxModificaBD extends Module {

    var $versionPS;
    var $idShop;
    var $idLang;
    var $idTab;

    const PREFIJO = 'imaxcajaavanproduct_';

    public function __construct() {
        $this->name = 'imaxmodificabd';
        $this->tab = 'administration';
        $this->version = '1.5';
        $this->author = 'Informax';
        $this->need_instance = 0;
        $this->versionPS = 15;
        $this->idShop = 1;
        $this->idLang = 1;

        parent::__construct();

        $this->displayName = $this->l('Informax modificar base de datos, aumenta la velocidad de tu Prestashop');
        $this->description = $this->l('Permite modificar parametros avanzados de la base de datos.');

        if (version_compare(_PS_VERSION_, '1.6.0.0 ', '>=')) {
            $this->versionPS = 16;
            $context = Context::getContext();
            $this->idShop = $context->shop->id;
            $this->idLang = $context->language->id;
        }
        elseif (version_compare(_PS_VERSION_, '1.5.0.0 ', '>=')) {
            $this->versionPS = 15;
            $context = Context::getContext();
            $this->idShop = $context->shop->id;
            $this->idLang = $context->language->id;
        }
        else {
            Global $cookie;
            $this->versionPS = 14;
            $this->idLang = $cookie->id_lang;
        }
    }

    public function install() {
        if (!parent::install() || !$this->registerHook('displayBackOfficeHome')) {
            return false;
        }

        if (!$this->installTab()) {
            $this->_errors[] = $this->l('Error al instalar el tab');
            return false;
        }

        return true;
    }

    public function uninstall() {
        if (!parent::uninstall()) {
            return false;
        }

        if (!$this->uninstallTab()) {
            $this->_errors[] = $this->l('Error al eliminar el tab');
            return false;
        }

        return true;
    }

    public function getContent() {
        $this->_html .= '<h2>' . $this->displayName . '</h2>';
        $this->_html .= '<h4>' . $this->l('Version: ') . $this->version . '</h4>';
        if (!empty($_POST)) {
            $this->_html .= $this->postProcess();
        }
        $this->displayForm();

        return $this->_html;
    }

    private function postProcess() {
        $accion = Tools::getValue("accion");
        $this->idTab = Tools::getValue("idTab");

        $html = '';
        if ($accion == 'cambiarMotor') {
            $errores = $this->cambiarMotor(Tools::getValue("motor"));
            if (!$errores) {
                $html .= $this->displayConfirmation('Todas las tablas se han actualizado correctamente.');
            }
            else {
                $html .= $this->displayError('Se ha encontrado los siguientes errores: '.implode(' ', $errores));
            }
        }
		if($accion == 'optimizar') {
            $errores = $this->optimizarTablas();
            if (!$errores) {
                $html .= $this->displayConfirmation('Todas las tablas se han actualizado correctamente.');
            }
            else {
                $html .= $this->displayError('Se ha encontrado los siguientes errores: '.implode(' ', $errores));
            }
        }
		
		if($accion == 'reparar') {
            $errores = $this->repararTablas();
            if (!$errores) {
                $html .= $this->displayConfirmation('Todas las tablas se han actualizado correctamente.');
            }
            else {
                $html .= $this->displayError('Se ha encontrado los siguientes errores: '.implode(' ', $errores));
            }
        }
        
        return $html;
    }

    private function displayForm() {
        if ($this->idTab == '' || empty($this->idTab)) {
            $this->idTab = 1;
        }

        $this->_html .= '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'css/css.css" />';

        $this->_html .= '<ul id="menuTab">
                    <li id="menuTab1" class="menuTabButton' . (($this->idTab == 1) ? " selected" : "") . '">1. ' . $this->l('Operaciones') . '</li>
                    <li id="menuTab2" class="menuTabButton' . (($this->idTab == 2) ? " selected" : "") . '">2. ' . $this->l('Ayuda') . '</li>';
        $this->_html .= '</ul>';
        $this->_html .= '
                <div id="tabList">
                    <div id="menuTab1Sheet" class="tabItem' . (($this->idTab == 1) ? " selected" : "" ) . '">' . $this->configuracion() . '</div>
                    <div id="menuTab2Sheet" class="tabItem' . (($this->idTab == 2) ? " selected" : "" ) . '">' . $this->ayuda() . '</div>';
        $this->_html .= '</div>';
        $this->_html .= '<style>
                                #menuTab { float: left; padding: 0; margin: 0; text-align: left; }
                                #menuTab li { text-align: left; float: left; display: inline; padding: 5px; padding-right: 10px; background: #EFEFEF; font-weight: bold; cursor: pointer; border-left: 1px solid #EFEFEF; border-right: 1px solid #EFEFEF; border-top: 1px solid #EFEFEF; }
                                #menuTab li.menuTabButton.selected { background: #FFF6D3; border-left: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; border-top: 1px solid #CCCCCC; }
                                #tabList { clear: left; }
                                .tabItem { display: none; }
                                .tabItem.selected { display: block; background: #FFFFF0; border: 1px solid #CCCCCC; padding: 10px; padding-top: 20px; }
                        </style>
                        <script>
                                $(".menuTabButton").click(function () {
                                  $(".menuTabButton.selected").removeClass("selected");
                                  $(this).addClass("selected");
                                  $(".tabItem.selected").removeClass("selected");
                                  $("#" + this.id + "Sheet").addClass("selected");                                 
                                });
                        </script>';
    }

    private function ayuda() {
        $html = "<fieldset>";
        $html .='<legend>' . $this->l('Informax - Tfno: 986484538') . ' </legend>';
		$html .= '<h2>Mira nuestros modulos en http://tienda.informax.es</h2>';
        $html .= '</div></fieldset>';

        return $html;
    }

    private function configuracion() {
        $this->addCSS('css.css');
        $motores = '<select name="motor"><option value="innodb">InnoDB</option><option value="myisam">MyIsam</option></select>';
        $html  = '<fieldset>';
        $html .='<legend>' . $this->l('Importantes leer antes de usar') . ' </legend>';
        $html .= '<div>';
        $html .= $this->displayWarning($this->l('Informax no se hace responsable del mal uso del modulo, te aconsejamos que guardes los datos en un backup antes de realizar estos cambios'));
        $html .= $this->displayWarning($this->l('En la elección se pretende conseguir la mejor relación de calidad acorde con nuestra aplicación. Si necesitamos transacciones, claves foráneas y bloqueos, tendremos que escoger InnoDB. Por el contrario, escogeremos MyISAM en aquellos casos en los que predominen las consultas SELECT a la base de datos. Prestashop en su mayoria usa consultas SQL con lo que el cambio a MyIsam sera el Idoneo'));              
        $html .= '</div></fieldset>'; 
        $html .= '<fieldset>';
        $html .='<legend>' . $this->l('Operaciones') . ' </legend>';
        $html .= '<div>';
        $html .= '<form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="POST" enctype="multipart/form-data">';
        $html .= '<p>' . $this->l('Cambiar el motor de la base de datos:') . ' ' . $motores . ' <input type="submit" name="submit" value="' . $this->l('Ejecutar') . '"/></p>';
        $html .= '<input type="hidden" name="accion" value="cambiarMotor"/>';
        $html .= '<input type="hidden" name="idTab" value="1"/>';
        $html .= '</form>';
        $html .= '<form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="POST" enctype="multipart/form-data">';
        $html .= '<p>' . $this->l('Optimizar las tablas de la base de datos:').' <input type="submit" name="submit" value="' . $this->l('Ejecutar') . '"/></p>';
        $html .= '<input type="hidden" name="accion" value="optimizar"/>';
        $html .= '<input type="hidden" name="idTab" value="1"/>';
        $html .= '</form>';
		$html .= '<form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="POST" enctype="multipart/form-data">';
        $html .= '<p>' . $this->l('Reparar todas las tablas de la base de datos:').' <input type="submit" name="submit" value="' . $this->l('Ejecutar') . '"/></p>';
        $html .= '<input type="hidden" name="accion" value="reparar"/>';
        $html .= '<input type="hidden" name="idTab" value="1"/>';
        $html .= '</form>';	
		
        $html .= '</div></fieldset>';        
        $result = Db::getInstance()->executeS('SHOW TABLE STATUS FROM ' . _DB_NAME_);
        $html.= '<table class="table full100">';
        $html .= '<thead>';
        $html.= '<tr>';
        $html.= '<th>' . $this->l('Nombre Tabla') . '</th>';
        $html.= '<th>' . $this->l('Motor') . '</th>';
        $html.= '<th>' . $this->l('Collation') . '</th>';
        $html.= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        foreach ($result as $resultado) {
            $html.= '<tr>';
            $html.= '<td>' . $resultado['Name'] . '</td>';
            $html.= '<td>' . $resultado['Engine'] . '</td>';
            $html.= '<td>' . $resultado['Collation'] . '</td>';
            $html.= '</tr>';
        }
        $html .= '</tbody>';
        $html.= '</table>';
        return $html;
    }

    /**
     * Actualiza el motor de las tablas.
     * @param string $motor
     * @return array Un array con errores.
     */
    private function cambiarMotor($motor) {
        $errores = array();
        
        try {
            $tablas = Db::getInstance()->executeS('SHOW TABLES');
            foreach ($tablas as $tabla) {
                try {
                    $sqlAlter = "ALTER TABLE " . $tabla['Tables_in_' . _DB_NAME_] . " ENGINE=" . $motor . ';';
                    if (!Db::getInstance()->execute($sqlAlter)) {
                        $errores[] = Db::getInstance()->getMsgError();
                    }
                }
                catch (Exception $e) {
                    $errores[] = $e->getMessage();
                }
            }
        }
        catch (Exception $e) {
            $errores[] = $e->getMessage();
        }
        
        return $errores;
    }
    
    /**
     * Optimiza las tablas para que ocupen menos y sean mas rapidas.
     * @return array Un array con errores.
     */
    private function optimizarTablas() {
        $errores = array();
        
        try {
            $tablas = Db::getInstance()->executeS('SHOW TABLES');
            foreach ($tablas as $tabla) {
                try {
                    $sqlAlter = "OPTIMIZE TABLE " . $tabla['Tables_in_' . _DB_NAME_];
                    if (!Db::getInstance()->execute($sqlAlter)) {
                        $errores[] = Db::getInstance()->getMsgError();
                    }
                }
                catch (Exception $e) {
                    $errores[] = $e->getMessage();
                }
            }
        }
        catch (Exception $e) {
            $errores[] = $e->getMessage();
        }
        
        return $errores;   
    }
	
	    /**
     * Optimiza las tablas para que ocupen menos y sean mas rapidas.
     * @return array Un array con errores.
     */
    private function repararTablas() {
        $errores = array();
        
        try {
            $tablas = Db::getInstance()->executeS('SHOW TABLES');
            foreach ($tablas as $tabla) {
                try {
                    $sqlAlter = "REPAIR TABLE `" . $tabla['Tables_in_' . _DB_NAME_] ."` QUICK EXTENDED;";
                    if (!Db::getInstance()->execute($sqlAlter)) {
                        $errores[] = Db::getInstance()->getMsgError();
                    }
                }
                catch (Exception $e) {
                    $errores[] = $e->getMessage();
                }
            }
        }
        catch (Exception $e) {
            $errores[] = $e->getMessage();
        }
        
        return $errores;   
    }
	

    public function hookDisplayBackOfficeHeader($params) {
        $this->context->controller->addCSS($this->_path . 'css/iconoImax.css');

        return '';
    }

    /**
     * Crea un nuevo tab.
     * @param string $clase
     * @param string $nombre
     * @param string $padre
     * @return boolean
     */
    private function crearTab($clase, $nombre, $padre = '') {
        if (!Tab::getIdFromClassName($clase)) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = $clase;
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang) {
                $tab->name[$lang['id_lang']] = $nombre;
            }
            if ($padre == '') {
                $posicion = 0;
            }
            else {
                $posicion = Tab::getIdFromClassName($padre);
            }
            $tab->id_parent = intval($posicion);
            $tab->module = $this->name;
            try {
                if (!$tab->add()) {
                    return false;
                }
            }
            catch (Exception $exc) {
                return false;
            }
        }

        return true;
    }

    /**
     * Borra un tab.
     * @param string $clase
     * @return boolean
     */
    private function borrarTab($clase) {
        $id_tab = (int) Tab::getIdFromClassName($clase);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            try {
                if (!$tab->delete()) {
                    return false;
                }
            }
            catch (Exception $exc) {
                return false;
            }
        }

        return true;
    }

    /**
     * Instala los tabs del módulo.
     * @return boolean
     */
    private function installTab() {
        include(dirname(__FILE__) . '/configuration.php');

        //Instalamos el root
        if (isset($moduleTabRoot) && $moduleTabRoot) {
            $this->crearTab($moduleTabRoot['clase'], $moduleTabRoot['name']);
        }

        //Instalamos el resto de tabs
        if (isset($moduleTabs) && $moduleTabs) {
            foreach ($moduleTabs AS $moduleTab) {
                $this->borrarTab($moduleTab['clase']);
                if (!$this->crearTab($moduleTab['clase'], $moduleTab['name'], $moduleTab['padre'])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Desinstala los tabs del módulo.
     * @return boolean
     */
    private function uninstallTab() {
        include(dirname(__FILE__) . '/configuration.php');

        //Desinstalamos las tabs de este módulo
        if (isset($moduleTabs) && $moduleTabs) {
            foreach ($moduleTabs AS $moduleTab) {
                if (!$this->borrarTab($moduleTab['clase'])) {
                    return false;
                }
            }
        }

        //Desinstalamos el root si está vacío
        if (isset($moduleTabRoot) && $moduleTabRoot) {
            $id_tab = (int) Tab::getIdFromClassName($moduleTabRoot['clase']);
            if ($id_tab && Tab::getNbTabs($id_tab) == 0) {
                if (!$this->borrarTab($moduleTabRoot['clase'])) {
                    return false;
                }
            }
        }

        return true;
    }

    function addCSS($css) {
        echo '<link type="text/css" rel="stylesheet" href="../modules/' . $this->name . '/css/' . $css . '" />' . "\n";
        return;
    }

    public function displayWarning($error) {
        if ($this->versionPS === 15) {
            $output = '
		<div class="module_error alert warn">
			' . $error . '
		</div>';
        }
        else {
            $output = '<div class="bootstrap">'
                    . '<p class="warning warn alert alert-warning">'
                    . $error .
                    '</p></div>';
        }
        return $output;
    }

}
