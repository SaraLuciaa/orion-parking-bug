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

class psbookingbookingProductModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Booking products', 'bookingproduct'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();

        $dateFrom = Tools::getValue('date_from');
        if (!$dateFrom) {
            $dateFrom = date('d-m-Y');
        }

        $dateTo = Tools::getValue('date_to');
        if (!$dateTo) {
            $dateTo = date('d-m-Y', strtotime('+ 1 months'));
        }

        $type = Tools::getValue('type');
        $quantity = trim(Tools::getValue('quantity'));
        if (!empty($quantity)) {
            if (!Validate::isUnsignedInt($quantity)) {
                $this->errors[] = $this->module->l('Please enter valid quantity.', 'bookingproduct');
            }
        } else {
            $quantity = 1;
        }
        $this->context->smarty->assign('date_from', $dateFrom);
        $this->context->smarty->assign('date_to', $dateTo);
        $this->context->smarty->assign('type', $type);
        $this->context->smarty->assign('quantity', $quantity);
        $this->context->smarty->assign('booking_types', WkBookingProductInformation::getBookingTypes());
        if (empty($this->errors)) {
            $p = Tools::getValue('p');
            $n = Configuration::get('PS_PRODUCTS_PER_PAGE');
            // default page number
            if (!$p) {
                $p = 1;
            }
            $orderBy = 'id_product'; // Display by default new product first
            $orderWay = 'desc'; // Display by default new product first
            if (Tools::getValue('orderby')) {
                $orderBy = Tools::getValue('orderby');
            }
            if (Tools::getValue('orderway')) {
                $orderWay = Tools::getValue('orderway');
            }
            if ($orderBy == 'name' && $orderWay == 'asc') {
                $order = 1;
            } elseif ($orderBy == 'name' && $orderWay == 'desc') {
                $order = 2;
            } elseif ($orderBy == 'price' && $orderWay == 'asc') {
                $order = 3;
            } elseif ($orderBy == 'price' && $orderWay == 'desc') {
                $order = 4;
            } else {
                $order = 0;
            }

            $bookingProducts = WkBookingProductInformation::getBookingProduct(
                $type,
                $this->context->language->id,
                $orderBy,
                $orderWay,
            );
            if ($bookingProducts) {
                // filter by date range
                if (!empty($dateFrom) && !empty($dateTo)) {
                    $bookingFrom = strtotime($dateFrom);
                    $traverseToDate = strtotime($dateTo);
                    $totalDaySeconds = 24 * 60 * 60;
                    $objSlotPrice = new WkBookingProductTimeSlotPrices();
                    $objBookingOrders = new WkBookingsOrders();
                    $objBookingDisableDates = new WkBookingProductDisabledDates();
                    foreach ($bookingProducts as $key => $product) {
                        if ($product['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                            if (!WkBookingProductExtraInfo::getByDateRange($product['id_product'], date('Y-m-d', $bookingFrom), date('Y-m-d', $traverseToDate))) {
                                unset($bookingProducts[$key]);
                            }
                        } elseif ($product['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT) {
                            $pFlag = false;
                            for ($date = $bookingFrom; $date <= $traverseToDate; $date = ($date + $totalDaySeconds)) {
                                // get date wise in time slot
                                $timeSlots = $objSlotPrice->getProductTimeSlotsOnDate(
                                    $product['id_product'],
                                    date('Y-m-d', $date),
                                    true,
                                    1,
                                    false,
                                );
                                if (empty($timeSlots)) {
                                    // get day wise in time slot
                                    $timeSlots = $objSlotPrice->getProductTimeSlotsOnDate(
                                        $product['id_product'],
                                        date('Y-m-d', $date),
                                        true,
                                        1,
                                        true,
                                    );
                                }
                                if (!empty($timeSlots)) {
                                    foreach ($timeSlots as $timeSlot) {
                                        if ($timeSlot['quantity'] >= $quantity) {
                                            $bookedSlotQuantity = $objBookingOrders->getProductTimeSlotOrderedQuantity(
                                                $product['id_product'],
                                                date('Y-m-d', $date),
                                                $timeSlot['time_slot_from'],
                                                $timeSlot['time_slot_to'],
                                                0,
                                            );

                                            $availQty = $timeSlot['quantity'] - $bookedSlotQuantity;

                                            if ($availQty > 0) {
                                                $pFlag = true;
                                                // break if availabel time slot
                                                break;
                                            }
                                        }
                                    }
                                }
                                // break if availabel on date
                                if ($pFlag) {
                                    break;
                                }
                            }
                            if (!$pFlag) {
                                unset($bookingProducts[$key]);
                            }
                        } else {
                            // check before booking & filter accordingly
                            if ($product['booking_before']) {
                                $availableDateFrom = date('Y-m-d', strtotime('+' . $product['booking_before'] . ' hours'));
                                if ($traverseToDate < strtotime($availableDateFrom)) {
                                    unset($bookingProducts[$key]);
                                    continue;
                                }
                            }
                            // check product have required quantity
                            if ($product['quantity'] >= $quantity) {
                                // now check quantity in order & decrease it
                                $orderQunatity = $objBookingOrders->getProductOrderedQuantityInDateRange(
                                    $product['id_product'],
                                    date('Y-m-d', $bookingFrom),
                                    date('Y-m-d', $traverseToDate),
                                    0,
                                );
                                $availQty = $product['quantity'] - $orderQunatity;
                                if ($availQty <= 0) {
                                    unset($bookingProducts[$key]);
                                    continue;
                                }
                            } else {
                                unset($bookingProducts[$key]);
                                continue;
                            }
                            // get booking product disable dates
                            $bookingDisableDatesInfo = $objBookingDisableDates->getBookingProductDisableDatesInfoFormatted(
                                $product['id_product'],
                            );
                            if (!empty($bookingDisableDatesInfo['disabledDays']) || !empty($bookingDisableDatesInfo['disabledDates'])) {
                                $pFlag = false;
                                for ($date = $bookingFrom; $date <= $traverseToDate; $date = ($date + $totalDaySeconds)) {
                                    if (!$objBookingDisableDates->isDateOrDayDisabled($date, $bookingDisableDatesInfo)) {
                                        $pFlag = true;
                                        break;
                                    }
                                }
                                if (!$pFlag) {
                                    unset($bookingProducts[$key]);
                                }
                            }
                        }
                    }
                }
            }

            $appliedProducts = WkBookingProductInformation::getProducts($bookingProducts);
            $appliedBookingProduct = $this->filterProductsByPage($appliedProducts, $p, $n);
            if ($appliedProducts) {
                $this->pagination(count($appliedProducts));
            }
            if ($bookingProducts) {
                $this->context->smarty->assign(
                    [
                        'products' => $appliedBookingProduct,
                        'total_items' => count($bookingProducts),
                        'order_selected' => $order,
                        'wk_hummingbird_theme' => WkBookingPsHelper::isHummingbirdTheme(),
                    ],
                );
            }
        }

        $this->setTemplate('module:psbooking/views/templates/front/booking_product.tpl');
    }

    public function filterProductsByPage($shopProduct, $p, $n)
    {
        $result = [];
        if ($shopProduct) {
            $start = ($p - 1) * $n;
            $end = $start + $n;
            for ($i = $start; $i < $end; ++$i) {
                if (array_key_exists($i, $shopProduct)) {
                    $result[] = $shopProduct[$i];
                }
            }
        }

        return $result;
    }

    public function pagination($totalProducts = null)
    {
        $this->context = Context::getContext();

        // Retrieve the default number of products per page and the other available selections
        $defaultProductsPerPage = max(1, (int) Configuration::get('PS_PRODUCTS_PER_PAGE'));
        $nArray = [$defaultProductsPerPage, $defaultProductsPerPage * 2, $defaultProductsPerPage * 5];

        if ((int) Tools::getValue('n') && (int) $totalProducts > 0) {
            $nArray[] = $totalProducts;
        }
        // Retrieve the current number of products per page
        // (either the default, the GET parameter or the one in the cookie)
        $n = $defaultProductsPerPage;
        if (
            isset($this->context->cookie->nb_item_per_page)
            && in_array($this->context->cookie->nb_item_per_page, $nArray)
        ) {
            $n = (int) $this->context->cookie->nb_item_per_page;
        }

        if ((int) Tools::getValue('n') && in_array((int) Tools::getValue('n'), $nArray)) {
            $n = (int) Tools::getValue('n');
        }

        // Retrieve the page number (either the GET parameter or the first page)
        $p = (int) Tools::getValue('p', 1);

        // If the parameter is not correct then redirect
        // (do not merge with the previous line,
        // the redirect is required in order to avoid duplicate content)
        if ($p < 1) {
            Tools::redirect($this->context->link->getPaginationLink('bookingproduct', 0, (bool) $n, false, true, false));
        }

        // Remove the page parameter in order to get a clean URL for the pagination template
        $currentUrl = preg_replace('/(?:(\?)|&amp;)p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

        if ($n != $defaultProductsPerPage || isset($this->context->cookie->nb_item_per_page)) {
            // $this->context->cookie->nb_item_per_page = $n;
        }

        $pagesNb = ceil($totalProducts / (int) $n);
        if ($p > $pagesNb && $totalProducts != 0) {
            Tools::redirect($this->context->link->getPaginationLink('bookingproduct', 0, (bool) $n, false, (bool) $pagesNb, false));
        }

        $range = 2; /* how many pages around page selected */
        $start = (int) ($p - $range);
        if ($start < 1) {
            $start = 1;
        }

        $stop = (int) ($p + $range);
        if ($stop > $pagesNb) {
            $stop = (int) $pagesNb;
        }

        $this->context->smarty->assign(
            [
                'nb_products' => $totalProducts,
                'products_per_page' => $n,
                'pages_nb' => $pagesNb,
                'p' => $p,
                'n' => $n,
                'nArray' => $nArray,
                'range' => $range,
                'start' => $start,
                'stop' => $stop,
                'current_url' => $currentUrl,
            ],
        );
    }

    public function setMedia()
    {
        $this->addCSS(_MODULE_DIR_ . 'psbooking/views/css/booking_page.css');
        $this->addJS(_MODULE_DIR_ . 'psbooking/views/js/booking_page.js');

        return parent::setMedia();
    }
}
