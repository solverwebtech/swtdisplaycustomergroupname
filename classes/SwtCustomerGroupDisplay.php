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

namespace Kaleem\SwtDisplayCustomerGroupName;

use Context;
use Db;
use DbQuery;
use Kaleem\SwtDisplayCustomerGroupName\Helper\SwtDisplayCustomerGroupHelper;
use ObjectModel;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class SwtCustomerGroupDisplay
 *
 * Represents a custom display configuration for a customer group.
 *
 * @property int $id_swt_customer_group_display
 * @property int $id_group
 * @property bool $active
 * @property string|null $icon
 * @property string $date_add
 * @property string $date_upd
 *
 * @property array<int, string> $display_name
 */
class SwtCustomerGroupDisplay extends ObjectModel
{
    /**
     * @var int
     */
    public $id_swt_customer_group_display;

    /**
     * @var int
     */
    public $id_group;

    /**
     * @var bool
     */
    public $active = true;

    /**
     * @var string|null
     */
    public $icon;

    /**
     * @var string
     */
    public $date_add;

    /**
     * @var string
     */
    public $date_upd;

    /**
     * @var array<int, string>
     */
    public $display_name = [];

    /**
     * @var array<string, mixed>
     */
    public static $definition = [
        'table' => 'swt_customer_group_display',
        'primary' => 'id_swt_customer_group_display',
        'multilang' => true,
        'fields' => [
            'id_group' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'active' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ],
            'icon' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 255,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
            'display_name' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 255,
            ],
        ],
    ];

    /**
     * Get display configuration by customer group ID.
     *
     * @param int $idGroup Customer group ID.
     *
     * @return static|null Returns SwtCustomerGroupDisplay instance or null if not found.
     */
    public static function getByGroupId(int $idGroup): ?self
    {
        if ($idGroup <= 0) {
            return null;
        }

        $query = new DbQuery();
        $query->select('scgd.id_swt_customer_group_display');
        $query->from('swt_customer_group_display', 'scgd');
        $query->where('scgd.id_group = ' . (int) $idGroup);

        $id = (int) Db::getInstance()->getValue($query);

        if ($id <= 0) {
            return null;
        }

        return new self($id);
    }

    /**
     * Get translated display name.
     *
     * @param int $idLang
     *
     * @return string|null
     */
    public function getDisplayName(int $idLang): ?string
    {
        return $this->display_name[$idLang] ?? null;
    }

    /**
     * Get current customer group display data (name + icon).
     *
     * Works for:
     * - Logged customers
     * - Visitors (unidentified group)
     *
     * @param Context|null $context
     *
     * @return array{name: string, icon_url: string|null}
     */
    public static function getCurrentGroupDisplay(?Context $context = null): array
    {
        $context = $context ?? Context::getContext();

        $idGroup = (int) SwtDisplayCustomerGroupHelper::getCustomerCurrentGroupId($context);
        $idLang  = (int) $context->language->id;

        $result = [
            'name' => '',
            'icon_url' => null,
        ];

        $display = SwtCustomerGroupDisplay::getByGroupId($idGroup);

        if ($display && (bool) $display->active) {


            // Name (multilang safe)
            if (isset($display->display_name[$idLang])) {
                $result['name'] = (string) $display->display_name[$idLang];
            }

            // Icon
            if (!empty($display->icon)) {
                $moduleName = 'swtdisplaycustomergroupname';
                $imagePath  = _PS_MODULE_DIR_ . $moduleName . '/views/img/' . $display->icon;

                if (file_exists($imagePath)) {
                    $result['icon_url'] = Context::getContext()->link->getMediaLink(
                        _MODULE_DIR_ . $moduleName . '/views/img/' . $display->icon
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Delete the icon image file associated with this object.
     *
     * @param bool $force_delete
     *
     * @return bool
     */
    public function deleteImage($force_delete = false)
    {
        if (!$this->id || empty($this->icon)) {
            return true;
        }

        $moduleName = 'swtdisplaycustomergroupname';
        $filePath = _PS_MODULE_DIR_ . $moduleName . '/views/img/' . $this->icon;

        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                return false;
            }
        }

        // Clear the icon field in the database
        $this->icon = '';
        
        return $this->update();
    }
}
