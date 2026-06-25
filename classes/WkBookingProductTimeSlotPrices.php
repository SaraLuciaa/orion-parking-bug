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
class WkBookingProductTimeSlotPrices extends ObjectModel
{
    public $id_product;
    public $slot_day;
    public $time_slot_type;
    public $date_from;
    public $date_to;
    public $time_slot_from;
    public $time_slot_to;
    public $price;
    public $quantity;
    public $active;
    public $date_add;
    public $date_upd;

    public const TIME_SLOT_TYPE_DAY = 1;
    public const TIME_SLOT_TYPE_DATE = 2;
    public const TIME_SLOT_EVENT_TYPE = 3;

    public static $definition = [
        'table' => 'wk_booking_time_slots_prices',
        'primary' => 'id',
        'fields' => [
            'id_product' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
                'shop' => true,
            ],
            'slot_day' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'time_slot_type' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
                'shop' => true,
            ],
            'date_from' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
            'date_to' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'shop' => true],
            'time_slot_from' => ['type' => self::TYPE_STRING, 'required' => true, 'shop' => true],
            'time_slot_to' => ['type' => self::TYPE_STRING, 'required' => true, 'shop' => true],
            'price' => ['type' => self::TYPE_FLOAT, 'required' => true, 'shop' => true],
            'quantity' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'active' => ['type' => self::TYPE_INT, 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'shop' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'shop' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation('wk_booking_time_slots_prices', ['type' => 'shop', 'primary' => 'id']);
    }

    // send  $status=2 for all slots 1 for active and 0 for incative
    public function getProductTimeSlotsOnDate(
        $idProduct,
        $date,
        $deactiveDisabledDates = false,
        $status = 2,
        $isDayWise = false
    ) {
        $date = date('Y-m-d', strtotime($date));

        if ($isDayWise) {
            $day = date('N', strtotime($date));
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . '
            AND wbtsps.`slot_day` = ' . (int) $day . '';
            if ($status == 1) {
                $sql .= ' AND wbtsps.`active` = 1';
            } elseif ($status == 0) {
                $sql .= ' AND wbtsps.`active` = 0';
            }
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . '
            AND wbtsps.`date_from` <= \'' . pSql($date) . '\'
            AND wbtsps.`date_to` >= \'' . pSql($date) . '\'';
            if ($status == 1) {
                $sql .= ' AND wbtsps.`active` = 1';
            } elseif ($status == 0) {
                $sql .= ' AND wbtsps.`active` = 0';
            }
        }
        $timeSlots = Db::getInstance()->executeS($sql);
        if ($deactiveDisabledDates) {
            if ($timeSlots) {
                $objBookingDisableDates = new WkBookingProductDisabledDates();
                $bookingDisableDates = $objBookingDisableDates->getBookingProductDisableDates($idProduct);

                $underDisabledays = 0;
                if ($bookingDisableDates) {
                    if ($bookingDisableDates['disable_special_days_active']
                        && $bookingDisableDates['disabled_special_days']
                    ) {
                        $disabledDays = json_decode($bookingDisableDates['disabled_special_days'], true);
                        $weekDay = date('w', strtotime($date));
                        if (in_array($weekDay, $disabledDays)) {
                            $underDisabledays = 1;
                        }
                    }
                }
                $objBookingProductInformation = new WkBookingProductInformation();
                $bookingProductInformation = $objBookingProductInformation->getBookingProductInfoByIdProduct(
                    $idProduct,
                );
                $bookingBefore = $bookingProductInformation['booking_before'];

                foreach ($timeSlots as $key => $slot) {
                    if (isset($bookingDisableDates['disabled_dates_slots_active'], $bookingDisableDates['disabled_dates_slots'])
                          && $bookingDisableDates['disabled_dates_slots_active'] && $bookingDisableDates['disabled_dates_slots']
                    ) {
                        $bookingDisableDatesArray = json_decode(
                            $bookingDisableDates['disabled_dates_slots'],
                            true,
                        );
                        foreach ($bookingDisableDatesArray as $disableDateRange) {
                            if (($dateFrom = $disableDateRange['date_from'])
                                && ($dateTo = $disableDateRange['date_to'])
                            ) {
                                if (strtotime($dateFrom) <= strtotime($date)
                                    && strtotime($dateTo) >= strtotime($date)
                                ) {
                                    if (isset($disableDateRange['slots_info'])) {
                                        if (count($disableDateRange['slots_info'])) {
                                            foreach ($disableDateRange['slots_info'] as $slotInfo) {
                                                if ($slotInfo['time_from'] == $slot['time_slot_from']
                                                    && $slotInfo['time_to'] == $slot['time_slot_to']
                                                ) {
                                                    $timeSlots[$key]['active'] = 0;
                                                }
                                            }
                                        } else {
                                            $timeSlots[$key]['active'] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // if curent time is greater than current date slot time then not show to user
                    $curentDateTime = date('Y-m-d H:i', strtotime('+' . $bookingBefore . ' hours'));

                    $slotDateTime = $date . ' ' . $timeSlots[$key]['time_slot_from'];

                    if (strtotime($curentDateTime) > strtotime($slotDateTime)) {
                        $timeSlots[$key]['active'] = 0;
                    }

                    if ($underDisabledays) {
                        $timeSlots[$key]['active'] = 0;
                    }
                    if (($status == 1 && !$timeSlots[$key]['active'])
                        || ($status == 0 && $timeSlots[$key]['active'])
                    ) {
                        unset($timeSlots[$key]);
                    }
                }
            }
        }

        return $timeSlots;
    }

    public static function getEventSlotDataForTplAssign($eventData, $idProduct, $dateFrom, $bookingBefore, $bookingQty, $minimalQuantity)
    {
        $wkBookingOrder = new WkBookingsOrders();
        $data['totalSlotsQty'] = '';
        $data['productFeaturePriceRegular'] = '';
        $data['productFeaturePrice'] = '';
        $data['bookingTimeSlots'] = [];
        $data['show_onwards'] = false;
        $productFeaturePrice = 0;
        if ($eventData['multiple_slot']) {
            $objTimeSlots = new WkBookingProductTimeSlotPrices();
            $bookingTimeSlots = $objTimeSlots->getProductAllTimeSlotsOnDateRange($idProduct, $dateFrom, $eventData['date_to']);
            if ($bookingTimeSlots) {
                $flag = 0;
                $totalSlotsQty = 0;
                $bkingTmSltPriceToday = [];
                foreach ($bookingTimeSlots as $key => $timeSlot) {
                    if ($timeSlot['active']) {
                        $eventStartTime = date('Y-m-d', strtotime($timeSlot['date_from'])) . ' ' . $timeSlot['time_slot_from'];
                        $curentDateTime = date('Y-m-d H:i', strtotime('+' . $bookingBefore . ' hours'));
                        if (strtotime($curentDateTime) > strtotime($eventStartTime)) {
                            unset($bookingTimeSlots[$key]);
                            continue;
                        } else {
                            $dateFrom = date('Y-m-d', strtotime($eventStartTime));
                        }
                        $bookedSlotQuantity = $wkBookingOrder->getProductTimeSlotOrderedQuantity(
                            $idProduct,
                            $timeSlot['date_from'],
                            $timeSlot['time_slot_from'],
                            $timeSlot['time_slot_to'],
                            1,
                        );
                        $dateDiff = (int) ((strtotime($timeSlot['time_slot_to']) - strtotime($timeSlot['time_slot_from'])) / 60);
                        $hours = (int) ($dateDiff / 60);
                        $minutes = $dateDiff % 60;
                        // $availQty = $bookingProductInformation['quantity'] - $bookedSlotQuantity;
                        $availQty = $timeSlot['quantity'] - $bookedSlotQuantity;
                        $bookingTimeSlots[$key]['available_qty'] = ($availQty < 0) ? 0 : $availQty;
                        $bookingTimeSlots[$key]['price_tax_excl'] = $timeSlot['price'];
                        $bookingTimeSlots[$key]['hours'] = $hours;
                        $bookingTimeSlots[$key]['minutes'] = $minutes;
                        $totalSlotsQty += $bookingQty - $bookedSlotQuantity;
                        $taxRate = (float) WkBookingProductInformation::getAppliedProductTaxRate($idProduct);
                        $bookingTimeSlots[$key]['price_tax_incl'] = $timeSlot['price'] * ((100 + $taxRate) / 100);
                        $bkingTmSltPrice['price_tax_excl'] = $bookingTimeSlots[$key]['price_tax_excl'];
                        $bkingTmSltPrice['price_tax_incl'] = $bookingTimeSlots[$key]['price_tax_incl'];
                        if ($flag == 0 && $bookingTimeSlots[$key]['available_qty']) {
                            $bookingTimeSlots[$key]['checked'] = 1;
                            $bkingTmSltPriceToday['price_tax_excl'] = $bookingTimeSlots[$key]['price_tax_excl'];
                            $bkingTmSltPriceToday['price_tax_incl'] = $bookingTimeSlots[$key]['price_tax_incl'];
                            $flag = 1;
                        } else {
                            $bookingTimeSlots[$key]['checked'] = 0;
                        }
                        $ttlFeatPri = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                            $idProduct,
                            $timeSlot['date_from'],
                            $timeSlot['date_from'],
                            $bkingTmSltPrice,
                            Context::getContext()->currency->id,
                        );
                        if ($ttlFeatPri) {
                            $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                            if (!$priceDisplay || $priceDisplay == 2) {
                                if ($productFeaturePrice != 0) {
                                    if ($productFeaturePrice > $ttlFeatPri['total_price_tax_incl']) {
                                        $productFeaturePrice = $ttlFeatPri['total_price_tax_incl'];
                                        if ($ttlFeatPri['have_price_rule']) {
                                            $productFeaturePriceRegular = $ttlFeatPri['regular_total_price_tax_incl'];
                                            $data['productFeaturePriceRegular'] = WkBookingPsHelper::displayPrice(
                                                $productFeaturePriceRegular,
                                            );
                                        }
                                        $data['show_onwards'] = true;
                                    }
                                } else {
                                    $productFeaturePrice = $ttlFeatPri['total_price_tax_incl'];
                                    if ($ttlFeatPri['have_price_rule']) {
                                        $productFeaturePriceRegular = $ttlFeatPri['regular_total_price_tax_incl'];
                                        $data['productFeaturePriceRegular'] = WkBookingPsHelper::displayPrice(
                                            $productFeaturePriceRegular,
                                        );
                                    }
                                }
                                $bookingTimeSlots[$key]['formated_slot_price'] = WkBookingPsHelper::displayPrice(
                                    $ttlFeatPri['total_price_tax_incl'],
                                );
                                if ($ttlFeatPri['have_price_rule']) {
                                    $bookingTimeSlots[$key]['formated_slot_price_regular'] = WkBookingPsHelper::displayPrice(
                                        $ttlFeatPri['regular_total_price_tax_incl'],
                                    );
                                }
                            } elseif ($priceDisplay == 1) {
                                if ($productFeaturePrice != 0) {
                                    if ($productFeaturePrice > $ttlFeatPri['total_price_tax_excl']) {
                                        $productFeaturePrice = $ttlFeatPri['total_price_tax_excl'];
                                        if ($ttlFeatPri['have_price_rule']) {
                                            $productFeaturePriceRegular = $ttlFeatPri['regular_total_price_tax_excl'];
                                            $data['productFeaturePriceRegular'] = WkBookingPsHelper::displayPrice(
                                                $productFeaturePriceRegular,
                                            );
                                        }
                                        $data['show_onwards'] = true;
                                    }
                                } else {
                                    $productFeaturePrice = $ttlFeatPri['total_price_tax_excl'];
                                    if ($ttlFeatPri['have_price_rule']) {
                                        $productFeaturePriceRegular = $ttlFeatPri['regular_total_price_tax_excl'];
                                        $data['productFeaturePriceRegular'] = WkBookingPsHelper::displayPrice(
                                            $productFeaturePriceRegular,
                                        );
                                    }
                                }
                                $bookingTimeSlots[$key]['formated_slot_price'] = WkBookingPsHelper::displayPrice(
                                    $ttlFeatPri['total_price_tax_excl'],
                                );
                                if ($ttlFeatPri['have_price_rule']) {
                                    $bookingTimeSlots[$key]['formated_slot_price_regular'] = WkBookingPsHelper::displayPrice(
                                        $ttlFeatPri['regular_total_price_tax_excl'],
                                    );
                                }
                            }
                        }
                    } else {
                        unset($bookingTimeSlots[$key]);
                    }
                }
            } else {
                $productFeaturePrice = 0;
            }
            $data['bookingTimeSlots'] = $bookingTimeSlots;
        } else {
            $totalPrice = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                $idProduct,
                $eventData['date_from'],
                $eventData['date_to'],
                false,
                Context::getContext()->currency->id,
                0,
            );
            if ($totalPrice) {
                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                if (!$priceDisplay || $priceDisplay == 2) {
                    $productFeaturePrice = $totalPrice['total_price_tax_incl'] * $minimalQuantity;
                    if ($totalPrice['have_price_rule']) {
                        $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_incl'] * $minimalQuantity;
                        $data['productFeaturePriceRegular'] = WkBookingPsHelper::displayPrice(
                            $productFeaturePriceRegular,
                        );
                    }
                } elseif ($priceDisplay == 1) {
                    $productFeaturePrice = $totalPrice['total_price_tax_excl'] * $minimalQuantity;
                    if ($totalPrice['have_price_rule']) {
                        $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_excl'] * $minimalQuantity;
                        $data['productFeaturePriceRegular'] = WkBookingPsHelper::displayPrice(
                            $productFeaturePriceRegular,
                        );
                    }
                }
            }
        }
        $data['productFeaturePrice'] = WkBookingPsHelper::displayPrice($productFeaturePrice);

        return $data;
    }

    public function getProductAllTimeSlotsOnDateRange($idProduct, $dateFrom, $dateTo)
    {
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo = date('Y-m-d', strtotime($dateTo));

        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . '
            AND wbtsps.`date_from` >= \'' . pSql($dateFrom) . '\'
            AND wbtsps.`date_to` <= \'' . pSql($dateTo) . '\'',
        );
    }

    public function getProductAllTimeSlotsFormatted($idProduct)
    {
        $timeSlots = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct,
        );
        if ($timeSlots) {
            $timeSlotsFormatted = [];
            foreach ($timeSlots as $key => $timeSlot) {
                if ($timeSlot['time_slot_type'] == WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DATE || $timeSlot['time_slot_type'] == WkBookingProductTimeSlotPrices::TIME_SLOT_EVENT_TYPE) {
                    $date_key = date('Y-m-d', strtotime($timeSlot['date_from'])) . '_' .
                    date('Y-m-d', strtotime($timeSlot['date_to']));
                    $timeSlotsFormatted[$date_key]['date_from'] = date('d-m-Y', strtotime($timeSlot['date_from']));
                    $timeSlotsFormatted[$date_key]['date_to'] = date('d-m-Y', strtotime($timeSlot['date_to']));
                    $timeSlotsFormatted[$date_key]['id_product'] = $timeSlot['id_product'];
                    $timeSlotsFormatted[$date_key]['time_slot_type'] = $timeSlot['time_slot_type'];
                    $timeSlotsFormatted[$date_key]['time_slots'][$key]['time_from'] = $timeSlot['time_slot_from'];
                    $timeSlotsFormatted[$date_key]['time_slots'][$key]['time_to'] = $timeSlot['time_slot_to'];
                    $timeSlotsFormatted[$date_key]['time_slots'][$key]['slot_price'] = $timeSlot['price'];
                    $timeSlotsFormatted[$date_key]['time_slots'][$key]['slot_qty'] = $timeSlot['quantity'];
                    $timeSlotsFormatted[$date_key]['time_slots'][$key]['id_slot'] = $timeSlot['id'];
                    $timeSlotsFormatted[$date_key]['time_slots'][$key]['active'] = $timeSlot['active'];
                } else {
                    $day_key = $timeSlot['slot_day'];
                    $timeSlotsFormatted[$day_key]['date_from'] = date('d-m-Y', strtotime($timeSlot['date_from']));
                    $timeSlotsFormatted[$day_key]['date_to'] = date('d-m-Y', strtotime($timeSlot['date_to']));
                    $timeSlotsFormatted[$day_key]['id_product'] = $timeSlot['id_product'];
                    $timeSlotsFormatted[$day_key]['time_slot_type'] = $timeSlot['time_slot_type'];
                    $timeSlotsFormatted[$day_key]['time_slots'][$key]['time_from'] = $timeSlot['time_slot_from'];
                    $timeSlotsFormatted[$day_key]['time_slots'][$key]['time_to'] = $timeSlot['time_slot_to'];
                    $timeSlotsFormatted[$day_key]['time_slots'][$key]['slot_price'] = $timeSlot['price'];
                    $timeSlotsFormatted[$day_key]['time_slots'][$key]['slot_qty'] = $timeSlot['quantity'];
                    $timeSlotsFormatted[$day_key]['time_slots'][$key]['id_slot'] = $timeSlot['id'];
                    $timeSlotsFormatted[$day_key]['time_slots'][$key]['active'] = $timeSlot['active'];
                }
            }

            return $timeSlotsFormatted;
        } else {
            return false;
        }
    }

    public function getProductTimeSlotsSelectedDates($idProduct)
    {
        $totalDaySeconds = 24 * 60 * 60;
        $selectedDates = [];
        $timeSlotsDates = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . '
            GROUP BY wbtsps.date_From, wbtsps.date_to',
        );
        foreach ($timeSlotsDates as $slotDates) {
            if ($slotDates['time_slot_type'] == 1) {
                $currentDate = date('Y-m-d');
                $timeSlotsOnDate = $this->getProductTimeSlotsOnDate($idProduct, $currentDate, true, 1, true);
                if ($timeSlotsOnDate) {
                    if (!in_array($currentDate, $selectedDates)) {
                        $selectedDates[] = $currentDate;
                    }
                }
            } else {
                $dateFrom = date('Y-m-d', strtotime($slotDates['date_from']));
                $dateTo = date('Y-m-d', strtotime($slotDates['date_to']));
                for ($date = strtotime($dateFrom); $date <= strtotime($dateTo); $date = ($date + $totalDaySeconds)) {
                    $currentDate = date('Y-m-d', $date);
                    // check if day is in disabled days
                    $timeSlotsOnDate = $this->getProductTimeSlotsOnDate(
                        $idProduct,
                        $currentDate,
                        true,
                        1,
                        false,
                    );
                    if ($timeSlotsOnDate) {
                        if (!in_array($currentDate, $selectedDates)) {
                            $selectedDates[] = $currentDate;
                        }
                    }
                }
            }
        }

        return $selectedDates;
    }

    public function getProductTimeSlotDetails($idProduct, $date, $timeSlotFrom, $timeSlotTo)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . '
            AND wbtsps.`date_from` <= \'' . pSql($date) . '\'
            AND wbtsps.`date_to` >= \'' . pSql($date) . '\'' . '
            AND wbtsps.`time_slot_from` = \'' . pSql($timeSlotFrom) . '\'
            AND wbtsps.`time_slot_to` = \'' . pSql($timeSlotTo) . '\'',
        );
    }

    public function getProductTimeDayWiseSlotDetails($idProduct, $day, $timeSlotFrom, $timeSlotTo)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . '
            AND wbtsps.`slot_day` = ' . (int) $day . '
            AND wbtsps.`time_slot_from` = \'' . pSql($timeSlotFrom) . '\'
            AND wbtsps.`time_slot_to` = \'' . pSql($timeSlotTo) . '\'',
        );
    }

    public function deleteBookingProductTimeSlots($idProduct)
    {
        $bookingTimeSlots = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct,
        );
        if (!empty($bookingTimeSlots)) {
            foreach ($bookingTimeSlots as $bookingTimeSlot) {
                $bookingTimeSlotObj = new WkBookingProductTimeSlotPrices((int) $bookingTimeSlot['id']);
                if (!$bookingTimeSlotObj->delete()) {
                    return false;
                }
            }
        }

        return true;
    }

    public function validateTimeSlotsDuplicacyInDayRanges($idProduct, $day, $timeFrom, $timeto)
    {
        $moduleInstance = new PsBooking();
        $error = false;
        $timSlotsInfo = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . '
            AND wbtsps.`slot_day` = ' . (int) $day . '',
        );
        if ($timSlotsInfo) {
            $dayString = '';
            if ($day == 1) {
                $dayString .= $moduleInstance->l(' Monday ', 'WkBookingProductTimeSlotPrices');
            } elseif ($day == 2) {
                $dayString .= $moduleInstance->l(' Tuesday ', 'WkBookingProductTimeSlotPrices');
            } elseif ($day == 3) {
                $dayString .= $moduleInstance->l(' Wednesday ', 'WkBookingProductTimeSlotPrices');
            } elseif ($day == 4) {
                $dayString .= $moduleInstance->l(' Thursday ', 'WkBookingProductTimeSlotPrices');
            } elseif ($day == 5) {
                $dayString .= $moduleInstance->l(' Friday ', 'WkBookingProductTimeSlotPrices');
            } elseif ($day == 6) {
                $dayString .= $moduleInstance->l(' Saturday ', 'WkBookingProductTimeSlotPrices');
            } elseif ($day == 7) {
                $dayString .= $moduleInstance->l(' Sunday ', 'WkBookingProductTimeSlotPrices');
            }
            foreach ($timSlotsInfo as $timeSlotRow) {
                if ((strtotime($timeFrom) <= strtotime($timeSlotRow['time_slot_to']))
                    && (strtotime($timeto) >= strtotime($timeSlotRow['time_slot_from']))
                ) {
                    $error = $moduleInstance->l('Time slot ', 'WkBookingProductTimeSlotPrices') . $timeFrom .
                    $moduleInstance->l(' to ', 'WkBookingProductTimeSlotPrices') . $timeto .
                    $moduleInstance->l(' for', 'WkBookingProductTimeSlotPrices') . $dayString .
                    $moduleInstance->l(' not saved because of duplicacy.', 'WkBookingProductTimeSlotPrices');
                    break;
                }
            }
        }

        return $error;
    }

    public function validateTimeSlotsDuplicacyInOtherDateRanges($idProduct, $dateFrom, $dateTo, $timeFrom, $timeto)
    {
        $moduleInstance = new PsBooking();
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo = date('Y-m-d', strtotime($dateTo));
        $error = false;
        $timSlotsInfo = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . '
            AND wbtsps.`date_from` <= \'' . pSql($dateTo) . '\'
            AND wbtsps.`date_to` >= \'' . pSql($dateFrom) . '\'',
        );
        if ($timSlotsInfo) {
            foreach ($timSlotsInfo as $timeSlotRow) {
                if ((strtotime($timeFrom) < strtotime($timeSlotRow['time_slot_to']))
                    && (strtotime($timeto) > strtotime($timeSlotRow['time_slot_from']))
                ) {
                    $error = $moduleInstance->l('Time slot ', 'WkBookingProductTimeSlotPrices') . $timeFrom .
                    $moduleInstance->l(' to ', 'WkBookingProductTimeSlotPrices') . $timeto .
                    $moduleInstance->l(' for the date range ', 'WkBookingProductTimeSlotPrices')
                    . date('Y-m-d', strtotime($dateFrom)) .
                    $moduleInstance->l(' to ', 'WkBookingProductTimeSlotPrices') . date('Y-m-d', strtotime($dateTo)) .
                    $moduleInstance->l(' not saved because of duplicacy.', 'WkBookingProductTimeSlotPrices');
                    break;
                }
            }
        }

        return $error;
    }

    public function checkTimeSlotType($idProduct)
    {
        $timeSlots = Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct,
        );
        if (!empty($timeSlots) && isset($timeSlots['time_slot_type'])) {
            return $timeSlots['time_slot_type'];
        }

        return 2;
    }

    public function getTimeSlotDays($idProduct)
    {
        $timeSlots = Db::getInstance()->executeS(
            'SELECT wbtsps.`slot_day` FROM `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices` wbtsp
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_time_slots_prices_shop` wbtsps ON (wbtsps.`id` = wbtsp.`id`
            AND wbtsps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbtsps.`id_product` = ' . (int) $idProduct . ' GROUP BY wbtsps.slot_day',
        );
        $activeDays = [];
        if (!empty($timeSlots)) {
            foreach ($timeSlots as $timeSlot) {
                $activeDays[] = $timeSlot['slot_day'];
            }
        }
        $disabledDays = [];
        for ($i = 1; $i <= 7; ++$i) {
            if (!in_array($i, $activeDays)) {
                // if 7 it means sunday we are passing 0 because in js 0 means sunday based on server
                if ($i == 7) {
                    $disabledDays[] = 0;
                }
                $disabledDays[] = $i;
            }
        }

        return $disabledDays;
    }

    public static function setTimeZoneByIP()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ipInfo = Tools::file_get_contents('http://ip-api.com/json/' . $ip);
        $ipInfo = json_decode($ipInfo);
        if ($ipInfo && isset($ipInfo->timezone)) {
            $timezone = $ipInfo->timezone;
            date_default_timezone_set($timezone);
        }
    }
}
