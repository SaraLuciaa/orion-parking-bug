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

class AdminBookingProductPricePlansSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'WkBookingProductFeaturePricing';
        $this->table = 'wk_booking_product_feature_pricing';
        $this->bootstrap = true;
        $this->identifier = 'id';
        parent::__construct();
        Shop::addTableAssociation('wk_booking_product_feature_pricing', ['type' => 'shop']);
        $this->_join .= WkBookingProductInformation::addSqlAssociationCustom(
            'wk_booking_product_feature_pricing',
            'a',
            true,
            null,
            false,
            $this->identifier,
        );
        $this->page_header_toolbar_title = $this->module->l('Price Rules', 'AdminBookingProductPricePlansSettingsController');
        $this->toolbar_title = $this->module->l('Price Rules', 'AdminBookingProductPricePlansSettingsController');

        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = a.`id_product` AND pl.`id_lang`=' .
        (int) $this->context->language->id . ' AND pl.`id_shop` = ' . (int) Shop::CONTEXT_SHOP . ')';
        $this->_select .= ' CONCAT(pl.`name`, " (#", a.`id_product`, ")") as product_name,
        IF(a.impact_type=1 , CONCAT(round(a.impact_value, 2), " ",  "%"), a.impact_value) AS impact_value';
        $this->_select .= ' ,IF(a.impact_type=1 , \'' . $this->module->l('Percentage', 'AdminBookingProductPricePlansSettingsController') . '\', \'' .
        $this->module->l('Fixed price') . '\') AS impact_type';
        $this->_select .= ' ,IF(a.impact_way=1 , \'' . $this->module->l('Decrease', 'AdminBookingProductPricePlansSettingsController') . '\', \'' .
        $this->module->l('Increase', 'AdminBookingProductPricePlansSettingsController') . '\') AS impact_way';

        $impactWays = [1 => $this->module->l('Decrease'), 2 => $this->module->l('Increase', 'AdminBookingProductPricePlansSettingsController')];
        $impactTypes = [1 => $this->module->l('Percentage'), 2 => $this->module->l('Fixed price', 'AdminBookingProductPricePlansSettingsController')];
        $priorities = Configuration::get('WK_PRODUCT_FEATURE_PRICING_PRIORITY');
        // To show overrided options.tpl have to set $this->fields_options
        $this->context->smarty->assign('featurePricePriority', explode(';', $priorities));
        $this->fields_options = [
            'feature_price_priority' => [
            ],
        ];
        // if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
        // In case of All Shops
        $this->_select .= ',shp.`name` as wk_ps_shop_name';
        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = wk_booking_product_feature_pricing_shop.`id_shop`)';
        // }

        $this->_group = ' GROUP BY a.id';
        $this->fields_list = [
            'id' => [
                'title' => $this->module->l('ID', 'AdminBookingProductPricePlansSettingsController'),
                'align' => 'center',
            ],
            'product_name' => [
                'title' => $this->module->l('Product name', 'AdminBookingProductPricePlansSettingsController'),
                'align' => 'center',
                'havingFilter' => true,
                'callback' => 'getBookingProductlink',
            ],
            'feature_price_name' => [
                'title' => $this->module->l('Booking rule name', 'AdminBookingProductPricePlansSettingsController'),
                'align' => 'center',
                'filter_key' => 'a!feature_price_name',
            ],
            'impact_way' => [
                'title' => $this->module->l('Impact way', 'AdminBookingProductPricePlansSettingsController'),
                'align' => 'center',
                'type' => 'select',
                'list' => $impactWays,
                'filter_key' => 'a!impact_way',
            ],
            'impact_type' => [
                'title' => $this->module->l('Impact type', 'AdminBookingProductPricePlansSettingsController'),
                'align' => 'center',
                'type' => 'select',
                'list' => $impactTypes,
                'filter_key' => 'a!impact_type',
            ],
            'impact_value' => [
                'title' => $this->module->l('Impact value', 'AdminBookingProductPricePlansSettingsController'),
                'align' => 'center',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'filter_key' => 'a!impact_value',
            ],
            'date_from' => [
                'title' => $this->module->l('Date from', 'AdminBookingProductPricePlansSettingsController'),
                'align' => 'center',
                'type' => 'date',
                'filter_key' => 'a!date_from',
                'callback' => 'setDateFrom',
            ],
            'date_to' => [
                'title' => $this->module->l('Date to', 'AdminBookingProductPricePlansSettingsController'),
                'align' => 'center',
                'type' => 'date',
                'filter_key' => 'a!date_to',
                'callback' => 'setDateTo',
            ],
            'active' => [
                'align' => 'center',
                'title' => $this->module->l('Status', 'AdminBookingProductPricePlansSettingsController'),
                'active' => 'status',
                'type' => 'bool',
                'filter_key' => 'a!active',
            ],
        ];
        if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
            // In case of All Shops
            $this->fields_list['wk_ps_shop_name'] = [
                'title' => $this->module->l('Shop', 'AdminBookingProductPricePlansSettingsController'),
                'havingFilter' => true,
                // 'orderby' => false,
            ];
        }
        if (Shop::isFeatureActive()) {
            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                $this->bulk_actions = [
                    'delete' => [
                        'text' => $this->module->l('Delete selected', 'AdminBookingProductPricePlansSettingsController'),
                        'icon' => 'icon-trash',
                        'confirm' => $this->module->l('Delete selected items?', 'AdminBookingProductPricePlansSettingsController'),
                    ],
                ];
            }
        } else {
            $this->bulk_actions = [
                'delete' => [
                    'text' => $this->module->l('Delete selected', 'AdminBookingProductPricePlansSettingsController'),
                    'icon' => 'icon-trash',
                    'confirm' => $this->module->l('Delete selected items?', 'AdminBookingProductPricePlansSettingsController'),
                ],
            ];
        }

        $this->list_no_link = true;
    }

    public function processFilter()
    {
        if (isset($this->list_id)) {
            foreach ($_POST as $key => $value) {
                if ($key == 'wk_booking_product_feature_pricingFilter_a!impact_value' && !empty($_POST['wk_booking_product_feature_pricingFilter_a!impact_value'])) {
                    $currencySign = ['$', '€', '₹', '£'];
                    $value = str_replace($currencySign, '', $value);
                    $_POST[$key] = (int) $value;
                }
            }
        }
        parent::processFilter();
    }

    public function initContent()
    {
        if (($this->display == 'edit') && (Shop::getContext() == Shop::CONTEXT_SHOP)) {
            if (!$this->loadObject(true)) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
            }
        }

        return parent::initContent();
    }

    public function getBookingProductlink($productName, $row)
    {
        $displayData = '';
        if ($productName && $row['id_product']) {
            $objBookingInfo = new WkBookingProductInformation();
            if ($productInfo = $objBookingInfo->getBookingProductInfoByIdProduct($row['id_product'], false, false)) {
                $productLink = $this->context->link->getAdminLink(
                    'AdminBookingProduct',
                    true,
                    [],
                    ['id' => $productInfo['id'], 'updatewk_booking_product_info' => ''],
                );
                $this->context->smarty->assign('productName', $productName);
                $this->context->smarty->assign('productLink', $productLink);
                $displayData = $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/booking_product_link.tpl',
                );
            }
        }

        return $displayData;
    }

    public static function setOrderCurrency($echo)
    {
        $currency_default = Configuration::get('PS_CURRENCY_DEFAULT');

        return WkBookingPsHelper::displayPrice($echo, (int) $currency_default);
    }

    public function setDateFrom($date)
    {
        return date('d/m/Y', strtotime($date));
    }

    public function setDateTo($date)
    {
        return date('d/m/Y', strtotime($date));
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['view'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name,
            'desc' => $this->module->l('Module configuration', 'AdminBookingProductPricePlansSettingsController'),
            'icon' => 'process-icon-cogs',
        ];
        $showAddbtn = false;
        if (Shop::isFeatureActive()) {
            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                $showAddbtn = true;
            }
        } else {
            $showAddbtn = true;
        }
        if ($showAddbtn) {
            $this->page_header_toolbar_btn['new'] = [
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->module->l('Add price rule', 'AdminBookingProductPricePlansSettingsController'),
                'imgclass' => 'new',
            ];
        } else {
            unset($this->toolbar_btn['new']);
        }
    }

    public function renderList()
    {
        if (Shop::isFeatureActive()) {
            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                $this->addRowAction('edit');
                $this->addRowAction('delete');
            }
        } else {
            $this->addRowAction('edit');
            $this->addRowAction('delete');
        }
        if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
            unset($this->fields_list['active']);
        }

        return parent::renderList();
    }

    public function renderForm()
    {
        if (Shop::getContext() != Shop::CONTEXT_SHOP) {
            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/shop_warning.tpl',
            );
        } else {
            $objCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
            $currencySign = $objCurrency->sign;
            $dateFrom = date('d-m-Y');
            $dateTo = date('d-m-Y', strtotime('+1 day', strtotime($dateFrom)));
            if ($this->display == 'edit') {
                $featurePriceId = Tools::getValue('id');
                $featurePriceInfo = new WkBookingProductFeaturePricing($featurePriceId);

                if ($featurePriceInfo->id_product) {
                    $product = new Product($featurePriceInfo->id_product, false, (int) Configuration::get('PS_LANG_DEFAULT'));
                    $productName = $product->name;
                    $this->context->smarty->assign('productName', $productName);
                    $objBookingInfo = new WkBookingProductInformation();
                    $bookingProductInfo = $objBookingInfo->getBookingProductInfoByIdProduct($featurePriceInfo->id_product);
                    if ($bookingProductInfo['booking_type'] == WkBookingProductInformation::TYPE_EVENT && !WkBookingProductExtraInfo::isMultiSlotEnable($featurePriceInfo->id_product)) {
                        $single_event = 1;
                    } else {
                        $single_event = 0;
                    }
                    $this->context->smarty->assign('single_event', $single_event);
                }
                if ($featurePriceInfo->special_days) {
                    $this->context->smarty->assign(
                        'special_days',
                        json_decode($featurePriceInfo->special_days, true),
                    );
                }
                $this->context->smarty->assign(
                    [
                        'edit' => 1,
                        'featurePriceInfo' => $featurePriceInfo,
                    ],
                );
            }
            $this->tpl_form_vars = [
                'defaultcurrency_sign' => $currencySign,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ];
            $this->fields_form = [
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminBookingProductPricePlansSettingsController'),
                ],
            ];

            return parent::renderForm();
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAddFeaturePricePriority')) {
            $priority = Tools::getValue('featurePricePriority');
            $uniquePriorities = array_unique($priority);
            if (count($priority) == count($uniquePriorities)) {
                $priorityConfig = implode(';', $priority);
                if (Configuration::updateValue('WK_PRODUCT_FEATURE_PRICING_PRIORITY', $priorityConfig)) {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                } else {
                    $this->errors[] = $this->module->l('Some error occurred while updating booking price rules priorities.', 'AdminBookingProductPricePlansSettingsController');
                }
            } else {
                $this->errors[] = $this->module->l('Duplicate values selected for booking price rules priorities.', 'AdminBookingProductPricePlansSettingsController');
            }
        } else {
            parent::postProcess();
        }
    }

    public function processSave()
    {
        $id = Tools::getValue('id');
        if (!isset($id) || !$id) {
            $id = 0;
        }
        $enableFeaturePrice = Tools::getValue('enable_feature_price');
        $productId = Tools::getValue('product_id');
        $single_event = Tools::getValue('single_event');
        if ($single_event) {
            if ($productId) {
                $eventData = WkBookingProductExtraInfo::getAlreadyAddedByProductId($productId);
                if ($eventData) {
                    $_POST['date_selection_type'] = 1;
                    $_POST['date_from'] = $eventData['date_from'];
                    $_POST['date_to'] = $eventData['date_to'];
                }
            }
        }
        $featurePriceName = Tools::getValue('feature_price_name');
        $dateFrom = date('Y-m-d', strtotime(Tools::getValue('date_from')));
        $dateTo = date('Y-m-d', strtotime(Tools::getValue('date_to')));

        $isSpecialDaysExists = Tools::getValue('is_special_days_exists');
        $specialDays = Tools::getValue('special_days');
        $priceImpactWay = Tools::getValue('price_impact_way');
        $priceImpactType = Tools::getValue('price_impact_type');
        $impactValue = Tools::getValue('impact_value');
        $dateSelectionType = Tools::getValue('date_selection_type');
        $specificDate = date('Y-m-d', strtotime(Tools::getValue('specific_date')));
        $jsonSpecialDays = json_encode($specialDays);
        $bookingProductFeaturePricing = new WkBookingProductFeaturePricing();
        $isPlanTypeExists = 0;
        if ($dateSelectionType == 2) {
            $isPlanTypeExists = $bookingProductFeaturePricing->checkBookingProductFeaturePriceExistance(
                $productId,
                $specificDate,
                date('Y-m-d', strtotime('+1 day', strtotime($specificDate))),
                'specific_date',
                false,
                $id,
            );
        } elseif (isset($isSpecialDaysExists) && $isSpecialDaysExists == 'on') {
            if ($jsonSpecialDays != 'false') {
                $isPlanTypeExists = $bookingProductFeaturePricing->checkBookingProductFeaturePriceExistance(
                    $productId,
                    $dateFrom,
                    $dateTo,
                    'special_day',
                    $jsonSpecialDays,
                    $id,
                );
            }
        } else {
            $isPlanTypeExists = $bookingProductFeaturePricing->checkBookingProductFeaturePriceExistance(
                $productId,
                $dateFrom,
                $dateTo,
                'date_range',
                false,
                $id,
            );
        }
        if ($isPlanTypeExists) {
            $e = $this->module->l('Booking price rule already exists in which some dates are common with this plan. ', 'AdminBookingProductPricePlansSettingsController')
            . $this->module->l(' Please select a different date range.', 'AdminBookingProductPricePlansSettingsController');
            $this->errors[] = $e;
        } else {
            if (!$productId) {
                $this->errors[] = $this->module->l('Please type booking product name.', 'AdminBookingProductPricePlansSettingsController');
            }

            if (trim($featurePriceName) == '') {
                $this->errors[] = $this->module->l('Booking price rule name is required.', 'AdminBookingProductPricePlansSettingsController');
            } elseif (!Validate::isGenericName($featurePriceName)) {
                $this->errors[] = $this->module->l('Booking price rule name must not have invalid characters <>;=#{}', 'AdminBookingProductPricePlansSettingsController');
            } elseif (strlen($featurePriceName) > 64) {
                $this->errors[] = $this->module->l('Booking price rule name length must not have greator than', 'AdminBookingProductPricePlansSettingsController') . ' 64.';
            }

            if ($dateSelectionType == 1) {
                if ($dateFrom == '') {
                    $this->errors[] = $this->module->l('Please choose date from for booking price rule.', 'AdminBookingProductPricePlansSettingsController');
                } elseif (!Validate::isDate($dateFrom)) {
                    $this->errors[] = $this->module->l('Invalid date from.', 'AdminBookingProductPricePlansSettingsController');
                }

                if ($dateTo == '') {
                    $this->errors[] = $this->module->l('Please choose date to for booking price rule.', 'AdminBookingProductPricePlansSettingsController');
                } elseif (!Validate::isDate($dateTo)) {
                    $this->errors[] = $this->module->l('Invalid date to.', 'AdminBookingProductPricePlansSettingsController');
                }

                if ($dateTo < $dateFrom) {
                    $this->errors[] = $this->module->l('Date to must be date after date from.', 'AdminBookingProductPricePlansSettingsController');
                }
                if (isset($isSpecialDaysExists) && $isSpecialDaysExists == 'on') {
                    $isSpecialDaysExists = 1;
                    if (!isset($specialDays) || !$specialDays) {
                        $isSpecialDaysExists = 0;
                        $this->errors[] = $this->module->l('Please select at least one day for special days.', 'AdminBookingProductPricePlansSettingsController');
                    }
                } else {
                    $isSpecialDaysExists = 0;
                }
            } else {
                if ($specificDate == '') {
                    $this->errors[] = $this->module->l('Please choose date from for booking price rule.', 'AdminBookingProductPricePlansSettingsController');
                } elseif (!Validate::isDate($specificDate)) {
                    $this->errors[] = $this->module->l('Invalid date from.', 'AdminBookingProductPricePlansSettingsController');
                }
            }
            if (!$impactValue) {
                $this->errors[] = $this->module->l('Please enter a valid impact value (tax excl.).', 'AdminBookingProductPricePlansSettingsController');
            } elseif ($priceImpactType == 1 && $impactValue > 100) {
                $this->errors[] = $this->module->l('Invalid precentage impact value (tax excl.).', 'AdminBookingProductPricePlansSettingsController');
            } elseif (!Validate::isPrice($impactValue)) {
                $this->errors[] = $this->module->l('Invalid impact value (tax excl.).', 'AdminBookingProductPricePlansSettingsController');
            }
            if (!count($this->errors)) {
                if ($id) {
                    $bookingProductFeaturePricing = new WkBookingProductFeaturePricing($id);
                } else {
                    $bookingProductFeaturePricing = new WkBookingProductFeaturePricing();
                }
                $bookingProductFeaturePricing->id_product = $productId;
                $bookingProductFeaturePricing->feature_price_name = $featurePriceName;
                $bookingProductFeaturePricing->date_selection_type = $dateSelectionType;
                if ($dateSelectionType == 1) {
                    $bookingProductFeaturePricing->date_from = $dateFrom;
                    $bookingProductFeaturePricing->date_to = $dateTo;
                } else {
                    $bookingProductFeaturePricing->date_from = $specificDate;
                    $bookingProductFeaturePricing->date_to = $specificDate;
                }
                $bookingProductFeaturePricing->impact_way = $priceImpactWay;
                $bookingProductFeaturePricing->is_special_days_exists = $isSpecialDaysExists;
                $bookingProductFeaturePricing->special_days = $jsonSpecialDays;
                $bookingProductFeaturePricing->impact_type = $priceImpactType;
                $bookingProductFeaturePricing->impact_value = $impactValue;
                $bookingProductFeaturePricing->active = $enableFeaturePrice;
                if ($bookingProductFeaturePricing->save()) {
                    if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                        if ($id) {
                            Tools::redirectAdmin(
                                self::$currentIndex . '&id=' . (int) $bookingProductFeaturePricing->id . '&update' .
                                $this->table . '&conf=4&token=' . $this->token,
                            );
                        } else {
                            Tools::redirectAdmin(
                                self::$currentIndex . '&id=' . (int) $bookingProductFeaturePricing->id . '&update' .
                                $this->table . '&conf=3&token=' . $this->token,
                            );
                        }
                    } else {
                        if ($id) {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . $this->token);
                        }
                    }
                }
            }
        }
        if ($id) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
    }

    public function ajaxProcessSearchBookingProductByName()
    {
        $productName = pSQL(Tools::getValue('room_type_name'));
        $withCombi = pSQL(Tools::getValue('with_combi'));
        if ($productName) {
            $productsByName = WkBookingProductInformation::searchBookingProductByName(
                $this->context->language->id,
                $productName,
                null,
                // $withCombi
            );
            if ($productsByName) {
                echo json_encode($productsByName, 1);
                exit;
            } else {
                exit(json_encode(
                    ['status' => 'failed', 'msg' => $this->module->l('No match found for entered product name.', 'AdminBookingProductPricePlansSettingsController')],
                ));
            }
        } else {
            exit(json_encode(
                ['status' => 'failed', 'msg' => $this->module->l('No match found for entered product name.', 'AdminBookingProductPricePlansSettingsController')],
            ));
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/featurePriceSetting.js');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/featurePriceSetting.css');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/datepickerCustom.css');
    }
}
