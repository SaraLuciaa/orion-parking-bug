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

class WkBookingsOrders extends ObjectModel
{
    public $id_cart;
    public $id_order;
    public $is_canceled;
    public $id_product;
    public $id_product_attribute;
    public $booking_type;
    public $quantity;
    public $date_from;
    public $date_to;
    public $time_from;
    public $time_to;
    public $product_real_price_tax_excl;
    public $product_real_price_tax_incl;
    public $range_feature_price_tax_incl;
    public $range_feature_price_tax_excl;
    public $total_order_tax_excl;
    public $total_order_tax_incl;
    public $consider_last_date;
    public $extra_info;
    public $is_scaned;

    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_bookings_orders',
        'primary' => 'id',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'is_canceled' => ['type' => self::TYPE_INT, 'shop' => true],
            'is_scaned' => ['type' => self::TYPE_INT, 'shop' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'booking_type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'date_from' => ['type' => self::TYPE_DATE, 'shop' => true],
            'date_to' => ['type' => self::TYPE_DATE, 'shop' => true],
            'time_from' => ['type' => self::TYPE_STRING, 'shop' => true],
            'time_to' => ['type' => self::TYPE_STRING, 'shop' => true],
            'product_real_price_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'shop' => true],
            'product_real_price_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'shop' => true],
            'range_feature_price_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'shop' => true],
            'range_feature_price_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'shop' => true],
            'total_order_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'shop' => true],
            'extra_info' => [
                'type' => self::TYPE_STRING, 'shop' => true,
            ],
            'total_order_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
            'consider_last_date' => ['type' => self::TYPE_INT, 'shop' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation('wk_bookings_orders', ['type' => 'shop', 'primary' => 'id']);
    }

    public function getBookingProductOrderInfo($idProduct, $idOrder, $bookingType = 0, $id_product_attribute = 0)
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_bookings_orders` wbo
                INNER JOIN `' . _DB_PREFIX_ . 'wk_bookings_orders_shop` wbos ON (wbos.`id` = wbo.`id`
                AND wbos.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                WHERE wbos.`id_product` = ' . (int) $idProduct . '
                AND wbos.`id_product_attribute` = ' . (int) $id_product_attribute . '
                AND wbos.`id_order` = ' . (int) $idOrder;
            if ($bookingType) {
                $sql .= ' AND wbos.`booking_type` = ' . (int) $bookingType;
            }
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_bookings_orders` wbo
                WHERE wbo.`id_product` = ' . (int) $idProduct . '
                AND wbo.`id_product_attribute` = ' . (int) $id_product_attribute . '
                AND wbo.`id_order` = ' . (int) $idOrder;
            if ($bookingType) {
                $sql .= ' AND wbo.`booking_type` = ' . (int) $bookingType;
            }
        }

        return Db::getInstance()->executeS(
            $sql,
        );
    }

    public function getOrderDetailsProductInfo($idOrder, $idProduct, $idProductAttribute)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'order_detail`
            WHERE `product_id` = ' . (int) $idProduct . '
            AND `product_attribute_id`  = ' . (int) $idProductAttribute . '
            AND `id_order` = ' . (int) $idOrder,
        );
    }

    public function updatePsOrderDetailsColumns($idOrder, $idProduct, $idProductAttribute, $updateInfo = [])
    {
        return Db::getInstance()->update(
            'order_detail',
            $updateInfo,
            '`product_id`=' . (int) $idProduct . '
            AND `product_attribute_id`=' . (int) $idProductAttribute . '
            AND `id_order`=' . (int) $idOrder . '
            AND `id_shop`=' . (int) Context::getContext()->shop->id,
        );
    }

    public function getProductOrderedQuantityInDateRange(
        $idProduct,
        $dateFrom,
        $dateTo,
        $inclCustomerCart = 0,
        $idCart = false,
        $id_product_attribute = 0
    ) {
        $orderedBookings = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_bookings_orders` wbo
            INNER JOIN `' . _DB_PREFIX_ . 'wk_bookings_orders_shop` wbos ON (wbos.`id` = wbo.`id`
            AND wbos.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbos.`id_product` = ' . (int) $idProduct . '
            AND wbos.`id_product_attribute` = ' . (int) $id_product_attribute . '
            AND wbos.`is_canceled` = 0
            AND IF(wbos.consider_last_date = 1,
            wbos.`date_from` <= \'' . pSql($dateTo) . '\',
            wbos.`date_from` < \'' . pSql($dateTo) . '\')
            AND IF(wbos.consider_last_date = 1,
            wbos.`date_to` >= \'' . pSql($dateFrom) . '\',
            wbos.`date_to` > \'' . pSql($dateFrom) . '\')',
        );
        if ($inclCustomerCart) {
            if (!$idCart) {
                if (isset(Context::getContext()->cart->id) && ($idCart = Context::getContext()->cart->id)) {
                    $idCart = Context::getContext()->cart->id;
                }
            }
            if ($idCart) {
                $totalDaySeconds = 24 * 60 * 60;
                if (!Configuration::get('WK_CONSIDER_DATE_TO')) {
                    $dateTo = date('Y-m-d', strtotime($dateTo) - $totalDaySeconds);
                }
                if ($cartBookings = Db::getInstance()->executeS(
                    'SELECT * FROM `' . _DB_PREFIX_ . 'wk_bookings_cart` wbc
                    INNER JOIN `' . _DB_PREFIX_ . 'wk_bookings_cart_shop` wbcs ON (wbcs.`id` = wbc.`id`
                    AND wbcs.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                    WHERE wbcs.`id_cart` = ' . (int) $idCart . '
                    AND wbcs.`id_product` = ' . (int) $idProduct . '
                    AND wbcs.`id_product_attribute` = ' . (int) $id_product_attribute . '
                    AND wbcs.`date_from` <= \'' . pSql($dateTo) . '\'
                    AND IF(wbcs.consider_last_date = 1, wbcs.`date_to` >= \'' . pSql($dateFrom) . '\', wbcs.`date_to` > \'' .
                    pSql($dateFrom) . '\')',
                )) {
                    $orderedBookings = array_merge($orderedBookings, $cartBookings);
                }
            }
        }

        $totalDaySeconds = 24 * 60 * 60;
        $bookedCount = 0;
        if ($orderedBookings) {
            $dateCovered = [];
            foreach ($orderedBookings as $booking) {
                $dateClashed = 0;
                if ($booking['consider_last_date']) {
                    $traverseToDate = strtotime($booking['date_to']);
                } else {
                    $traverseToDate = strtotime($booking['date_to']) - $totalDaySeconds;
                }
                $dateFrom = strtotime($booking['date_from']);
                for ($date = $dateFrom; $date <= $traverseToDate; $date = ($date + $totalDaySeconds)) {
                    if (!count($dateCovered)) {
                        $bookedCount = $booking['quantity'];
                    }
                    if (!in_array($date, $dateCovered)) {
                        $dateCovered[] = $date;
                    } else {
                        $dateClashed = 1;
                    }
                }
                if ($dateClashed) {
                    $bookedCount += $booking['quantity'];
                } elseif ($booking['quantity'] > $bookedCount) {
                    $bookedCount = $booking['quantity'];
                }
            }
        }

        return $bookedCount;
    }

    public function getProductTimeSlotOrderedQuantity(
        $idProduct,
        $date,
        $timeFrom,
        $timeTo,
        $inclCustomerCart = 0,
        $idCart = false
    ) {
        $date = date('Y-m-d', strtotime($date));
        $orderedQty = Db::getInstance()->getValue(
            'SELECT SUM(wbos.`quantity`) FROM `' . _DB_PREFIX_ . 'wk_bookings_orders` wbo
            INNER JOIN `' . _DB_PREFIX_ . 'wk_bookings_orders_shop` wbos ON (wbos.`id` = wbo.`id`
            AND wbos.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbos.`id_product` = ' . (int) $idProduct . '
            AND wbos.`is_canceled` = 0
            AND wbos.`date_from` = \'' . pSql($date) . '\'
            AND wbos.`time_from` = \'' . pSql($timeFrom) . '\'
            AND wbos.`time_to` = \'' . pSql($timeTo) . '\'',
        );
        if ($inclCustomerCart) {
            if (!$idCart) {
                if (isset(Context::getContext()->cart->id) && ($idCart = Context::getContext()->cart->id)) {
                    $idCart = Context::getContext()->cart->id;
                }
            }
            if ($idCart) {
                $cartQty = Db::getInstance()->getValue(
                    'SELECT SUM(wbcs.`quantity`) FROM `' . _DB_PREFIX_ . 'wk_bookings_cart` wbc
                    INNER JOIN `' . _DB_PREFIX_ . 'wk_bookings_cart_shop` wbcs ON (wbcs.`id` = wbc.`id`
                    AND wbcs.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                    WHERE wbcs.`id_cart` = ' . (int) $idCart . '
                    AND wbcs.`id_product` = ' . (int) $idProduct . '
                    AND wbcs.`date_from` = \'' . pSql($date) . '\'
                    AND wbcs.`time_from` = \'' . pSql($timeFrom) . '\'
                    AND wbcs.`time_to` = \'' . pSql($timeTo) . '\'',
                );
                $orderedQty += $cartQty;
            }
        }

        return $orderedQty;
    }

    public static function updateOrderCancelled($orderId)
    {
        $bookingOrders = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_bookings_orders`
            WHERE `id_order` = ' . (int) $orderId,
        );
        if (!empty($bookingOrders)) {
            foreach ($bookingOrders as $bookingOrder) {
                $bookingOrderObj = new WkBookingsOrders((int) $bookingOrder['id']);
                $bookingOrderObj->is_canceled = 1;
                $bookingOrderObj->update();
            }
        }
    }

    public static function getCustomerForOrder($searchQuery)
    {
        $queryMultishop = Shop::isFeatureActive() ? 's.`name` AS `from_shop_name`,' : '';
        $customers = Db::getInstance()->executeS(
            'SELECT c.`id_customer`, c.`email`, ' . $queryMultishop . ' CONCAT(c.`firstname`, \' \', c.`lastname`) as cname
            FROM `' . _DB_PREFIX_ . 'customer` c
            LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (c.`id_shop` = s.`id_shop`)
            WHERE c.`deleted` = 0 AND c.`is_guest` = 0 AND c.`active` = 1
            AND (
                c.`id_customer` = ' . (int) $searchQuery . '
                OR c.`email` LIKE "%' . pSQL($searchQuery) . '%"
                OR c.`firstname` LIKE "%' . pSQL($searchQuery) . '%"
                OR c.`lastname` LIKE "%' . pSQL($searchQuery) . '%"
            )
            ORDER BY c.`firstname`, c.`lastname` ASC
            LIMIT 50',
        );

        return $customers;
    }

    public static function getPSOrderIdById($id)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_order` FROM `' . _DB_PREFIX_ . 'wk_bookings_orders`
            WHERE `id` = ' . (int) $id,
        );
    }

    public static function getAllBooking()
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $sql = 'SELECT wbos.`id`, wbos.`id_order`, wbos.`id_product`, wbos.`id_product_attribute`, wbos.`quantity`, wbos.`booking_type`, wbos.`date_from`, wbos.`date_to`, wbos.`time_from`, wbos.`time_to` FROM `' . _DB_PREFIX_ . 'wk_bookings_orders` wbo
                INNER JOIN `' . _DB_PREFIX_ . 'wk_bookings_orders_shop` wbos ON (wbos.`id` = wbo.`id`
                AND wbos.`id_shop` = ' . (int) Context::getContext()->shop->id . ')';
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_bookings_orders` wbo';
        }

        return Db::getInstance()->executeS(
            $sql,
        );
    }

    public static function getProductName($idOrder, $idProduct, $idProductAttribute)
    {
        $sql = 'SELECT `product_name` FROM `' . _DB_PREFIX_ . 'order_detail`
                WHERE `id_shop` = ' . (int) Context::getContext()->shop->id . ' AND `id_order` = ' . (int) $idOrder . ' AND `product_id` = ' . (int) $idProduct . ' AND `product_attribute_id` = ' . (int) $idProductAttribute;

        return Db::getInstance()->getValue(
            $sql,
        );
    }
}
