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
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

include_once dirname(__FILE__) . '/../psbooking/classes/WkBookingRequiredClasses.php';

class PsBooking extends Module
{
    public $_html;

    public function __construct()
    {
        $this->name = 'psbooking';
        $this->tab = 'front_office_features';
        $this->version = '6.0.3';
        $this->module_key = '7a17a3db8cc99dfc09d2ddf87a321a09';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        parent::__construct();
        $this->displayName = $this->l('Booking and reservation system');
        $this->description = $this->l('Online booking and reservation system');
    }

    private function postProcess()
    {
        if (Tools::isSubmit('btnSubmit_generalconfig')) {
            $WK_CONSIDER_DATE_TO = Configuration::get('WK_CONSIDER_DATE_TO');
            if (Tools::getValue('WK_CONSIDER_DATE_TO') != $WK_CONSIDER_DATE_TO
            ) {
                $objBookingsCart = new WkBookingsCart();
                if (!$objBookingsCart->deleteCurrentCustomerCarts($this->context->shop->id)) {
                    $this->context->controller->errors[] = $this->l('Some issue occurred while deleting cart.');
                }
            }
        }
        if (Tools::isSubmit('btnSubmit_mapconfig')) {
            if (!Validate::isMessage(trim(Tools::getValue('WK_BOOKING_GEOLOCATION_API_KEY')))) {
                $this->context->controller->errors[] = $this->l('Invalid google map API key.');
            }
        }

        if (!count($this->context->controller->errors)) {
            if (Tools::isSubmit('btnSubmit_generalconfig')) {
                Configuration::updateValue('WK_CONSIDER_DATE_TO', Tools::getValue('WK_CONSIDER_DATE_TO'));
                Configuration::updateValue('WK_FEATURE_PRICE_RULES_SHOW', Tools::getValue('WK_FEATURE_PRICE_RULES_SHOW'));
                Configuration::updateValue('WK_MONDAY_FIRST_DAY_WEEK', Tools::getValue('WK_MONDAY_FIRST_DAY_WEEK'));
                Configuration::updateValue(
                    'WK_BOOKING_PRODUCTS_DISPLAY_LINK',
                    Tools::getValue('WK_BOOKING_PRODUCTS_DISPLAY_LINK'),
                );
                Configuration::updateValue(
                    'WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT',
                    Tools::getValue('WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT'),
                );
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' .
                    $this->tab . '&module_name=' . $this->name . '&conf=4',
                );
            }

            if (Tools::isSubmit('btnSubmit_mapconfig')) {
                Configuration::updateValue(
                    'WK_BOOKING_GEOLOCATION_API_KEY',
                    trim(Tools::getValue('WK_BOOKING_GEOLOCATION_API_KEY')),
                );
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' .
                    $this->tab . '&module_name=' . $this->name . '&page=mapSetting&conf=4',
                );
            }

            if (Tools::isSubmit('btnSubmit_eventconfig')) {
                Configuration::updateValue('WK_SHOW_BOOKING_EVENT_PAGE', Tools::getValue('WK_SHOW_BOOKING_EVENT_PAGE'));
                Configuration::updateValue('WK_SHOW_EVENT_PAGE_WHATSAPP', Tools::getValue('WK_SHOW_EVENT_PAGE_WHATSAPP'));
                Configuration::updateValue('WK_SHOW_EVENT_PAGE_FACEBOOK', Tools::getValue('WK_SHOW_EVENT_PAGE_FACEBOOK'));
                Configuration::updateValue('WK_SHOW_EVENT_PAGE_INSTAGRAM', Tools::getValue('WK_SHOW_EVENT_PAGE_INSTAGRAM'));
                Configuration::updateValue('WK_SHOW_EVENT_PAGE_TWITTER', Tools::getValue('WK_SHOW_EVENT_PAGE_TWITTER'));
                Configuration::updateValue('WK_SHOW_EVENT_PAGE_CLIPBOARD', Tools::getValue('WK_SHOW_EVENT_PAGE_CLIPBOARD'));
                Configuration::updateValue('WK_SHOW_RELATED_EVENTS', Tools::getValue('WK_SHOW_RELATED_EVENTS'));
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' .
                    $this->tab . '&module_name=' . $this->name . '&page=eventSetting&conf=4',
                );
            }
        }
    }

    public function generateTicketQrCode($data, $filename)
    {
        $size = 200;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://chart.googleapis.com/chart?');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "chs={$size}x{$size}&cht=qr&chl=" . urlencode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $img = curl_exec($ch);
        curl_close($ch);
        if ($img) {
            if ($filename) {
                if (!preg_match("#\.png$#i", $filename)) {
                    $filename .= '.png';
                }
                file_put_contents(_PS_MODULE_DIR_ . 'psbooking/views/img/qrcode/' . $filename, $img);
            }
        }
    }

    public function getContent()
    {
        // start cross-selling
        Media::addJsDef([
            'wkModuleAddonKey' => $this->module_key,
            'wkModuleAddonsId' => 8764,
            'wkModuleTechName' => $this->name,
            'wkModuleDoc' => file_exists(_PS_MODULE_DIR_ . $this->name . '/docs/doc_en.pdf'),
        ]);
        $this->context->controller->addJs('https://prestashop.webkul.com/crossselling/wkcrossselling.min.js?t=' . time());
        // end
        $params = ['configure' => $this->name];
        $moduleAdminLink = Context::getContext()->link->getAdminLink('AdminModules', true, [], $params);
        // get current page
        $currentPage = 'generalSetting';
        $page = Tools::getValue('page');
        if (!empty($page)) {
            $currentPage = Tools::getValue('page');
        }
        Media::addJsDef([
            'moduleAdminLink' => $moduleAdminLink,
            'currentPage' => $currentPage,
        ]);
        $this->context->controller->addJS([
            $this->_path . 'views/js/vue/vue.min.js',
            $this->_path . 'views/js/config/menu.js',
        ]);
        if (Tools::isSubmit('btnSubmit_generalconfig')) {
            $this->postProcess();
        } elseif (Tools::isSubmit('btnSubmit_eventconfig')) {
            $this->postProcess();
        } elseif (Tools::isSubmit('btnSubmit_mapconfig')) {
            $this->postProcess();
        }
        $this->context->smarty->assign(
            [
                'moduleAdminLink' => $moduleAdminLink,
                'module_version' => $this->version,
            ],
        );
        $this->context->smarty->assign([
            'generalForm' => $this->renderGeneralSettings(),
            'eventForm' => $this->renderEventSettings(),
            'mapForm' => $this->renderMapSettings(),
        ]);

        $this->_html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/menu.tpl');

        return $this->_html;
    }

    public function renderGeneralSettings()
    {
        $objOrderCurrency = new Currency($this->context->currency->id);
        $this->context->smarty->assign(
            [
                'wk_strike_price' => WkBookingPsHelper::displayPrice(
                    100,
                    $objOrderCurrency,
                ),
            ],
        );
        $regularPriceDesc = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'psbooking/views/templates/admin/_partials/booking_config_regular_price.tpl',
        );
        // Get default language
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm = [];
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('General'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Consider price for \'date to\''),
                    'name' => 'WK_CONSIDER_DATE_TO',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'desc' => $this->l('If yes, last date price will be added for the booking product. ')
                    . $this->l('Otherwise booking will not be considered for the last date. '),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display booking price rules to customers'),
                    'name' => 'WK_FEATURE_PRICE_RULES_SHOW',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display booking products link'),
                    'name' => 'WK_BOOKING_PRODUCTS_DISPLAY_LINK',
                    'is_bool' => true,
                    'desc' => $this->l('If yes, booking products link will display in header navigation bar.'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display regular price after discount to customers'),
                    'name' => 'WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT',
                    'is_bool' => true,
                    'desc' => $regularPriceDesc,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Is monday the first day of the week?'),
                    'name' => 'WK_MONDAY_FIRST_DAY_WEEK',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'desc' => $this->l('It works only for front-office calendar.'),
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'btnSubmit_generalconfig',
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit_generalconfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&page=generalSetting';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fieldsForm);
    }

    public function renderEventSettings()
    {
        // Get default language
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm = [];
        $fieldsForm[1]['form'] = [
            'legend' => [
                'title' => $this->l('Event page'),
                'icon' => 'icon-file',
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Display module design page'),
                    'name' => 'WK_SHOW_BOOKING_EVENT_PAGE',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'desc' => $this->l('If yes, only event type booking products land on our module page'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display whatsapp share'),
                    'name' => 'WK_SHOW_EVENT_PAGE_WHATSAPP',
                    'form_group_class' => 'share_option',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display facebook share'),
                    'name' => 'WK_SHOW_EVENT_PAGE_FACEBOOK',
                    'form_group_class' => 'share_option',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display instagram share'),
                    'name' => 'WK_SHOW_EVENT_PAGE_INSTAGRAM',
                    'form_group_class' => 'share_option',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display twitter share'),
                    'name' => 'WK_SHOW_EVENT_PAGE_TWITTER',
                    'form_group_class' => 'share_option',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display copy to clipboard link'),
                    'name' => 'WK_SHOW_EVENT_PAGE_CLIPBOARD',
                    'form_group_class' => 'share_option',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display related events'),
                    'name' => 'WK_SHOW_RELATED_EVENTS',
                    'form_group_class' => 'share_option',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'desc' => $this->l('If yes, related events will be display on the basis of default category of product'),
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'btnSubmit_eventconfig',
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit_eventconfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&page=eventSetting';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fieldsForm);
    }

    public function renderMapSettings()
    {
        $apiCreateLink = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'psbooking/views/templates/admin/_partials/create_api.tpl',
        );
        // Get default language
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm = [];
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Google map'),
                'icon' => 'icon-map-marker',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Google map API key'),
                    'name' => 'WK_BOOKING_GEOLOCATION_API_KEY',
                    'desc' => $this->l('Unique API key for google map.') . $apiCreateLink,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'btnSubmit_mapconfig',
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit_mapconfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&page=mapSetting';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fieldsForm);
    }

    public function getConfigFieldsValues()
    {
        $configVars = [
            'WK_CONSIDER_DATE_TO' => Tools::getValue('WK_CONSIDER_DATE_TO', Configuration::get('WK_CONSIDER_DATE_TO')),
            'WK_FEATURE_PRICE_RULES_SHOW' => Tools::getValue(
                'WK_FEATURE_PRICE_RULES_SHOW',
                Configuration::get('WK_FEATURE_PRICE_RULES_SHOW'),
            ),
            'WK_MONDAY_FIRST_DAY_WEEK' => Tools::getValue(
                'WK_MONDAY_FIRST_DAY_WEEK',
                Configuration::get('WK_MONDAY_FIRST_DAY_WEEK'),
            ),
            'WK_BOOKING_PRODUCTS_DISPLAY_LINK' => Tools::getValue(
                'WK_BOOKING_PRODUCTS_DISPLAY_LINK',
                Configuration::get('WK_BOOKING_PRODUCTS_DISPLAY_LINK'),
            ),
            'WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT' => Tools::getValue(
                'WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT',
                Configuration::get('WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT'),
            ),
            'WK_BOOKING_GEOLOCATION_API_KEY' => Tools::getValue(
                'WK_BOOKING_GEOLOCATION_API_KEY',
                Configuration::get('WK_BOOKING_GEOLOCATION_API_KEY'),
            ),
            'WK_SHOW_BOOKING_EVENT_PAGE' => Tools::getValue(
                'WK_SHOW_BOOKING_EVENT_PAGE',
                Configuration::get('WK_SHOW_BOOKING_EVENT_PAGE'),
            ),
            'WK_SHOW_EVENT_PAGE_WHATSAPP' => Tools::getValue(
                'WK_SHOW_EVENT_PAGE_WHATSAPP',
                Configuration::get('WK_SHOW_EVENT_PAGE_WHATSAPP'),
            ),
            'WK_SHOW_EVENT_PAGE_FACEBOOK' => Tools::getValue(
                'WK_SHOW_EVENT_PAGE_FACEBOOK',
                Configuration::get('WK_SHOW_EVENT_PAGE_FACEBOOK'),
            ),
            'WK_SHOW_EVENT_PAGE_INSTAGRAM' => Tools::getValue(
                'WK_SHOW_EVENT_PAGE_INSTAGRAM',
                Configuration::get('WK_SHOW_EVENT_PAGE_INSTAGRAM'),
            ),
            'WK_SHOW_EVENT_PAGE_TWITTER' => Tools::getValue(
                'WK_SHOW_EVENT_PAGE_TWITTER',
                Configuration::get('WK_SHOW_EVENT_PAGE_TWITTER'),
            ),
            'WK_SHOW_EVENT_PAGE_CLIPBOARD' => Tools::getValue(
                'WK_SHOW_EVENT_PAGE_CLIPBOARD',
                Configuration::get('WK_SHOW_EVENT_PAGE_CLIPBOARD'),
            ),
            'WK_SHOW_RELATED_EVENTS' => Tools::getValue(
                'WK_SHOW_RELATED_EVENTS',
                Configuration::get('WK_SHOW_RELATED_EVENTS'),
            ),
        ];

        return $configVars;
    }

    public function hookDisplayHeader()
    {
        if (isset($this->context->cart->id) && $this->context->cart->id) {
            $wkBookingsCart = new WkBookingsCart();
            $cartBData = $wkBookingsCart->getCartInfo($this->context->cart->id);
            if ($cartBData) {
                foreach ($cartBData as $booking) {
                    /* To remove room from cart before today's date */
                    $objWkBookingsCart = new WkBookingsCart($booking['id']);
                    $idProduct = $booking['id_product'];
                    $idProductAttribute = $booking['id_product_attribute'];
                    $bookingType = $objWkBookingsCart->booking_type;
                    if (strtotime($booking['date_from']) < strtotime(date('Y-m-d'))) {
                        if ($bookingType == 1) {
                            $daysCount = (int) WkBookingProductFeaturePricing::getNumberOfDays(
                                $objWkBookingsCart->date_from,
                                $objWkBookingsCart->date_to,
                            );
                        } else {
                            $daysCount = 1;
                        }
                        $quantityToReduce = ($daysCount * (int) $objWkBookingsCart->quantity);
                        if ($this->context->cart->updateQty(
                            (int) $quantityToReduce,
                            (int) $idProduct,
                            (int) $idProductAttribute,
                            false,
                            'down',
                            0,
                            null,
                            true,
                        )) {
                            if (!$objWkBookingsCart->delete()) {
                                $e = $this->l('Error while deleting product from cart.');
                                $this->context->controller->errors[] = $e;
                            }
                        } else {
                            $this->context->controller->errors[] = $this->l('Error while updating cart quantity.');
                        }
                    }
                    /* To remove bookings from cart if product is deleted */
                    $product = new Product($idProduct);
                    if (!Validate::isLoadedObject($product)) {
                        if (!$wkBookingsCart->deleteBookingProductCartByIdProductIdCart(
                            $idProduct,
                            $this->context->cart->id,
                        )) {
                            $this->context->controller->errors[] = $this->l('Error while deleting product from cart.');
                        }
                    } else {
                        /* To remove bookings from cart if time slots are deleted */
                        if ($bookingType == 2) {
                            $objTimeSlotPrices = new WkBookingProductTimeSlotPrices();
                            $slotDetails = $objTimeSlotPrices->getProductTimeSlotDetails(
                                $idProduct,
                                $objWkBookingsCart->date_from,
                                $objWkBookingsCart->time_from,
                                $objWkBookingsCart->time_to,
                            );
                            if (empty($slotDetails)) {
                                $day = date('N', strtotime($objWkBookingsCart->date_from));
                                $slotDetails = $objTimeSlotPrices->getProductTimeDayWiseSlotDetails(
                                    $idProduct,
                                    $day,
                                    $objWkBookingsCart->time_from,
                                    $objWkBookingsCart->time_to,
                                );
                            }
                            if (!$slotDetails) {
                                if ($this->context->cart->updateQty(
                                    (int) $objWkBookingsCart->quantity,
                                    (int) $idProduct,
                                    (int) $idProductAttribute,
                                    false,
                                    'down',
                                    0,
                                    null,
                                    true,
                                )) {
                                    if (!$objWkBookingsCart->delete()) {
                                        $e = $this->l('Error while deleting booking from cart.');
                                        $this->context->controller->errors[] = $e;
                                    }
                                } else {
                                    $e = $this->l('Error while updating cart quantity.');
                                    $this->context->controller->errors[] = $e;
                                }
                            } else {
                                // if quantity is changed then remove from cart
                                $wkBookingOrder = new WkBookingsOrders();
                                $bookedSlotQuantity = $wkBookingOrder->getProductTimeSlotOrderedQuantity(
                                    $booking['id_product'],
                                    date('Y-m-d', strtotime($booking['date_from'])),
                                    $booking['time_from'],
                                    $booking['time_to'],
                                    false,
                                );
                                if ($bookedSlotQuantity == null) {
                                    $bookedSlotQuantity = 0;
                                }
                                $maxAvailableQuantity = $slotDetails['quantity'] - $bookedSlotQuantity;
                                if ($maxAvailableQuantity < $objWkBookingsCart->quantity) {
                                    if ($this->context->cart->updateQty(
                                        (int) $objWkBookingsCart->quantity,
                                        (int) $idProduct,
                                        (int) $idProductAttribute,
                                        false,
                                        'down',
                                        0,
                                        null,
                                        true,
                                    )) {
                                        if (!$objWkBookingsCart->delete()) {
                                            $e = $this->l('Error while deleting booking from cart.');
                                            $this->context->controller->errors[] = $e;
                                        }
                                    } else {
                                        $e = $this->l('Error while updating cart quantity.');
                                        $this->context->controller->errors[] = $e;
                                    }
                                }
                            }
                        }
                    }
                    // To remove if product is added in disable days and date
                    $objBookingDisableDates = new WkBookingProductDisabledDates();
                    $bookingDisableDatesInfo = $objBookingDisableDates->getBookingProductDisableDatesInfoFormatted(
                        $idProduct,
                    );
                    if ($bookingDisableDatesInfo) {
                        $this->removeProductForDisableDaysAndDate(
                            $bookingDisableDatesInfo,
                            $bookingType,
                            $objWkBookingsCart,
                            $idProduct,
                            $idProductAttribute,
                        );
                    }
                }
            }
            // check booking product is added into cart without booking cart information then delete product from cart because booking product can not booked without booking information
            $products = $this->context->cart->getProducts();
            if (!empty($products) && is_array($products)) {
                $bookingProductInfo = new WkBookingProductInformation();
                foreach ($products as $product) {
                    $isBookingProduct = $bookingProductInfo->getBookingProductInfoByIdProduct($product['id_product']);
                    if ($isBookingProduct) {
                        $bookingCartDetails = $wkBookingsCart->getBookingProductCartInfo($product['id_product'], $this->context->cart->id);
                        if (empty($bookingCartDetails)) {
                            $this->context->cart->deleteProduct($product['id_product'], 0);
                        }
                    }
                }
            }
        }
        // disable the feature price plans which date range has been expired
        if ('product' == Tools::getValue('controller')) {
            if ($productFeaturePlans = WkBookingProductFeaturePricing::getFeaturePriceByIdProduct(
                Tools::getValue('id_product'),
            )) {
                $currentDateTime = strtotime(date('Y-m-d'));
                foreach ($productFeaturePlans as $plan) {
                    $planDateFrom = strtotime($plan['date_from']);
                    $planDateTo = strtotime($plan['date_to']);
                    if ($plan['date_selection_type'] == 2) {
                        $condition = ($currentDateTime > $planDateFrom);
                    } else {
                        $condition = ($currentDateTime > $planDateFrom && $currentDateTime > $planDateTo);
                    }
                    if ($condition) {
                        $objFeaturePrice = new WkBookingProductFeaturePricing($plan['id']);
                        $objFeaturePrice->active = 0;
                        $objFeaturePrice->save();
                    }
                }
            }
        }

        // redirect event booking product on module design event page
        if ('product' == Tools::getValue('controller') && 'quickview' != Tools::getValue('action') && Configuration::get('WK_SHOW_BOOKING_EVENT_PAGE')) {
            $idProduct = Tools::getValue('id_product');
            $objBookingProduct = new WkBookingProductInformation();
            $bookingData = $objBookingProduct->getBookingProductInfoByIdProduct($idProduct);
            if ($bookingData) {
                if ($bookingData['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                    $link = $this->context->link->getModuleLink('psbooking', 'event', ['id' => $bookingData['id']]);
                    Tools::redirect($link);
                }
            }
        }
        
        if ($this->context->controller->php_self == 'index') {
        // Cargar CSS y JS solo en la p¨¢gina de inicio
        $this->context->controller->addCSS($this->_path . 'views/css/booking_page.css');
        $this->context->controller->addJS($this->_path . 'views/js/booking_page.js');
        }
    }

    public function removeProductForDisableDaysAndDate(
        $bookingDisableDatesInfo,
        $bookingType,
        $objWkBookingsCart,
        $idProduct,
        $idProductAttribute
    ) {
        if (isset($bookingDisableDatesInfo['disabledDays'])
        && !empty($bookingDisableDatesInfo['disabledDays'])) {
            if ($bookingType == 1) {
                $totalDaySeconds = 24 * 60 * 60;
                if ($objWkBookingsCart->consider_last_date) {
                    $cToDate = strtotime($objWkBookingsCart->date_to);
                } else {
                    $cToDate = strtotime($objWkBookingsCart->date_to) - $totalDaySeconds;
                }
                $dateFrom = strtotime($objWkBookingsCart->date_from);
                for ($date = $dateFrom; $date <= $cToDate; $date = ($date + $totalDaySeconds)) {
                    $day = date('w', $date);
                    if (in_array($day, $bookingDisableDatesInfo['disabledDays'])) {
                        $daysCount = (int) WkBookingProductFeaturePricing::getNumberOfDays(
                            $objWkBookingsCart->date_from,
                            $objWkBookingsCart->date_to,
                        );
                        $quantityToReduce = ($daysCount * (int) $objWkBookingsCart->quantity);
                        if ($this->context->cart->updateQty(
                            (int) $quantityToReduce,
                            (int) $idProduct,
                            (int) $idProductAttribute,
                            false,
                            'down',
                            0,
                            null,
                            true,
                        )) {
                            if (!$objWkBookingsCart->delete()) {
                                $error = $this->l('Error while deleting booking from cart.');
                                $this->context->controller->errors[] = $error;
                            } else {
                                break;
                            }
                        } else {
                            $error = $this->l('Error while updating cart quantity.');
                            $this->context->controller->errors[] = $error;
                        }
                    }
                }
            }
            if ($bookingType == 2) {
                $dateFromT = strtotime($objWkBookingsCart->date_from);
                $day = date('w', $dateFromT);
                if (in_array($day, $bookingDisableDatesInfo['disabledDays'])) {
                    if ($this->context->cart->updateQty(
                        (int) $objWkBookingsCart->quantity,
                        (int) $idProduct,
                        (int) $idProductAttribute,
                        false,
                        'down',
                        0,
                        null,
                        true,
                    )) {
                        if (!$objWkBookingsCart->delete()) {
                            $error = $this->l('Error while deleting booking from cart.');
                            $this->context->controller->errors[] = $error;
                        }
                    } else {
                        $this->context->controller->errors[] = $this->l('Error while updating cart quantity.');
                    }
                }
            }
        }
        if (isset($bookingDisableDatesInfo['disabledDates'])
        && !empty($bookingDisableDatesInfo['disabledDates'])) {
            if ($bookingType == 1) {
                $totalDaySeconds = 24 * 60 * 60;
                if ($objWkBookingsCart->consider_last_date) {
                    $cToDate = strtotime($objWkBookingsCart->date_to);
                } else {
                    $cToDate = strtotime($objWkBookingsCart->date_to) - $totalDaySeconds;
                }
                $dateFrom = strtotime($objWkBookingsCart->date_from);
                foreach ($bookingDisableDatesInfo['disabledDates'] as $disabledDates) {
                    $disabledate = strtotime($disabledDates);
                    for ($date = $dateFrom; $date <= $cToDate; $date = ($date + $totalDaySeconds)) {
                        if ($date == $disabledate) {
                            $daysCount = (int) WkBookingProductFeaturePricing::getNumberOfDays(
                                $objWkBookingsCart->date_from,
                                $objWkBookingsCart->date_to,
                            );
                            $quantityToReduce = ($daysCount * (int) $objWkBookingsCart->quantity);
                            if ($this->context->cart->updateQty(
                                (int) $quantityToReduce,
                                (int) $idProduct,
                                (int) $idProductAttribute,
                                false,
                                'down',
                                0,
                                null,
                                true,
                            )) {
                                if (!$objWkBookingsCart->delete()) {
                                    $error = $this->l('Error while deleting booking from cart.');
                                    $this->context->controller->errors[] = $error;
                                } else {
                                    break;
                                }
                            } else {
                                $error = $this->l('Error while updating cart quantity.');
                                $this->context->controller->errors[] = $error;
                            }
                        }
                    }
                }
            }
            if ($bookingType == 2) {
                foreach ($bookingDisableDatesInfo['disabledDates'] as $disabledDates) {
                    $dateFromT = strtotime($objWkBookingsCart->date_from);
                    $disabledate = strtotime($disabledDates);
                    if ($dateFromT == $disabledate) {
                        if ($this->context->cart->updateQty(
                            (int) $objWkBookingsCart->quantity,
                            (int) $idProduct,
                            (int) $idProductAttribute,
                            false,
                            'down',
                            0,
                            null,
                            true,
                        )) {
                            if (!$objWkBookingsCart->delete()) {
                                $e = $this->l('Error while deleting booking from cart.');
                                $this->context->controller->errors[] = $e;
                            }
                        } else {
                            $this->context->controller->errors[] = $this->l('Error while updating cart quantity.');
                        }
                    }
                }
            }
        }
    }

    public function cartOverridedTpl()
    {
        $presenter = new CartPresenter();
        $isBookingProductAvailable = 0;
        $presentedCart = $presenter->present($this->context->cart, true);
        $bookingProductInfo = new WkBookingProductInformation();
        $wkBookingsCart = new WkBookingsCart();
        foreach ($presentedCart['products'] as $key => $product) {
            $idProduct = $product['id_product'];
            $idProductAttribute = $product['id_product_attribute'];
            $isBookingProduct = $bookingProductInfo->getBookingProductInfoByIdProduct($idProduct);

            if ($isBookingProduct) {
                $bookingProCartInfo = $wkBookingsCart->getBookingProductCartInfo(
                    $idProduct,
                    $this->context->cart->id,
                    $idProductAttribute,
                );
                $isBookingProductAvailable = 1;
                if ($bookingProCartInfo) {
                    // $presentedCart->products[$key]->isBookingProduct = '';
                    foreach ($bookingProCartInfo as $keyP => $cartB) {
                        $ttlPriBkingProd = [];
                        if ($cartB['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $cartB['booking_type'] == WkBookingProductInformation::TYPE_RENTAL) {
                            $numDays = WkBookingProductFeaturePricing::getNumberOfDays(
                                $cartB['date_from'],
                                $cartB['date_to'],
                            );
                            $totalQty = $cartB['quantity'] * $numDays;
                            $bookingProCartInfo[$keyP]['totalQty'] = $totalQty;
                            $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $idProduct,
                                $cartB['date_from'],
                                $cartB['date_to'],
                                false,
                                $this->context->currency->id,
                                1,
                                $idProductAttribute,
                            );
                            $bookingProCartInfo[$keyP]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_excl']),
                            );
                            $bookingProCartInfo[$keyP]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_incl']),
                            );
                        } elseif ($cartB['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT) {
                            $bkingTmSltPrice = [];
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
                                $bkingTmSltPrice['price_tax_excl'] = $slotDetails['price'];
                                $taxRate = (float) WkBookingProductInformation::getAppliedProductTaxRate(
                                    $idProduct,
                                );
                                $per = ((100 + $taxRate) / 100);
                                $bkingTmSltPrice['price_tax_incl'] = $bkingTmSltPrice['price_tax_excl'] * $per;
                                $bookingProCartInfo[$keyP]['totalQty'] = $cartB['quantity'];
                                $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idProduct,
                                    $cartB['date_from'],
                                    $cartB['date_from'],
                                    $bkingTmSltPrice,
                                    $this->context->currency->id,
                                );
                            }
                        } elseif ($cartB['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                            $totalQty = $cartB['quantity'];
                            $bookingProCartInfo[$keyP]['totalQty'] = $totalQty;
                            $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $idProduct,
                                $cartB['date_from'],
                                $cartB['date_to'],
                                false,
                                $this->context->currency->id,
                                0,
                            );
                            $bookingProCartInfo[$keyP]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_excl']),
                            );
                            $bookingProCartInfo[$keyP]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_incl']),
                            );
                        }
                        if ($ttlPriBkingProd) {
                            $bookingProCartInfo[$keyP]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_excl']),
                            );
                            $bookingProCartInfo[$keyP]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_incl']),
                            );
                            $bookingProCartInfo[$keyP]['unit_feature_price_tax_excl_formated'] = WkBookingPsHelper::displayPrice(
                                (float) $ttlPriBkingProd['total_price_tax_excl'],
                            );
                            $bookingProCartInfo[$keyP]['unit_feature_price_tax_incl_formated'] = WkBookingPsHelper::displayPrice(
                                (float) $ttlPriBkingProd['total_price_tax_incl'],
                            );
                        }
                    }
                    $productValue = $presentedCart['products'][$key]; //  To resolve Notice: Indirect modification of overloaded element
                    $productValue['isBookingProduct'] = 1;
                    $productValue['booking_product_data'] = $bookingProCartInfo;
                }
            }
        }
        $this->context->smarty->assign(
            [
                'presentedCart' => $presentedCart,
            ],
        );

        return $isBookingProductAvailable;
    }

    public function orderConfirmationTpl($idOrder)
    {
        $order = new Order($idOrder);
        $orderPresenter = new OrderPresenter();
        $presentedOrder = $orderPresenter->present($order);
        // if ($presentedOrder) {
        $orderProducts = $presentedOrder['products'];
        $bookingProductInfo = new WkBookingProductInformation();
        $wkBookingsOrders = new WkBookingsOrders();
        foreach ($orderProducts as $key => $product) {
            $isBookingProduct = $bookingProductInfo->getBookingProductInfoByIdProduct($product['id_product']);
            if ($isBookingProduct) {
                $bkingPOrrInf = $wkBookingsOrders->getBookingProductOrderInfo(
                    $product['id_product'],
                    $idOrder,
                    0,
                    $product['id_product_attribute'],
                );
                if ($bkingPOrrInf) {
                    foreach ($bkingPOrrInf as $keyP => $cartB) {
                        $noOfDays = WkBookingProductFeaturePricing::getNumberOfDays(
                            $cartB['date_from'],
                            $cartB['date_to'],
                        );
                        $bkingPOrrInf[$keyP]['totalQty'] = $cartB['quantity'] * $noOfDays;
                        $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                            $product['id_product'],
                            $cartB['date_from'],
                            $cartB['date_to'],
                            false,
                            null,
                            1,
                            $cartB['id_product_attribute'],
                        );
                        $bkingPOrrInf[$keyP]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                            (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_excl']),
                        );
                        $bkingPOrrInf[$keyP]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                            (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_incl']),
                        );
                        $bkingPOrrInf[$keyP]['product_real_price_tax_excl_formated'] = WkBookingPsHelper::displayPrice(
                            (float) ($cartB['quantity'] * $cartB['product_real_price_tax_excl']),
                        );
                        $bkingPOrrInf[$keyP]['total_range_feature_price_tax_excl_formated'] = WkBookingPsHelper::displayPrice(
                            (float) ($cartB['quantity'] * $cartB['range_feature_price_tax_excl']),
                        );
                        $bkingPOrrInf[$keyP]['total_range_feature_price_tax_incl_formated'] = WkBookingPsHelper::displayPrice(
                            (float) ($cartB['quantity'] * $cartB['range_feature_price_tax_incl']),
                        );
                        $bkingPOrrInf[$keyP]['unit_feature_price_tax_excl_formated'] = WkBookingPsHelper::displayPrice(
                            (float) $cartB['range_feature_price_tax_excl'],
                        );
                        $bkingPOrrInf[$keyP]['unit_feature_price_tax_incl_formated'] = WkBookingPsHelper::displayPrice(
                            (float) $cartB['range_feature_price_tax_incl'],
                        );
                    }
                    $orderProducts[$key]['isBookingProduct'] = 1;
                    $orderProducts[$key]['booking_product_data'] = $bkingPOrrInf;
                }
            }
        }
        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        $this->context->smarty->assign(
            [
                'orderProducts' => $orderProducts,
                'subtotals' => $presentedOrder['subtotals'],
                'totals' => $presentedOrder['totals'],
                'labels' => $presentedOrder['labels'],
                'add_product_link' => false,
                'order_confirmation_template_file' => _PS_THEME_DIR_ . 'templates/checkout/order-confirmation.tpl',
                'priceDisplay' => $priceDisplay,
            ],
        );
        // }
    }

    public function hookDisplayOverrideTemplate($params)
    {
        if ('customer/history' == $params['template_file']) {
            $orders = [];
            $customerOrders = Order::getCustomerOrders($this->context->customer->id);
            $orderPresenter = new OrderPresenter();
            foreach ($customerOrders as $customerOrder) {
                $order = new Order((int) $customerOrder['id_order']);
                $orders[$customerOrder['id_order']] = $orderPresenter->present($order);
            }
            if ($orders) {
                foreach ($orders as &$order) {
                    if ($orderProducts = $order['products']) {
                        $bookingProductInfo = new WkBookingProductInformation();
                        foreach ($orderProducts as $product) {
                            $isBookingProduct = $bookingProductInfo->getBookingProductInfoByIdProduct(
                                $product['id_product'],
                            );
                            if ($isBookingProduct) {
                                $order['bookingProductExists'] = 1;
                            }
                        }
                    }
                }
                $this->context->smarty->assign('orders', $orders);
                if (WkBookingPsHelper::isHummingbirdTheme()) {
                    return 'module:psbooking/views/templates/hook/historyOverrided_hummingbird.tpl';
                } else {
                    return 'module:psbooking/views/templates/hook/historyOverrided.tpl';
                }
            }
        }
        if ('checkout/cart' == $params['template_file']
        || 'checkout/_partials/cart-detailed' == $params['template_file']) {
            $isBookingProductAvailable = $this->cartOverridedTpl();
            if ($isBookingProductAvailable) {
                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                $this->context->smarty->assign(
                    [
                        'priceDisplay' => $priceDisplay,
                        'cart_template_file' => _PS_THEME_DIR_ . 'templates/checkout/cart.tpl',
                    ],
                );
                if ('checkout/cart' == $params['template_file']) {
                    if (WkBookingPsHelper::isHummingbirdTheme()) {
                        return 'module:psbooking/views/templates/hook/cartCheckoutOverrided_hummingbird.tpl';
                    } else {
                        return 'module:psbooking/views/templates/hook/cartCheckoutOverrided.tpl';
                    }
                }
                if ('checkout/_partials/cart-detailed' == $params['template_file']) {
                    if (WkBookingPsHelper::isHummingbirdTheme()) {
                        return 'module:psbooking/views/templates/hook/cart-detailed-overrided_hummingbird.tpl';
                    } else {
                        return 'module:psbooking/views/templates/hook/cart-detailed-overrided.tpl';
                    }
                }
            }
        }
        if ('checkout/order-confirmation' == $params['template_file']) {
            $idOrder = $params['controller']->id_order;
            $this->orderConfirmationTpl($idOrder);
            if (WkBookingPsHelper::isHummingbirdTheme()) {
                return 'module:psbooking/views/templates/hook/checkoutOrderConfirmationOverrided_hummingbird.tpl';
            } else {
                return 'module:psbooking/views/templates/hook/checkoutOrderConfirmationOverrided.tpl';
            }
        }
        if ('customer/order-detail' == $params['template_file']) {
            $idOrder = Tools::getValue('id_order');
            $order = new Order($idOrder);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                $orderDetails = (new OrderPresenter())->present($order);
                $orderProducts = $orderDetails['products'];
                if (!empty($orderProducts)) {
                    $bookingProductInfo = new WkBookingProductInformation();
                    $wkBookingsOrders = new WkBookingsOrders();
                    $objOrderCurrency = new Currency($order->id_currency);
                    $bookingProductExists = 0;
                    foreach ($orderProducts as $key => $product) {
                        $isBookingProduct = $bookingProductInfo->getBookingProductInfoByIdProduct(
                            $product['id_product'],
                        );
                        $bkingPOrrInf = $wkBookingsOrders->getBookingProductOrderInfo(
                            $product['id_product'],
                            $idOrder,
                            0,
                            $product['id_product_attribute'],
                        );
                        if ($bkingPOrrInf) {
                            foreach ($bkingPOrrInf as $keyP => $cartB) {
                                $noOfDays = WkBookingProductFeaturePricing::getNumberOfDays(
                                    $cartB['date_from'],
                                    $cartB['date_to'],
                                );
                                $bkingPOrrInf[$keyP]['totalQty'] = $cartB['quantity'] * $noOfDays;
                                $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $product['id_product'],
                                    $cartB['date_from'],
                                    $cartB['date_to'],
                                    false,
                                    $order->id_currency,
                                    1,
                                    $cartB['id_product_attribute'],
                                );
                                $bkingPOrrInf[$keyP]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                    (float) ($cartB['quantity'] * $cartB['range_feature_price_tax_excl']),
                                    $objOrderCurrency,
                                );
                                $bkingPOrrInf[$keyP]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                    (float) ($cartB['quantity'] * $cartB['range_feature_price_tax_incl']),
                                    $objOrderCurrency,
                                );
                                $pri = (float) ($cartB['quantity'] * $cartB['product_real_price_tax_excl']);
                                $rlP = WkBookingPsHelper::displayPrice(
                                    $pri,
                                    $objOrderCurrency,
                                );
                                $bkingPOrrInf[$keyP]['product_real_price_tax_excl_formated'] = $rlP;
                                $tRFPTE = WkBookingPsHelper::displayPrice(
                                    (float) ($cartB['quantity'] * $cartB['range_feature_price_tax_excl']),
                                    $objOrderCurrency,
                                );
                                $bkingPOrrInf[$keyP]['total_range_feature_price_tax_excl_formated'] = $tRFPTE;
                                $tRFPTI = WkBookingPsHelper::displayPrice(
                                    (float) ($cartB['quantity'] * $cartB['range_feature_price_tax_incl']),
                                    $objOrderCurrency,
                                );
                                $bkingPOrrInf[$keyP]['total_range_feature_price_tax_incl_formated'] = $tRFPTI;
                                $rFPE = WkBookingPsHelper::displayPrice(
                                    (float) $cartB['range_feature_price_tax_excl'],
                                    $objOrderCurrency,
                                );
                                $bkingPOrrInf[$keyP]['unit_feature_price_tax_excl_formated'] = $rFPE;
                                $rFPI = WkBookingPsHelper::displayPrice(
                                    (float) $cartB['range_feature_price_tax_incl'],
                                    $objOrderCurrency,
                                );
                                $bkingPOrrInf[$keyP]['unit_feature_price_tax_incl_formated'] = $rFPI;
                            }
                            $orderProducts[$key]['isBookingProduct'] = 1;
                            $orderProducts[$key]['booking_product_data'] = $bkingPOrrInf;
                            $bookingProductExists = 1;
                        }
                    }
                    if ($bookingProductExists) {
                        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                        $this->context->smarty->assign(
                            [
                                'bookingProductExists' => $bookingProductExists,
                                'priceDisplay' => $priceDisplay,
                                'orderProducts' => $orderProducts,
                                'order_details_template_file' => _PS_THEME_DIR_ .
                                'templates/customer/order-detail.tpl',
                            ],
                        );
                        if (WkBookingPsHelper::isHummingbirdTheme()) {
                            return 'module:psbooking/views/templates/hook/frontOrderDetailsOverrided_hummingbird.tpl';
                        } else {
                            return 'module:psbooking/views/templates/hook/frontOrderDetailsOverrided.tpl';
                        }
                    }
                }
            }
        }
        if ('catalog/_partials/quickview' == $params['template_file']) {
            $idProduct = Tools::getValue('id_product');
            $controller = Tools::getValue('controller');
            if ('product' == $controller || 'index' == $controller || 'category' == $controller) {
                if (isset($idProduct) && $idProduct) {
                    $objBookingProductInformation = new WkBookingProductInformation();
                    $bookingProduct = $objBookingProductInformation->getBookingProductInfoByIdProduct($idProduct);
                    if ($bookingProduct) {
                        // Data to show Disables dates (Disable dates/slots tab)
                        $objBookingDisableDates = new WkBookingProductDisabledDates();
                        // get booking product disable dates
                        $disableDatesInfo = $objBookingDisableDates->getBookingProductDisableDatesInfoFormatted(
                            $idProduct,
                        );
                        $disabledDays = 0;
                        $disabledDates = 0;

                        if ($disableDatesInfo) {
                            if (isset($disableDatesInfo['disabledDays']) && $disableDatesInfo['disabledDays']) {
                                $disabledDays = $disableDatesInfo['disabledDays'];
                            }
                            if (isset($disableDatesInfo['disabledDates']) && $disableDatesInfo['disabledDates']) {
                                $disabledDates = $disableDatesInfo['disabledDates'];
                            }
                        }
                        // Data to show Disables dates (Disable dates/slots tab)
                        $objTimeSlots = new WkBookingProductTimeSlotPrices();
                        $selectedDates = $objTimeSlots->getProductTimeSlotsSelectedDates($idProduct);
                        $timeSlotType = $objTimeSlots->checkTimeSlotType($idProduct);
                        $timeSlotDays = '';
                        if ($timeSlotType == 1) {
                            $timeSlotDays = $objTimeSlots->getTimeSlotDays($idProduct);
                        }
                        $redBookedDate = $objBookingDisableDates->getAllSlotBookedDate($idProduct, $bookingProduct['quantity'], $bookingProduct['booking_type']);
                        $availableDateFrom = date('Y-m-d', strtotime('+' . $bookingProduct['booking_before'] . ' hours'));
                        $todayDate = date('Y-m-d');
                        $diff = strtotime($availableDateFrom) - strtotime($todayDate);
                        // 1 day = 24 hours
                        // 24 * 60 * 60 = 86400 seconds
                        $availableAfter = $diff / 86400;
                        Media::addJsDefL('wk_booking_show_map', $bookingProduct['show_map']);
                        Media::addJsDefL('wk_booking_address', $bookingProduct['address']);
                        Media::addJsDefL('wk_booking_latitude', $bookingProduct['latitude']);
                        Media::addJsDefL('wk_booking_longitude', $bookingProduct['longitude']);
                        $this->context->smarty->assign(
                            [
                                'disabledDays' => $disabledDays,
                                'disabledDates' => $disabledDates,
                                'timeSlotType' => $timeSlotType,
                                'availableAfter' => $availableAfter,
                                'timeSlotDays' => $timeSlotDays,
                                'wk_booking_show_map' => $bookingProduct['show_map'],
                                'wk_booking_address' => $bookingProduct['address'],
                                'wk_booking_latitude' => $bookingProduct['latitude'],
                                'wk_booking_longitude' => $bookingProduct['longitude'],
                                'min_days_booking' => $bookingProduct['min_days_booking'],
                                'max_days_booking' => $bookingProduct['max_days_booking'],
                                'selectedDates' => json_encode($selectedDates),
                                'redBookedDate' => $redBookedDate,
                                'isBookingProduct' => 1,
                                'moduleDir' => _PS_MODULE_DIR_ . 'psbooking',
                            ],
                        );
                        if (WkBookingPsHelper::isHummingbirdTheme()) {
                            return 'module:psbooking/views/templates/hook/productQuickReviewOverrided_hummingbird.tpl';
                        } else {
                            return 'module:psbooking/views/templates/hook/productQuickReviewOverrided.tpl';
                        }
                    }
                }
            }
        }
        if ('checkout/checkout' == $params['template_file']) {
            $presenter = new CartPresenter();
            $isBookingProductAvailable = 0;
            $presentedCart = $presenter->present($this->context->cart, true);
            // if ($presentedCart) {
            $bookingProductInfo = new WkBookingProductInformation();
            $wkBookingsCart = new WkBookingsCart();
            foreach ($presentedCart['products'] as $key => $product) {
                $idProduct = $product['id_product'];
                $idProductAttribute = $product['id_product_attribute'];
                $isBookingProduct = $bookingProductInfo->getBookingProductInfoByIdProduct($idProduct);
                if ($isBookingProduct) {
                    $bookingProCartInfo = $wkBookingsCart->getBookingProductCartInfo(
                        $idProduct,
                        $this->context->cart->id,
                        $idProductAttribute,
                    );
                    $isBookingProductAvailable = 1;
                    if ($bookingProCartInfo) {
                        $totalPriceTE = 0;
                        foreach ($bookingProCartInfo as $keyP => $cartB) {
                            if ($cartB['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $cartB['booking_type'] == WkBookingProductInformation::TYPE_RENTAL) {
                                $noOfDays = WkBookingProductFeaturePricing::getNumberOfDays(
                                    $cartB['date_from'],
                                    $cartB['date_to'],
                                );
                                $bookingProCartInfo[$keyP]['totalQty'] = $cartB['quantity'] * $noOfDays;
                                $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idProduct,
                                    $cartB['date_from'],
                                    $cartB['date_to'],
                                    false,
                                    $this->context->currency->id,
                                    1,
                                    $idProductAttribute,
                                );
                                $tlPrTxEx = $ttlPriBkingProd['total_price_tax_excl'];
                                $totalPriceTE += ($cartB['quantity'] * $tlPrTxEx);
                            } elseif ($cartB['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT) {
                                $bkingTmSltPrice = [];
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
                                    $bkingTmSltPrice['price_tax_excl'] = $slotDetails['price'];
                                    $taxRate = (float) WkBookingProductInformation::getAppliedProductTaxRate(
                                        $idProduct,
                                    );
                                    $per = ((100 + $taxRate) / 100);
                                    $bkingTmSltPrice['price_tax_incl'] = $bkingTmSltPrice['price_tax_excl'] * $per;
                                    $bookingProCartInfo[$keyP]['totalQty'] = $cartB['quantity'];
                                    $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $idProduct,
                                        $cartB['date_from'],
                                        $cartB['date_from'],
                                        $bkingTmSltPrice,
                                        $this->context->currency->id,
                                    );
                                    $tlPrTxEx = $ttlPriBkingProd['total_price_tax_excl'];
                                    $totalPriceTE += ($cartB['quantity'] * $tlPrTxEx);
                                }
                            } elseif ($cartB['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                                $bookingProCartInfo[$keyP]['totalQty'] = $cartB['quantity'];
                                $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idProduct,
                                    $cartB['date_from'],
                                    $cartB['date_to'],
                                    false,
                                    $this->context->currency->id,
                                    0,
                                );
                                $tlPrTxEx = $ttlPriBkingProd['total_price_tax_excl'];
                                $totalPriceTE += ($cartB['quantity'] * $tlPrTxEx);
                            }
                        }
                        $productValue = $presentedCart['products'][$key]; //  To resolve Notice: Indirect modification of overloaded element
                        $productValue['isBookingProduct'] = 1;
                        $productValue['total_price_tax_excl'] = $totalPriceTE;
                        $productValue['total_price_tax_excl_formatted'] = WkBookingPsHelper::displayPrice(
                            $totalPriceTE,
                        );
                    }
                }
            }
            // }
            if ($isBookingProductAvailable) {
                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                $this->context->smarty->assign(
                    [
                        'is_hummingbird_theme' => WkBookingPsHelper::isHummingbirdTheme(),
                        'priceDisplay' => $priceDisplay,
                        'cart' => $presentedCart,
                        'checkout_template_file' => _PS_THEME_DIR_ . 'templates/checkout/checkout.tpl',
                    ],
                );
                if (WkBookingPsHelper::isHummingbirdTheme()) {
                    return 'module:psbooking/views/templates/hook/checkoutCheckoutOverrided_hummingbird.tpl';
                } else {
                    return 'module:psbooking/views/templates/hook/checkoutCheckoutOverrided.tpl';
                }
            }
        }
    }

    // DisplayProductButtons is changed in hookDisplayProductAdditionalInfo in new versions.
    public function hookDisplayProductAdditionalInfo($params)
    {
        $idProductAttribute = $params['product']['id_product_attribute'];
        $objBookingProductInformation = new WkBookingProductInformation();
        $idProduct = Tools::getValue('id_product');
        if ($bookingProductInformation = $objBookingProductInformation->getBookingProductInfoByIdProduct($idProduct, true)) {
            $oldTimeStamp = date_default_timezone_get();
            // set time zone by ip address for correct time for customer
            WkBookingProductTimeSlotPrices::setTimeZoneByIP();
            $timeGone = 0;
            $product = new Product((int) $idProduct);
            $minimal_quantity = $product->minimal_quantity;
            $dateFrom = date('Y-m-d', strtotime('+' . $bookingProductInformation['booking_before'] . ' hours'));
            // if min days booking set then date to be (from date + min days booking)
            $minDaysBooking = $bookingProductInformation['min_days_booking'];
            // $minDaysBooking = 0;
            if (Configuration::get('WK_CONSIDER_DATE_TO')) {
                if ($minDaysBooking > 0) {
                    --$minDaysBooking; // here 1 is minus because same date is count 1 ex: 30 jun to 30 jun is 1 so if min days is 2 then 30 june + 2 => 2 july that is 3 days from 30 june to 2 july
                }
                $dateTo = date('Y-m-d', strtotime($minDaysBooking . ' day', strtotime($dateFrom)));
            } else {
                // by default 1 day should be according to WK_CONSIDER_DATE_TO so added 1
                if ($minDaysBooking <= 0) {
                    $minDaysBooking = 1; // here 1 is added because same date is count 0 ex: 30 jun to 30 jun is 0 so if min days is 0 then 30 june + 0 => 30 jun that is 0 days from 30 june to 30 june, we need add  default 1 if minDaysBooking is 0
                }
                $dateTo = date('Y-m-d', strtotime($minDaysBooking . ' day', strtotime($dateFrom)));
            }
            $this->context->smarty->assign(
                [
                    'date_from' => date('d-m-Y', strtotime($dateFrom)),
                    'date_to' => date('d-m-Y', strtotime($dateTo)),
                    'minimal_quantity' => $minimal_quantity,
                ],
            );
            $wkBookingOrder = new WkBookingsOrders();
            $bkingTmSltPriceToday = [];
            $bkingTmSltPrice = false;
            $objBookingDisableDates = new WkBookingProductDisabledDates();
            $productFeaturePrice = 0;
            if ($bookingProductInformation['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT) {
                $objTimeSlots = new WkBookingProductTimeSlotPrices();
                $bookingTimeSlots = $objTimeSlots->getProductTimeSlotsOnDate($idProduct, $dateFrom, true, 1, false);
                if (empty($bookingTimeSlots)) {
                    $bookingTimeSlots = $objTimeSlots->getProductTimeSlotsOnDate($idProduct, $dateFrom, true, 1, true);
                }
                if ($bookingTimeSlots) {
                    $flag = 0;
                    $totalSlotsQty = 0;
                    foreach ($bookingTimeSlots as $key => $timeSlot) {
                        $bookedSlotQuantity = $wkBookingOrder->getProductTimeSlotOrderedQuantity(
                            $idProduct,
                            $dateFrom,
                            $timeSlot['time_slot_from'],
                            $timeSlot['time_slot_to'],
                            1,
                        );
                        // $availQty = $bookingProductInformation['quantity'] - $bookedSlotQuantity;
                        $availQty = $timeSlot['quantity'] - $bookedSlotQuantity;
                        $bookingTimeSlots[$key]['available_qty'] = ($availQty < 0) ? 0 : $availQty;
                        if ($availQty <= 0) {
                            unset($bookingTimeSlots[$key]);
                            continue;
                        }
                        $bookingTimeSlots[$key]['price_tax_excl'] = $timeSlot['price'];
                        $totalSlotsQty += $bookingProductInformation['quantity'] - $bookedSlotQuantity;
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
                            $dateFrom,
                            $dateFrom,
                            $bkingTmSltPrice,
                            $this->context->currency->id,
                        );
                        if ($ttlFeatPri) {
                            $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                            if (!$priceDisplay || $priceDisplay == 2) {
                                $bookingTimeSlots[$key]['formated_slot_price'] = WkBookingPsHelper::displayPrice(
                                    $ttlFeatPri['total_price_tax_incl'],
                                );
                                if ($ttlFeatPri['have_price_rule']) {
                                    $bookingTimeSlots[$key]['formated_slot_price_regular'] = WkBookingPsHelper::displayPrice(
                                        $ttlFeatPri['regular_total_price_tax_incl'],
                                    );
                                }
                            } elseif ($priceDisplay == 1) {
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
                    }
                    if ($flag == 0 && !$bkingTmSltPriceToday) {
                        $bkingTmSltPriceToday['price_tax_excl'] = 0;
                        $bkingTmSltPriceToday['price_tax_incl'] = 0;
                    }
                    $this->context->smarty->assign('totalSlotsQty', $totalSlotsQty);
                    $totalPrice = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                        $idProduct,
                        $dateFrom,
                        $dateFrom,
                        $bkingTmSltPriceToday,
                        $this->context->currency->id,
                    );
                    if ($totalPrice) {
                        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                        if (!$priceDisplay || $priceDisplay == 2) {
                            $productFeaturePrice = $totalPrice['total_price_tax_incl'] * $minimal_quantity;
                            if ($totalPrice['have_price_rule']) {
                                $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_incl'] * $minimal_quantity;
                                $this->context->smarty->assign(
                                    [
                                        'productFeaturePriceRegular' => WkBookingPsHelper::displayPrice(
                                            $productFeaturePriceRegular,
                                        ),
                                    ],
                                );
                            }
                        } elseif ($priceDisplay == 1) {
                            $productFeaturePrice = $totalPrice['total_price_tax_excl'] * $minimal_quantity;
                            if ($totalPrice['have_price_rule']) {
                                $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_excl'] * $minimal_quantity;
                                $this->context->smarty->assign(
                                    [
                                        'productFeaturePriceRegular' => WkBookingPsHelper::displayPrice(
                                            $productFeaturePriceRegular,
                                        ),
                                    ],
                                );
                            }
                        }
                    }
                } else {
                    $productFeaturePrice = 0;
                }
                $this->context->smarty->assign('bookingTimeSlots', $bookingTimeSlots);
            } elseif ($bookingProductInformation['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $bookingProductInformation['booking_type'] == WkBookingProductInformation::TYPE_RENTAL) {
                $totalPrice = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                    $idProduct,
                    $dateFrom,
                    $dateTo,
                    $bkingTmSltPriceToday,
                    $this->context->currency->id,
                    1,
                    $idProductAttribute,
                );
                if ($totalPrice) {
                    $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                    if (!$priceDisplay || $priceDisplay == 2) {
                        $productFeaturePrice = $totalPrice['total_price_tax_incl'] * $minimal_quantity;
                        if ($totalPrice['have_price_rule']) {
                            $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_incl'] * $minimal_quantity;
                            $this->context->smarty->assign(
                                [
                                    'productFeaturePriceRegular' => WkBookingPsHelper::displayPrice(
                                        $productFeaturePriceRegular,
                                    ),
                                ],
                            );
                        }
                    } elseif ($priceDisplay == 1) {
                        $productFeaturePrice = $totalPrice['total_price_tax_excl'] * $minimal_quantity;
                        if ($totalPrice['have_price_rule']) {
                            $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_excl'] * $minimal_quantity;
                            $this->context->smarty->assign(
                                [
                                    'productFeaturePriceRegular' => WkBookingPsHelper::displayPrice(
                                        $productFeaturePriceRegular,
                                    ),
                                ],
                            );
                        }
                    }
                }
            } elseif ($bookingProductInformation['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                $eventData = WkBookingProductExtraInfo::getAlreadyAddedByProductId($idProduct);
                if ($eventData) {
                    if ($eventData['multiple_slot']) {
                        $objTimeSlots = new WkBookingProductTimeSlotPrices();
                        $bookingTimeSlots = $objTimeSlots->getProductAllTimeSlotsOnDateRange($idProduct, $dateFrom, $eventData['date_to']);
                        if ($bookingTimeSlots) {
                            $flag = 0;
                            $totalSlotsQty = 0;
                            foreach ($bookingTimeSlots as $key => $timeSlot) {
                                if ($timeSlot['active']) {
                                    $eventStartTime = date('Y-m-d', strtotime($timeSlot['date_from'])) . ' ' . $timeSlot['time_slot_from'];
                                    $curentDateTime = date('Y-m-d H:i', strtotime('+' . $bookingProductInformation['booking_before'] . ' hours'));
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
                                    $totalSlotsQty += $bookingProductInformation['quantity'] - $bookedSlotQuantity;
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
                                        $this->context->currency->id,
                                    );
                                    if ($ttlFeatPri) {
                                        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                                        if (!$priceDisplay || $priceDisplay == 2) {
                                            $bookingTimeSlots[$key]['formated_slot_price'] = WkBookingPsHelper::displayPrice(
                                                $ttlFeatPri['total_price_tax_incl'],
                                            );
                                            if ($ttlFeatPri['have_price_rule']) {
                                                $bookingTimeSlots[$key]['formated_slot_price_regular'] = WkBookingPsHelper::displayPrice(
                                                    $ttlFeatPri['regular_total_price_tax_incl'],
                                                );
                                            }
                                        } elseif ($priceDisplay == 1) {
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
                            if ($flag == 0 && !$bkingTmSltPriceToday) {
                                $bkingTmSltPriceToday['price_tax_excl'] = 0;
                                $bkingTmSltPriceToday['price_tax_incl'] = 0;
                            }
                            $this->context->smarty->assign('totalSlotsQty', $totalSlotsQty);
                            $totalPrice = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $idProduct,
                                $dateFrom,
                                $dateFrom,
                                $bkingTmSltPriceToday,
                                $this->context->currency->id,
                            );
                            if ($totalPrice) {
                                $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                                if (!$priceDisplay || $priceDisplay == 2) {
                                    $productFeaturePrice = $totalPrice['total_price_tax_incl'] * $minimal_quantity;
                                    if ($totalPrice['have_price_rule']) {
                                        $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_incl'] * $minimal_quantity;
                                        $this->context->smarty->assign(
                                            [
                                                'productFeaturePriceRegular' => WkBookingPsHelper::displayPrice(
                                                    $productFeaturePriceRegular,
                                                ),
                                            ],
                                        );
                                    }
                                } elseif ($priceDisplay == 1) {
                                    $productFeaturePrice = $totalPrice['total_price_tax_excl'] * $minimal_quantity;
                                    if ($totalPrice['have_price_rule']) {
                                        $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_excl'] * $minimal_quantity;
                                        $this->context->smarty->assign(
                                            [
                                                'productFeaturePriceRegular' => WkBookingPsHelper::displayPrice(
                                                    $productFeaturePriceRegular,
                                                ),
                                            ],
                                        );
                                    }
                                }
                            }
                        } else {
                            $productFeaturePrice = 0;
                        }
                        $this->context->smarty->assign('bookingTimeSlots', $bookingTimeSlots);
                    } else {
                        $totalPrice = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                            $idProduct,
                            $eventData['date_from'],
                            $eventData['date_to'],
                            false,
                            $this->context->currency->id,
                            0,
                        );
                        if ($totalPrice) {
                            $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
                            if (!$priceDisplay || $priceDisplay == 2) {
                                $productFeaturePrice = $totalPrice['total_price_tax_incl'] * $minimal_quantity;
                                if ($totalPrice['have_price_rule']) {
                                    $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_incl'] * $minimal_quantity;
                                    $this->context->smarty->assign(
                                        [
                                            'productFeaturePriceRegular' => WkBookingPsHelper::displayPrice(
                                                $productFeaturePriceRegular,
                                            ),
                                        ],
                                    );
                                }
                            } elseif ($priceDisplay == 1) {
                                $productFeaturePrice = $totalPrice['total_price_tax_excl'] * $minimal_quantity;
                                if ($totalPrice['have_price_rule']) {
                                    $productFeaturePriceRegular = $totalPrice['regular_total_price_tax_excl'] * $minimal_quantity;
                                    $this->context->smarty->assign(
                                        [
                                            'productFeaturePriceRegular' => WkBookingPsHelper::displayPrice(
                                                $productFeaturePriceRegular,
                                            ),
                                        ],
                                    );
                                }
                            }
                        }
                    }
                    $eventdateFrom = $eventData['date_from'] . ' ' . $eventData['time_to'];
                    $eventdateFrom = date('Y-m-d H:i', strtotime($eventdateFrom . '+' . $bookingProductInformation['booking_before'] . ' hours'));
                    if (strtotime($eventdateFrom) < strtotime(date('Y-m-d H:i'))) {
                        $timeGone = 1;
                    }
                    // to check for multiple slots case
                    if (isset($bookingTimeSlots) && !empty($bookingTimeSlots)) {
                        $timeGone = 0;
                    }
                    $this->context->smarty->assign('eventData', $eventData);
                }
            }
            // get disable dates info for current selected dates
            $selectedDatesDisableInfo = $objBookingDisableDates->getBookingProductDisableDatesInDateRange(
                $idProduct,
                $dateFrom,
                $dateFrom,
            );
            $bookedQuantity = $wkBookingOrder->getProductOrderedQuantityInDateRange($idProduct, $dateFrom, $dateTo, 1, false, $idProductAttribute);
            $maxAvailableQuantity = $bookingProductInformation['quantity'] - $bookedQuantity;
            if ($maxAvailableQuantity <= 0) {
                $maxAvailableQuantity = 0;
                $productFeaturePrice = 0;
                $productFeaturePriceRegular = 0;
            }
            $bookingPricePlans = WkBookingProductFeaturePricing::getActiveFeaturePricesByIdProduct($idProduct);
            if ($bookingPricePlans) {
                foreach ($bookingPricePlans as &$plan) {
                    $plan['impact_value_formated'] = WkBookingPsHelper::displayPrice(
                        Tools::convertPrice($plan['impact_value']),
                    );
                }
            }
            // Get featurePrice priority
            $featurePricePriority = Configuration::get('WK_PRODUCT_FEATURE_PRICING_PRIORITY');
            $featurePricePriority = explode(';', $featurePricePriority);
            foreach ($featurePricePriority as $key => $priority) {
                if ($priority == 'date_range') {
                    $featurePricePriority[$key] = $this->l('For date range');
                } elseif ($priority == 'specific_date') {
                    $featurePricePriority[$key] = $this->l('For specific date');
                } elseif ($priority == 'special_day') {
                    $featurePricePriority[$key] = $this->l('For special days');
                }
            }
            $googleMapKey = '';
            if (!empty(Configuration::get('WK_BOOKING_GEOLOCATION_API_KEY'))) {
                $googleMapKey = Configuration::get('WK_BOOKING_GEOLOCATION_API_KEY');
            }
            $this->context->smarty->assign(
                [
                    'is_humingbird_theme' => WkBookingPsHelper::isHummingbirdTheme(),
                    'selectedDatesDisabled' => $selectedDatesDisableInfo ? 1 : 0,
                    'featurePricePriority' => $featurePricePriority,
                    'maxAvailableQuantity' => $maxAvailableQuantity,
                    'bookingPricePlans' => $bookingPricePlans,
                    'idProductAttribute' => $idProductAttribute,
                    'bookingProductInformation' => $bookingProductInformation,
                    'productFeaturePrice' => WkBookingPsHelper::displayPrice(
                        $productFeaturePrice,
                    ),
                    'module_dir' => _MODULE_DIR_,
                    'show_feature_price_rules' => Configuration::get('WK_FEATURE_PRICE_RULES_SHOW'),
                    'show_regular_price_after_discount' => Configuration::get('WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT'),
                    'wk_google_map_key' => $googleMapKey,
                    'wk_show_map' => $bookingProductInformation['show_map'],
                    'is_catalog_mode' => Configuration::get('PS_CATALOG_MODE'),
                    'is_show_stock_quantity' => Configuration::get('PS_DISPLAY_QTIES'),
                    'type_date_range' => WkBookingProductInformation::TYPE_DATE_RANGE,
                    'type_time_slot' => WkBookingProductInformation::TYPE_TIME_SLOT,
                    'type_event' => WkBookingProductInformation::TYPE_EVENT,
                    'type_rental' => WkBookingProductInformation::TYPE_RENTAL,
                    'timeGone' => $timeGone,
                ],
            );

            // set old timezone
            date_default_timezone_set($oldTimeStamp);

            return $this->fetch('module:psbooking/views/templates/hook/customerBookingInterface.tpl');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        $idProduct = Tools::getValue('id_product');
        $controller = Tools::getValue('controller');
        if (empty($controller)) {
            $controller = $this->context->controller->php_self;
        }
        if ('product' == $controller
            || 'index' == $controller
            || 'category' == $controller
            || 'cart' == $controller
            || 'order' == $controller
            || 'bookingproduct' == $controller
            || 'event' == $controller
        ) {
            $jsDef = [];
            if ($idProduct) {
                $objBookingProductInfo = new WkBookingProductInformation();
                $bookingProduct = $objBookingProductInfo->getBookingProductInfoByIdProduct($idProduct);
                if ($bookingProduct) {
                    // Data to show Disables dates (Disable dates/slots tab)
                    $objBookingDisableDates = new WkBookingProductDisabledDates();
                    // get booking product disable dates
                    $bookingDisableDatesInfo = $objBookingDisableDates->getBookingProductDisableDatesInfoFormatted(
                        $idProduct,
                    );
                    if ($bookingDisableDatesInfo) {
                        if (isset($bookingDisableDatesInfo['disabledDays'])) {
                            $jsDef['disabledDays'] = $bookingDisableDatesInfo['disabledDays'];
                        }
                        if (isset($bookingDisableDatesInfo['disabledDates'])) {
                            $jsDef['disabledDates'] = $bookingDisableDatesInfo['disabledDates'];
                        }
                    }
                    $jsDef['redBookedDate'] = $objBookingDisableDates->getAllSlotBookedDate($idProduct, $bookingProduct['quantity'], $bookingProduct['booking_type']);
                    // Data to show Disables dates (Disable dates/slots tab)
                    $objTimeSlots = new WkBookingProductTimeSlotPrices();
                    $selectedDates = $objTimeSlots->getProductTimeSlotsSelectedDates($idProduct);
                    $timeSlotType = $objTimeSlots->checkTimeSlotType($idProduct);
                    if ($timeSlotType == 1) {
                        $timeSlotDays = $objTimeSlots->getTimeSlotDays($idProduct);
                        $jsDef['timeSlotDays'] = $timeSlotDays;
                    }
                    $availableDateFrom = date('Y-m-d', strtotime('+' . $bookingProduct['booking_before'] . ' hours'));
                    $todayDate = date('Y-m-d');
                    $diff = strtotime($availableDateFrom) - strtotime($todayDate);
                    // 1 day = 24 hours
                    // 24 * 60 * 60 = 86400 seconds
                    $availableAfter = $diff / 86400;
                    Media::addJsDefL('selectedDatesJson', json_encode($selectedDates));
                    Media::addJsDefL('available_after', (string) $availableAfter);
                    Media::addJsDefL('timeSlotType', $timeSlotType);
                    Media::addJsDefL('min_days_booking', $bookingProduct['min_days_booking']);
                    Media::addJsDefL('max_days_booking', $bookingProduct['max_days_booking']);
                    Media::addJsDefL('wk_booking_show_map', $bookingProduct['show_map']);
                    Media::addJsDefL('wk_booking_address', $bookingProduct['address']);
                    Media::addJsDefL('wk_booking_latitude', $bookingProduct['latitude']);
                    Media::addJsDefL('wk_booking_longitude', $bookingProduct['longitude']);
                }
            }
            Media::addJsDefL('disable_date_title', $this->l('Bookings are unavailable on this date'));
            Media::addJsDefL(
                'bookings_in_select_range_label',
                $this->l('Following bookings will be created for selected date range'),
            );
            Media::addJsDefL(
                'booking_one_slot_select_msg',
                $this->l('At least one slot must be selected for booking.'),
            );
            Media::addJsDefL('no_slots_available_text', $this->l('No slots available'));
            Media::addJsDefL('total_price_text', $this->l('Total price'));
            Media::addJsDefL('dateText', $this->l('Date selected'));
            Media::addJsDefL('dateRangeText', $this->l('Date range'));
            Media::addJsDefL('priceText', $this->l('Price'));
            Media::addJsDefL('To_txt', $this->l('To'));
            Media::addJsDefL('for_txt', $this->l('for'));
            Media::addJsDefL('qtyText', $this->l('quantity'));
            Media::addJsDefL('invalidQtyErr', $this->l('Invalid quantity.'));
            Media::addJsDefL('slot_booked_text', $this->l('Slot booked!'));
            Media::addJsDefL('slot_max_text', $this->l('Max'));
            Media::addJsDefL('get_directions_text', $this->l('Get directions'));

            $jsDef['wkBookingCartLink'] = $this->context->link->getModuleLink('psbooking', 'BookingProductCartActions');
            $jsDef['considerDateToConfiguration'] = Configuration::get('WK_CONSIDER_DATE_TO');
            $jsDef['wk_monday_first_day_week'] = Configuration::get('WK_MONDAY_FIRST_DAY_WEEK');
            $jsDef['wk_static_token'] = Tools::getToken(false);
            $jsDef['wk_type_date_range'] = WkBookingProductInformation::TYPE_DATE_RANGE;
            $jsDef['wk_type_time_slot'] = WkBookingProductInformation::TYPE_TIME_SLOT;
            $jsDef['wk_type_event'] = WkBookingProductInformation::TYPE_EVENT;
            $jsDef['wk_type_rental'] = WkBookingProductInformation::TYPE_RENTAL;
            $jsDef['wk_hummingbird_theme'] = WkBookingPsHelper::isHummingbirdTheme();
            Media::addJsDef($jsDef);
            $this->context->controller->registerJavascript(
                'datepicker-i18n.js',
                'js/jquery/ui/i18n/jquery-ui-i18n.js',
                ['position' => 'bottom', 'priority' => 999],
            );
            $this->context->controller->addJqueryPlugin('growl', null, false);
            $this->context->controller->registerStylesheet('growl-css', 'js/jquery/plugins/growl/jquery.growl.css');
            $this->context->controller->registerJavascript(
                'module-customerBookingInterface-js',
                'modules/' . $this->name . '/views/js/customerBookingInterface.js',
                ['position' => 'bottom', 'priority' => 999],
            );
            $this->context->controller->addJqueryUI(['ui.slider', 'ui.datepicker']);
        }
    }

    public function hookActionValidateOrder($data)
    {
        $cart = $data['cart'];
        $order = $data['order'];
        $idOrder = $order->id;
        $cartProducts = $cart->getProducts();
        $bookingProductInfo = new WkBookingProductInformation();
        $wkBookingsCart = new WkBookingsCart();
        $paidProPri = [];
        foreach ($cartProducts as $product) {
            $isBookingProduct = $bookingProductInfo->getBookingProductInfoByIdProduct($product['id_product']);
            if ($isBookingProduct) {
                $bookingProCartInfo = $wkBookingsCart->getBookingProductCartInfo($product['id_product'], $cart->id, $product['id_product_attribute']);
                if ($bookingProCartInfo) {
                    foreach ($bookingProCartInfo as $keyP => $cartBPro) {
                        $idProduct = $cartBPro['id_product'];
                        $idProductAttribute = $cartBPro['id_product_attribute'];
                        $productPriceTI = Product::getPriceStatic((int) $idProduct, true, $idProductAttribute);
                        $productPriceTE = Product::getPriceStatic((int) $idProduct, false, $idProductAttribute);
                        $extraInfo = [];
                        $ttlFeatPri = [];
                        if ($cartBPro['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT) {
                            $bkingTmSltPrice = [];
                            $objTimeSlot = new WkBookingProductTimeSlotPrices();
                            $slotDetails = $objTimeSlot->getProductTimeSlotDetails(
                                $idProduct,
                                $cartBPro['date_from'],
                                $cartBPro['time_from'],
                                $cartBPro['time_to'],
                            );
                            if (empty($slotDetails)) {
                                $day = date('N', strtotime($cartBPro['date_from']));
                                $slotDetails = $objTimeSlot->getProductTimeDayWiseSlotDetails(
                                    $idProduct,
                                    $day,
                                    $cartBPro['time_from'],
                                    $cartBPro['time_to'],
                                );
                            }
                            if ($slotDetails) {
                                $bkingTmSltPrice['price_tax_excl'] = $slotDetails['price'];
                                $taxRate = (float) WkBookingProductInformation::getAppliedProductTaxRate($idProduct);
                                $priceTaxIncl = $bkingTmSltPrice['price_tax_excl'] * ((100 + $taxRate) / 100);
                                $bkingTmSltPrice['price_tax_incl'] = $priceTaxIncl;
                                $bookingProCartInfo[$keyP]['totalQty'] = $cartBPro['quantity'];
                                $ttlFeatPri = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idProduct,
                                    $cartBPro['date_from'],
                                    $cartBPro['date_from'],
                                    $bkingTmSltPrice,
                                );
                            }
                        } elseif ($cartBPro['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $cartBPro['booking_type'] == WkBookingProductInformation::TYPE_RENTAL) {
                            $ttlFeatPri = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $idProduct,
                                $cartBPro['date_from'],
                                $cartBPro['date_to'],
                                false,
                                null,
                                1,
                                $idProductAttribute,
                            );
                        } elseif ($cartBPro['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                            $ttlFeatPri = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                $idProduct,
                                $cartBPro['date_from'],
                                $cartBPro['date_to'],
                                false,
                                null,
                                0,
                            );
                            $extraInfo = WkBookingProductExtraInfo::getExtraInfoForOrder($cartBPro['id_product'], $cartBPro['id_shop'], $cart->id_lang);
                        }
                        // create array of product price differences for creating specific prices
                        $paidProPriKey = $cartBPro['id_product'] . '_' . $cartBPro['id_product_attribute'];
                        if (isset($paidProPri[$paidProPriKey])) {
                            $priceTI = $ttlFeatPri['total_price_tax_incl'] * $cartBPro['quantity'];
                            $paidProPri[$paidProPriKey]['paid_total_product_price_ti'] += $priceTI;
                            $priceTE = $ttlFeatPri['total_price_tax_excl'] * $cartBPro['quantity'];
                            $paidProPri[$paidProPriKey]['paid_total_product_price_te'] += $priceTE;
                        } else {
                            $priceTI = $ttlFeatPri['total_price_tax_incl'] * $cartBPro['quantity'];
                            $paidProPri[$paidProPriKey]['paid_total_product_price_ti'] = $priceTI;
                            $priceTE = $ttlFeatPri['total_price_tax_excl'] * $cartBPro['quantity'];
                            $paidProPri[$paidProPriKey]['paid_total_product_price_te'] = $priceTE;
                        }

                        // enter the bookings ptoducts order information in our booking order table
                        $wkBookingsOrders = new WkBookingsOrders();
                        $wkBookingsOrders->id_cart = $cartBPro['id_cart'];
                        $wkBookingsOrders->id_order = $order->id;
                        $wkBookingsOrders->id_product = $cartBPro['id_product'];
                        $wkBookingsOrders->id_product_attribute = $cartBPro['id_product_attribute'];
                        $wkBookingsOrders->quantity = $cartBPro['quantity'];
                        $wkBookingsOrders->booking_type = $cartBPro['booking_type'];
                        $wkBookingsOrders->date_from = $cartBPro['date_from'];
                        $wkBookingsOrders->date_to = $cartBPro['date_to'];
                        $wkBookingsOrders->time_from = $cartBPro['time_from'];
                        $wkBookingsOrders->time_to = $cartBPro['time_to'];
                        $wkBookingsOrders->consider_last_date = $cartBPro['consider_last_date'];
                        $wkBookingsOrders->product_real_price_tax_excl = $productPriceTE;
                        $wkBookingsOrders->product_real_price_tax_incl = $productPriceTI;
                        $wkBookingsOrders->range_feature_price_tax_incl = Tools::ps_round(
                            $ttlFeatPri['total_price_tax_incl'],
                            6,
                        );
                        $wkBookingsOrders->range_feature_price_tax_excl = Tools::ps_round(
                            $ttlFeatPri['total_price_tax_excl'],
                            6,
                        );
                        $wkBookingsOrders->total_order_tax_excl = $order->total_paid_tax_excl;
                        $wkBookingsOrders->total_order_tax_incl = $order->total_paid_tax_incl;
                        $wkBookingsOrders->extra_info = json_encode($extraInfo);

                        if (!$wkBookingsOrders->save()) {
                            error_log(
                                date('[Y-m-d H:i e] ') . 'WkBookingsOrders save Error : Error occured while making entry
                                with the details :: cartBookingProduct = ' . $cartBPro . PHP_EOL .
                                'totalFeaturePriceArray = ' . $ttlFeatPri . PHP_EOL,
                                3,
                                _PS_MODULE_DIR_ . 'psbooking/error.log',
                            );
                        }
                    }
                }
            }
        }
        // change the order details product price info as paid by cusstomer after applying feature prices
        if (count($paidProPri)) {
            $wkBookingsOrders = new WkBookingsOrders();
            foreach ($paidProPri as $id_product => $productPrice) {
                $productIdAttrId = explode('_', $id_product);
                $orderProductDetails = $wkBookingsOrders->getOrderDetailsProductInfo($idOrder, $productIdAttrId[0], $productIdAttrId[1]);
                if ($orderProductDetails) {
                    if ($orderProductDetails['total_price_tax_incl'] != $productPrice['paid_total_product_price_ti']) {
                        $fieldsToUpdate = [];

                        $fieldsToUpdate['total_price_tax_incl'] = $productPrice['paid_total_product_price_ti'];
                        $fieldsToUpdate['total_price_tax_excl'] = $productPrice['paid_total_product_price_te'];
                        $productQty = $orderProductDetails['product_quantity'];

                        $fieldsToUpdate['unit_price_tax_incl'] = Tools::ps_round(
                            $productPrice['paid_total_product_price_ti'] / $productQty,
                            6,
                        );
                        $fieldsToUpdate['unit_price_tax_excl'] = Tools::ps_round(
                            $productPrice['paid_total_product_price_te'] / $productQty,
                            6,
                        );
                        if (!$wkBookingsOrders->updatePsOrderDetailsColumns($idOrder, $productIdAttrId[0], $productIdAttrId[1], $fieldsToUpdate)) {
                            error_log(
                                date('[Y-m-d H:i e] ') . 'actionValidateOrder : Error occurred while updating product prices in order_detail (feature prices) for id_product : ' . $id_product . PHP_EOL .
                                'id_order = ' . $idOrder . PHP_EOL . 'fieldsToUpdate = ' . json_encode($fieldsToUpdate),
                                3,
                                _PS_MODULE_DIR_ . 'psbooking/error.log',
                            );
                        }
                    }
                }
            }
        }
    }

    // * admin display booking product orders details.
    public function hookDisplayAdminOrder()
    {
        $idOrder = Tools::getValue('id_order');
        $order = new Order($idOrder);
        $orderProducts = $order->getProducts();
        $wkBookingsOrders = new WkBookingsOrders();
        $showDetails = false;
        foreach ($orderProducts as $key => &$product) {
            if ($product['image'] != null) {
                $imageName = 'product_mini_' . (int) $product['product_id'] .
                (isset($product['product_attribute_id']) ? '_' . (int) $product['product_attribute_id'] : '') . '.jpg';

                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(
                    _PS_IMG_DIR_ . 'p/' . $product['image']->getExistingImgPath() . '.jpg',
                    $imageName,
                    45,
                    'jpg',
                );
                if (file_exists(_PS_TMP_IMG_DIR_ . $imageName)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $imageName);
                } else {
                    $product['image_size'] = false;
                }
            }
            $bkingPOrrInf = $wkBookingsOrders->getBookingProductOrderInfo(
                $product['product_id'],
                $idOrder,
                0,
                $product['product_attribute_id'],
            );
            $objOrderCurrency = new Currency($order->id_currency);
            if ($bkingPOrrInf) {
                $showDetails = true;
                foreach ($bkingPOrrInf as $keyP => $cartB) {
                    $noOfDays = WkBookingProductFeaturePricing::getNumberOfDays(
                        $cartB['date_from'],
                        $cartB['date_to'],
                    );
                    $bkingPOrrInf[$keyP]['totalQty'] = $cartB['quantity'] * $noOfDays;
                    $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                        $product['id_product'],
                        $cartB['date_from'],
                        $cartB['date_to'],
                        false,
                        null,
                        1,
                        $cartB['id_product_attribute'],
                    );
                    $bkingPOrrInf[$keyP]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                        (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_excl']),
                        $objOrderCurrency,
                    );
                    $bkingPOrrInf[$keyP]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                        (float) ($cartB['quantity'] * $ttlPriBkingProd['total_price_tax_incl']),
                        $objOrderCurrency,
                    );
                    $bkingPOrrInf[$keyP]['product_real_price_tax_excl_formated'] = WkBookingPsHelper::displayPrice(
                        (float) ($cartB['quantity'] * $cartB['product_real_price_tax_excl']),
                        $objOrderCurrency,
                    );
                    $bkingPOrrInf[$keyP]['total_range_feature_price_tax_excl_formated'] = WkBookingPsHelper::displayPrice(
                        (float) ($cartB['quantity'] * $cartB['range_feature_price_tax_excl']),
                        $objOrderCurrency,
                    );
                    $bkingPOrrInf[$keyP]['total_range_feature_price_tax_incl_formated'] = WkBookingPsHelper::displayPrice(
                        (float) ($cartB['quantity'] * $cartB['range_feature_price_tax_incl']),
                        $objOrderCurrency,
                    );
                    $bkingPOrrInf[$keyP]['unit_feature_price_tax_excl_formated'] = WkBookingPsHelper::displayPrice(
                        (float) $cartB['range_feature_price_tax_excl'],
                        $objOrderCurrency,
                    );
                    $bkingPOrrInf[$keyP]['unit_feature_price_tax_incl_formated'] = WkBookingPsHelper::displayPrice(
                        (float) $cartB['range_feature_price_tax_incl'],
                        $objOrderCurrency,
                    );
                }
                $orderProducts[$key]['isBookingProduct'] = 1;
                $orderProducts[$key]['booking_product_data'] = $bkingPOrrInf;
            }
        }
        if ($showDetails) {
            $this->context->smarty->assign('orderProducts', $orderProducts);

            return $this->display(__FILE__, 'adminBookingProductOrderDetails.tpl');
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if ($params['smarty']->template_resource == 'module:ps_shoppingcart/modal.tpl') {
            $lastProductAdded = $this->context->cart->getLastProduct();
            if ($lastProductAdded) {
                $idProduct = $lastProductAdded['id_product'];
                $bookingProductInfo = new WkBookingProductInformation();
                $isBookingProduct = $bookingProductInfo->getBookingProductInfoByIdProduct($idProduct);
                if ($isBookingProduct) {
                    $wkBookingsCart = new WkBookingsCart();
                    $bookingProCartInfo = $wkBookingsCart->getCartInfoByProduct($idProduct, $this->context->cart->id);
                    if ($bookingProCartInfo) {
                        foreach ($bookingProCartInfo as $key => $product) {
                            $ttlPriBkingProd = [];
                            if ($product['booking_type'] == WkBookingProductInformation::TYPE_DATE_RANGE || $product['booking_type'] == WkBookingProductInformation::TYPE_RENTAL) {
                                $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idProduct,
                                    $bookingProCartInfo[$key]['date_from'],
                                    $product['date_to'],
                                    false,
                                    null,
                                    1,
                                    $product['id_product_attribute'],
                                );
                                $bookingProCartInfo[$key]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                    (float) ($product['quantity'] * $ttlPriBkingProd['total_price_tax_excl']),
                                );
                                $bookingProCartInfo[$key]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                    (float) ($product['quantity'] * $ttlPriBkingProd['total_price_tax_incl']),
                                );
                            } elseif ($product['booking_type'] == WkBookingProductInformation::TYPE_TIME_SLOT) {
                                $bkingTmSltPrice = [];
                                $objTimeSlot = new WkBookingProductTimeSlotPrices();
                                $slotDetails = $objTimeSlot->getProductTimeSlotDetails(
                                    $idProduct,
                                    $product['date_from'],
                                    $product['time_from'],
                                    $product['time_to'],
                                );
                                if (empty($slotDetails)) {
                                    $day = date('N', strtotime($product['date_from']));
                                    $slotDetails = $objTimeSlot->getProductTimeDayWiseSlotDetails(
                                        $idProduct,
                                        $day,
                                        $product['time_from'],
                                        $product['time_to'],
                                    );
                                }
                                if ($slotDetails) {
                                    $bkingTmSltPrice['price_tax_excl'] = $slotDetails['price'];

                                    $taxRate = (float) WkBookingProductInformation::getAppliedProductTaxRate($idProduct);
                                    $priceTaxIncl = $bkingTmSltPrice['price_tax_excl'] * ((100 + $taxRate) / 100);
                                    $bkingTmSltPrice['price_tax_incl'] = $priceTaxIncl;
                                    $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                        $idProduct,
                                        $product['date_from'],
                                        $product['date_from'],
                                        $bkingTmSltPrice,
                                    );
                                }
                            } elseif ($product['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                                $ttlPriBkingProd = WkBookingProductFeaturePricing::getBookingProductTotalPrice(
                                    $idProduct,
                                    $bookingProCartInfo[$key]['date_from'],
                                    $product['date_to'],
                                    false,
                                    $this->context->currency->id,
                                    0,
                                );
                                $bookingProCartInfo[$key]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                    (float) ($product['quantity'] * $ttlPriBkingProd['total_price_tax_excl']),
                                );
                                $bookingProCartInfo[$key]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                    (float) ($product['quantity'] * $ttlPriBkingProd['total_price_tax_incl']),
                                );
                            }
                            $bookingProCartInfo[$key]['totalPriceTE'] = WkBookingPsHelper::displayPrice(
                                (float) ($product['quantity'] * $ttlPriBkingProd['total_price_tax_excl']),
                            );
                            $bookingProCartInfo[$key]['totalPriceTI'] = WkBookingPsHelper::displayPrice(
                                (float) ($product['quantity'] * $ttlPriBkingProd['total_price_tax_incl']),
                            );
                        }
                        $this->context->smarty->assign('bookingProductCartInfo', $bookingProCartInfo);

                        return $this->fetch('module:psbooking/views/templates/hook/cartPopUpBookingInfo.tpl');
                    }
                }
            }
        }
    }

    public function hookActionProductDelete($params)
    {
        $idProduct = $params['id_product'];
        $objBookingProductInformation = new WkBookingProductInformation();
        if ($objBookingProductInformation->getBookingProductInfoByIdProduct($idProduct)) {
            $objProductFeaturePricing = new WkBookingProductFeaturePricing();
            if (!$objProductFeaturePricing->deleteFeaturePricePlansByIdProduct($idProduct)) {
                $e = $this->l('Some error occurred while deleting booking price rules of this product.');
                $this->context->controller->errors[] = $e;
            }
            $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
            if (!$wkTimeSlotPrices->deleteBookingProductTimeSlots($idProduct)) {
                $e = $this->l('Some error occurred while deleting time slots
                info of this product.');
                $this->context->controller->errors[] = $e;
            }
            if (!$objBookingProductInformation->deleteBookingProductByIdProduct($idProduct)) {
                $e = $this->l('Some error occurred while deleting booking product info of this product.');
                $this->context->controller->errors[] = $e;
            }
        }
    }

    public function hookActionProductUpdate($params)
    {
        if (isset($params['id_product'])) {
            $idProduct = $params['id_product'];
            $objBookingProductInformation = new WkBookingProductInformation();
            if ($bookingProduct = $objBookingProductInformation->getBookingProductInfoByIdProduct($idProduct)) {
                if ($bookingProduct['id']) {
                    $objProduct = new Product($idProduct);
                    if (!$objProduct->is_virtual && $bookingProduct['booking_type'] != WkBookingProductInformation::TYPE_RENTAL) {
                        $objProduct->is_virtual = true;
                        $objProduct->save();
                    }
                    $objBookingProductInformation = new WkBookingProductInformation($bookingProduct['id']);
                    $objBookingProductInformation->active = $objProduct->active;
                    $objBookingProductInformation->save();
                }
            }
        }
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($params['object']->id) {
            $this->createMailLangDirectoryWithFiles($params['object']->id);
        }
    }

    /**
     * [hookActionAdminProductsListingFieldsModifier - show custom filed on product list in admin product controller]
     */
    public function hookActionAdminProductsListingFieldsModifier($list)
    {
        if (isset($list['sql_select'])) {
            $list['sql_select']['booking_type'] = [
                'table' => 'bpi',
                'field' => 'booking_type',
                'filtering' => ' %s ',
            ];
        }
        if (isset($list['sql_table'])) {
            $list['sql_table']['bpi'] = [
                'table' => 'wk_booking_product_info',
                'join' => 'LEFT JOIN',
                'on' => 'bpi.`id_product` = p.`id_product`',
            ];
        }
    }

    public function callInstallTab()
    {
        $this->installTab('AdminManageBookingProductConfiguration', 'Bookings', 'AdminCatalog');
        $this->installTab(
            'AdminBookingProduct',
            'Products',
            'AdminManageBookingProductConfiguration',
        );
        $this->installTab(
            'AdminBookingProductPricePlansSettings',
            'Price Rules',
            'AdminManageBookingProductConfiguration',
        );
        $this->installTab(
            'AdminBookingOrders',
            'Orders',
            'AdminManageBookingProductConfiguration',
        );
        $this->installTab(
            'AdminBookingCalendar',
            'Calendar',
            'AdminManageBookingProductConfiguration',
        );

        return true;
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $idOrder = $params['id_order'];
        $currentState = $params['newOrderStatus']->id;
        if ($currentState == 6) {
            WkBookingsOrders::updateOrderCancelled($idOrder);
        }
        // if order is paid then send event booking order ticket to customer
        $order = new Order($idOrder);
        $orderPaid = $order->getCurrentOrderState() && $order->getCurrentOrderState()->paid;
        if ($orderPaid) {
            $orderProducts = $order->getCartProducts();
            $bookingsOrdersObj = new WkBookingsOrders();
            foreach ($orderProducts as $orderProduct) {
                $productName = $orderProduct['product_name'];
                $product = new Product($orderProduct['product_id'], false, $order->id_lang);
                if (Image::hasImages($order->id_lang, $orderProduct['product_id'])) {
                    $image = Image::getCover($orderProduct['product_id']);
                    $imagePath = $this->context->link->getImageLink(
                        $product->link_rewrite,
                        $image['id_image'],
                        ImageType::getFormattedName('small'),
                    );
                } else {
                    $imagePath = '';
                }
                $eventOrders = $bookingsOrdersObj->getBookingProductOrderInfo($orderProduct['id_product'], $idOrder, WkBookingProductInformation::TYPE_EVENT);
                if ($eventOrders) {
                    // send mail to customer with ticket info
                    $this->sendTicketInMail($eventOrders, $order, $productName, $imagePath);
                }
            }
        }
    }

    public function sendTicketInMail($eventOrders, $order, $productName, $imagePath)
    {
        if (!empty($eventOrders)) {
            $customer = new Customer((int) $order->id_customer);
            $data = [
                '{shop_logo}' => Configuration::get('PS_LOGO'),
                '{lastname}' => $customer->lastname,
                '{firstname}' => $customer->firstname,
                '{id_order}' => (int) $order->id,
                '{order_name}' => $order->getUniqReference(),
                '{product_name}' => $productName,
                '{image_path}' => $imagePath,
            ];
            foreach ($eventOrders as $eventOrder) {
                $dateTime = Tools::displayDate($eventOrder['date_from']) . ' ' . $eventOrder['time_from'];
                if ($eventOrder['date_from'] != $eventOrder['date_to']) {
                    $dateTime .= ' - ' . Tools::displayDate($eventOrder['date_to']) . ' ' . $eventOrder['time_to'];
                } else {
                    $dateTime .= ' - ' . $eventOrder['time_to'];
                }
                // extra booking info
                $extraInfo = json_decode($eventOrder['extra_info'], true);
                $address = WkBookingProductInformation::getBookingProductAddress($eventOrder['id_product'], $eventOrder['id_shop']);
                $mapLink = 'https://maps.google.com/?q=' . (isset($address['latitude']) ? $address['latitude'] : '') . ',' . (isset($address['longitude']) ? $address['longitude'] : '');
                // scan link to mark as scanned whene scan qrcode
                $scaningLink = $this->context->link->getModuleLink('psbooking', 'scan', ['id' => base64_encode($eventOrder['id'])]);
                // generate qrcode and save in module
                $this->generateTicketQrCode($scaningLink, $eventOrder['id'] . '.png');
                // get scan image link for email
                $qrcodeImage = $this->context->link->protocol_content . Tools::getMediaServer($eventOrder['id'] . '.png') . _MODULE_DIR_ . 'psbooking/views/img/qrcode/' . $eventOrder['id'] . '.png';

                $eventData = [
                    '{date_time}' => $dateTime,
                    '{get_direction}' => $mapLink,
                    '{location}' => isset($address['address']) ? $address['address'] : '',
                    '{language}' => isset($extraInfo['language']) ? $extraInfo['language'] : '',
                    '{artist}' => isset($extraInfo['artist']) ? $extraInfo['artist'] : '',
                    '{category}' => isset($extraInfo['category']) ? $extraInfo['category'] : '',
                    '{age_group}' => isset($extraInfo['age_group']) ? $extraInfo['age_group'] : '',
                    '{quantity}' => $eventOrder['quantity'],
                    '{qrcode_image}' => $qrcodeImage,
                ];
                $data = array_merge($data, $eventData);
                $template_path = _PS_MODULE_DIR_ . 'psbooking/mails/';
                Mail::Send(
                    (int) $order->id_lang,
                    'event_ticket',
                    Mail::l('Your event ticket', (int) $order->id_lang),
                    $data,
                    $customer->email,
                    $customer->firstname . ' ' . $customer->lastname,
                    null,
                    null,
                    null,
                    null,
                    $template_path,
                    false,
                    (int) $order->id_shop,
                );
            }
        }
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = true;
        $tab->class_name = $className;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($tabParentName) {
            $entityManager = SymfonyContainer::getInstance()->get('doctrine.orm.entity_manager');
            $tabRepository = $entityManager->getRepository(PrestaShopBundle\Entity\Tab::class);
            $tabId = $tabRepository->findOneIdByClassName($tabParentName);
            $tab->id_parent = (int) $tabId;
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;

        return $tab->add();
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            [
                'actionFrontControllerSetMedia',
                'displayProductButtons',
                'displayOverrideTemplate',
                'actionValidateOrder',
                'displayAdminOrder',
                'displayProductPriceBlock',
                'displayHeader',
                'actionProductDelete',
                'displayProductAdditionalInfo',
                'actionProductUpdate',
                'actionObjectLanguageAddAfter',
                'actionAdminProductsListingFieldsModifier',
                'actionOrderStatusPostUpdate',
                'displayNav1',
                'actionProductGridDefinitionModifier',
                'actionProductGridQueryBuilderModifier',
                'actionAdminControllerSetMedia',
                'displayHome',
            ],
        );
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (
            Tools::getValue('controller') == 'AdminThemes'
        ) {
            $this->registerHook('displayNav1');
        }
    }
    
    public function hookDisplayHome($params)
    {
        // Obtener el enlace al controlador 'bookingproduct' dentro del módulo 'psbooking'
        $this->context->smarty->assign('form_action', $this->context->link->getModuleLink('psbooking', 'bookingproduct'));
    
        // Obtener valores desde el formulario
        $dateFrom = Tools::getValue('date_from', date('Y-m-d'));
        $dateTo = Tools::getValue('date_to', date('Y-m-d', strtotime('+1 month')));
        $timeFrom = Tools::getValue('time_from', '');
        $timeTo = Tools::getValue('time_to', '');
        $quantity = Tools::getValue('quantity', 1);
        $type = Tools::getValue('type', '');
        $categories = Category::getCategories($this->context->language->id, true, false);
        
        $categoryId = (int) Tools::getValue('category', 0);
    
        // Asignar variables a Smarty
        $this->context->smarty->assign(array(
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
            'quantity' => $quantity,
            'type' => $type,
            'categories' => $categories,
            'selected_category' => $categoryId,
            'booking_types' => WkBookingProductInformation::getBookingTypes()
        ));
    
        return $this->fetch('module:psbooking/views/templates/hook/psbooking_home.tpl');
    }

    public function checkNewPSProductPage()
    {
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            return true;
        }
        if (version_compare(_PS_VERSION_, '1.7.8.9', '>')) {
            if (SymfonyContainer::getInstance()->get('prestashop.core.admin.feature_flag.repository')->isEnabled(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2) || SymfonyContainer::getInstance()->get('prestashop.core.admin.feature_flag.repository')->isEnabled(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2_MULTI_SHOP)) {
                return true;
            }
        }

        return false;
    }

    public function hookActionProductGridDefinitionModifier($params)
    {
        if ($this->checkNewPSProductPage()) {
            // /** @var GridDefinitionInterface $definition */
            $definition = $params['definition'];
            $definition
                ->getColumns()
                ->addAfter(
                    'price_tax_included',
                    (new DataColumn('booking_type'))
                        ->setName($this->l('Type'))
                        ->setOptions(
                            [
                                'field' => 'booking_type',
                            ],
                        ),
                );
        }
    }

    public function hookActionProductGridQueryBuilderModifier($params)
    {
        if ($this->checkNewPSProductPage()) {
            $searchQueryBuilder = $params['search_query_builder'];

            $searchCriteria = $params['search_criteria'];
            $booking = $this->l('Booking');
            $dot = '--';
            $searchQueryBuilder->addSelect(
                'IF(ad.`booking_type` IS NULL, \'' . pSQL($dot) . '\',IF(ad.`booking_type` != 2, \'' . pSQL($booking) . '\', \'' . pSQL($booking) . '\')) AS `booking_type`',
            );
            $currentUrl = $_SERVER['REQUEST_URI'];
            if ((bool) strpos($currentUrl, 'view')) {
                $searchQueryBuilder->leftJoin(
                    'product',
                    '`' . pSQL(_DB_PREFIX_) . 'wk_booking_product_info_shop`',
                    'ad',
                    'ad.`id_product` = product.`id_product` AND ad.`id_shop` = pl.`id_shop`',
                );
            } else {
                $searchQueryBuilder->leftJoin(
                    'p',
                    '`' . pSQL(_DB_PREFIX_) . 'wk_booking_product_info_shop`',
                    'ad',
                    'ad.`id_product` = p.`id_product` AND ad.`id_shop` = pl.`id_shop`',
                );
            }

            foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
                if ('booking_type' === $filterName) {
                    $searchQueryBuilder->andWhere('ad.`booking_type` = :booking_type');
                    $searchQueryBuilder->setParameter('booking_type', $filterValue);

                    if (!$filterValue) {
                        $searchQueryBuilder->orWhere('ad.`booking_type` IS NULL');
                    }
                }
            }
        }
    }

    /**
     * Display booking products link on navigation bar.
     */
    public function hookDisplayNav1()
    {
        if (Configuration::get('WK_BOOKING_PRODUCTS_DISPLAY_LINK')) {
            $this->context->smarty->assign('wk_nav_link', 1);

            return $this->displayBookingOrdersLink();
        }
    }

    public function displayBookingOrdersLink()
    {
        $this->context->smarty->assign(
            'bookingproductlink',
            $this->context->link->getModuleLink(
                'psbooking',
                'bookingproduct',
            ),
        );
        $this->context->smarty->assign(
            [
                'bookingproductlink' => $this->context->link->getModuleLink(
                    'psbooking',
                    'bookingproduct',
                ),
                'is_hummingbird_theme' => WkBookingPsHelper::isHummingbirdTheme(),
            ],
        );

        return $this->fetch('module:' . $this->name . '/views/templates/hook/bookingproduct_link.tpl');
    }

    public function install()
    {
        $objModuleDb = new WkPsBookingDb();
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()
            || !$this->registerModuleHooks()
            || !$objModuleDb->createTables()
            || !$this->callInstallTab()
            || !$this->setDefaultConfig()
            || !$this->createMailLangDirectoryWithFiles()
        ) {
            return false;
        }

        return true;
    }

    public function setDefaultConfig()
    {
        Configuration::updateValue('WK_CONSIDER_DATE_TO', 0);
        Configuration::updateValue('WK_FEATURE_PRICE_RULES_SHOW', 1);
        Configuration::updateValue('WK_MONDAY_FIRST_DAY_WEEK', 0);
        Configuration::updateValue('WK_BOOKING_PRODUCTS_DISPLAY_LINK', 1);
        Configuration::updateValue('WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT', 0);
        Configuration::updateValue(
            'WK_PRODUCT_FEATURE_PRICING_PRIORITY',
            'specific_date;special_day;date_range',
        );

        // event page config
        Configuration::updateValue('WK_SHOW_BOOKING_EVENT_PAGE', 1);
        Configuration::updateValue('WK_SHOW_EVENT_PAGE_WHATSAPP', 1);
        Configuration::updateValue('WK_SHOW_EVENT_PAGE_FACEBOOK', 1);
        Configuration::updateValue('WK_SHOW_EVENT_PAGE_INSTAGRAM', 1);
        Configuration::updateValue('WK_SHOW_EVENT_PAGE_TWITTER', 1);
        Configuration::updateValue('WK_SHOW_EVENT_PAGE_CLIPBOARD', 1);
        Configuration::updateValue('WK_SHOW_RELATED_EVENTS', 1);

        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                if (!$moduleTab->delete()) {
                    return false;
                }
            }
        }

        return true;
    }

    public function deleteConfigVars()
    {
        $config_keys = [
            'WK_CONSIDER_DATE_TO',
            'WK_PRODUCT_FEATURE_PRICING_PRIORITY',
            'WK_FEATURE_PRICE_RULES_SHOW',
            'WK_MONDAY_FIRST_DAY_WEEK',
            'WK_BOOKING_PRODUCTS_DISPLAY_LINK',
            'WK_BOOKING_DISPLAY_REGULAR_PRICE_AFTER_DISCOUNT',
            'WK_BOOKING_GEOLOCATION_API_KEY',
            'WK_SHOW_BOOKING_EVENT_PAGE',
            'WK_SHOW_EVENT_PAGE_WHATSAPP',
            'WK_SHOW_EVENT_PAGE_FACEBOOK',
            'WK_SHOW_EVENT_PAGE_INSTAGRAM',
            'WK_SHOW_EVENT_PAGE_TWITTER',
            'WK_SHOW_EVENT_PAGE_CLIPBOARD',
            'WK_SHOW_RELATED_EVENTS',
        ];
        foreach ($config_keys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        $objModuleDb = new WkPsBookingDb();
        if (!parent::uninstall()
            || !$objModuleDb->dropTables()
            || !$this->deleteConfigVars()
            || !$this->uninstallTab()
        ) {
            return false;
        }

        return true;
    }

    // Ps all imported language's Mail directory will be created with all files in module's mails folder
    private function createMailLangDirectoryWithFiles($idLang = 0)
    {
        if ($idLang) {
            $language = new Language($idLang);
            $langISO = $language->iso_code;
            // Ignore 'en' directory because we already have this in our module folder
            if ($langISO != 'en') {
                $this->createModuleMailDir($langISO);
            }
        } else {
            if ($allLanguages = Language::getLanguages(false, $this->context->shop->id)) {
                foreach ($allLanguages as $language) {
                    $langISO = $language['iso_code'];
                    // Ignore 'en' directory because we already have this in our module folder
                    if ($langISO != 'en') {
                        $this->createModuleMailDir($langISO);
                    }
                }
            }
        }

        return true;
    }

    private function createModuleMailDir($langIso)
    {
        $moduleMailDir = _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/mails/';
        // create lang dir if not exist in module mails directory
        if (!file_exists($moduleMailDir . $langIso)) {
            @mkdir($moduleMailDir . $langIso, 0777, true);
        }
        // Now if lang dir is exist or created by above code
        if (is_dir($moduleMailDir . $langIso)) {
            $mailEnDir = _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/mails/en/';
            if (is_dir($mailEnDir)) {
                if ($allFiles = scandir($mailEnDir)) {
                    foreach ($allFiles as $fileName) {
                        if ($fileName != '.' && $fileName != '..') {
                            $source = $mailEnDir . $fileName;
                            $destination = $moduleMailDir . $langIso . '/' . $fileName;
                            // if file not exist in desti directory then create that file
                            if (!file_exists($destination) && file_exists($source)) {
                                Tools::copy($source, $destination);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
