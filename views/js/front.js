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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function() {
    if (typeof swtdisplaycustomergroupname_js !== 'undefined') {
        const groupData = swtdisplaycustomergroupname_js.group_data;
        const iconSelector = swtdisplaycustomergroupname_js.icon_js_selector;
        const nameSelector = swtdisplaycustomergroupname_js.name_js_selector;
        const mobileIconSelector = swtdisplaycustomergroupname_js.mobile_icon_js_selector;
        const mobileNameSelector = swtdisplaycustomergroupname_js.mobile_name_js_selector;

        if (groupData) {
            // replace the current icon html tag with a new one if selector is provided and element exists
            if (iconSelector) {
                const iconElement = document.querySelector(iconSelector);
                if (iconElement && groupData.icon_url) {
                    const newIcon = document.createElement('img');
                    newIcon.src = groupData.icon_url;
                    newIcon.alt = groupData.name;
                    newIcon.classList.add('swt-customer-group-icon');
                    newIcon.width = 24;
                    newIcon.height = 24;
                    iconElement.replaceWith(newIcon);
                }
            }

            // replace the current mobile icon html tag with a new one if selector is provided and element exists
            if (mobileIconSelector) {
                const mobileIconElement = document.querySelector(mobileIconSelector);
                if (mobileIconElement && groupData.icon_url) {
                    const newMobileIcon = document.createElement('img');
                    newMobileIcon.src = groupData.icon_url;
                    newMobileIcon.alt = groupData.name;
                    newMobileIcon.classList.add('swt-customer-group-mobile-icon');
                    newMobileIcon.width = 24;
                    newMobileIcon.height = 24;
                    mobileIconElement.replaceWith(newMobileIcon);
                }
            }

            // replace the current mobile name html tag with a new one if selector is provided and element exists
            if (mobileNameSelector) {
                const mobileNameElement = document.querySelector(mobileNameSelector);
                if (mobileNameElement && groupData.name) {
                    const newMobileName = document.createElement('span');
                    newMobileName.textContent = ' (' + groupData.name + ')';
                    newMobileName.classList.add('swt-customer-group-mobile-name');
                    mobileNameElement.appendChild(newMobileName);
                }
            }

            // append the group name text if selector is provided and element exists
            if (nameSelector) {
                const nameElement = document.querySelector(nameSelector);
                if (nameElement && groupData.name) {
                    // Create new span element for the group name
                    const span = document.createElement('span');
                    span.textContent = ' (' + groupData.name + ')';
                    span.classList.add('swt-customer-group-name');
                    // Append the group name to the element
                    nameElement.appendChild(span);
                }
            }
        }
    }
});