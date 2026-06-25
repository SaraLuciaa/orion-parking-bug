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

function upgrade_module_5_3_0()
{
    $queries = [
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_booking_product_info`
        ADD `show_map` TINYINT NOT NULL DEFAULT '0' AFTER `active`,
        ADD `address` TEXT NULL AFTER `show_map`,
        ADD `latitude` DECIMAL(13,8) NULL AFTER `address`,
        ADD `longitude` DECIMAL(13,8) NULL AFTER `latitude`;",
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_booking_product_info_shop` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) unsigned NOT NULL,
            `id_product` int(10) unsigned NOT NULL,
            `quantity` int(10) unsigned NOT NULL DEFAULT '0',
            `booking_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
            `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `show_map` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `address` TEXT NULL,
            `latitude` DECIMAL(13,8) NULL,
            `longitude` DECIMAL(13,8),
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_booking_time_slots_prices_shop` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) unsigned NOT NULL,
            `id_product` int(10) unsigned NOT NULL,
            `slot_day` int(10) unsigned NOT NULL,
            `time_slot_type` int(10) unsigned NOT NULL,
            `date_from` datetime NOT NULL,
            `date_to` datetime NOT NULL,
            `time_slot_from` varchar(255) DEFAULT NULL,
            `time_slot_to` varchar(255) DEFAULT NULL,
            `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
            `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_booking_product_feature_pricing_shop` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) unsigned NOT NULL,
            `id_product` int(11) NOT NULL,
            `feature_price_name` varchar(64) NOT NULL,
            `date_from` date NOT NULL,
            `date_to` date NOT NULL,
            `is_special_days_exists` tinyint(1) NOT NULL,
            `date_selection_type` tinyint(1) NOT NULL,
            `special_days` text,
            `impact_way` tinyint(1) NOT NULL,
            `impact_type` tinyint(1) NOT NULL,
            `impact_value` decimal(20,6) NOT NULL DEFAULT '0.000000',
            `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_bookings_cart_shop` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) unsigned NOT NULL,
            `id_cart` int(11) NOT NULL,
            `id_product` int(11) NOT NULL,
            `quantity` int(11) NOT NULL,
            `booking_type` tinyint(4) NOT NULL,
            `date_from` datetime NOT NULL,
            `date_to` datetime NOT NULL,
            `time_from` varchar(255) DEFAULT NULL,
            `time_to` varchar(255) DEFAULT NULL,
            `consider_last_date` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_bookings_orders_shop` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(10) unsigned NOT NULL,
            `id_cart` int(11) NOT NULL,
            `id_order` int(11) NOT NULL,
            `id_product` int(11) NOT NULL,
            `quantity` int(11) NOT NULL,
            `booking_type` tinyint(4) NOT NULL,
            `date_from` datetime NOT NULL,
            `date_to` datetime NOT NULL,
            `time_from` varchar(255) DEFAULT NULL,
            `time_to` varchar(255) DEFAULT NULL,
            `product_real_price_tax_excl` decimal(20,6) NOT NULL,
            `product_real_price_tax_incl` decimal(20,6) NOT NULL,
            `range_feature_price_tax_incl` decimal(20,6) NOT NULL,
            `range_feature_price_tax_excl` decimal(20,6) NOT NULL,
            `total_order_tax_excl` decimal(20,6) NOT NULL,
            `total_order_tax_incl` decimal(20,6) NOT NULL,
            `consider_last_date` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id`, `id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;',
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
