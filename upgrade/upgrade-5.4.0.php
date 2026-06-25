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

function upgrade_module_5_4_0()
{
    $queries = [
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_bookings_orders`
        ADD `is_canceled` INT NOT NULL DEFAULT '0' AFTER `consider_last_date`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_bookings_orders_shop`
        ADD `is_canceled` INT NOT NULL DEFAULT '0' AFTER `consider_last_date`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_booking_time_slots_prices`
        ADD `quantity` INT NOT NULL DEFAULT '0' AFTER `price`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_booking_time_slots_prices_shop`
        ADD `quantity` INT NOT NULL DEFAULT '0' AFTER `price`",
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
