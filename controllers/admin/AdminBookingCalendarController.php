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

class AdminBookingCalendarController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->display = 'view';
        $this->page_header_toolbar_title = $this->module->l('Calendar', 'AdminBookingCalendarController');
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(
            [
                'https://uicdn.toast.com/tui-calendar/latest/tui-calendar.css',
                'https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.css',
                'https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.css',
                _MODULE_DIR_ . $this->module->name . '/views/css/wkadminbookingcalendar.css',
            ],
        );
        $allBookings = WkBookingsOrders::getAllBooking();
        if ($allBookings) {
            foreach ($allBookings as $key => $booking) {
                $ref = Order::getUniqReferenceOf($booking['id_order']);
                $allBookings[$key]['product_name'] = WkBookingsOrders::getProductName($booking['id_order'], $booking['id_product'], $booking['id_product_attribute']);
                $allBookings[$key]['ref'] = $ref;
                if (Tools::version_compare(_PS_VERSION_, '1.7.6', '>')) {
                    $allBookings[$key]['order_link'] = $this->context->link->getAdminLink(
                        'AdminOrders',
                        true,
                        ['vieworder' => 1, 'id_order' => (int) $booking['id_order']],
                        [],
                    );
                } else {
                    $allBookings[$key]['order_link'] = $this->context->link->getAdminLink('AdminOrders') .
                    '&id_order=' . (int) $booking['id_order'] . '&vieworder';
                }
                if ($booking['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT) {
                    $allBookings[$key]['duration'] = Tools::displayDate($booking['date_from']) . ' ' . $booking['time_from'] . '-' . $booking['time_to'];
                    $allBookings[$key]['start_time'] = date('Y-m-d', strtotime($booking['date_from'])) . ' ' . $booking['time_from'];
                    $allBookings[$key]['end_time'] = date('Y-m-d', strtotime($booking['date_from'])) . ' ' . $booking['time_to'];
                    $allBookings[$key]['start_time'] = strtotime($allBookings[$key]['start_time']);
                    $allBookings[$key]['end_time'] = strtotime($allBookings[$key]['end_time']);
                } elseif ($booking['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $booking['booking_type'] == WkBookingProductInformation::TYPE_RENTAL) {
                    $allBookings[$key]['duration'] = Tools::displayDate($booking['date_from']) . '-' . Tools::displayDate($booking['date_to']);
                    $allBookings[$key]['start_time'] = strtotime($booking['date_from']);
                    $allBookings[$key]['end_time'] = strtotime($booking['date_to']);
                } elseif ($booking['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                    $allBookings[$key]['duration'] = Tools::displayDate($booking['date_from']);
                    if ($booking['time_from'] != '00:00') {
                        $allBookings[$key]['duration'] .= ' ' . $booking['time_from'];
                    }
                    if ($booking['date_to'] != '0000-00-00 00:00:00') {
                        $allBookings[$key]['duration'] .= ' ' . $this->module->l('to', 'AdminBookingCalendarController');
                        $allBookings[$key]['duration'] .= ' ' . Tools::displayDate($booking['date_to']);
                        if ($booking['time_to'] != '00:00') {
                            $allBookings[$key]['duration'] .= $booking['time_to'];
                        }
                    }
                    $allBookings[$key]['start_time'] = date('Y-m-d', strtotime($booking['date_from'])) . ' ' . $booking['time_from'];
                    $allBookings[$key]['end_time'] = date('Y-m-d', strtotime($booking['date_to'])) . ' ' . $booking['time_to'];
                    $allBookings[$key]['start_time'] = strtotime($allBookings[$key]['start_time']);
                    $allBookings[$key]['end_time'] = strtotime($allBookings[$key]['end_time']);
                }
            }
        }
        Media::addJsDef(
            [
                'wk_all_bookings' => $allBookings,
                'wk_type_date_range' => WkBookingProductInformation::TYPE_DATE_RANGE,
                'wk_type_time_slot' => WkBookingProductInformation::TYPE_TIME_SLOT,
                'wk_type_event' => WkBookingProductInformation::TYPE_EVENT,
                'wk_type_rental' => WkBookingProductInformation::TYPE_RENTAL,
                'months' => [
                    $this->module->l('January', 'AdminBookingCalendarController'),
                    $this->module->l('February', 'AdminBookingCalendarController'),
                    $this->module->l('March', 'AdminBookingCalendarController'),
                    $this->module->l('April', 'AdminBookingCalendarController'),
                    $this->module->l('May', 'AdminBookingCalendarController'),
                    $this->module->l('June', 'AdminBookingCalendarController'),
                    $this->module->l('July', 'AdminBookingCalendarController'),
                    $this->module->l('August', 'AdminBookingCalendarController'),
                    $this->module->l('September', 'AdminBookingCalendarController'),
                    $this->module->l('October', 'AdminBookingCalendarController'),
                    $this->module->l('November', 'AdminBookingCalendarController'),
                    $this->module->l('December', 'AdminBookingCalendarController'),
                ],
            ],
        );
        $this->addJS(
            [
                _PS_JS_DIR_ . 'jquery/plugins/jquery.tagify.js',
                'https://uicdn.toast.com/tui.code-snippet/latest/tui-code-snippet.js',
                'https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.js',
                'https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.js',
                'https://uicdn.toast.com/tui-calendar/latest/tui-calendar.js',
                _MODULE_DIR_ . $this->module->name . '/views/js/wkbookingcalendar.js',
            ],
        );
    }
}
