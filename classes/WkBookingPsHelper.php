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

class WkBookingPsHelper
{
    public static function displayPrice($price, $currency = null)
    {
        if (!is_numeric($price)) {
            return $price;
        }

        $context = Context::getContext();
        $currency = $currency ?: $context->currency;

        if (Tools::version_compare(_PS_VERSION_, '8.0.0', '<')) {
            return Tools::displayPrice((float) $price, $currency);
        }

        if (is_int($currency)) {
            $currency = Currency::getCurrencyInstance($currency);
        }

        $locale = $context->getCurrentLocale();
        $currencyCode = is_array($currency) ? $currency['iso_code'] : $currency->iso_code;

        return $locale->formatPrice($price, $currencyCode);
    }

    public static function isHummingbirdTheme()
    {
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            if (Context::getContext()->shop->theme->getName() == 'hummingbird') {
                return true;
            }
        }

        return false;
    }
}
