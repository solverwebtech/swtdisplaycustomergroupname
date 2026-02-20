<?php

/**
 * 2007-2026 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2026 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use Kaleem\SwtDisplayCustomerGroupName\SwtCustomerGroupDisplay;
use Kaleem\SwtDisplayCustomerGroupName\Helper\SwtDisplayCustomerGroupHelper;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class SwtDisplayCustomerGroupName extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'swtdisplaycustomergroupname';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Kaleem Ullah | SolverWebTech | Freelance';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Display Customer Group Name');
        $this->description = $this->l('This module will show customer group name with profile icon or anywhere with custom hook.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '9.0');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        require_once __DIR__ . '/sql/install.php';
        Configuration::updateValue('SWTDISPLAYCUSTOMERGROUPNAME_DISPLAY_METHOD_JS', false);
        // for desktop theme compatibility, default selectors are set to empty. User can set them according to their theme structure.
        Configuration::updateValue('SWTDISPLAYCUSTOMERGROUPNAME_ICON_SELECTOR_JS', '');
        Configuration::updateValue('SWTDISPLAYCUSTOMERGROUPNAME_NAME_SELECTOR_JS', '');
        // for mobile theme compatibility, default selectors are set to common mobile theme selectors. User can change them if not compatible with their theme.
        Configuration::updateValue('SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_ICON_SELECTOR_JS', '');
        Configuration::updateValue('SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_NAME_SELECTOR_JS', '');

        return parent::install()
            && installSql()
            && SwtDisplayCustomerGroupHelper::installTab($this->name)
            && SwtDisplayCustomerGroupHelper::initializeGroupDisplayRecords()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displaySwtCustomerProfileIcon')
            && $this->registerHook('displaySwtCustomerGroupName');
    }

    public function uninstall()
    {
        require_once __DIR__ . '/sql/uninstall.php';
        Configuration::deleteByName('SWTDISPLAYCUSTOMERGROUPNAME_DISPLAY_METHOD_JS');
        Configuration::deleteByName('SWTDISPLAYCUSTOMERGROUPNAME_ICON_SELECTOR_JS');
        Configuration::deleteByName('SWTDISPLAYCUSTOMERGROUPNAME_NAME_SELECTOR_JS');
        Configuration::deleteByName('SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_ICON_SELECTOR_JS');
        Configuration::deleteByName('SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_NAME_SELECTOR_JS');

        return
            uninstallSql()
            && SwtDisplayCustomerGroupHelper::uninstallTab()
            && parent::uninstall();
    }



    /**
     * Load the configuration form
     */
    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitSwtDisplayCustomerGroupNameModule')) == true) {
            $this->postProcess();
        }

        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSwtDisplayCustomerGroupNameModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Use JavaScript OR Hook'),
                        'name' => 'SWTDISPLAYCUSTOMERGROUPNAME_DISPLAY_METHOD_JS',
                        'is_bool' => true,
                        'desc' => $this->l('Enable will use JavaScript to display icon and customer group'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'SWTDISPLAYCUSTOMERGROUPNAME_ICON_SELECTOR_JS',
                        'label' => $this->l('Icon JS Selector'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'SWTDISPLAYCUSTOMERGROUPNAME_NAME_SELECTOR_JS',
                        'label' => $this->l('Customer Name JS Selector'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_ICON_SELECTOR_JS',
                        'label' => $this->l('Mobile Icon JS Selector'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_NAME_SELECTOR_JS',
                        'label' => $this->l('Mobile Customer Name JS Selector'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'SWTDISPLAYCUSTOMERGROUPNAME_ICON_SELECTOR_JS' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_ICON_SELECTOR_JS'),
            'SWTDISPLAYCUSTOMERGROUPNAME_DISPLAY_METHOD_JS' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_DISPLAY_METHOD_JS'),
            'SWTDISPLAYCUSTOMERGROUPNAME_NAME_SELECTOR_JS' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_NAME_SELECTOR_JS'),
            'SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_ICON_SELECTOR_JS' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_ICON_SELECTOR_JS'),
            'SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_NAME_SELECTOR_JS' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_NAME_SELECTOR_JS'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        $this->context->controller->confirmations[] = $this->l('Update successful');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        if (!Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_DISPLAY_METHOD_JS')) {
            return;
        }

        Media::addJsDef([
            'swtdisplaycustomergroupname_js' => [
                'group_data' => SwtCustomerGroupDisplay::getCurrentGroupDisplay($this->context),
                'icon_js_selector' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_ICON_SELECTOR_JS'),
                'name_js_selector' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_NAME_SELECTOR_JS'),
                'mobile_icon_js_selector' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_ICON_SELECTOR_JS'),
                'mobile_name_js_selector' => Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_MOBILE_NAME_SELECTOR_JS'),
            ],
        ]);

        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    /**
     * Render customer profile icon.
     *
     * @return string
     */
    public function hookDisplaySwtCustomerProfileIcon(): string
    {
        if (Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_DISPLAY_METHOD_JS')) {
            return '';
        }

        $groupData = SwtCustomerGroupDisplay::getCurrentGroupDisplay($this->context);

        $this->context->smarty->assign([
            'group_icon_url' => $groupData['icon_url'],
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/profile-icon.tpl');
    }

    /**
     * Render customer group display name and icon.
     *
     * @return string
     */
    public function hookDisplaySwtCustomerGroupName(): string
    {
        if (Configuration::get('SWTDISPLAYCUSTOMERGROUPNAME_DISPLAY_METHOD_JS')) {
            return '';
        }

        $groupData = SwtCustomerGroupDisplay::getCurrentGroupDisplay($this->context);

        $this->context->smarty->assign([
            'group_name' => $groupData['name'],
        ]);

        return $this->fetch('module:' . $this->name . '/views/templates/hook/group-name.tpl');
    }
}
