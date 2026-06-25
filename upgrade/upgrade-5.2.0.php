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

function upgrade_module_5_2_0()
{
    $queries = [
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices`
        ADD `slot_day` int(10) unsigned NOT NULL AFTER `id_product`;',
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_booking_time_slots_prices`
        ADD `time_slot_type` int(10) unsigned NOT NULL DEFAULT '2' AFTER `slot_day`;",
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
