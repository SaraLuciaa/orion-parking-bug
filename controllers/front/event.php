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

class PsBookingEventModuleFrontController extends ModuleFrontController
{
    public function assignJsVars($vars)
    {
        Media::addJsDef($vars);
    }

    public function initContent()
    {
        parent::initContent();

        $id = Tools::getValue('id');
        $jsVars = [];
        if ($id) {
            $objBookingProduct = new WkBookingProductInformation($id, true);
            if (Validate::isLoadedObject($objBookingProduct)) {
                $timeGone = 0;
                $idProduct = $objBookingProduct->id_product;
                $product = new Product((int) $idProduct, false, $this->context->language->id);
                $idCategoryDefault = $product->id_category_default;
                $jsVars['wk_booking_show_map'] = $objBookingProduct->show_map;
                $jsVars['wk_booking_latitude'] = $objBookingProduct->latitude;
                $jsVars['wk_booking_longitude'] = $objBookingProduct->longitude;
                $jsVars['wk_booking_address'] = $objBookingProduct->address;

                $extraInfo = WkBookingProductExtraInfo::getAlreadyAddedByProductId($idProduct);
                if (!empty($extraInfo)) {
                    $dateFrom = date('Y-m-d', strtotime('+' . $objBookingProduct->booking_before . ' hours'));
                    $assignData = WkBookingProductTimeSlotPrices::getEventSlotDataForTplAssign($extraInfo, $idProduct, $dateFrom, $objBookingProduct->booking_before, $objBookingProduct->quantity, $product->minimal_quantity);
                    $eventdateFrom = $extraInfo['date_from'] . ' ' . $extraInfo['time_to'];
                    $eventdateFrom = date('Y-m-d H:i', strtotime($eventdateFrom . '+' . $objBookingProduct->booking_before . ' hours'));
                    if (strtotime($eventdateFrom) < strtotime(date('Y-m-d H:i'))) {
                        $timeGone = 1;
                    }
                    // to check for multiple slots case
                    if (isset($assignData['bookingTimeSlots']) && !empty($assignData['bookingTimeSlots'])) {
                        $timeGone = 0;
                    }
                    $this->context->smarty->assign($assignData);
                    $this->context->smarty->assign(
                        [
                            'e_language' => trim(str_replace(',', ', ', $extraInfo['language'])),
                            'e_age_group' => trim(str_replace(',', ', ', $extraInfo['age_group'])),
                            'e_artist' => trim(str_replace(',', ', ', $extraInfo['artist'])),
                            'e_date_from' => $extraInfo['date_from'],
                            'e_time_from' => $extraInfo['time_from'],
                            'e_date_to' => $extraInfo['date_to'],
                            'e_time_to' => $extraInfo['time_to'],
                            'e_addres' => $objBookingProduct->address,
                            'multiple_slot' => $extraInfo['multiple_slot'],
                            'timeGone' => $timeGone,
                            'share_link' => $this->context->link->getModuleLink('psbooking', 'event', ['id' => $id]),
                            'show_regular_price_after_discount' => Configuration::get('WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT'),
                        ],
                    );
                    $durations = WkBookingProductExtraInfo::getEventTimeDuration($idProduct);
                    $durationText = '';
                    if ($durations) {
                        foreach ($durations as $key => $duration) {
                            $hours = intdiv($duration, 60);
                            $min = ($duration % 60);
                            if ($hours > 0) {
                                $durationText .= $hours . $this->module->l('hr ', 'event');
                            }
                            if ($min > 0) {
                                $durationText .= $min . $this->module->l('mins', 'event');
                            }
                            if (isset($durations[$key + 1])) {
                                $durationText .= ' | ';
                            }
                        }
                    } else {
                        $mintues = (strtotime($extraInfo['date_to'] . ' ' . $extraInfo['time_to']) - strtotime($extraInfo['date_from'] . ' ' . $extraInfo['time_from'])) / 60;
                        $hours = intdiv($mintues, 60);
                        $min = ($mintues % 60);
                        if ($hours > 0) {
                            $durationText .= $hours . $this->module->l('hr ', 'event');
                        }
                        if ($min > 0) {
                            $durationText .= $min . $this->module->l('mins', 'event');
                        }
                    }
                    $this->context->smarty->assign(
                        [
                            'durationText' => $durationText,
                        ],
                    );
                    $eventBannerImg = _PS_MODULE_DIR_ . 'psbooking/views/img/event//' . $idProduct . '/' . $idProduct . '_' . $this->context->shop->id . 'banner.jpg';
                    $eventBannerSrc = _MODULE_DIR_ . 'psbooking/views/img/event//' . $idProduct . '/' . $idProduct . '_' . $this->context->shop->id . 'banner.jpg';
                    if (file_exists($eventBannerImg)) {
                        $this->context->smarty->assign('eventBannerImg', 1);
                    }
                    $this->context->smarty->assign(
                        [
                            'module_dir' => _MODULE_DIR_,
                            'is_catalog_mode' => Configuration::get('PS_CATALOG_MODE'),
                            'product_name' => $product->name,
                            'banner_img' => $eventBannerSrc,
                            'summary' => $product->description_short,
                            'description' => $product->description,
                            'minimal_quantity' => $product->minimal_quantity,
                            'idProduct' => $idProduct,
                            'wk_image_dir' => _MODULE_DIR_ . $this->module->name . '/views/img/',
                        ],
                    );

                    // Other events code start
                    $displayRelatedEvents = Configuration::get('WK_SHOW_RELATED_EVENTS');
                    if ($displayRelatedEvents) {
                        $otherRunningEvents = WkBookingProductExtraInfo::getAllRunningEvents($idProduct);
                        foreach ($otherRunningEvents as $otherkey => &$otherRunningEvent) {
                            $otherRunningEvent['objproduct'] = new Product(
                                $otherRunningEvent['id_product'],
                                true,
                                $this->context->language->id,
                            );
                            // check if current event default category should be same for related events
                            if ($idCategoryDefault == $otherRunningEvent['objproduct']->id_category_default) {
                                $bookingPro = $objBookingProduct->getBookingProductInfoByIdProduct($otherRunningEvent['id_product']);
                                $assignData = WkBookingProductTimeSlotPrices::getEventSlotDataForTplAssign($otherRunningEvent, $otherRunningEvent['id_product'], date('Y-m-d'), 0, 1, $otherRunningEvent['objproduct']->minimal_quantity);
                                $otherRunningEvent['price'] = isset($assignData['productFeaturePrice']) ? $assignData['productFeaturePrice'] : 0;
                                $otherRunningEvent['address'] = $bookingPro['address'];
                                $otherRunningEvent['name'] = $otherRunningEvent['objproduct']->name;
                                $otherRunningEvent['link_rewrite'] = $otherRunningEvent['objproduct']->link_rewrite;
                                $cover = Product::getCover($otherRunningEvent['id_product']);
                                if ($cover) {
                                    $otherRunningEvent['image'] = $otherRunningEvent['id_product'] . '-' . $cover['id_image'];
                                } else {
                                    $otherRunningEvent['image'] = 0;
                                }
                            } else {
                                unset($otherRunningEvents[$otherkey]);
                            }
                        }
                        $this->context->smarty->assign('booking_shop_product', $otherRunningEvents);
                    }
                    $this->assignJsVars($jsVars);

                    // assign display share option and related events config
                    $this->context->smarty->assign(
                        [
                            'whatsapp_share' => Configuration::get('WK_SHOW_EVENT_PAGE_WHATSAPP'),
                            'facebook_share' => Configuration::get('WK_SHOW_EVENT_PAGE_FACEBOOK'),
                            'instagram_share' => Configuration::get('WK_SHOW_EVENT_PAGE_INSTAGRAM'),
                            'twitter_share' => Configuration::get('WK_SHOW_EVENT_PAGE_TWITTER'),
                            'clipboard_share' => Configuration::get('WK_SHOW_EVENT_PAGE_CLIPBOARD'),
                            'display_related_events' => Configuration::get('WK_SHOW_RELATED_EVENTS'),
                        ],
                    );
                    $this->setTemplate('module:psbooking/views/templates/front/event.tpl');
                } else {
                    $this->setTemplate('module:psbooking/views/templates/front/no_event.tpl');
                }
            } else {
                $this->setTemplate('module:psbooking/views/templates/front/no_event.tpl');
            }
        } else {
            $this->setTemplate('module:psbooking/views/templates/front/no_event.tpl');
        }
    }

    public function setMedia()
    {
        if (!empty(Configuration::get('WK_BOOKING_GEOLOCATION_API_KEY'))) {
            $googleMapKey = Configuration::get('WK_BOOKING_GEOLOCATION_API_KEY');
            $path = 'https://maps.googleapis.com/maps/api/js?key=' . $googleMapKey . '&libraries=&v=weekly';
            $this->registerJavascript(
                'mapapi', // Unique ID
                $path, // JS path
                ['server' => 'remote'],
            );

            Media::addJsDef(
                [
                    'getDirections' => $this->module->l('Get direction', 'event'),
                ],
            );
            $this->addJS(_MODULE_DIR_ . 'psbooking/views/js/event_page_map.js');
        }
        $this->registerJavascript(
            'bxslider',
            'modules/' . $this->module->name . '/views/js/jquery.bxslider.js',
        );
        $this->addCSS(_MODULE_DIR_ . 'psbooking/views/css/event_page.css');
        $this->addJS(_MODULE_DIR_ . 'psbooking/views/js/event_page.js');

        return parent::setMedia();
    }
}
