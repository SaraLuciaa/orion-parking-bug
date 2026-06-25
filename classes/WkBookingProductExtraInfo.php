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

class WkBookingProductExtraInfo extends ObjectModel
{
    public $id_product;
    public $date_from;
    public $date_to;
    public $time_from;
    public $time_to;
    public $language;
    public $artist;
    public $organized_by;
    public $category;
    public $age_group;
    public $multiple_slot;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_booking_product_extra_info',
        'primary' => 'id',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'id_product' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
                'shop' => true,
            ],
            'date_from' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
            'date_to' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
            'time_from' => ['type' => self::TYPE_STRING, 'required' => true, 'shop' => true],
            'time_to' => ['type' => self::TYPE_STRING, 'required' => true, 'shop' => true],
            'multiple_slot' => ['type' => self::TYPE_INT, 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'shop' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'shop' => true],

            // multi lang
            'language' => ['type' => self::TYPE_STRING, 'lang' => true],
            'artist' => ['type' => self::TYPE_STRING, 'lang' => true],
            'organized_by' => ['type' => self::TYPE_STRING, 'lang' => true],
            'category' => ['type' => self::TYPE_STRING, 'lang' => true],
            'age_group' => ['type' => self::TYPE_STRING, 'lang' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation('wk_booking_product_extra_info', ['type' => 'shop', 'primary' => 'id']);
        Shop::addTableAssociation('wk_booking_product_extra_info', ['type' => 'fk_shop', 'primary' => 'id']);
    }

    public static function getAlreadyAddedByProductId($idProduct)
    {
        $sql = 'SELECT wbpeis.*, wkbpelang.* FROM `' . _DB_PREFIX_ . 'wk_booking_product_extra_info` wbpei
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_extra_info_shop` wbpeis ON (wbpeis.`id` = wbpei.`id`
            AND wbpeis.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_booking_product_extra_info_lang` wkbpelang
            ON (wbpei.`id` = wkbpelang.`id` AND wkbpelang.`id_lang` = ' . (int) Context::getContext()->language->id . ')
            WHERE wbpeis.`id_product` = ' . (int) $idProduct;
        $sql .= Shop::addSqlRestrictionOnLang('wkbpelang');

        return Db::getInstance()->getRow($sql);
    }

    public static function getExtraInfoForOrder($idProduct, $idShop, $idLang)
    {
        $sql = 'SELECT wkbpelang.`language`, wkbpelang.`artist`, wkbpelang.`organized_by`, wkbpelang.`category`, wkbpelang.`age_group` FROM `' . _DB_PREFIX_ . 'wk_booking_product_extra_info` wbpei
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_extra_info_shop` wbpeis ON (wbpeis.`id` = wbpei.`id`
            AND wbpeis.`id_shop` = ' . (int) $idShop . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_booking_product_extra_info_lang` wkbpelang
            ON (wbpei.`id` = wkbpelang.`id` AND wkbpelang.`id_lang` = ' . (int) $idLang . ')
            WHERE wbpeis.`id_product` = ' . (int) $idProduct;
        $sql .= Shop::addSqlRestrictionOnLang('wkbpelang', $idLang);

        return Db::getInstance()->getRow($sql);
    }

    public static function isMultiSlotEnable($idProduct)
    {
        $sql = 'SELECT wbpeis.`multiple_slot` FROM `' . _DB_PREFIX_ . 'wk_booking_product_extra_info` wbpei
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_extra_info_shop` wbpeis ON (wbpeis.`id` = wbpei.`id`
            AND wbpeis.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbpeis.`id_product` = ' . (int) $idProduct;

        return Db::getInstance()->getValue($sql);
    }

    public static function getEventTimeDuration($idProduct)
    {
        $slots = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct,
        );
        $durations = [];
        if (!empty($slots)) {
            foreach ($slots as $slot) {
                $timeFrom = $slot['time_slot_from'];
                $timeTo = $slot['time_slot_to'];
                $mintues = (strtotime($timeTo) - strtotime($timeFrom)) / 60;
                if (!in_array($mintues, $durations)) {
                    $durations[] = $mintues;
                }
            }
        }

        return $durations;
    }

    public static function getAllRunningEvents($exculdeProductId = 0)
    {
        $currentDate = date('Y-m-d');
        $sql = 'SELECT wbpeis.* FROM `' . _DB_PREFIX_ . 'wk_booking_product_extra_info` wbpei
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_extra_info_shop` wbpeis ON (wbpeis.`id` = wbpei.`id`
            AND wbpeis.`id_shop` = ' . (int) Context::getContext()->shop->id . ')';
        $sql .= ' WHERE wbpeis.`date_to` >= \'' . pSQL($currentDate) . '\'';

        if ($exculdeProductId) {
            $sql .= ' AND wbpeis.`id_product` != ' . (int) $exculdeProductId;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getByDateRange($idProduct, $dateFrom, $dateTo)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_extra_info` wbpei
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_extra_info_shop` wbpeis ON (wbpeis.`id` = wbpei.`id`
            AND wbpeis.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE (wbpeis.`id_product` = ' . (int) $idProduct . ')
            AND wbpeis.`date_from` <= \'' . pSQL($dateTo) . '\'
            AND wbpeis.`date_to` >= \'' . pSQL($dateFrom) . '\'',
        );
    }
}
