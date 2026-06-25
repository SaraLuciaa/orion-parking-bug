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

class WkBookingProductFeaturePricing extends ObjectModel
{
    public $id_product;
    public $feature_price_name;
    public $date_selection_type;
    public $date_from;
    public $date_to;
    public $is_special_days_exists;
    public $special_days;
    public $impact_way;
    public $impact_type;
    public $impact_value;
    public $active;
    public $date_add;
    public $date_upd;
    public $moduleInstance;

    public static $definition = [
        'table' => 'wk_booking_product_feature_pricing',
        'primary' => 'id',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'feature_price_name' => ['type' => self::TYPE_STRING, 'shop' => true],
            'date_from' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
            'date_to' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
            'impact_way' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'is_special_days_exists' => ['type' => self::TYPE_INT, 'shop' => true],
            'date_selection_type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'special_days' => ['type' => self::TYPE_STRING, 'shop' => true],
            'impact_type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'impact_value' => ['type' => self::TYPE_FLOAT, 'shop' => true],
            'active' => ['type' => self::TYPE_INT, 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'shop' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'shop' => true],
        ], ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        $this->moduleInstance = Module::getInstanceByName('psbooking');
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation('wk_booking_product_feature_pricing', ['type' => 'shop', 'primary' => 'id']);
    }

    public function deleteFeaturePricePlansByIdProduct($idProduct)
    {
        $featurePlans = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps ON (wbpfps.`id` = wbpfp.`id`
            AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbpfps.`id_product` = ' . (int) $idProduct,
        );
        if (!empty($featurePlans)) {
            foreach ($featurePlans as $featurePlan) {
                $featurePricingObj = new WkBookingProductFeaturePricing((int) $featurePlan['id']);
                if (!$featurePricingObj->delete()) {
                    return false;
                }
            }
        }
    }

    /**
     * [getFeaturePriceByIdProduct returns Product all feature price plans].
     */
    public static function getFeaturePriceByIdProduct($idProduct)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps ON (wbpfps.`id` = wbpfp.`id`
            AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbpfps.`id_product` = ' . (int) $idProduct,
        );
    }

    /**
     * [getActiveFeaturePricesByIdProduct returns Product active feature price plans].
     */
    public static function getActiveFeaturePricesByIdProduct($idProduct)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps ON (wbpfps.`id` = wbpfp.`id`
            AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbpfps.`id_product` = ' . (int) $idProduct .
            ' AND wbpfps.`active` = 1',
        );
    }

    /**
     * [getBookingProductActiveFeaturePricesByDateRange returns Product active feature price plans by
     * supplied date Range].
     */
    public static function getBookingProductActiveFeaturePricesByDateRange(
        $idProduct,
        $dateFrom,
        $dateTo
    ) {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps ON (wbpfps.`id` = wbpfp.`id`
            AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE (wbpfps.`id_product`= 0 OR wbpfps.`id_product` = ' . (int) $idProduct . ')
            AND wbpfps.`active` = 1 AND wbpfps.`date_from` <= \'' . pSQL($dateTo) . '\'
            AND wbpfps.`date_to` >= \'' . pSQL($dateFrom) . '\'',
        );
    }

    /**
     * [checkBookingProductFeaturePriceExistance returns Product active feature price plan by supplied date Range
     * and supplied feature price plan type else returns false].
     */
    public function checkBookingProductFeaturePriceExistance(
        $idProduct,
        $dateFrom,
        $dateTo,
        $type = 'date_range',
        $currentSpecialDays = false,
        $idFeaturePrice = 0
    ) {
        if ($type == 'specific_date') {
            return Db::getInstance()->getRow(
                'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
                INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps ON (wbpfps.`id` = wbpfp.`id`
                AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                WHERE wbpfps.`id_product` = ' . (int) $idProduct . '
                AND wbpfps.`date_selection_type` = 2
                AND wbpfps.`date_from` = \'' . pSQL($dateFrom) . '\'
                AND wbpfps.`id`!= ' . (int) $idFeaturePrice,
            );
        } elseif ($type == 'special_day') {
            $featurePrice = Db::getInstance()->getRow(
                'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
                INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps ON (wbpfps.`id` = wbpfp.`id`
                AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                WHERE wbpfps.`id_product` = ' . (int) $idProduct . '
                AND wbpfps.`is_special_days_exists` = 1
                AND wbpfps.`active` = 1
                AND wbpfps.`date_from` < \'' . pSQL($dateTo) . '\'
                AND wbpfps.`date_to` > \'' . pSQL($dateFrom) . '\'
                AND wbpfps.`id`!= ' . (int) $idFeaturePrice,
            );
            if ($featurePrice) {
                $specialDays = json_decode($featurePrice['special_days']);
                $currentSpecialDays = json_decode($currentSpecialDays);
                $commonValues = array_intersect($specialDays, $currentSpecialDays);
                if ($commonValues) {
                    return $featurePrice;
                }
            }

            return false;
        } elseif ($type == 'date_range') {
            return Db::getInstance()->getRow(
                'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
                INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps ON (wbpfps.`id` = wbpfp.`id`
                AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                WHERE wbpfps.`id_product` = ' . (int) $idProduct . '
                AND wbpfps.`date_selection_type` = 1
                AND wbpfps.`is_special_days_exists`= 0
                AND wbpfps.`date_from` <= \'' . pSQL($dateTo) . '\'
                AND wbpfps.`date_to` >= \'' . pSQL($dateFrom) . '\'
                AND wbpfps.`id` != ' . (int) $idFeaturePrice,
            );
        }
    }

    /**
     * [countFeaturePriceSpecialDays returns number of special days between a date range].
     */
    public static function countFeaturePriceSpecialDays($specialDays, $dateFrom, $dateTo)
    {
        $totalDaySeconds = 24 * 60 * 60;
        $specialDaysCount = 0;
        $dateFrom = strtotime($dateFrom);
        $dateTo = strtotime($dateTo);
        for ($date = $dateFrom; $date <= $dateTo - $totalDaySeconds; $date = ($date + $totalDaySeconds)) {
            if (in_array(Tools::strtolower(date('D', $date)), $specialDays)) {
                ++$specialDaysCount;
            }
        }

        return $specialDaysCount;
    }

    /**
     * [getBookingProductTotalPrice Returns Total price of the Product according to supplied dates].
     */
    public static function getBookingProductTotalPrice(
        $idProduct,
        $dateFrom,
        $dateTo,
        $productPrice = [],
        $idCurrency = null,
        $calDateWise = 1,
        $idProductAttribute = null
    ) {
        // get old timestamp and set UTC timestamp
        $oldTimeStamp = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $totalDaySeconds = 24 * 60 * 60;
        if (Configuration::get('WK_CONSIDER_DATE_TO')) {
            $lastDateSeconds = 0;
        } else {
            if (strtotime($dateFrom) == strtotime($dateTo)) {
                $dateTo = date('Y-m-d', strtotime($dateFrom) + $totalDaySeconds);
            }
            $lastDateSeconds = $totalDaySeconds;
        }
        $totalPrice = [];
        $totalPrice['total_price_tax_incl'] = 0;
        $totalPrice['total_price_tax_excl'] = 0;
        $totalPrice['regular_total_price_tax_incl'] = 0;
        $totalPrice['regular_total_price_tax_excl'] = 0;
        $totalPrice['have_price_rule'] = 0;
        $featureImpactPriceTE = 0;
        $featureImpactPriceTI = 0;
        if (Validate::isLoadedObject(new Currency($idCurrency))) {
            $idCurrency = (int) $idCurrency;
        } else {
            $idCurrency = (int) Context::getContext()->currency->id;
        }
        if (empty($productPrice)) {
            $productPriceTI = Product::getPriceStatic((int) $idProduct, true, $idProductAttribute);
            $productPriceTE = Product::getPriceStatic((int) $idProduct, false, $idProductAttribute);
        } else {
            $productPriceTI = Tools::convertPrice($productPrice['price_tax_incl'], $idCurrency);
            $productPriceTE = Tools::convertPrice($productPrice['price_tax_excl'], $idCurrency);
            $productPriceTI = Tools::ps_round($productPriceTI, Context::getContext()->getComputingPrecision());
            $productPriceTE = Tools::ps_round($productPriceTE, Context::getContext()->getComputingPrecision());
        }

        if (!$calDateWise) {
            $currentDate = date('Y-m-d', strtotime($dateFrom));
            $featurePrice = self::getBookingProductFeaturePricePlanByDateByPriority($idProduct, $currentDate);
            $totalPrice['regular_total_price_tax_incl'] = $productPriceTI;
            $totalPrice['regular_total_price_tax_excl'] = $productPriceTE;
            if ($featurePrice) {
                $totalPrice['have_price_rule'] = 1;
                if ($featurePrice['impact_type'] == 1) {
                    // percentage
                    $featureImpactPriceTE = $productPriceTE * ($featurePrice['impact_value'] / 100);
                    $featureImpactPriceTI = $productPriceTI * ($featurePrice['impact_value'] / 100);
                } else {
                    // Fixed Price
                    $taxRate = (float) WkBookingProductInformation::getAppliedProductTaxRate($idProduct);
                    $taxPrice = ($featurePrice['impact_value'] * $taxRate) / 100;
                    $featureImpactPriceTE = Tools::convertPrice($featurePrice['impact_value'], $idCurrency);
                    $featureImpactPriceTI = Tools::convertPrice($featurePrice['impact_value'] + $taxPrice, $idCurrency);
                }

                if ($featurePrice['impact_way'] == 1) {
                    // Decrease
                    $priceWithFeatureTE = ($productPriceTE - $featureImpactPriceTE);
                    $priceWithFeatureTI = ($productPriceTI - $featureImpactPriceTI);
                } else {
                    // Increase
                    $priceWithFeatureTE = ($productPriceTE + $featureImpactPriceTE);
                    $priceWithFeatureTI = ($productPriceTI + $featureImpactPriceTI);
                }

                if ($priceWithFeatureTI < 0) {
                    $priceWithFeatureTI = 0;
                    $priceWithFeatureTE = 0;
                }
                $totalPrice['total_price_tax_incl'] = $priceWithFeatureTI;
                $totalPrice['total_price_tax_excl'] = $priceWithFeatureTE;
            } else {
                $totalPrice['total_price_tax_incl'] = $productPriceTI;
                $totalPrice['total_price_tax_excl'] = $productPriceTE;
            }

            return $totalPrice;
        }

        $dateFrom = strtotime($dateFrom);
        $dateTo = strtotime($dateTo);
        for ($date = $dateFrom; $date <= ($dateTo - $lastDateSeconds); $date = ($date + $totalDaySeconds)) {
            $currentDate = date('Y-m-d', $date);
            $featurePrice = self::getBookingProductFeaturePricePlanByDateByPriority($idProduct, $currentDate);
            $totalPrice['regular_total_price_tax_incl'] += $productPriceTI;
            $totalPrice['regular_total_price_tax_excl'] += $productPriceTE;
            if ($featurePrice) {
                $totalPrice['have_price_rule'] = 1;
                if ($featurePrice['impact_type'] == 1) {
                    // percentage
                    $featureImpactPriceTE = $productPriceTE * ($featurePrice['impact_value'] / 100);
                    $featureImpactPriceTI = $productPriceTI * ($featurePrice['impact_value'] / 100);
                } else {
                    // Fixed Price
                    $taxRate = (float) WkBookingProductInformation::getAppliedProductTaxRate($idProduct);
                    $taxPrice = ($featurePrice['impact_value'] * $taxRate) / 100;
                    $featureImpactPriceTE = Tools::convertPrice($featurePrice['impact_value'], $idCurrency);
                    $featureImpactPriceTI = Tools::convertPrice($featurePrice['impact_value'] + $taxPrice, $idCurrency);
                }

                if ($featurePrice['impact_way'] == 1) {
                    // Decrease
                    $priceWithFeatureTE = ($productPriceTE - $featureImpactPriceTE);
                    $priceWithFeatureTI = ($productPriceTI - $featureImpactPriceTI);
                } else {
                    // Increase
                    $priceWithFeatureTE = ($productPriceTE + $featureImpactPriceTE);
                    $priceWithFeatureTI = ($productPriceTI + $featureImpactPriceTI);
                }
                if ($priceWithFeatureTI < 0) {
                    $priceWithFeatureTI = 0;
                    $priceWithFeatureTE = 0;
                }
                $totalPrice['total_price_tax_incl'] += $priceWithFeatureTI;
                $totalPrice['total_price_tax_excl'] += $priceWithFeatureTE;
            } else {
                $totalPrice['total_price_tax_incl'] += $productPriceTI;
                $totalPrice['total_price_tax_excl'] += $productPriceTE;
            }
        }
        // set old timestamp after calculated price
        date_default_timezone_set($oldTimeStamp);

        return $totalPrice;
    }

    /**
     * [getNumberOfDays ::To get number of datys between two dates].
     */
    public static function getNumberOfDays($dateFrom, $dateTo)
    {
        $startDate = new DateTime($dateFrom);
        $endDate = new DateTime($dateTo);
        $daysDifference = $startDate->diff($endDate)->days;

        if (Configuration::get('WK_CONSIDER_DATE_TO')) {
            ++$daysDifference;
        }

        return $daysDifference;
    }

    /**
     * [getBookingProductFeaturePricePlanByDateByPriority :: priority wise feature price plan on a perticular date].
     */
    public static function getBookingProductFeaturePricePlanByDateByPriority($idProduct, $date)
    {
        // Get priority
        $featurePricePriority = Configuration::get('WK_PRODUCT_FEATURE_PRICING_PRIORITY');
        $featurePricePriority = explode(';', $featurePricePriority);
        if (is_array($featurePricePriority)) {
            foreach ($featurePricePriority as $priority) {
                if ($priority == 'specific_date') {
                    $featurePrice = Db::getInstance()->getRow(
                        'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
                        INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps
                        ON (wbpfps.`id` = wbpfp.`id`
                        AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                        WHERE wbpfps.`id_product` = ' . (int) $idProduct . '
                        AND wbpfps.`active` = 1
                        AND wbpfps.`date_selection_type` = 2
                        AND wbpfps.`date_from` = \'' . pSQL($date) . '\'',
                    );
                    if ($featurePrice) {
                        return $featurePrice;
                    }
                } elseif ($priority == 'special_day') {
                    $featurePrice = Db::getInstance()->getRow(
                        'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
                        INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps
                        ON (wbpfps.`id` = wbpfp.`id`
                        AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                        WHERE wbpfps.`id_product` = ' . (int) $idProduct . '
                        AND wbpfps.`is_special_days_exists` = 1
                        AND wbpfps.`active` = 1
                        AND wbpfps.`date_from` <= \'' . pSQL($date) . '\'
                        AND wbpfps.`date_to` >= \'' . pSQL($date) . '\'',
                    );
                    if ($featurePrice) {
                        $specialDays = json_decode($featurePrice['special_days']);
                        if (in_array(Tools::strtolower(date('D', strtotime($date))), $specialDays)) {
                            return $featurePrice;
                        }
                    }
                } elseif ($priority == 'date_range') {
                    $featurePrice = Db::getInstance()->getRow(
                        'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing` wbpfp
                        INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_feature_pricing_shop` wbpfps
                        ON (wbpfps.`id` = wbpfp.`id`
                        AND wbpfps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
                        WHERE wbpfps.`id_product` = ' . (int) $idProduct . '
                        AND wbpfps.`date_selection_type` = 1
                        AND wbpfps.`is_special_days_exists` = 0
                        AND wbpfps.`active` = 1
                        AND wbpfps.`date_from` <= \'' . pSQL($date) . '\'
                        AND wbpfps.`date_to` >= \'' . pSQL($date) . '\'',
                    );
                    if ($featurePrice) {
                        return $featurePrice;
                    }
                }
            }
        }

        return false;
    }
}
