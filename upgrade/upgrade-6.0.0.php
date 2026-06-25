<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_6_0_0($module)
{
    Configuration::updateValue('WK_SHOW_BOOKING_EVENT_PAGE', 1);
    Configuration::updateValue('WK_SHOW_EVENT_PAGE_WHATSAPP', 1);
    Configuration::updateValue('WK_SHOW_EVENT_PAGE_FACEBOOK', 1);
    Configuration::updateValue('WK_SHOW_EVENT_PAGE_INSTAGRAM', 1);
    Configuration::updateValue('WK_SHOW_EVENT_PAGE_TWITTER', 1);
    Configuration::updateValue('WK_SHOW_EVENT_PAGE_CLIPBOARD', 1);
    Configuration::updateValue('WK_SHOW_RELATED_EVENTS', 1);

    $queries = [
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_booking_product_info`
        ADD `min_days_booking` int(10) unsigned NOT NULL DEFAULT '0' AFTER `booking_before`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_booking_product_info_shop`
        ADD `min_days_booking` int(10) unsigned NOT NULL DEFAULT '0' AFTER `booking_before`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_booking_product_info`
        ADD `max_days_booking` int(10) unsigned NOT NULL DEFAULT '0' AFTER `booking_before`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_booking_product_info_shop`
        ADD `max_days_booking` int(10) unsigned NOT NULL DEFAULT '0' AFTER `booking_before`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_bookings_cart`
        ADD `id_product_attribute` int(11) NOT NULL DEFAULT '0' AFTER `id_product`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_bookings_cart_shop`
        ADD `id_product_attribute` int(11) NOT NULL DEFAULT '0' AFTER `id_product`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_bookings_orders`
        ADD `id_product_attribute` int(11) NOT NULL DEFAULT '0' AFTER `id_product`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_bookings_orders_shop`
        ADD `id_product_attribute` int(11) NOT NULL DEFAULT '0' AFTER `id_product`",
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_bookings_orders`
        ADD `extra_info` TEXT NULL AFTER `is_canceled`',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_bookings_orders_shop`
        ADD `extra_info` TEXT NULL AFTER `is_canceled`',
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_bookings_orders`
        ADD `is_scaned` INT NOT NULL DEFAULT '0' AFTER `extra_info`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_bookings_orders_shop`
        ADD `is_scaned` INT NOT NULL DEFAULT '0' AFTER `extra_info`",
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_booking_product_extra_info` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_product` int(10) unsigned NOT NULL,
            `date_from` date NOT NULL,
            `date_to` date NOT NULL,
            `time_from` varchar(255) DEFAULT NULL,
            `time_to` varchar(255) DEFAULT NULL,
            `multiple_slot` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_booking_product_extra_info_shop` (
            `id` int(11) NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
            `id_product` int(10) unsigned NOT NULL,
            `date_from` date NOT NULL,
            `date_to` date NOT NULL,
            `time_from` varchar(255) DEFAULT NULL,
            `time_to` varchar(255) DEFAULT NULL,
            `multiple_slot` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1;',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_booking_product_extra_info_lang` (
            `id` int(11) unsigned NOT NULL,
            `id_shop` int(11) unsigned NOT NULL,
            `id_lang` int(11) NOT NULL,
            `language` TEXT NULL,
            `artist` TEXT NULL,
            `organized_by` TEXT NULL,
            `category` TEXT NULL,
            `age_group` TEXT NULL,
            PRIMARY KEY (`id`, `id_lang`,`id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1',
    ];
    $db = Db::getInstance();
    $success = true;
    foreach ($queries as $query) {
        $success &= $db->execute($query);
    }
    if ($success) {
        return true;
    }

    return true;
}
