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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AdminSwtCustomerGroupDisplayController
 *
 * Back-office controller to manage custom display names
 * for customer groups.
 */
class AdminSwtCustomerGroupDisplayController extends ModuleAdminController
{
    /**
     * AdminSwtCustomerGroupDisplayController constructor.
     */
    public function __construct()
    {
        $this->table = 'swt_customer_group_display';
        $this->className = SwtCustomerGroupDisplay::class;
        $this->lang = true;
        $this->bootstrap = true;

        parent::__construct();

        $this->_select = 'gl.name AS group_name';

        $this->_join = '
            LEFT JOIN ' . _DB_PREFIX_ . 'group_lang gl 
                ON (gl.id_group = a.id_group 
                AND gl.id_lang = ' . (int) $this->context->language->id . ')
        ';

        $this->fields_list = [
            'id_swt_customer_group_display' => [
                'title' => $this->module->l('ID', 'AdminSwtCustomerGroupDisplay'),
                'class' => 'fixed-width-xs',
            ],
            'icon' => [
                'title' => $this->module->l('Icon', 'AdminSwtCustomerGroupDisplay'),
                'callback' => 'renderIconColumn',
                'orderby' => false,
                'search' => false,
                'align' => 'center',
            ],
            'group_name' => [
                'title' => $this->l('Customer Group'),
                'filter_key' => 'gl!name',
            ],
            'display_name' => [
                'title' => $this->module->l('FO Display Name', 'AdminSwtCustomerGroupDisplay'),
                'filter_key' => 'b!display_name',
            ],
            'active' => [
                'title' => $this->module->l('Active', 'AdminSwtCustomerGroupDisplay'),
                'type' => 'bool',
                'align' => 'center',
                'active' => 'status',
            ],
            'date_add' => [
                'title' => $this->module->l('Date Added', 'AdminSwtCustomerGroupDisplay'),
                'type' => 'datetime',
            ],
        ];

        /* -----------------------------
         * Row actions
         * ----------------------------- */
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        /** Bulk actions */
        $this->bulk_actions = [
            'enableSelection' => [
                'text' => $this->module->l('Enable selected', 'AdminSwtCustomerGroupDisplay'),
                'icon' => 'icon-power-off text-success',
                'confirm' => $this->module->l('Enable selected rules?', 'AdminSwtCustomerGroupDisplay'),
            ],
            'disableSelection' => [
                'text' => $this->module->l('Disable selected', 'AdminSwtCustomerGroupDisplay'),
                'icon' => 'icon-power-off text-danger',
                'confirm' => $this->module->l('Disable selected rules?', 'AdminSwtCustomerGroupDisplay'),
            ],
            'delete' => [
                'text' => $this->module->l('Delete selected', 'AdminSwtCustomerGroupDisplay'),
                'icon' => 'icon-trash',
                'confirm' => $this->module->l('Delete selected rules?', 'AdminSwtCustomerGroupDisplay'),
            ],
        ];
    }

    /**
     * Render icon column using Smarty template.
     *
     * @param string|null $icon
     * @param array $row
     *
     * @return string
     */
    public function renderIconColumn($icon, array $row): string
    {
        if (empty($icon)) {
            return '-';
        }

        $imagePath = _PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $icon;

        if (!file_exists($imagePath)) {
            return '-';
        }

        $thumbnail = ImageManager::thumbnail(
            $imagePath,
            $this->table . '_' . (int) $row['id_swt_customer_group_display'] . '_list.jpg',
            30,
            'jpg',
            true,
            true
        );

        return $thumbnail;
    }

    /**
     * Render form for create/edit.
     *
     * @return string
     */
    public function renderForm(): string
    {
        /** @var SwtCustomerGroupDisplay $object */
        $object = $this->loadObject(true);

        $groups = Group::getGroups($this->context->language->id);

        $groupOptions = [];
        foreach ($groups as $group) {
            $groupOptions[] = [
                'id_option' => (int) $group['id_group'],
                'name' => $group['name'],
            ];
        }

        $imageUrl = false;
        $imageSize = false;

        if ($object && !empty($object->icon)) {

            $imagePath = _PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $object->icon;

            if (file_exists($imagePath)) {

                $imageUrl = ImageManager::thumbnail(
                    $imagePath,
                    $this->table . '_' . (int) $object->id . '.jpg',
                    100, // thumbnail width in BO
                    'jpg',
                    true,
                    true
                );

                $imageSize = filesize($imagePath) / 1000;
            }
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Customer Group Display', 'AdminSwtCustomerGroupDisplay'),
                'icon' => 'icon-users',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->module->l('Customer Group', 'AdminSwtCustomerGroupDisplay'),
                    'name' => 'id_group',
                    'required' => true,
                    'options' => [
                        'query' => $groupOptions,
                        'id' => 'id_option',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'col' => 5,
                    'label' => $this->module->l('Display Name', 'AdminSwtCustomerGroupDisplay'),
                    'name' => 'display_name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->module->l('Custom name that will be displayed on the front office.', 'AdminSwtCustomerGroupDisplay'),
                ],
                [
                    'type' => 'file',
                    'label' => $this->module->l('Icon', 'AdminSwtCustomerGroupDisplay'),
                    'name' => 'icon',
                    'image' => $imageUrl ?: false,
                    'size' => $imageSize,
                    'display_image' => true,
                    'col' => 6,
                    'desc' => $this->module->l('Icon must be 30x30 pixels or smaller.', 'AdminSwtCustomerGroupDisplay'),
                    'delete_url' => $imageUrl ? $this->context->link->getAdminLink(
                        'AdminSwtCustomerGroupDisplay',
                        true,
                        [],
                        [
                            'update' . $this->table => 1,
                            $this->identifier => Tools::getValue($this->identifier),
                            'deleteImage' => 1,
                        ]
                    ) : null,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->module->l('Active', 'AdminSwtCustomerGroupDisplay'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminSwtCustomerGroupDisplay'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminSwtCustomerGroupDisplay'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save', 'AdminSwtCustomerGroupDisplay'),
            ],
        ];

        return parent::renderForm();
    }

    /**
     * Handle icon upload before saving.
     *
     * @return ObjectModel|false
     */
    public function processAdd()
    {
        $this->handleIconUpload();

        return parent::processAdd();
    }

    /**
     * Handle icon upload before update.
     *
     * @return ObjectModel|false
     */
    public function processUpdate()
    {
        /** @var SwtCustomerGroupDisplay $object */
        $object = $this->loadObject(true);

        // Preserve old icon if no new file uploaded
        if (empty($_FILES['icon']['name'])) {
            $_POST['icon'] = $object->icon;
        }

        $this->handleIconUpload();

        return parent::processUpdate();
    }

    /**
     * Handle icon upload and auto-delete old icon if replaced.
     *
     * @return void
     */
    protected function handleIconUpload(): void
    {
        if (empty($_FILES['icon']['name'])) {
            return;
        }

        $file = $_FILES['icon'];

        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $this->errors[] = $this->l('Invalid uploaded file.');
            return;
        }

        // Native image validation
        $error = ImageManager::validateUpload($file);

        if ($error) {
            $this->errors[] = $error;
            return;
        }

        // Check image dimensions
        $imageSize = @getimagesize($file['tmp_name']);

        if (!$imageSize) {
            $this->errors[] = $this->l('Unable to read image dimensions.');
            return;
        }

        $width  = (int) $imageSize[0];
        $height = (int) $imageSize[1];

        if ($width > 30 || $height > 30) {
            $this->errors[] = $this->l('Icon dimensions must be 30x30 pixels or smaller.');
            return;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid('group_', true) . '.' . $extension;

        $destination = _PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->errors[] = $this->l('Failed to save uploaded image.');
            return;
        }

        /** @var SwtCustomerGroupDisplay $object */
        $object = $this->loadObject(true);

        // Auto-delete old icon when editing
        if (!empty($object->icon)) {
            $oldFile = _PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $object->icon;

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $_POST['icon'] = $fileName;
    }
}
