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

class AdminBookingProductController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'WkBookingProductInformation';
        $this->table = 'wk_booking_product_info';
        $this->bootstrap = true;
        $this->identifier = 'id';

        parent::__construct();

        $this->page_header_toolbar_title = $this->module->l('Products', 'AdminBookingProductController');
        $this->toolbar_title = $this->module->l('Products', 'AdminBookingProductController');

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected', 'AdminBookingProductController'),
                'confirm' => $this->module->l('Delete selected items?', 'AdminBookingProductController'),
                'icon' => 'icon-trash',
            ],
        ];
        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = a.`id_product`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = a.`id_product` AND pl.`id_lang`=' .
        (int) $this->context->language->id . ' AND pl.`id_shop` =' . (int) $this->context->shop->id . ')';
        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'product_shop` sa ON (p.`id_product` = sa.`id_product` AND sa.id_shop =' . (int) $this->context->shop->id . ')';
        $this->_select = 'pl.`link_rewrite` as link_rewrite, pl.`name` as name, pl.`id_product` as temp_ps_id';
        // if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
        // In case of All Shops
        $this->_select .= ',shp.`name` as wk_ps_shop_name';
        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = pl.`id_shop`)';
        // }

        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            Shop::addTableAssociation('wk_booking_product_info', ['type' => 'shop', 'primary' => 'id']);
        }
        $this->_join .= WkBookingProductInformation::addSqlAssociationCustom(
            'wk_booking_product_info',
            'a',
            true,
            null,
            false,
            $this->identifier,
        );
        $this->_group = ' GROUP BY a.id';
        $this->fields_list = [
            'id' => [
                'title' => $this->module->l('ID', 'AdminBookingProductController'),
                'align' => 'center',
            ],
            'id_product' => [
                'title' => $this->module->l('Product ID', 'AdminBookingProductController'),
                'align' => 'center',
                'filter_key' => 'a!id_product',
            ],
            'link_rewrite' => [
                'title' => $this->module->l('Image', 'AdminBookingProductController'),
                'align' => 'center',
                'callback' => 'displayProductImage',
                'search' => false,
            ],
            'name' => [
                'title' => $this->module->l('Product name', 'AdminBookingProductController'),
                'align' => 'center',
                'filter_key' => 'pl!name',
            ],
            'booking_type' => [
                'title' => $this->module->l('Type', 'AdminBookingProductController'),
                'align' => 'center',
                'type' => 'select',
                'list' => WkBookingProductInformation::getBookingTypes(),
                'filter_key' => 'a!booking_type',
                'callback' => 'getBookingType',
            ],
            'temp_ps_id' => [
                'title' => $this->module->l('Preview', 'AdminBookingProductController'),
                'align' => 'center',
                'search' => false,
                'remove_onclick' => true,
                'callback' => 'previewProduct',
                'orderby' => false,
            ],
            'active' => [
                'title' => $this->module->l('Status', 'AdminBookingProductController'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'filter_key' => 'a!active',
            ],
        ];
        if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
            // In case of All Shops
            $this->fields_list['wk_ps_shop_name'] = [
                'title' => $this->module->l('Shop', 'AdminBookingProductController'),
                'havingFilter' => true,
                // 'orderby' => false,
            ];
        }
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

    public function previewProduct($idProduct, $rowData)
    {
        if ($idProduct && $rowData['active']) {
            $idLang = $this->context->language->id;
            $product = new Product($idProduct);

            $isRewriteActive = (bool) Configuration::get('PS_REWRITING_SETTINGS');
            $productPreviewLink = $this->context->link->getProductLink(
                $product,
                $this->getFieldValue($product, 'link_rewrite', $idLang),
                Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $idLang),
                null,
                $idLang,
                (int) $this->context->shop->id,
                0,
                $isRewriteActive,
            );
            $entityManager = PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('doctrine.orm.entity_manager');
            $tabRepository = $entityManager->getRepository(PrestaShopBundle\Entity\Tab::class);
            $idTab = $tabRepository->findOneIdByClassName('AdminProducts');

            $token = Tools::getAdminToken('AdminProducts' . (int) $idTab . (int) $this->context->employee->id);
            if (!$product->active) {
                $adminDir = dirname($_SERVER['PHP_SELF']);
                $adminDir = Tools::substr($adminDir, strrpos($adminDir, '/') + 1);
                $productPreviewLink .= ((strpos($productPreviewLink, '?') === false) ? '?' : '&') . 'adtoken=' . $token .
                '&ad=' . $adminDir . '&id_employee=' . (int) $this->context->employee->id;
            }
            $context = Context::getContext();
            $context->smarty->assign('productPreviewLink', $productPreviewLink);
            $tpl = $context->smarty->fetch(
                _PS_MODULE_DIR_ . 'psbooking/views/templates/admin/preview.tpl',
            );

            return $tpl;
        }
    }

    public function getBookingType($echo)
    {
        if ($echo == WkBookingProductInformation::TYPE_DATE_RANGE) {
            $return = $this->module->l('Date range', 'AdminBookingProductController');
        } elseif ($echo == WkBookingProductInformation::TYPE_TIME_SLOT) {
            $return = $this->module->l('Time slots', 'AdminBookingProductController');
        } elseif ($echo == WkBookingProductInformation::TYPE_EVENT) {
            $return = $this->module->l('Event', 'AdminBookingProductController');
        } elseif ($echo == WkBookingProductInformation::TYPE_RENTAL) {
            $return = $this->module->l('Rental', 'AdminBookingProductController');
        } else {
            return '--';
        }

        return $return;
    }

    // To display product Image on render list
    public function displayProductImage($linkRewrite, $rowData)
    {
        if ($rowData['id_product']) { // if product activated
            $idPsProduct = $rowData['id_product'];
            $cover = Product::getCover($idPsProduct);
            if ($cover) {
                $coverImage = $rowData['id_product'] . '-' . $cover['id_image'];
                $imageTypeName = ImageType::getFormattedName('small');
                $src = $this->context->link->getImageLink($linkRewrite, $coverImage, $imageTypeName);

                if ($src) {
                    $this->context->smarty->assign('image_src', $src);

                    return $this->context->smarty->fetch(
                        _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/display_product_image.tpl',
                    );
                }
            }
        }
        // if product images not available then show default image
        $imageSrc = _MODULE_DIR_ . $this->module->name . '/views/img/home-default.jpg';
        $this->context->smarty->assign('image_src', $imageSrc);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/display_product_image.tpl',
        );
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['view'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name,
            'desc' => $this->module->l('Module configuration', 'AdminBookingProductController'),
            'icon' => 'process-icon-cogs',
        ];

        $this->page_header_toolbar_btn['new'] = [
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->module->l('Add product', 'AdminBookingProductController'),
        ];
        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $objCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $currencySign = $objCurrency->sign;
        $dateFrom = date('d-m-Y');
        $dateTo = date('d-m-Y', strtotime('+1 day', strtotime($dateFrom)));
        $timeFrom = date('H:i');
        $timeTo = date('H:i', strtotime('1 hour'));
        // tinymce setup
        $this->context->smarty->assign('path_css', _THEME_CSS_DIR_);
        $this->context->smarty->assign('ad', __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_));
        $this->context->smarty->assign('autoload_rte', true);
        $this->context->smarty->assign('lang', true);
        $this->context->smarty->assign('iso', $this->context->language->iso_code);
        $this->context->smarty->assign('total_languages', count(Language::getLanguages()));
        $curruntLang = Configuration::get('PS_LANG_DEFAULT');
        $this->context->smarty->assign('current_lang', Language::getLanguage((int) $curruntLang));
        $this->context->smarty->assign('languages', Language::getLanguages());
        $this->context->smarty->assign('ps_img_dir', _PS_IMG_ . 'l/');
        // show tax rule group on add product page
        $taxRuleGroups = TaxRulesGroup::getTaxRulesGroups(true);
        if ($taxRuleGroups) {
            $this->context->smarty->assign('tax_rules_groups', $taxRuleGroups);
        }
        $adminCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $objProduct = new Product();
        $this->context->smarty->assign('defaultTaxRuleGroup', $objProduct->getIdTaxRulesGroup());
        $tab = Tools::getValue('active_tab');
        $idTable = 0;
        if ($this->display == 'edit') {
            /** @var WkBookingProductInformation $bookingProductInformation */
            $bookingProductInformation = $this->object;
            $idTable = $this->object->id;
            $idLang = Configuration::get('PS_LANG_DEFAULT');
            if (Validate::isLoadedObject($bookingProductInformation)) {
                $idProduct = $bookingProductInformation->id_product;
                if ($bookingProductInformation->booking_type == WkBookingProductInformation::TYPE_TIME_SLOT) {
                    $bkingProdTimeSlotsObj = new WkBookingProductTimeSlotPrices();
                    $bkingProdTimeSlots = $bkingProdTimeSlotsObj->getProductAllTimeSlotsFormatted($idProduct);
                    if (!empty($bkingProdTimeSlots)) {
                        $typeDate = WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DATE;
                        $typeDay = WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DAY;
                        foreach ($bkingProdTimeSlots as $value) {
                            if ($value['time_slot_type'] == $typeDay) {
                                $this->context->smarty->assign(
                                    'timeSlotType',
                                    $typeDay,
                                );
                            } elseif ($value['time_slot_type'] == $typeDate) {
                                $this->context->smarty->assign(
                                    'timeSlotType',
                                    $typeDate,
                                );
                            } else {
                                $this->context->smarty->assign(
                                    'timeSlotType',
                                    $typeDay,
                                );
                            }
                        }
                    } else {
                        $this->context->smarty->assign(
                            'timeSlotType',
                            WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DAY,
                        );
                    }
                    $this->context->smarty->assign('bookingProductTimeSlots', $bkingProdTimeSlots);
                } elseif ($bookingProductInformation->booking_type == WkBookingProductInformation::TYPE_EVENT) {
                    $eventData = WkBookingProductExtraInfo::getAlreadyAddedByProductId($idProduct);
                    if ($eventData) {
                        $eventObj = new WkBookingProductExtraInfo((int) $eventData['id']);
                        if (isset($eventData['date_from'])) {
                            $eventData['date_from'] = date('d-m-Y', strtotime($eventData['date_from']));
                        }
                        if (isset($eventData['date_to'])) {
                            $eventData['date_to'] = date('d-m-Y', strtotime($eventData['date_to']));
                        }
                        $bkingProdTimeSlotsObj = new WkBookingProductTimeSlotPrices();
                        $bkingProdTimeSlots = $bkingProdTimeSlotsObj->getProductAllTimeSlotsFormatted($idProduct);
                        $eventBannerImg = _PS_MODULE_DIR_ . 'psbooking/views/img/event//' . $idProduct . '/' . $idProduct . '_' . $this->context->shop->id . 'banner.jpg';
                        if (file_exists($eventBannerImg)) {
                            $this->context->smarty->assign('eventBannerImg', 1);
                        }
                        $this->context->smarty->assign(
                            ['eventData' => $eventObj, 'bookingProductTimeSlots' => $bkingProdTimeSlots,  'time' => time(), 'id_shop' => $this->context->shop->id],
                        );
                    }
                }

                $objProduct = new Product($idProduct, false, null);

                $defaultIdCategory = $objProduct->getDefaultCategory();

                $idCategory = [];
                $checkedProductCategory = Product::getProductCategories($idProduct);
                if ($checkedProductCategory) {
                    // Default category
                    foreach ($checkedProductCategory as $checkIdCategory) {
                        $idCategory[] = $checkIdCategory;
                    }

                    $catIdsJoin = implode(',', $idCategory);
                    $this->context->smarty->assign('catIdsJoin', $catIdsJoin);
                }
                $defaultCategory = Category::getCategoryInformation($idCategory, $this->context->language->id);
                $objBookingCart = new WkBookingsCart();

                // Data to show rates/Availability information on the calendar (Availability & Rates Tab)
                $bookingCalendarData = [];
                if (Tools::isSubmit('availability-search-submit')) {
                    $dateFrom = Tools::getValue('date_from');
                    $availablityDateTo = Tools::getValue('date_to');

                    $dateFrom = date('Y-m-d', strtotime($dateFrom));
                    $availablityDateTo = date('Y-m-d', strtotime($availablityDateTo));
                    if ($dateFrom == '') {
                        $this->errors[] = $this->module->l('Date from is required.', 'AdminBookingProductController');
                    }
                    if ($availablityDateTo == '') {
                        $this->errors[] = $this->module->l('Date to is required.', 'AdminBookingProductController');
                    }
                    if ($availablityDateTo < $dateFrom) {
                        $this->errors[] = $this->module->l('Date to should be greater than date from.', 'AdminBookingProductController');
                    }
                    $tab = Tools::getValue('active_tab');
                    if (!count($this->errors)) {
                        if (isset($eventData) && $eventData['date_to']) {
                            if (strtotime($eventData['date_to']) < strtotime($availablityDateTo)) {
                                $availablityDateTo = date('Y-m-d', strtotime($eventData['date_to']));
                            }
                        }
                        $dateStart = $dateFrom;
                        while (strtotime($dateStart) <= strtotime($availablityDateTo)) {
                            $tempDateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateStart)));
                            $available = $objBookingCart->getBookingProductDateWiseAvailabilityAndRates(
                                $idProduct,
                                $dateStart,
                                // $tempDateTo
                            );
                            $bookingCalendarData[$dateStart] = $available;
                            $dateStart = date('Y-m-d', strtotime('+1 day', strtotime($dateStart)));
                        }
                    }
                } else {
                    // assign booking info for today on the page
                    $dateFrom = date('Y-m-d');
                    if (isset($eventData) && $eventData) {
                        // $eventData['date_to']
                        $availablityDateTo = date('Y-m-d', strtotime($eventData['date_to']));
                        $dateFrom = date('Y-m-d', strtotime($eventData['date_from']));
                    } else {
                        $availablityDateTo = date('Y-m-t', strtotime("$dateFrom +1 month"));
                    }
                    $dateStart = $dateFrom;
                    while (strtotime($dateStart) <= strtotime($availablityDateTo)) {
                        $tempDateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateStart)));
                        $available = $objBookingCart->getBookingProductDateWiseAvailabilityAndRates(
                            $idProduct,
                            $dateStart,
                            // $tempDateTo
                        );
                        $bookingCalendarData[$dateStart] = $available;
                        $dateStart = date('Y-m-d', strtotime('+1 day', strtotime($dateStart)));
                    }
                }
                // End (Availability & Rates Tab)
                // Data to show Disables dates (Disable dates/slots tab)
                $objBookingDisableDates = new WkBookingProductDisabledDates();
                // get booking product disable dates
                $bookingDisableDates = $objBookingDisableDates->getBookingProductDisableDates($idProduct);
                if ($bookingDisableDates) {
                    if ($bookingDisableDates['disabled_special_days']) {
                        $bookingDisableDates['disabled_special_days'] = json_decode(
                            $bookingDisableDates['disabled_special_days'],
                            true,
                        );
                    }
                    if ($bookingDisableDates['disabled_dates_slots']) {
                        $bookingDisableDates['disabled_dates_slots_array'] = json_decode(
                            $bookingDisableDates['disabled_dates_slots'],
                            true,
                        );
                    }
                    $bookingDisableDatesInfo = $objBookingDisableDates->getBookingProductDisableDatesInfoFormatted(
                        $idProduct,
                    );
                    if ($bookingDisableDatesInfo) {
                        $this->context->smarty->assign(
                            [
                                'disabledDays' => $bookingDisableDatesInfo['disabledDays'],
                                'disabledDates' => $bookingDisableDatesInfo['disabledDates'],
                            ],
                        );
                    }
                    $this->context->smarty->assign(
                        [
                            'DISABLE_SPECIAL_DAYS_ACTIVE' => $bookingDisableDates['disable_special_days_active'],
                            'DISABLE_SPECIFIC_DAYS_ACTIVE' => $bookingDisableDates['disabled_dates_slots_active'],
                        ],
                    );
                }
                // End (Disable dates/slots tab)
                $this->context->smarty->assign(
                    [
                        'timeFrom' => $timeFrom,
                        'timeTo' => $timeTo,
                        'date_from' => $dateTo,
                        'date_to' => $dateTo,
                        'availablity_date_to' => $availablityDateTo,
                        'bookingProduct' => $objProduct,
                        'productBookingType' => $bookingProductInformation->booking_type,
                        'productQuantity' => $bookingProductInformation->quantity,
                        'booking_before' => $bookingProductInformation->booking_before,
                        'min_days_booking' => $bookingProductInformation->min_days_booking,
                        'max_days_booking' => $bookingProductInformation->max_days_booking,
                        'show_map' => $bookingProductInformation->show_map,
                        'address' => $bookingProductInformation->address,
                        'latitude' => $bookingProductInformation->latitude,
                        'longitude' => $bookingProductInformation->longitude,
                        'defaultCategory' => $defaultCategory,
                        'defaultIdCategory' => $defaultIdCategory,
                        'edit' => 1,
                        'bookingDisableDates' => $bookingDisableDates, // assign disabled booking dates
                        'bookingCalendarData' => $bookingCalendarData, // assign calendar (qty/rate) info
                        'wk_booking_api_key' => Configuration::get('WK_BOOKING_GEOLOCATION_API_KEY'),
                    ],
                );
                // Assign booking product image details
                $objProduct = new Product($idProduct, false, (int) $idLang);
                $productImage = $objProduct->getImages((int) $idLang);
                if ($productImage) {
                    $imageType = Tools::getValue('image_type');
                    foreach ($productImage as &$image) {
                        $objImage = new Image($image['id_image']);
                        $image['image_path'] = _THEME_PROD_DIR_ . $objImage->getExistingImgPath() . '.jpg';
                        $image['image_link'] = $this->context->link->getImageLink(
                            $objProduct->link_rewrite,
                            $idProduct . '-' . $image['id_image'],
                            $imageType,
                        );
                        $image['product_image'] = $idProduct . '-' . $image['id_image'];
                    }
                }
                // set calendar data
                $calendarHelper = new HelperCalendar();
                $calendarHelper->setDateFrom(date('Y-m-d')); // current date
                // next date
                $calendarHelper->setDateTo(date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d')))));

                // So that compare dates options do not show on the calendar
                $calendarHelper->setCompareDateFrom(null);
                $calendarHelper->setCompareDateTo(null);
                $calendarHelper->setCompareOption(null);

                $this->context->smarty->assign(
                    [
                        'link_rewrite' => $objProduct->link_rewrite,
                        'image_detail' => $productImage,
                        'id_product' => $idProduct,
                        'date_from' => $this->context->employee->stats_date_from,
                        'date_to' => $this->context->employee->stats_date_to,
                        'calendar' => $calendarHelper->generate(), // send calendar view to the tpl
                    ],
                );
            }
        }
        // Category tree
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/categorytree/themes/default/style.min.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/categorytree/jstree.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/categorytree/wk_jstree.js');
        $uploadMb = $this->getFileUploadSize();
        $this->context->smarty->assign(
            [
                'adminCurrency' => $adminCurrency,
                'active_tab' => empty($tab) ? 'information' : $tab,
                'wk_image_dir' => _MODULE_DIR_ . $this->module->name . '/views/img/',
                'idBookingProductInformation' => $idTable,
                'defaultCurrencySign' => $currencySign,
                'modules_dir' => _MODULE_DIR_,
                'ps_modules_dir' => _PS_MODULE_DIR_,
                'self' => dirname(__FILE__),
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'uploadMb' => $uploadMb,
                'type_date_range' => WkBookingProductInformation::TYPE_DATE_RANGE,
                'type_time_slot' => WkBookingProductInformation::TYPE_TIME_SLOT,
                'type_event' => WkBookingProductInformation::TYPE_EVENT,
                'type_rental' => WkBookingProductInformation::TYPE_RENTAL,
            ],
        );
        $this->fields_form = [
            'submit' => [
                'title' => $this->module->l('Save', 'AdminBookingProductController'),
            ],
        ];

        return parent::renderForm();
    }

    public function saveDaysSlots(
        $bookingTimeFroms,
        $bookingTimeTos,
        $idProduct,
        $day,
        $slotRangePrices,
        $slotRangeQty,
        $timeSloType,
        $slotActives
    ) {
        $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
        $errorList = [];
        foreach ($bookingTimeFroms as $keyTimeFrom => $tmFrm) {
            // validate time slots duplicacy
            foreach ($bookingTimeFroms as $keyTime => $timeSlotFrom) {
                $chkTmTo = $bookingTimeTos[$keyTime];
                if ($keyTimeFrom == $keyTime) {
                    break;
                } else {
                    if (strtotime($tmFrm) <= strtotime($chkTmTo)
                        && strtotime($bookingTimeTos[$keyTimeFrom]) >= strtotime($timeSlotFrom)
                    ) {
                        $errorMSg = $this->module->l('Duplicate time slots data not saved ', 'AdminBookingProductController');
                        if ($day == 1) {
                            $errorMSg .= $this->module->l(' for monday ', 'AdminBookingProductController');
                        } elseif ($day == 2) {
                            $errorMSg .= $this->module->l(' for tuesday ', 'AdminBookingProductController');
                        } elseif ($day == 3) {
                            $errorMSg .= $this->module->l(' for wednesday ', 'AdminBookingProductController');
                        } elseif ($day == 4) {
                            $errorMSg .= $this->module->l(' for thursday ', 'AdminBookingProductController');
                        } elseif ($day == 5) {
                            $errorMSg .= $this->module->l(' for friday ', 'AdminBookingProductController');
                        } elseif ($day == 6) {
                            $errorMSg .= $this->module->l(' for saturday ', 'AdminBookingProductController');
                        } elseif ($day == 7) {
                            $errorMSg .= $this->module->l(' for sunday ', 'AdminBookingProductController');
                        }
                        $errorList[] = $errorMSg;
                        $this->errors[] = $errorMSg;
                    }
                }
            }
            $vError = $wkTimeSlotPrices->validateTimeSlotsDuplicacyInDayRanges(
                $idProduct,
                $day,
                $tmFrm,
                $bookingTimeTos[$keyTimeFrom],
            );

            if ($vError) {
                $errorList[] = $vError;
                $this->errors[] = $vError;
            }
            if (count($errorList)) {
                continue; // if duplicate time slot dont proceed
            }
            if ($tmFrm
                && $bookingTimeTos[$keyTimeFrom]
            ) {
                if ($tmFrm < $bookingTimeTos[$keyTimeFrom]) {
                    if (Validate::isUnsignedInt($slotRangeQty[$keyTimeFrom])) {
                        if (Validate::isPrice(
                            $slotRangePrices[$keyTimeFrom],
                        )) {
                            $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
                            $wkTimeSlotPrices->id_product = $idProduct;
                            $wkTimeSlotPrices->slot_day = $day;
                            $wkTimeSlotPrices->time_slot_type = $timeSloType;
                            $wkTimeSlotPrices->time_slot_from = $tmFrm;
                            $wkTimeSlotPrices->time_slot_to = $bookingTimeTos[$keyTimeFrom];
                            $wkTimeSlotPrices->price = $slotRangePrices[$keyTimeFrom];
                            $wkTimeSlotPrices->quantity = $slotRangeQty[$keyTimeFrom];
                            $wkTimeSlotPrices->active = $slotActives[$keyTimeFrom];
                            $wkTimeSlotPrices->save();
                        } else {
                            $errorMSg = $this->module->l('Time slot ', 'AdminBookingProductController') . $tmFrm . $this->module->l(' to ', 'AdminBookingProductController') . $bookingTimeTos[$keyTimeFrom];
                            if ($day == 1) {
                                $errorMSg .= $this->module->l(' for monday ', 'AdminBookingProductController');
                            } elseif ($day == 2) {
                                $errorMSg .= $this->module->l(' for tuesday ', 'AdminBookingProductController');
                            } elseif ($day == 3) {
                                $errorMSg .= $this->module->l(' for wednesday ', 'AdminBookingProductController');
                            } elseif ($day == 4) {
                                $errorMSg .= $this->module->l(' for thursday ', 'AdminBookingProductController');
                            } elseif ($day == 5) {
                                $errorMSg .= $this->module->l(' for friday ', 'AdminBookingProductController');
                            } elseif ($day == 6) {
                                $errorMSg .= $this->module->l(' for saturday ', 'AdminBookingProductController');
                            } elseif ($day == 7) {
                                $errorMSg .= $this->module->l(' for sunday ', 'AdminBookingProductController');
                            }
                            $errorMSg .= $this->module->l(' not saved because of invalid price : ', 'AdminBookingProductController')
                            . $slotRangePrices[$keyTimeFrom];
                            $errorList[] = $errorMSg;
                            $this->errors[] = $errorMSg;
                        }
                    } else {
                        $errorMSg = $this->module->l('Time slot ', 'AdminBookingProductController') . $tmFrm . $this->module->l(' to ', 'AdminBookingProductController') . $bookingTimeTos[$keyTimeFrom];
                        if ($day == 1) {
                            $errorMSg .= $this->module->l(' for monday ', 'AdminBookingProductController');
                        } elseif ($day == 2) {
                            $errorMSg .= $this->module->l(' for tuesday ', 'AdminBookingProductController');
                        } elseif ($day == 3) {
                            $errorMSg .= $this->module->l(' for wednesday ', 'AdminBookingProductController');
                        } elseif ($day == 4) {
                            $errorMSg .= $this->module->l(' for thursday ', 'AdminBookingProductController');
                        } elseif ($day == 5) {
                            $errorMSg .= $this->module->l(' for friday ', 'AdminBookingProductController');
                        } elseif ($day == 6) {
                            $errorMSg .= $this->module->l(' for saturday ', 'AdminBookingProductController');
                        } elseif ($day == 7) {
                            $errorMSg .= $this->module->l(' for sunday ', 'AdminBookingProductController');
                        }
                        $errorMSg .= $this->module->l(' not saved because of invalid quantity : ', 'AdminBookingProductController') . $slotRangeQty[$keyTimeFrom];
                        $errorList[] = $errorMSg;
                        $this->errors[] = $errorMSg;
                    }
                } else {
                    $errorMSg = $this->module->l('Time slot ', 'AdminBookingProductController') . $tmFrm . $this->module->l(' to ', 'AdminBookingProductController') . $bookingTimeTos[$keyTimeFrom];
                    if ($day == 1) {
                        $errorMSg .= $this->module->l(' for monday ', 'AdminBookingProductController');
                    } elseif ($day == 2) {
                        $errorMSg .= $this->module->l(' for tuesday ', 'AdminBookingProductController');
                    } elseif ($day == 3) {
                        $errorMSg .= $this->module->l(' for wednesday ', 'AdminBookingProductController');
                    } elseif ($day == 4) {
                        $errorMSg .= $this->module->l(' for thursday ', 'AdminBookingProductController');
                    } elseif ($day == 5) {
                        $errorMSg .= $this->module->l(' for friday ', 'AdminBookingProductController');
                    } elseif ($day == 6) {
                        $errorMSg .= $this->module->l(' for saturday ', 'AdminBookingProductController');
                    } elseif ($day == 7) {
                        $errorMSg .= $this->module->l(' for sunday ', 'AdminBookingProductController');
                    }
                    $errorMSg .= $this->module->l(' not saved because of invalid time slots.', 'AdminBookingProductController');
                    $errorList[] = $errorMSg;
                    $this->errors[] = $errorMSg;
                }
            } else {
                $errorMSg = $this->module->l('Time slot not saved because of missing info of time slots', 'AdminBookingProductController');
                $errorList[] = $errorMSg;
                $this->errors[] = $errorMSg;
            }
        }
    }

    public function uploadEventBannerImage($idProduct)
    {
        $context = Context::getContext();
        $idShop = $context->shop->id;
        $path = _PS_MODULE_DIR_ . 'psbooking/views/img/event/';
        $source_index = _PS_MODULE_DIR_ . 'psbooking/index.php';
        if (is_uploaded_file($_FILES['event_banner']['tmp_name'])
        ) {
            // $img_name = $id.'_blog.jpg';
            if (!file_exists($path . '/' . $idProduct)) {
                @mkdir($path . '/' . $idProduct, 0777, true);
                @copy($source_index, $path . '/' . $idProduct . '/index.php');
            }
            $img_name = $idProduct . '_' . $idShop . 'banner.jpg';
            $tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmpName || (
                !move_uploaded_file($_FILES['event_banner']['tmp_name'], $tmpName))
            ) {
                return false;
            } elseif (!ImageManager::resize($tmpName, $path . '/' . $idProduct . '/' . $img_name)) {
                return false;
            }
        }
    }

    public function validateImageExt($image_name)
    {
        if (is_array($image_name)) {
            $img_name = $image_name['name'];
            $tmp_name = $image_name['tmp_name'];
            if (!ImageManager::isCorrectImageFileExt($img_name)
                || !ImageManager::isRealImage($tmp_name)) {
                return false;
            } else {
                return true;
            }
        } else {
            if (!ImageManager::isCorrectImageFileExt($image_name)) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function saveEventInfo($idProduct)
    {
        $eventBannerImage = $_FILES['event_banner']['name'];
        if (!empty($eventBannerImage)) {
            $this->uploadEventBannerImage($idProduct);
        }
        if (empty($this->errors)) {
            $alreadyAdded = WkBookingProductExtraInfo::getAlreadyAddedByProductId($idProduct);
            if ($alreadyAdded) {
                $objExtraInfo = new WkBookingProductExtraInfo($alreadyAdded['id']);
            } else {
                $objExtraInfo = new WkBookingProductExtraInfo();
            }
            $dateTimeFrom = Tools::getValue('event_start_date');
            $eventStartDate = date('Y-m-d', strtotime($dateTimeFrom));
            $eventStartTime = date('H:i', strtotime($dateTimeFrom));
            $dateTimeTo = Tools::getValue('event_end_date');
            $eventEndDate = date('Y-m-d', strtotime($dateTimeTo));
            $eventEndTime = date('H:i', strtotime($dateTimeTo));

            $selectedLangId = Tools::getValue('id_selected_lang');
            foreach (Language::getLanguages(false) as $lang) {
                $objExtraInfo->language[$lang['id_lang']] = trim(Tools::getValue(
                    'event_lang' . $lang['id_lang'],
                ));
                $objExtraInfo->artist[$lang['id_lang']] = trim(Tools::getValue(
                    'artist' . $lang['id_lang'],
                ));
                $objExtraInfo->organized_by[$lang['id_lang']] = trim(Tools::getValue(
                    'organized_by' . $selectedLangId,
                ));
                $objExtraInfo->category[$lang['id_lang']] = trim(Tools::getValue(
                    'event_category' . $lang['id_lang'],
                ));
                $objExtraInfo->age_group[$lang['id_lang']] = trim(Tools::getValue(
                    'age_group' . $lang['id_lang'],
                ));
            }
            $multipleSlot = trim(Tools::getValue('multiple_slot'));

            $objExtraInfo->id_product = $idProduct;
            $objExtraInfo->date_from = $eventStartDate;
            $objExtraInfo->date_to = $eventEndDate;
            $objExtraInfo->time_from = $eventStartTime;
            $objExtraInfo->time_to = $eventEndTime;
            $objExtraInfo->multiple_slot = $multipleSlot;
            if ($objExtraInfo->save()) {
                $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
                $invalidRange = 0;
                if ($wkTimeSlotPrices->deleteBookingProductTimeSlots($idProduct)) {
                    if ($objExtraInfo->multiple_slot) {
                        $invalidRange = $this->saveEventTimeSlot($idProduct);
                        if ($invalidRange) {
                            $this->errors[] = $this->module->l('Event slots have invalid date range.', 'AdminBookingProductController');
                        } else {
                            // if time slot is not exist then off multiple slot option
                            if (!$wkTimeSlotPrices->getProductAllTimeSlotsFormatted($idProduct)) {
                                $objExtraInfo->multiple_slot = 0;
                                $objExtraInfo->save();
                            }
                        }
                    }
                } else {
                    $this->errors[] = $this->module->l('Some error occurred while saving time slots info.', 'AdminBookingProductController');
                }
            } else {
                $this->errors[] = $this->module->l('Something went wrong during saving event info tab data.', 'AdminBookingProductController');
            }
        }
    }

    public function saveTimeSlotDayWise($idProduct)
    {
        $bookingTimeMondayFroms = Tools::getValue('booking_time_day_from1');
        $bookingTimeMondayTos = Tools::getValue('booking_time_day_to1');
        $slotRangePriceMondays = Tools::getValue('slot_range_price_day1');
        $slotRangeQtyMondays = Tools::getValue('slot_range_qty_day1');
        $slotActiveMondays = Tools::getValue('slot_active_day1');
        $timeSloType = Tools::getValue('time_slot_type');
        if (!empty($bookingTimeMondayFroms)) {
            $this->saveDaysSlots(
                $bookingTimeMondayFroms,
                $bookingTimeMondayTos,
                $idProduct,
                1,
                $slotRangePriceMondays,
                $slotRangeQtyMondays,
                $timeSloType,
                $slotActiveMondays,
            );
        }

        $bookingTimeTuesdayFroms = Tools::getValue('booking_time_day_from2');
        $bookingTimeTuesdayTos = Tools::getValue('booking_time_day_to2');
        $slotRangePriceTuesdays = Tools::getValue('slot_range_price_day2');
        $slotRangeQtyTuesdays = Tools::getValue('slot_range_qty_day2');
        $slotActiveTuesdays = Tools::getValue('slot_active_day2');
        if (!empty($bookingTimeTuesdayFroms)) {
            $this->saveDaysSlots(
                $bookingTimeTuesdayFroms,
                $bookingTimeTuesdayTos,
                $idProduct,
                2,
                $slotRangePriceTuesdays,
                $slotRangeQtyTuesdays,
                $timeSloType,
                $slotActiveTuesdays,
            );
        }

        $bookingTimeWednesdayFroms = Tools::getValue('booking_time_day_from3');
        $bookingTimeWednesdayTos = Tools::getValue('booking_time_day_to3');
        $slotRangePriceWednesdays = Tools::getValue('slot_range_price_day3');
        $slotRangeQtyWednesdays = Tools::getValue('slot_range_qty_day3');
        $slotActiveWednesdays = Tools::getValue('slot_active_day3');
        if (!empty($bookingTimeWednesdayFroms)) {
            $this->saveDaysSlots(
                $bookingTimeWednesdayFroms,
                $bookingTimeWednesdayTos,
                $idProduct,
                3,
                $slotRangePriceWednesdays,
                $slotRangeQtyWednesdays,
                $timeSloType,
                $slotActiveWednesdays,
            );
        }

        $bookingTimeThursdayFroms = Tools::getValue('booking_time_day_from4');
        $bookingTimeThursdayTos = Tools::getValue('booking_time_day_to4');
        $slotRangePriceThursdays = Tools::getValue('slot_range_price_day4');
        $slotRangeQtyThursdays = Tools::getValue('slot_range_qty_day4');
        $slotActiveThursdays = Tools::getValue('slot_active_day4');
        if (!empty($bookingTimeThursdayFroms)) {
            $this->saveDaysSlots(
                $bookingTimeThursdayFroms,
                $bookingTimeThursdayTos,
                $idProduct,
                4,
                $slotRangePriceThursdays,
                $slotRangeQtyThursdays,
                $timeSloType,
                $slotActiveThursdays,
            );
        }

        $bookingTimeFridayFroms = Tools::getValue('booking_time_day_from5');
        $bookingTimeFridayTos = Tools::getValue('booking_time_day_to5');
        $slotRangePriceFridays = Tools::getValue('slot_range_price_day5');
        $slotRangeQtyFridays = Tools::getValue('slot_range_qty_day5');
        $slotActiveFridays = Tools::getValue('slot_active_day5');
        if (!empty($bookingTimeFridayFroms)) {
            $this->saveDaysSlots(
                $bookingTimeFridayFroms,
                $bookingTimeFridayTos,
                $idProduct,
                5,
                $slotRangePriceFridays,
                $slotRangeQtyFridays,
                $timeSloType,
                $slotActiveFridays,
            );
        }

        $bookingTimeSaturdayFroms = Tools::getValue('booking_time_day_from6');
        $bookingTimeSaturdayTos = Tools::getValue('booking_time_day_to6');
        $slotRangePriceSaturdays = Tools::getValue('slot_range_price_day6');
        $slotRangeQtySaturdays = Tools::getValue('slot_range_qty_day6');
        $slotActiveSaturdays = Tools::getValue('slot_active_day6');
        if (!empty($bookingTimeSaturdayFroms)) {
            $this->saveDaysSlots(
                $bookingTimeSaturdayFroms,
                $bookingTimeSaturdayTos,
                $idProduct,
                6,
                $slotRangePriceSaturdays,
                $slotRangeQtySaturdays,
                $timeSloType,
                $slotActiveSaturdays,
            );
        }

        $bookingTimeSundayFroms = Tools::getValue('booking_time_day_from7');
        $bookingTimeSundayTos = Tools::getValue('booking_time_day_to7');
        $slotRangePriceSundays = Tools::getValue('slot_range_price_day7');
        $slotRangeQtySundays = Tools::getValue('slot_range_qty_day7');
        $slotActiveSundays = Tools::getValue('slot_active_day7');
        if (!empty($bookingTimeSundayFroms)) {
            $this->saveDaysSlots(
                $bookingTimeSundayFroms,
                $bookingTimeSundayTos,
                $idProduct,
                7,
                $slotRangePriceSundays,
                $slotRangeQtySundays,
                $timeSloType,
                $slotActiveSundays,
            );
        }
    }

    public function saveEventTimeSlot($idProduct)
    {
        $invalidRange = 0;
        $slotingDateFrom = Tools::getValue('event_sloting_date');
        // $slotingDateTo = Tools::getValue('sloting_date_to');
        if (!empty($slotingDateFrom)) {
            foreach ($slotingDateFrom as $keyDtFrm => $dateFrom) {
                if (!empty($dateFrom)) {
                    $bookingTmFrm = Tools::getValue(
                        'booking_time_from' . $keyDtFrm,
                    );
                    $bookingTimeTo = Tools::getValue(
                        'booking_time_to' . $keyDtFrm,
                    );
                    $slotRangePrice = Tools::getValue(
                        'slot_range_price' . $keyDtFrm,
                    );
                    $slotRangeQty = Tools::getValue(
                        'slot_range_qty' . $keyDtFrm,
                    );
                    $slotRangeId = Tools::getValue('time_slot_id' . $keyDtFrm);
                    $slotActive = Tools::getValue('slot_active' . $keyDtFrm);
                    if (isset($bookingTmFrm[0])
                        && $bookingTmFrm[0]
                        && $bookingTimeTo
                        && $slotRangePrice
                        && $slotRangeQty
                    ) {
                        foreach ($bookingTmFrm as $keyTimeFrom => $tmFrm) {
                            // validate time slots duplicacy
                            foreach ($bookingTmFrm as $keyTime => $timeSlotFrom) {
                                $chkTmTo = $bookingTimeTo[$keyTime];
                                if ($keyTimeFrom == $keyTime) {
                                    break;
                                } else {
                                    if (strtotime($tmFrm) < strtotime($chkTmTo)
                                        && strtotime($bookingTimeTo[$keyTimeFrom]) > strtotime($timeSlotFrom)
                                    ) {
                                        $this->errors[] = $this->module->l('Duplicate time slots data not saved.', 'AdminBookingProductController');
                                    }
                                }
                            }
                            $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
                            $vError = $wkTimeSlotPrices->validateTimeSlotsDuplicacyInOtherDateRanges(
                                $idProduct,
                                $dateFrom,
                                $slotingDateFrom[$keyDtFrm],
                                $tmFrm,
                                $bookingTimeTo[$keyTimeFrom],
                            );
                            if ($vError) {
                                $this->errors[] = $vError;
                            }
                            if (count($this->errors)) {
                                continue; // if duplicate time slot dont proceed
                            }
                            if ($tmFrm
                                && $bookingTimeTo[$keyTimeFrom]
                            ) {
                                if ($tmFrm < $bookingTimeTo[$keyTimeFrom]) {
                                    if (Validate::isUnsignedInt($slotRangeQty[$keyTimeFrom])) {
                                        if (Validate::isPrice(
                                            $slotRangePrice[$keyTimeFrom],
                                        )) {
                                            if (isset($slotRangeId[$keyTimeFrom])
                                                && $slotRangeId[$keyTimeFrom]
                                            ) {
                                                $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices(
                                                    $slotRangeId[$keyTimeFrom],
                                                );
                                            } else {
                                                $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
                                            }
                                            $wkTimeSlotPrices->id_product = $idProduct;
                                            $wkTimeSlotPrices->time_slot_type = WkBookingProductTimeSlotPrices::TIME_SLOT_EVENT_TYPE;
                                            $wkTimeSlotPrices->date_from = date(
                                                'Y-m-d',
                                                strtotime($dateFrom),
                                            );
                                            $wkTimeSlotPrices->date_to = date(
                                                'Y-m-d',
                                                strtotime($slotingDateFrom[$keyDtFrm]),
                                            );
                                            $wkTimeSlotPrices->time_slot_from = $tmFrm;
                                            $wkTimeSlotPrices->time_slot_to = $bookingTimeTo[$keyTimeFrom];
                                            $wkTimeSlotPrices->price = $slotRangePrice[$keyTimeFrom];
                                            $wkTimeSlotPrices->quantity = $slotRangeQty[$keyTimeFrom];
                                            $wkTimeSlotPrices->active = $slotActive[$keyTimeFrom];
                                            $wkTimeSlotPrices->save();
                                        } else {
                                            $this->errors[] = $this->module->l('Time slot ', 'AdminBookingProductController') .
                                            $tmFrm .
                                            $this->module->l(' to ', 'AdminBookingProductController') .
                                            $bookingTimeTo[$keyTimeFrom] .
                                            $this->module->l(' for the date ', 'AdminBookingProductController') .
                                            date('Y-m-d', strtotime($dateFrom)) .
                                            $this->module->l(' not saved because of invalid price', 'AdminBookingProductController');
                                        }
                                    } else {
                                        $this->errors[] = $this->module->l('Time Slot ', 'AdminBookingProductController') .
                                        $tmFrm .
                                        $this->module->l(' to ', 'AdminBookingProductController') .
                                        $bookingTimeTo[$keyTimeFrom] .
                                        $this->module->l(' for the date ', 'AdminBookingProductController') .
                                        date('Y-m-d', strtotime($dateFrom)) .
                                        $this->module->l(' not saved because of invalid quantity : ', 'AdminBookingProductController');
                                    }
                                } else {
                                    $this->errors[] = $this->module->l('Time slot ', 'AdminBookingProductController') .
                                    $tmFrm .
                                    $this->module->l(' to ', 'AdminBookingProductController') . $bookingTimeTo[$keyTimeFrom] .
                                    $this->module->l(' for the date ', 'AdminBookingProductController') .
                                    date('Y-m-d', strtotime($dateFrom)) .
                                    $this->module->l(' not saved because of invalid time slots.', 'AdminBookingProductController');
                                }
                            } else {
                                $this->errors[] = $this->module->l('Time slot not saved ', 'AdminBookingProductController')
                                . $this->module->l('because of missing info of time slots.', 'AdminBookingProductController');
                            }
                        }
                    }
                }
            }
        }

        return $invalidRange;
    }

    public function saveTimeSlotDateWise($idProduct, $timeSloType)
    {
        $invalidRange = 0;
        $slotingDateFrom = Tools::getValue('sloting_date_from');
        $slotingDateTo = Tools::getValue('sloting_date_to');
        if (!empty($slotingDateFrom)) {
            foreach ($slotingDateFrom as $keyDtFrm => $dateFrom) {
                if ($dateFrom && $slotingDateTo[$keyDtFrm]) {
                    if (strtotime($dateFrom) <= strtotime($slotingDateTo[$keyDtFrm])) {
                        if (!count($this->errors)) {
                            $bookingTmFrm = Tools::getValue(
                                'booking_time_from' . $keyDtFrm,
                            );
                            $bookingTimeTo = Tools::getValue(
                                'booking_time_to' . $keyDtFrm,
                            );
                            $slotRangePrice = Tools::getValue(
                                'slot_range_price' . $keyDtFrm,
                            );
                            $slotRangeQty = Tools::getValue(
                                'slot_range_qty' . $keyDtFrm,
                            );
                            $slotRangeId = Tools::getValue('time_slot_id' . $keyDtFrm);
                            $slotActive = Tools::getValue('slot_active' . $keyDtFrm);
                            if (isset($bookingTmFrm[0])
                                && $bookingTmFrm[0]
                                && $bookingTimeTo
                                && $slotRangePrice
                                && $slotRangeQty
                            ) {
                                foreach ($bookingTmFrm as $keyTimeFrom => $tmFrm) {
                                    // validate time slots duplicacy
                                    foreach ($bookingTmFrm as $keyTime => $timeSlotFrom) {
                                        $chkTmTo = $bookingTimeTo[$keyTime];
                                        if ($keyTimeFrom == $keyTime) {
                                            break;
                                        } else {
                                            if (strtotime($tmFrm) < strtotime($chkTmTo)
                                                && strtotime($bookingTimeTo[$keyTimeFrom]) > strtotime($timeSlotFrom)
                                            ) {
                                                $this->errors[] = $this->module->l('Duplicate time slots data not saved.', 'AdminBookingProductController');
                                            }
                                        }
                                    }
                                    $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
                                    $vError = $wkTimeSlotPrices->validateTimeSlotsDuplicacyInOtherDateRanges(
                                        $idProduct,
                                        $dateFrom,
                                        $slotingDateTo[$keyDtFrm],
                                        $tmFrm,
                                        $bookingTimeTo[$keyTimeFrom],
                                    );
                                    if ($vError) {
                                        $this->errors[] = $vError;
                                    }
                                    if (count($this->errors)) {
                                        continue; // if duplicate time slot dont proceed
                                    }
                                    if ($tmFrm
                                        && $bookingTimeTo[$keyTimeFrom]
                                    ) {
                                        if ($tmFrm < $bookingTimeTo[$keyTimeFrom]) {
                                            if (Validate::isUnsignedInt($slotRangeQty[$keyTimeFrom])) {
                                                if (Validate::isPrice(
                                                    $slotRangePrice[$keyTimeFrom],
                                                )) {
                                                    if (isset($slotRangeId[$keyTimeFrom])
                                                        && $slotRangeId[$keyTimeFrom]
                                                    ) {
                                                        $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices(
                                                            $slotRangeId[$keyTimeFrom],
                                                        );
                                                    } else {
                                                        $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
                                                    }
                                                    $wkTimeSlotPrices->id_product = $idProduct;
                                                    $wkTimeSlotPrices->time_slot_type = $timeSloType;
                                                    $wkTimeSlotPrices->date_from = date(
                                                        'Y-m-d',
                                                        strtotime($dateFrom),
                                                    );
                                                    $wkTimeSlotPrices->date_to = date(
                                                        'Y-m-d',
                                                        strtotime($slotingDateTo[$keyDtFrm]),
                                                    );
                                                    $wkTimeSlotPrices->time_slot_from = $tmFrm;
                                                    $wkTimeSlotPrices->time_slot_to = $bookingTimeTo[$keyTimeFrom];
                                                    $wkTimeSlotPrices->price = $slotRangePrice[$keyTimeFrom];
                                                    $wkTimeSlotPrices->quantity = $slotRangeQty[$keyTimeFrom];
                                                    $wkTimeSlotPrices->active = $slotActive[$keyTimeFrom];
                                                    $wkTimeSlotPrices->save();
                                                } else {
                                                    $this->errors[] = $this->module->l('Time slot ', 'AdminBookingProductController') .
                                                    $tmFrm .
                                                    $this->module->l(' to ', 'AdminBookingProductController') .
                                                    $bookingTimeTo[$keyTimeFrom] .
                                                    $this->module->l(' for the date range ', 'AdminBookingProductController') .
                                                    date('Y-m-d', strtotime($dateFrom)) .
                                                    $this->module->l(' to ', 'AdminBookingProductController')
                                                    . date(
                                                        'Y-m-d',
                                                        strtotime($slotingDateTo[$keyDtFrm]),
                                                    ) .
                                                    $this->module->l(' not saved because of invalid price', 'AdminBookingProductController');
                                                }
                                            } else {
                                                $this->errors[] = $this->module->l('Time Slot ', 'AdminBookingProductController') .
                                                $tmFrm .
                                                $this->module->l(' to ', 'AdminBookingProductController') .
                                                $bookingTimeTo[$keyTimeFrom] .
                                                $this->module->l(' for the date range ', 'AdminBookingProductController') .
                                                date('Y-m-d', strtotime($dateFrom)) .
                                                $this->module->l(' To ', 'AdminBookingProductController')
                                                . date(
                                                    'Y-m-d',
                                                    strtotime($slotingDateTo[$keyDtFrm]),
                                                ) .
                                                $this->module->l(' not saved because of invalid quantity', 'AdminBookingProductController');
                                            }
                                        } else {
                                            $this->errors[] = $this->module->l('Time slot ', 'AdminBookingProductController') .
                                            $tmFrm .
                                            $this->module->l(' to ', 'AdminBookingProductController') . $bookingTimeTo[$keyTimeFrom] .
                                            $this->module->l(' for the date range ', 'AdminBookingProductController') .
                                            date('Y-m-d', strtotime($dateFrom)) .
                                            $this->module->l(' to ', 'AdminBookingProductController') . date(
                                                'Y-m-d',
                                                strtotime($slotingDateTo[$keyDtFrm]),
                                            ) . $this->module->l(' not saved because of invalid time slots.', 'AdminBookingProductController');
                                        }
                                    } else {
                                        $this->errors[] = $this->module->l('Time slot not saved ', 'AdminBookingProductController')
                                        . $this->module->l('because of missing info of time slots.', 'AdminBookingProductController');
                                    }
                                }
                            }
                        }
                    } else {
                        $this->errors[] = $this->module->l('Date from can not be after date to.', 'AdminBookingProductController');
                    }
                } else {
                    $invalidRange = 1;
                }
            }
        }

        return $invalidRange;
    }

    public function postProcess()
    {
        $id = Tools::getValue('idTable');
        if ($id) {
            $bookingProductObj = new WkBookingProductInformation($id);
            $idBookingProduct = $bookingProductObj->id_product;
            $conf = 4;
        } else {
            $bookingProductObj = new WkBookingProductInformation();
            $idBookingProduct = 0;
            $conf = 3;
        }
        // if admin will submit the time slots of the booking product
        if (Tools::isSubmit('submitAddwk_booking_product_infoAndStay')
            || Tools::isSubmit('submitAddwk_booking_product_info')
        ) {
            $this->validateBookingProductDetails();
            if (!Tools::getValue('booking_type')) {
                $this->errors[] = $this->module->l('Please select product booking type.', 'AdminBookingProductController');
            }
            if (!count($this->errors)) {
                $wkTimeSlotPrices = new WkBookingProductTimeSlotPrices();
                if ($idProduct = $this->saveBookingProduct($idBookingProduct)) {
                    $productObj = new Product($idProduct);
                    $bookingProductObj->id_product = $productObj->id;
                    $bookingProductObj->quantity = Tools::getValue('product_quantity');
                    $bookingProductObj->booking_type = Tools::getValue('booking_type');
                    $bookingProductObj->booking_before = Tools::getValue('booking_before');
                    $bookingProductObj->min_days_booking = Tools::getValue('min_days_booking');
                    $bookingProductObj->max_days_booking = Tools::getValue('max_days_booking');
                    $bookingProductObj->show_map = Tools::getValue('show_map');
                    $bookingProductObj->address = Tools::getValue('address');
                    $bookingProductObj->longitude = Tools::getValue('longitude');
                    $bookingProductObj->latitude = Tools::getValue('latitude');
                    $bookingProductObj->active = $productObj->active;
                    // save the disable dates and time slots info
                    $toDisableSpecialDays = Tools::getValue('disable_special_days_active');
                    $toDisableDates = Tools::getValue('disable_specific_days_active');
                    if ($bookingProductObj->save()) {
                        // if product is successfully saved the save the event info information if available
                        $eventInfoSave = Tools::getValue('event_info_save');
                        if (Tools::getValue('booking_type') == WkBookingProductInformation::TYPE_EVENT && isset($eventInfoSave) && $eventInfoSave) {
                            $this->saveEventInfo($idProduct);
                        }
                        // if product is successfully saved the save the time slot information if available
                        $saveTimeSlotInfo = Tools::getValue('time_slots_data_save');
                        if (isset($saveTimeSlotInfo) && $saveTimeSlotInfo) {
                            $slotingDateFrom = Tools::getValue('sloting_date_from');
                            $slotingDateTo = Tools::getValue('sloting_date_to');
                            $idProduct = Tools::getValue('id_booking_product');
                            $timeSloType = Tools::getValue('time_slot_type');
                            if (isset($slotingDateFrom[0]) && $slotingDateFrom[0] && !$slotingDateTo) {
                                $this->errors[] = $this->module->l('Please select at least one valid date range.', 'AdminBookingProductController');
                            }
                            if (!$idProduct) {
                                $this->errors[] = $this->module->l('Booking product id is missing.', 'AdminBookingProductController');
                            }
                            if (!count($this->errors)) {
                                $invalidRange = 0;
                                if ($wkTimeSlotPrices->deleteBookingProductTimeSlots($idProduct)) {
                                    if ($timeSloType == WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DATE) {
                                        $invalidRange = $this->saveTimeSlotDateWise($idProduct, $timeSloType);
                                    } else {
                                        $this->saveTimeSlotDayWise($idProduct);
                                    }
                                } else {
                                    $this->errors[] = $this->module->l('Some error occurred while saving time slots info.', 'AdminBookingProductController');
                                }
                                if ($invalidRange) {
                                    $this->errors[] = $this->module->l('Invalid date ranges were not saved.', 'AdminBookingProductController');
                                }
                            }
                        }
                        // Data to show Disables dates (Disable dates/slots tab)
                        $dsbldSpclDays = Tools::getValue('disabled_special_days');
                        $disabledSpecificDatesJson = Tools::getValue('disabled_specific_dates_json');
                        if ($toDisableSpecialDays) {
                            if (!$dsbldSpclDays) {
                                $this->errors[] = $this->module->l('If disable special days is active, ', 'AdminBookingProductController')
                                . $this->module->l(' Please select at least one special day to disable.', 'AdminBookingProductController');
                            }
                        }
                        if ($toDisableDates) {
                            if (!$disabledSpecificDatesJson || !count(json_decode($disabledSpecificDatesJson, true))) {
                                $this->errors[] = $this->module->l('If disable specific dates is active, ', 'AdminBookingProductController')
                                . $this->module->l(' Please select at least one date to disable.', 'AdminBookingProductController');
                            }
                        }
                    }
                    if (empty($this->errors)) {
                        $objBookingDisableDates = new WkBookingProductDisabledDates();
                        $bookingDisableDates = $objBookingDisableDates->getBookingProductDisableDates($idProduct);
                        if ($bookingDisableDates) {
                            $objBookingDisableDates = new WkBookingProductDisabledDates($bookingDisableDates['id']);
                        }
                        $objBookingDisableDates->disable_special_days_active = $toDisableSpecialDays;
                        $objBookingDisableDates->disabled_dates_slots_active = $toDisableDates;
                        $objBookingDisableDates->id_product = $idProduct;
                        $objBookingDisableDates->disabled_special_days = (isset($dsbldSpclDays) && $dsbldSpclDays ?
                         json_encode($dsbldSpclDays) : 0);
                        $objBookingDisableDates->disabled_dates_slots = (isset($disabledSpecificDatesJson)
                        && $disabledSpecificDatesJson ? $disabledSpecificDatesJson : 0);
                        if ($objBookingDisableDates->save()) {
                            if (Tools::isSubmit('submitAddwk_booking_product_infoAndStay')) {
                                Tools::redirectAdmin(
                                    self::$currentIndex . '&conf=' . $conf . '&token=' . $this->token . '&id=' . $bookingProductObj->id .
                                    '&updatewk_booking_product_info&tab=' . Tools::getValue('active_tab'),
                                );
                            } else {
                                Tools::redirectAdmin(self::$currentIndex . '&conf=' . $conf . '&token=' . $this->token);
                            }
                        } else {
                            $this->errors[] = $this->module->l('Some error has been occurred while saving disable dates info.', 'AdminBookingProductController');
                        }
                    } else {
                        $tab = Tools::getValue('active_tab');
                        $this->context->smarty->assign(
                            [
                                'active_tab' => empty($tab) ? 'information' : $tab,
                            ],
                        );
                        // if product is saved but some errors are occurred while saving time slots information
                        $this->confirmations[] = $this->module->l('Product has been saved successfully. ', 'AdminBookingProductController')
                        . $this->module->l(' But above errors were occurred while saving time slots information.', 'AdminBookingProductController');
                        if ($id) {
                            $this->display = 'edit';
                        } else {
                            $this->display = 'add';
                        }
                    }
                }
            } else {
                $this->display = 'edit';
            }
        }
        parent::postProcess();
    }

    /**
     * [saveBookingProduct save booking products information]
     */
    public function saveBookingProduct($id = 0)
    {
        if ($id) {
            $product = new Product($id);
        } else {
            $product = new Product();
        }
        // Add Product
        $product->name = [];
        $product->description = [];
        $product->description_short = [];
        $product->link_rewrite = [];
        $selectedLangId = Tools::getValue('id_selected_lang');
        foreach (Language::getLanguages(false) as $lang) {
            $product->name[$lang['id_lang']] = trim(Tools::getValue(
                'name' . $lang['id_lang'],
            ));
            if (empty($product->name[$lang['id_lang']])) {
                $product->name[$lang['id_lang']] = trim(Tools::getValue(
                    'name' . $selectedLangId,
                ));
            }
            $product->description[$lang['id_lang']] = Tools::getValue(
                'description' . $lang['id_lang'],
            );
            $product->description_short[$lang['id_lang']] = Tools::getValue(
                'description_short' . $lang['id_lang'],
            );
            if (Tools::getValue(
                'name' . $lang['id_lang'],
            )) {
                $product->link_rewrite[$lang['id_lang']] = Tools::str2url(trim(Tools::getValue(
                    'name' . $lang['id_lang'],
                )));
            } else {
                $product->link_rewrite[$lang['id_lang']] = Tools::str2url(trim(Tools::getValue(
                    'name' . $selectedLangId,
                )));
            }
        }
        $product->id_shop_default = Context::getContext()->shop->id;
        $product->id_category_default = Tools::getValue('default_category');
        $product->price = Tools::getValue('price');
        $product->active = Tools::getValue('active');
        $product->indexed = true;
        if (Tools::getValue('booking_type') != WkBookingProductInformation::TYPE_RENTAL) {
            $product->is_virtual = true;
        }
        $product->condition = Tools::getValue('condition');
        $product->show_condition = true;
        $product->id_tax_rules_group = Tools::getValue('id_tax_rules_group');
        if ($product->save()) {
            $categoryIds = Tools::getValue('product_category');
            $categoryIds = explode(',', $categoryIds);
            if ($id) {
                $product->updateCategories(array_map('intval', (array) $categoryIds));
            } else {
                $product->addToCategories(array_map('intval', (array) $categoryIds));
            }
            $quantity = Tools::getValue('product_quantity');
            if ($quantity >= 0) {
                // Rental product can have combinations so update stock for combinations
                $productAttributes = $product->getAttributeCombinations();
                // if shop then update in current shop only
                if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    StockAvailable::setQuantity($product->id, 0, 999999999, $this->context->shop->id);
                    if (!empty($productAttributes)) {
                        foreach ($productAttributes as $productAttribute) {
                            StockAvailable::setQuantity($product->id, (int) $productAttribute['id_product_attribute'], 999999999, $this->context->shop->id);
                        }
                    }
                } else {
                    $groupShops = '';
                    // if group then update in current group shop only
                    if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                        $groupShops = Shop::getShops(true, $this->context->shop->id_shop_group);
                    }
                    // if all then update in all shop
                    if (Shop::getContext() == Shop::CONTEXT_ALL) {
                        $groupShops = Shop::getShops(true);
                    }
                    if ($groupShops) {
                        $oldContext = Shop::getContext();
                        $currentShop = Context::getContext()->shop;
                        foreach ($groupShops as $groupShop) {
                            $shop = Context::getContext()->shop;
                            // Important to setContext
                            Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
                            $this->context->shop = $shop;
                            // $this->context->cookie->id_shop = $shop->id;
                            StockAvailable::setQuantity($product->id, 0, 999999999, $groupShop['id_shop']);
                            if (!empty($productAttributes)) {
                                foreach ($productAttributes as $productAttribute) {
                                    StockAvailable::setQuantity($product->id, (int) $productAttribute['id_product_attribute'], 999999999, $this->context->shop->id);
                                }
                            }
                        }
                        // Important to setContext
                        Shop::setContext($oldContext, $currentShop->id);
                        $this->context->shop = $currentShop;
                        // $this->context->cookie->id_shop = $currentShop->id;
                    }
                }
            }
            foreach (Language::getLanguages(false) as $lang) {
                Search::indexation($product->link_rewrite[$lang['id_lang']], $product->id);
            }

            return $product->id;
        }

        return false;
    }

    /**
     * [validateBookingProductDetails validate booking product submitted information]
     */
    public function validateBookingProductDetails()
    {
        $nameMultiLang = [];
        $shortdescriptionMultiLang = [];
        $descriptionMultiLang = [];
        $langulages = Language::getLanguages();
        foreach ($langulages as $langulage) {
            $descriptionMultiLang['description' . $langulage['id_lang']] = Tools::getValue(
                'description' . $langulage['id_lang'],
            );
            $nameMultiLang['name' . $langulage['id_lang']] = Tools::getValue(
                'name' . $langulage['id_lang'],
            );
            $shortdescriptionMultiLang['description_short' . $langulage['id_lang']] = Tools::getValue(
                'description_short' . $langulage['id_lang'],
            );
            if ($langulage['id_lang'] == Tools::getValue('id_selected_lang')) {
                if (empty(trim($nameMultiLang['name' . $langulage['id_lang']]))) {
                    $this->errors[] = $this->module->l('Product name is required.', 'AdminBookingProductController');
                }
            }
            if (!Validate::isCatalogName($nameMultiLang['name' . $langulage['id_lang']])) {
                $this->errors[] = sprintf(
                    $this->module->l('Product name must not have invalid characters <>;=#{} in %s language.', 'AdminBookingProductController'),
                    $langulage['name'],
                );
            }
        }
        $productPrice = Tools::getValue('price');
        $productQuantity = Tools::getValue('product_quantity');
        $bookingBefore = Tools::getValue('booking_before');
        $minDaysBooking = Tools::getValue('min_days_booking');
        $maxDaysBooking = Tools::getValue('max_days_booking');
        $showMap = Tools::getValue('show_map');
        $address = Tools::getValue('address');
        $longitude = Tools::getValue('longitude');
        $latitude = Tools::getValue('latitude');
        $productCategory = Tools::getValue('product_category');
        // check category exist or not before save
        $categoryIds = explode(',', $productCategory);
        foreach ($categoryIds as $key => $categoryId) {
            $categoryObj = new Category((int) $categoryId);
            if (!Validate::isLoadedObject($categoryObj)) {
                unset($categoryIds[$key]);
            }
        }
        $productCategory = implode(',', $categoryIds);
        if ($shortdescriptionMultiLang) {
            $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
            if ($limit <= 0) {
                $limit = 400;
            }
            foreach ($shortdescriptionMultiLang as $key => $shortDesc) {
                $shortDescHtmlContent = strip_tags(stripslashes($shortDesc));
                $shortDescHtmlContent = str_replace(chr(0xC2) . chr(0xA0), '', $shortDescHtmlContent);
                if (!Validate::isCleanHtml($shortDesc) || $this->checkScriptInHtml($shortDescHtmlContent)) {
                    $this->errors[] = $this->module->l('Invalid short description.', 'AdminBookingProductController');
                }

                if (Tools::strlen(strip_tags($shortDesc)) > $limit) {
                    $this->errors[] = sprintf(
                        $this->module->l('Short description is too long: %1$d chars max (current count %2$d).', 'AdminBookingProductController'),
                        $limit,
                        Tools::strlen(strip_tags($shortDesc)),
                    );
                }
            }
        }
        if ($descriptionMultiLang) {
            foreach ($descriptionMultiLang as $desc) {
                $descHtmlContent = strip_tags(stripslashes($desc));
                $descHtmlContent = str_replace(chr(0xC2) . chr(0xA0), '', $descHtmlContent);
                if (!Validate::isCleanHtml($desc, (int) Configuration::get('PS_ALLOW_HTML_IFRAME')) || $this->checkScriptInHtml($descHtmlContent)) {
                    $this->errors[] = $this->module->l('Invalid product description.', 'AdminBookingProductController');
                }
            }
        }
        if ($productPrice == '') {
            $this->errors[] = $this->module->l('Product price is required.', 'AdminBookingProductController');
        } elseif (!Validate::isPrice($productPrice)) {
            $this->errors[] = $this->module->l('Invalid product price.', 'AdminBookingProductController');
        }
        if ($productQuantity == '') {
            $this->errors[] = $this->module->l('Product quantity is required.', 'AdminBookingProductController');
        } elseif (!Validate::isInt($productQuantity)) {
            $this->errors[] = $this->module->l('Invalid product quantity.', 'AdminBookingProductController');
        }
        if (!empty($bookingBefore) && !Validate::isUnsignedInt($bookingBefore)) {
            $this->errors[] = $this->module->l('Booking before is invalid.', 'AdminBookingProductController');
        }
        if (!empty($minDaysBooking) && !Validate::isUnsignedInt($minDaysBooking)) {
            $this->errors[] = $this->module->l('Minimum days is invalid.', 'AdminBookingProductController');
        }
        if (!empty($maxDaysBooking) && !Validate::isUnsignedInt($maxDaysBooking)) {
            $this->errors[] = $this->module->l('Maximum days is invalid.', 'AdminBookingProductController');
        }
        if (!empty($maxDaysBooking) && $maxDaysBooking < $minDaysBooking) {
            $this->errors[] = $this->module->l('Maximum days should be greater than minimum days.', 'AdminBookingProductController');
        }
        if (!$productCategory) {
            $this->errors[] = $this->module->l('Please select at least one category.', 'AdminBookingProductController');
        }

        if ($showMap) {
            if (empty(trim($address))) {
                $this->errors[] = $this->module->l('Address is required.', 'AdminBookingProductController');
            } elseif (!Validate::isAddress($address)) {
                $this->errors[] = $this->module->l('Address is invalid.', 'AdminBookingProductController');
            }
            if (empty($longitude) || $longitude == '0.00000000' || empty($latitude) || $latitude == '0.00000000') {
                $this->errors[] = $this->module->l('Please select location on google map.', 'AdminBookingProductController');
            }
        }
        if (Tools::getValue('event_info_save')) {
            $eventStartDate = Tools::getValue('event_start_date');
            $eventEndDate = Tools::getValue('event_end_date');
            if (empty($eventStartDate)) {
                $this->errors[] = $this->module->l('Please enter event start date time.', 'AdminBookingProductController');
            }
            if (empty($eventEndDate)) {
                $this->errors[] = $this->module->l('Please enter event end date time.', 'AdminBookingProductController');
            }
            if (!empty($eventEndDate) && !empty($eventStartDate)) {
                if (strtotime($eventEndDate) <= strtotime($eventStartDate)) {
                    $this->errors[] = $this->module->l('Event start date time must be less than end date time.', 'AdminBookingProductController');
                }
            }
            foreach ($langulages as $langulage) {
                if (!Validate::isCatalogName(Tools::getValue(
                    'event_lang' . $langulage['id_lang'],
                ))) {
                    $this->errors[] = sprintf(
                        $this->module->l('Event language must not have invalid characters <>;=#{} in %s language.', 'AdminBookingProductController'),
                        $langulage['name'],
                    );
                }
                if (!Validate::isCatalogName(Tools::getValue(
                    'event_lang' . $langulage['id_lang'],
                ))) {
                    $this->errors[] = sprintf(
                        $this->module->l('Event age group must not have invalid characters <>;=#{} in %s language.', 'AdminBookingProductController'),
                        $langulage['name'],
                    );
                }
                if (!Validate::isCatalogName(Tools::getValue(
                    'artist' . $langulage['id_lang'],
                ))) {
                    $this->errors[] = sprintf(
                        $this->module->l('Event artist must not have invalid characters <>;=#{} in %s language.', 'AdminBookingProductController'),
                        $langulage['name'],
                    );
                }
                if (!Validate::isCatalogName(Tools::getValue(
                    'organized_by' . $langulage['id_lang'],
                ))) {
                    $this->errors[] = sprintf(
                        $this->module->l('Event organized by must not have invalid characters <>;=#{} in %s language.', 'AdminBookingProductController'),
                        $langulage['name'],
                    );
                }
                if (!Validate::isCatalogName(Tools::getValue(
                    'event_category' . $langulage['id_lang'],
                ))) {
                    $this->errors[] = sprintf(
                        $this->module->l('Event category must not have invalid characters <>;=#{} in %s language.', 'AdminBookingProductController'),
                        $langulage['name'],
                    );
                }
            }
            $artist = trim(Tools::getValue('artist'));
            if (!empty($artist) && !Validate::isName($artist)) {
                $this->errors[] = $this->module->l('Artist is invalid.', 'AdminBookingProductController');
            }
            $organizedBy = trim(Tools::getValue('organized_by'));
            if (!empty($organizedBy) && !Validate::isName($organizedBy)) {
                $this->errors[] = $this->module->l('Organized by is invalid.', 'AdminBookingProductController');
            }
            $eventBannerImage = $_FILES['event_banner']['name'];
            if (!empty($eventBannerImage) && $_FILES['event_banner']['size'] > 0) {
                if (!$this->validateImageExt($_FILES['event_banner'])) {
                    $this->errors[] = $this->module->l('Event banner image format not recognized, allowed formats are: .gif, .jpg, .png', 'AdminBookingProductController');
                }
            }
        }
    }

    public function checkScriptInHtml($html)
    {
        if (trim($html)) {
            $dom = new DOMDocument();
            @$dom->loadHTML(htmlspecialchars_decode($html));
            $script = $dom->getElementsByTagName('script');

            return $script->length;
        } else {
            return 0;
        }
    }

    public function ajaxProcessUploadimage()
    {
        // Update product image
        if (Tools::getValue('actionIdForUpload')) {
            $actionIdForUpload = Tools::getValue('actionIdForUpload'); // it will be Product Id OR Seller Id
            $finalData = WkBookingProductInformation::uploadImage($_FILES, $actionIdForUpload);
            echo json_encode($finalData);
        }
        exit; // ajax close
    }

    public function ajaxProcessChangeImageCover()
    {
        $idImage = Tools::getValue('id_image');
        if ($idImage) {
            $idProduct = Tools::getValue('id_pro');
            Image::deleteCover((int) $idProduct);
            $image = new Image((int) $idImage);
            $image->cover = true;
            // unlink existing cover image in temp folder
            @unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $image->id_product);
            @unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $image->id_product . '_' . $this->context->shop->id);
            if ($image->update()) {
                exit('1');
            } else {
                exit('0');
            }
        } else {
            exit('0');
        }
    }

    public function ajaxProcessDeleteimage()
    {
        // Delete product image
        if (Tools::getValue('actionpage') == 'product') {
            $idImage = Tools::getValue('id_image');
            $image = new Image($idImage);
            $status = $image->delete();
            Product::cleanPositions($idImage);
            if ($status) {
                exit('1');
            } else {
                exit('0');
            }
        }
        exit; // ajax close
    }

    public function ajaxProcessDeleteProductImage()
    {
        $idImage = Tools::getValue('id_image');
        $idProduct = Tools::getValue('id_pro');
        $image = new Image($idImage);
        $status = $image->delete();
        Product::cleanPositions($idImage);
        if ($status) {
            // if cover image deleting, make first image as a cover
            $isCover = Tools::getValue('is_cover');
            if ($isCover) {
                $images = Image::getImages($this->context->language->id, $idProduct);
                if ($images) {
                    $objImage = new Image($images[0]['id_image']);
                    $objImage->cover = true;
                    $objImage->save();
                }
                exit('2'); // if cover image deleted
            } else {
                exit('1');
            }
        } else {
            exit('0');
        }
    }

    public function ajaxProcessProductCategory()
    {
        // Load Prestashop category with ajax load of plugin jstree
        WkBookingProductInformation::getWkBookingProductCategory();
    }

    public function ajaxProcessDeleteEventBanner()
    {
        $idProduct = Tools::getValue('id_product');
        $result = [];
        if ($idProduct) {
            $idShop = $this->context->shop->id;
            $path = _PS_MODULE_DIR_ . 'psbooking/views/img/event/' . $idProduct . '/' . $idProduct . '_' . $idShop . 'banner.jpg';
            if (file_exists($path)) {
                unlink($path);
                $result['status'] = 'success';
            } else {
                $result['status'] = 'failed';
                $result['errors'] = $this->module->l('Banner image does not exist.', 'AdminBookingProductController');
            }
        } else {
            $result['status'] = 'failed';
            $result['errors'] = $this->module->l('Product id is not passed.', 'AdminBookingProductController');
        }
        exit(json_encode($result));
    }

    public function ajaxProcessGetDateRangeAvailableBookingSlots()
    {
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        $idProduct = Tools::getValue('id_product');
        $result = [];
        if (!$dateFrom) {
            $this->errors[] = $this->module->l('Invalid date from.', 'AdminBookingProductController');
        }
        if (!$dateTo) {
            $this->errors[] = $this->module->l('Invalid date to.', 'AdminBookingProductController');
        } elseif (strtotime($dateTo) < strtotime($dateFrom)) {
            $this->errors[] = $this->module->l('Date to must be date after date from.', 'AdminBookingProductController');
        }
        if (!$idProduct) {
            $this->errors[] = $this->module->l('Product Id not found.', 'AdminBookingProductController');
        }
        if (!count($this->errors)) {
            $objBookingSlots = new WkBookingProductTimeSlotPrices();
            $slotsInDateFrom = $objBookingSlots->getProductTimeSlotsOnDate($idProduct, $dateFrom, false, 2, false);
            if (empty($slotsInDateFrom)) {
                $slotsInDateFrom = $objBookingSlots->getProductTimeSlotsOnDate($idProduct, $dateFrom, false, 2, true);
            }
            $slotsInDateTo = $objBookingSlots->getProductTimeSlotsOnDate($idProduct, $dateTo, false, 2, false);
            if (empty($slotsInDateTo)) {
                $slotsInDateTo = $objBookingSlots->getProductTimeSlotsOnDate($idProduct, $dateTo, false, 2, true);
            }
            if ($slotsInDateFrom && $slotsInDateTo) {
                if ($slotsInDateTo[0]['id'] == $slotsInDateFrom[0]['id']) {
                    $result['status'] = 'success';
                    $result['slots'] = $slotsInDateFrom;
                } else {
                    $result['status'] = 'success';
                    $result['slots'] = 'all';
                }
            } elseif ($slotsInDateFrom || $slotsInDateTo) {
                $result['status'] = 'success';
                $result['slots'] = 'all';
            } else {
                $result['status'] = 'success';
                $result['slots'] = 'no_slot';
            }
        } else {
            $result['status'] = 'failed';
            $result['errors'] = $this->errors;
        }
        exit(json_encode($result));
    }

    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $object = $this->loadObject();
            if ($object->id) {
                /** @var WkBookingProductInformation $object */
                $objProduct = new Product($object->id_product);
                if (!$objProduct->delete()) {
                    $this->errors[] = $this->module->l('An error occurred while deleting product.', 'AdminBookingProductController');
                }
            }
        } else {
            $this->errors[] = $this->module->l('An error occurred while deleting object.', 'AdminBookingProductController') .
                ' ' . $this->table . ' ' . $this->module->l('(can not load object)', 'AdminBookingProductController');
        }

        return parent::processDelete();
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $value) {
                $bookingProductInfo = new WkBookingProductInformation($value);
                $objProduct = new Product($bookingProductInfo->id_product);
                if (!$objProduct->delete()) {
                    $this->errors[] = $this->module->l('An error occurred while deleting product.', 'AdminBookingProductController');
                }
            }

            return parent::processBulkDelete();
        } else {
            $this->errors[] = $this->module->l('You must select at least one element to delete.', 'AdminBookingProductController');

            return false;
        }
    }

    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            /** @var WkBookingProductInformation $object */
            if ($object->id) {
                $objProduct = new Product($object->id_product);
                $objProduct->toggleStatus();
            }
        } else {
            $this->errors[] = $this->module->l('An error occurred while updating status for an object.', 'AdminBookingProductController') .
            ' ' . $this->table . ' ' . $this->module->l('(can not load object)', 'AdminBookingProductController');
        }

        return parent::processStatus();
    }

    protected function processBulkEnableSelection()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $this->processBulkStatusSelection(true);
        } else {
            $this->errors[] = $this->module->l('You must select at least one element to enable.', 'AdminBookingProductController');
        }

        return parent::processBulkEnableSelection();
    }

    protected function processBulkDisableSelection()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            return $this->processBulkStatusSelection(false);
        } else {
            $this->errors[] = $this->module->l('You must select at least one element to disable.', 'AdminBookingProductController');
        }

        return false;
    }

    protected function processBulkStatusSelection($status)
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $bookingProductInfo = new WkBookingProductInformation($id);
                $objProduct = new Product($bookingProductInfo->id_product);
                $objProduct->active = $status;
                $isUpdated = (bool) $objProduct->update();
                $result &= $isUpdated;
                if (!$isUpdated) {
                    $this->errors[] = sprintf(
                        $this->module->l('Can\'t update %d status.', 'AdminBookingProductController'),
                        (int) $id,
                    );
                }
            }
            if ($result) {
                $this->redirect_after = self::$currentIndex . '&conf=5&token=' . $this->token;
            } else {
                $this->errors[] = $this->module->l('An error occurred while updating the status.', 'AdminBookingProductController');
            }
        }

        return $result;
    }

    protected function getFileUploadSize()
    {
        $maxUpload = (int) ini_get('upload_max_filesize');
        $maxPost = (int) ini_get('post_max_size');
        $uploadMb = min($maxUpload, $maxPost);
        if ($uploadMb < 10) {
            return $uploadMb;
        }

        return 10;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $uploadMb = $this->getFileUploadSize();
        Media::addJsDef([
            'upload_file_error' => str_replace(
                '%upload_mb%',
                $uploadMb,
                $this->module->l('File exceeds size allowed by the server. The limit is set to %upload_mb% MB.', 'AdminBookingProductController'),
            ),
            'time_slot_type_date' => WkBookingProductTimeSlotPrices::TIME_SLOT_TYPE_DATE,
            'wk_date_range_type' => WkBookingProductInformation::TYPE_DATE_RANGE,
            'wk_time_slot_type' => WkBookingProductInformation::TYPE_TIME_SLOT,
            'wk_event_type' => WkBookingProductInformation::TYPE_EVENT,
            'wk_total_available_qty' => $this->module->l('Total available qty', 'AdminBookingProductController'),
            'wk_total_booked_qty' => $this->module->l('Total booked qty', 'AdminBookingProductController'),
            'wk_price' => $this->module->l('Price', 'AdminBookingProductController'),
            'wk_sun' => $this->module->l('Sun', 'AdminBookingProductController'),
            'wk_mon' => $this->module->l('Mon', 'AdminBookingProductController'),
            'wk_tue' => $this->module->l('Tue', 'AdminBookingProductController'),
            'wk_wed' => $this->module->l('Wed', 'AdminBookingProductController'),
            'wk_thu' => $this->module->l('Thu', 'AdminBookingProductController'),
            'wk_fri' => $this->module->l('Fri', 'AdminBookingProductController'),
            'wk_sat' => $this->module->l('Sat', 'AdminBookingProductController'),
            'wk_drag_drop_img' => $this->module->l('Drag & drop to upload', 'AdminBookingProductController'),
            'wk_or_img' => $this->module->l('or', 'AdminBookingProductController'),
            'wk_pick_img' => $this->module->l('Pick image', 'AdminBookingProductController'),
            'wk_success_txt' => $this->module->l('Success', 'AdminBookingProductController'),
            'img_dir_l' => _PS_IMG_ . 'l/',
        ]);
        // tinymce
        $this->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_ . 'tinymce.inc.js');
        }
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/bookingProduct.js');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/bookingProduct.css');
        $this->addjQueryPlugin('tagify', null, false);
        if ($this->display == 'edit') {
            $geo_api_key = Configuration::get('WK_BOOKING_GEOLOCATION_API_KEY');
            if ($geo_api_key) {
                $this->addJS("https://maps.googleapis.com/maps/api/js?key=$geo_api_key&loading=async&libraries=places");
                Media::addJSDef(
                    [
                        'apiKey' => $geo_api_key,
                    ],
                );
            }
            // Upload images
            $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/uploadimage-css/jquery.filer.css');
            $this->addCSS(
                _MODULE_DIR_ . $this->module->name . '/views/css/uploadimage-css/jquery.filer-dragdropbox-theme.css',
            );
            $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/uploadimage-css/uploadphoto.css');
            $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/uploadimage-js/jquery.filer.js');
            $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/uploadimage-js/uploadimage.js');
            $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/imageedit.js');
            $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/datepickerCustom.css');
        }
    }
}
