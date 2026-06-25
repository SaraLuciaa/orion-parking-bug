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

class PaymentModule extends PaymentModuleCore
{
    protected function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }
        if (Module::isInstalled('psbooking') && Module::isEnabled('psbooking')) {
            include_once dirname(__FILE__) . '/../../modules/psbooking/classes/WkBookingRequiredClasses.php';
            $pathToFindEmail = _PS_MODULE_DIR_
            . 'psbooking/views/templates/hook/mails/'
            . $this->context->language->iso_code .
            DIRECTORY_SEPARATOR
            . $template_name;
            if (Tools::file_exists_cache($pathToFindEmail)) {
                $isExistBookingProduct = false;
                $bookingProductInfo = new WkBookingProductInformation();
                $wkBookingsCart = new WkBookingsCart();
                $this->context = Context::getContext();
                foreach ($var as $key => &$product) {
                    $idProduct = $product['id_product'];
                    $idProductAttribute = isset($product['id_product_attribute']) ? $product['id_product_attribute'] : 0;
                    if ($bookingProductInfo->getBookingProductInfoByIdProduct($idProduct)) {
                        $isExistBookingProduct = true;
                        if ($bookingProductCartInfo = $wkBookingsCart->getBookingProductCartInfo(
                            $product['id_product'],
                            $this->context->cart->id,
                            $idProductAttribute,
                        )) {
                            foreach ($bookingProductCartInfo as $keyProduct => $cartB) {
                                if ($cartB['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $cartB['booking_type'] == WkBookingProductInformation::TYPE_RENTAL) {
                                    $numDays = WkBookingProductFeaturePricing::getNumberOfDays(
                                        $cartB['date_from'],
                                        $cartB['date_to'],
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartB['quantity'] * $numDays;
                                    $totalPriceBkingP = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $idProduct,
                                        $cartB['date_from'],
                                        $cartB['date_to'],
                                        false,
                                        $this->context->currency->id,
                                        1,
                                        $idProductAttribute,
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                        (float) ($cartB['quantity'] * $totalPriceBkingP['total_price_tax_excl']),
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                        (float) ($cartB['quantity'] * $totalPriceBkingP['total_price_tax_incl']),
                                    );
                                } elseif ($cartB['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT) {
                                    $bkingTmSltPr = [];
                                    $objTimeSlot = new WkBookingProductTimeSlotPrices();
                                    $slotDetails = $objTimeSlot->getProductTimeSlotDetails(
                                        $idProduct,
                                        $cartB['date_from'],
                                        $cartB['time_from'],
                                        $cartB['time_to'],
                                    );
                                    if (empty($slotDetails)) {
                                        $day = date('N', strtotime($cartB['date_from']));
                                        $slotDetails = $objTimeSlot->getProductTimeDayWiseSlotDetails(
                                            $idProduct,
                                            $day,
                                            $cartB['time_from'],
                                            $cartB['time_to'],
                                        );
                                    }
                                    if ($slotDetails) {
                                        $bkingTmSltPr['price_tax_excl'] = $slotDetails['price'];
                                        $taxRate = (float) WkBookingProductInformation::getAppliedProductTaxRate(
                                            $idProduct,
                                        );
                                        $per = ((100 + $taxRate) / 100);
                                        $bkingTmSltPr['price_tax_incl'] = $bkingTmSltPr['price_tax_excl'] * $per;
                                        $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartB['quantity'];
                                        $totalPriceBkingP = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                            $idProduct,
                                            $cartB['date_from'],
                                            $cartB['date_from'],
                                            $bkingTmSltPr,
                                            $this->context->currency->id,
                                        );
                                    }
                                } elseif ($cartB['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                                    $bookingProductCartInfo[$keyProduct]['totalQty'] = $cartB['quantity'];
                                    $totalPriceBkingP = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $idProduct,
                                        $cartB['date_from'],
                                        $cartB['date_to'],
                                        false,
                                        $this->context->currency->id,
                                        0,
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                        (float) ($cartB['quantity'] * $totalPriceBkingP['total_price_tax_excl']),
                                    );
                                    $bookingProductCartInfo[$keyProduct]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                        (float) ($cartB['quantity'] * $totalPriceBkingP['total_price_tax_incl']),
                                    );
                                }
                                if (isset($totalPriceBkingP)) {
                                    $uFP = Product::getTaxCalculationMethod() == PS_TAX_EXC ? WkBookingPsHelper::displayPrice(
                                        (float) $totalPriceBkingP['total_price_tax_incl'],
                                    ) : WkBookingPsHelper::displayPrice(
                                        (float) $totalPriceBkingP['total_price_tax_excl'],
                                    );
                                    $bookingProductCartInfo[$keyProduct]['unit_feature_price'] = $uFP;
                                    $tRFPF = Product::getTaxCalculationMethod() == PS_TAX_EXC ? WkBookingPsHelper::displayPrice(
                                        (float) ($cartB['quantity'] * $totalPriceBkingP['total_price_tax_incl']),
                                    ) : WkBookingPsHelper::displayPrice(
                                        (float) ($cartB['quantity'] * $totalPriceBkingP['total_price_tax_excl']),
                                    );
                                    $bookingProductCartInfo[$keyProduct]['total_range_feature_price_formated'] = $tRFPF;
                                }
                            }
                            $var[$key]['isBookingProduct'] = 1;
                            $var[$key]['booking_product_data'] = $bookingProductCartInfo;
                        }
                    }
                }
                if ($isExistBookingProduct) {
                    $this->context->smarty->assign('list', $var);

                    return $this->context->smarty->fetch($pathToFindEmail);
                }
            }
        }

        return parent::getEmailTemplateContent($template_name, $mail_type, $var);
    }
}
