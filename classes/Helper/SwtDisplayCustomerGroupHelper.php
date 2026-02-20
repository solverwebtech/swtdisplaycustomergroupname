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

namespace Kaleem\SwtDisplayCustomerGroupName\Helper;

use Configuration;
use Context;
use Db;
use DbQuery;
use Group;
use Kaleem\SwtDisplayCustomerGroupName\SwtCustomerGroupDisplay;
use Language;
use Tab;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class SwtDisplayCustomerGroupHelper
 *
 * Provides reusable helper methods for the
 * swtdisplaycustomergroupname module.
 */
class SwtDisplayCustomerGroupHelper
{
    /**
     * Install admin tab.
     *
     * @param string $moduleName Module technical name.
     *
     * @return bool
     */
    public static function installTab(string $moduleName): bool
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminSwtCustomerGroupDisplay';
        $tab->module = $moduleName;
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentCustomer');

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[(int) $lang['id_lang']] = 'FO Profile Group Name';
        }

        return $tab->add();
    }

    /**
     * Uninstall admin tab.
     *
     * @return bool
     */
    public static function uninstallTab(): bool
    {
        $idTab = (int) Tab::getIdFromClassName('AdminSwtCustomerGroupDisplay');

        if ($idTab <= 0) {
            return true;
        }

        $tab = new Tab($idTab);

        return $tab->delete();
    }

    /**
     * Create display entries for all existing customer groups.
     *
     * This method ensures every customer group has a corresponding
     * SwtCustomerGroupDisplay record.
     *
     * @return bool
     */
    public static function initializeGroupDisplayRecords(): bool
    {
        $idLangDefault = (int) Context::getContext()->language->id;

        $groups = Group::getGroups($idLangDefault);

        if (empty($groups)) {
            return true;
        }

        foreach ($groups as $group) {
            $idGroup = (int) $group['id_group'];

            if (self::groupDisplayExists($idGroup)) {
                continue;
            }

            $display = new SwtCustomerGroupDisplay();
            $display->id_group = $idGroup;
            $display->active = false;

            foreach (Language::getLanguages(true) as $lang) {
                $display->display_name[(int) $lang['id_lang']] = $group['name'];
            }

            if (!$display->add()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a display record exists for a customer group.
     *
     * @param int $idGroup
     *
     * @return bool
     */
    private static function groupDisplayExists(int $idGroup): bool
    {
        $query = new DbQuery();
        $query->select('id_swt_customer_group_display');
        $query->from('swt_customer_group_display');
        $query->where('id_group = ' . (int) $idGroup);

        return (bool) Db::getInstance()->getValue($query);
    }

    /**
     * Redirect admin to module main controller.
     *
     * @return void
     */
    public static function redirectToAdminController(): void
    {
        $context = Context::getContext();

        $link = $context->link->getAdminLink(
            'AdminSwtCustomerGroupDisplay',
            true
        );

        Tools::redirectAdmin($link);
    }

    /**
     * Get current context group ID (logged or not).
     *
     * @return int
     */
    public static function getCustomerCurrentGroupId(): int
    {
        $customer = Context::getContext()->customer;

        // Logged customer
        if ($customer->isLogged(true)) {
            return (int) $customer->id_default_group;
        }

        // Not logged → Visitor group
        return (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
    }
}
