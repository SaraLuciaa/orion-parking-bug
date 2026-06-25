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

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class WkBookingProductInformation extends ObjectModel
{
    public $id_product;
    public $quantity;
    public $booking_before;
    public $min_days_booking;
    public $max_days_booking;
    public $booking_type;
    public $active;
    public $show_map;
    public $address;
    public $latitude;
    public $longitude;
    public $date_add;
    public $date_upd;

    public const TYPE_DATE_RANGE = 1;
    public const TYPE_TIME_SLOT = 2;
    public const TYPE_EVENT = 3;
    public const TYPE_RENTAL = 4;

    public static $definition = [
        'table' => 'wk_booking_product_info',
        'primary' => 'id',
        'fields' => [
            'id_product' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
                'shop' => true,
            ],
            'quantity' => ['type' => self::TYPE_INT, 'shop' => true],
            'booking_before' => ['type' => self::TYPE_INT, 'shop' => true],
            'min_days_booking' => ['type' => self::TYPE_INT, 'shop' => true],
            'max_days_booking' => ['type' => self::TYPE_INT, 'shop' => true],
            'booking_type' => ['type' => self::TYPE_INT, 'required' => true, 'shop' => true],
            'active' => ['type' => self::TYPE_INT, 'shop' => true],
            'show_map' => ['type' => self::TYPE_INT, 'shop' => true],
            'address' => [
                'type' => self::TYPE_STRING, 'shop' => true,
            ],
            'latitude' => ['type' => self::TYPE_FLOAT, 'shop' => true],
            'longitude' => ['type' => self::TYPE_FLOAT, 'shop' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'shop' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'shop' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation('wk_booking_product_info', ['type' => 'shop', 'primary' => 'id']);
    }

    public static function getBookingTypes()
    {
        $m = Module::getInstanceByName('psbooking');

        return [
            WkBookingProductInformation::TYPE_DATE_RANGE => $m->l('Date range', 'WkBookingProductInformation'),
            WkBookingProductInformation::TYPE_TIME_SLOT => $m->l('Time slots', 'WkBookingProductInformation'),
            WkBookingProductInformation::TYPE_EVENT => $m->l('Event', 'WkBookingProductInformation'),
            WkBookingProductInformation::TYPE_RENTAL => $m->l('Rental', 'WkBookingProductInformation'),
        ];
    }

    public function getBookingProductInfoByIdProduct($idProduct, $active = false, $addShop = true)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_info` wbpi';
        if ($addShop) {
            $sql .= ' INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_info_shop` wbpis ON (wbpis.`id` = wbpi.`id` AND wbpis.`id_shop` = ' . (int) Context::getContext()->shop->id . ')';
        } else {
            $sql .= ' INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_info_shop` wbpis ON (wbpis.`id` = wbpi.`id`)';
        }
        $sql .= ' WHERE wbpis.`id_product` = ' . (int) $idProduct;
        if ($active) {
            $sql .= ' AND wbpis.`active` = 1';
        }

        return Db::getInstance()->getRow(
            $sql,
        );
    }

    /**
     * Upload seller product imgage, profile image and shop image.
     */
    public static function uploadImage($files, $actionIdForUpload)
    {
        $imageFiles = isset($files['productimages']) ? $files['productimages'] : '';
        $uploader = new WkBookingImageUploader();
        $data = $uploader->upload(
            $imageFiles,
            [
                'limit' => 10, // Maximum Limit of files. {null, Number}
                'maxSize' => 10, // Maximum Size of files {null, Number(in MB's)}
                'id_product' => $actionIdForUpload,
                // Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
                'extensions' => ['jpg', 'png', 'gif', 'jpeg'],
                'required' => false, // Minimum one file is required for upload {Boolean}
                'title' => ['name'], // New file name {null, String, Array} *please read documentation in README.md
            ],
        );

        $finalData = [];
        if ($data['hasErrors']) {
            $finalData['status'] = 'fail';
            $finalData['file_name'] = '';
            $finalData['error_message'] = $data['errors'][0];
        } elseif ($data['isComplete']) {
            if ($data['data']['metas'][0]['name']) {
                $finalData['status'] = 'success';
                $finalData['file_name'] = $data['data']['metas'][0]['name'];
                $finalData['id_image'] = $data['data']['id_image'];
                $finalData['error_message'] = '';
            }
        }

        return $finalData;
    }

    public static function updatePsProductImage($idProduct, $source)
    {
        $haveCover = false;
        $images = Image::getImages(Context::getContext()->language->id, $idProduct);
        if ($images) {
            foreach ($images as $img) {
                if ($img['cover'] == 1) {
                    $haveCover = true;
                }
            }
        }

        $objImage = new Image();
        $objImage->id_product = $idProduct;
        $objImage->position = Image::getHighestPosition($idProduct) + 1;

        if (!$haveCover) {
            $objImage->cover = true;
        } else {
            $objImage->cover = false;
        }

        if ($objImage->add()) {
            $imageId = $objImage->id;

            $newPath = $objImage->getPathForCreation();
            $imagesTypes = ImageType::getImagesTypes('products');

            if ($imagesTypes) {
                foreach ($imagesTypes as $imageType) {
                    ImageManager::resize(
                        $source,
                        $newPath . '-' . stripslashes($imageType['name']) . '.' . $objImage->image_format,
                        $imageType['width'],
                        $imageType['height'],
                        $objImage->image_format,
                    );
                }
            }

            ImageManager::resize($source, $newPath . '.' . $objImage->image_format);

            return $imageId;
        }

        return false;
    }

    // Not using any more, using getHomeCategories funrcion instead of
    public function getPsCategories($idParent, $idLang)
    {
        return Db::getInstance()->executeS(
            'SELECT a.`id_category`, a.`id_parent`, l.`name`
            FROM `' . _DB_PREFIX_ . 'category` a
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` l ON (a.`id_category` = l.`id_category`)
            WHERE a.`id_parent` = ' . (int) $idParent . '
            AND l.`id_lang` = ' . (int) $idLang . '
            AND l.`id_shop` = ' . (int) Context::getContext()->shop->id . '
            AND a.`active` = 1
            ORDER BY a.`id_category`',
        );
    }

    /**
     * Load Prestashop category with ajax load of plugin jstree.
     */
    public static function getWkBookingProductCategory()
    {
        if (!Tools::getValue('id')) {
            // Add product
            $catId = Tools::getValue('catsingleId');
            $selectedCatIds = [Category::getRootCategory()->id]; // Root Category will be automatically selected
        } else {
            // Edit product
            $catId = Tools::getValue('catsingleId');
            $selectedCatIds = explode(',', Tools::getValue('catIds'));
        }
        $objBookingProductInformation = new WkBookingProductInformation();
        $treeLoad = $objBookingProductInformation->getProductCategory(
            $catId,
            $selectedCatIds,
            Context::getContext()->language->id,
        );
        if (!empty($treeLoad)) {
            exit(json_encode($treeLoad)); // ajax close
        } else {
            exit('fail'); // ajax close
        }
    }

    /**
     * Get prestashop jstree category
     */
    public function getProductCategory($catId, $selectedCatIds, $idLang)
    {
        if ($catId == '#') {
            // First time load
            $root = Category::getRootCategory();
            $category = Category::getHomeCategories($idLang, true);
            $categoryArray = [];
            foreach ($category as $catkey => $cat) {
                $categoryArray[$catkey]['id'] = $cat['id_category'];
                $categoryArray[$catkey]['text'] = $cat['name'];
                $subcategory = $this->getPsCategories($cat['id_category'], $idLang);
                $subChildSelect = false;
                if ($subcategory) {
                    $categoryArray[$catkey]['children'] = true;

                    foreach ($subcategory as $subcat) {
                        if (in_array($subcat['id_category'], $selectedCatIds)) {
                            $subChildSelect = true;
                        }
                    }
                } else {
                    $categoryArray[$catkey]['children'] = false;
                }

                if (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $categoryArray[$catkey]['state'] = ['opened' => true, 'selected' => true];
                } elseif (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == false) {
                    $categoryArray[$catkey]['state'] = ['selected' => true];
                } elseif (!in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $categoryArray[$catkey]['state'] = ['opened' => true];
                }
            }

            $treeLoad = [];
            if (in_array($root->id_category, $selectedCatIds)) {
                $treeLoad = ['id' => $root->id_category,
                    'text' => $root->name,
                    'children' => $categoryArray,
                    'state' => ['opened' => true, 'selected' => true],
                ];
            } else {
                $treeLoad = ['id' => $root->id_category,
                    'text' => $root->name,
                    'children' => $categoryArray,
                    'state' => ['opened' => true],
                ];
            }
        } else {
            // If sub-category is selected then its automatically called
            $childcategory = $this->getPsCategories($catId, $idLang);
            $treeLoad = [];
            $singletreeLoad = [];
            foreach ($childcategory as $cat) {
                $subcategoryArray = [];
                $subcategoryArray['id'] = $cat['id_category'];
                $subcategoryArray['text'] = $cat['name'];
                $subcategory = $this->getPsCategories($cat['id_category'], $idLang);

                $subChildSelect = false;
                if ($subcategory) {
                    $subcategoryArray['children'] = true;

                    foreach ($subcategory as $subcat) {
                        if (in_array($subcat['id_category'], $selectedCatIds)) {
                            $subChildSelect = true;
                        }
                    }
                } else {
                    $subcategoryArray['children'] = false;
                }

                if (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $subcategoryArray['state'] = ['opened' => true, 'selected' => true];
                } elseif (in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == false) {
                    $subcategoryArray['state'] = ['selected' => true];
                } elseif (!in_array($cat['id_category'], $selectedCatIds) && $subChildSelect == true) {
                    $subcategoryArray['state'] = ['opened' => true];
                }

                $singletreeLoad[] = $subcategoryArray;
            }

            $treeLoad = $singletreeLoad;
        }

        return $treeLoad;
    }

    public function isBookingProduct($idProduct)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_info` wbpi
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_info` wbpis ON (wbpis.`id` = wbpi.`id`
            AND wbpis.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbpis.`id_product` = ' . (int) $idProduct,
        );
    }

    public static function getAppliedProductTaxRate($idProduct)
    {
        $productPriceTI = Product::getPriceStatic((int) $idProduct, true);
        $productPriceTE = Product::getPriceStatic((int) $idProduct, false);
        $taxRate = 0;
        if ($productPriceTE) {
            $tax = $productPriceTI - $productPriceTE;
            $taxRate = ($tax / $productPriceTE) * 100;
        }

        return $taxRate;
    }

    /**
     * Admin feature price product search
     *
     * @param int $idLang Language id
     * @param string $query Search query
     */
    public static function searchBookingProductByName($idLang, $query, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $sql = new DbQuery();
        $sql->select(
            'p.`id_product`, pl.`name`, p.`ean13`, p.`upc`, p.`active`, p.`reference`, m.`name` AS manufacturer_name,
            stock.`quantity`, product_shop.advanced_stock_management, bp.`booking_type`',
        );
        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->innerJoin('wk_booking_product_info', 'bp', 'bp.`id_product` = p.`id_product`');
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl'),
        );
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $where = 'pl.`name` LIKE \'%' . pSQL($query) . '%\'';
        $sql->where($where);
        $sql->join(Product::sqlStock('p', 0));

        $result = Db::getInstance()->executeS($sql);

        if (!$result) {
            return false;
        }

        $results_array = [];
        foreach ($result as $row) {
            $idDefaultCombination = Product::getDefaultAttribute($row['id_product']);
            if (Combination::isFeatureActive() && $idDefaultCombination) {
                $sql = 'SELECT pa.`id_product_attribute`, ag.`id_attribute_group`, agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                    ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                    ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
                    ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag
                    ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
                    ON (a.`id_attribute` = al.`id_attribute`
                        AND al.`id_lang` = ' . (int) $context->language->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
                    ON (ag.`id_attribute_group` = agl.`id_attribute_group`
                        AND agl.`id_lang` = ' . (int) $context->language->id . ')
                WHERE pa.`id_product` = ' . (int) $row['id_product'] . '
                GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                ORDER BY pa.`id_product_attribute`';
                $combinations = Db::getInstance()->executeS($sql);
                if (!empty($combinations)) {
                    $variants = [];
                    foreach ($combinations as $combination) {
                        $variants[$combination['id_product_attribute']]['id_product'] = $row['id_product'];
                        $variants[$combination['id_product_attribute']]['id_product_attribute']
                            = $combination['id_product_attribute'];
                        if (!empty($variants[$combination['id_product_attribute']]['name'])) {
                            $variants[$combination['id_product_attribute']]['name']
                            .= ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                        } else {
                            $variants[$combination['id_product_attribute']]['name']
                            = $combination['group_name'] . '-' . $combination['attribute_name'];
                        }
                    }
                    $variants = array_values($variants);
                    if (!empty($variants)) {
                        $allCombIds = array_column($variants, 'id_product_attribute');
                        if (!in_array($idDefaultCombination, $allCombIds)) {
                            $idDefaultCombination = $allCombIds[0];
                        }
                        $product = $row;
                        foreach ($variants as $variant) {
                            $product['id_product_attribute'] = $variant['id_product_attribute'];
                            $product['name'] = $row['name'] . ' ' . $variant['name'];
                            $product['price_tax_incl'] = Product::getPriceStatic($row['id_product'], true, $variant['id_product_attribute'], 2);
                            $product['price_tax_excl'] = Product::getPriceStatic($row['id_product'], false, $variant['id_product_attribute'], 2);
                            $results_array[] = $product;
                        }
                    }
                }
            } else {
                if ($row['booking_type'] == WkBookingProductInformation::TYPE_EVENT) {
                    $row['single_event'] = !WkBookingProductExtraInfo::isMultiSlotEnable($row['id_product']);
                }
                $row['price_tax_incl'] = Product::getPriceStatic($row['id_product'], true, null, 2);
                $row['price_tax_excl'] = Product::getPriceStatic($row['id_product'], false, null, 2);
                $row['id_product_attribute'] = 0;
                $results_array[] = $row;
            }
        }

        return $results_array;
    }

    public function deleteBookingProductByIdProduct($idProduct)
    {
        $bookingProducts = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_booking_product_info` wbpi
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_info_shop` wbpis ON (wbpis.`id` = wbpi.`id`
            AND wbpis.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wbpis.`id_product` = ' . (int) $idProduct,
        );
        if (!empty($bookingProducts)) {
            foreach ($bookingProducts as $bookingProduct) {
                $bookingProductObj = new WkBookingProductInformation((int) $bookingProduct['id']);
                if (!$bookingProductObj->delete()) {
                    return false;
                }
            }
        }
    }

    public static function getBookingProduct(
        $type = false,
        $idLang = false,
        $orderby = false,
        $orderway = false,
        $start_point = 0,
        $limit_point = 10000000
    ) {
        if (!$idLang) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        if (!$orderway) {
            $orderway = 'desc';
        }
        $context = Context::getContext();
        $sql = 'SELECT wbpis.`id_product`,  wbpis.`booking_type`, wbpis.`quantity`, wbpis.`booking_before`
        FROM `' . _DB_PREFIX_ . 'wk_booking_product_info` wbpi
        INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_info_shop` wbpis ON (wbpis.`id` = wbpi.`id`
        AND wbpis.`id_shop` = ' . (int) $context->shop->id . ')
        INNER JOIN (SELECT p.`id_product`, p.`price`, pl.`name` FROM `' . _DB_PREFIX_ . 'product` p
        INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product`
        AND pl.`id_shop` = ' . (int) $context->shop->id . ' AND pl.`id_lang` = ' . (int) $context->language->id . ')
        INNER JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON (p.`id_product` = ps.`id_product`
        AND ps.`id_shop` = ' . (int) $context->shop->id . ' AND ps.`active` = TRUE)
        WHERE ps.`id_shop` = ' . (int) $context->shop->id . '
        GROUP BY p.`id_product`) p ON (wbpi.`id_product` = p.`id_product`)
        WHERE wbpis.`active` = 1';

        if ($type) {
            $sql .= ' AND wbpi.`booking_type` = ' . (int) $type;
        }

        if (!$orderby) {
            $sql .= ' ORDER BY p.`id_product` ' . pSQL($orderway);
        } else {
            $sql .= ' ORDER BY p.`' . $orderby . '` ' . pSQL($orderway);
        }
        $sql .= ' LIMIT ' . $start_point . ', ' . $limit_point;

        $bookingProducts = Db::getInstance()->executeS($sql);

        if (!empty($bookingProducts)) {
            return $bookingProducts;
        }

        return false;
    }

    public static function getProducts($products = false)
    {
        $assembler = new ProductAssembler(Context::getContext());

        $presenterFactory = new ProductPresenterFactory(Context::getContext());
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                Context::getContext()->link,
            ),
            Context::getContext()->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            Context::getContext()->getTranslator(),
        );
        $productsForTemplate = [];
        if (is_array($products)) {
            foreach ($products as $rawProduct) {
                $productsForTemplate[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    Context::getContext()->language,
                );
            }
        }

        return $productsForTemplate;
    }

    public static function getBookingProductAddress($idProduct, $idShop)
    {
        return Db::getInstance()->getRow(
            'SELECT wbpis.`address`, wbpis.`latitude`, wbpis.`longitude` FROM `' . _DB_PREFIX_ . 'wk_booking_product_info` wbpi
            INNER JOIN `' . _DB_PREFIX_ . 'wk_booking_product_info_shop` wbpis ON (wbpis.`id` = wbpi.`id`
            AND wbpis.`id_shop` = ' . (int) $idShop . ')
            WHERE wbpis.`id_product` = ' . (int) $idProduct,
        );
    }

    public static function addSqlAssociationCustom(
        $table,
        $alias,
        $inner_join = true,
        $on = null,
        $force_not_default = false,
        $identifier = 'id'
    ) {
        $table_alias = $table . '_shop';
        if (strpos($table, '.') !== false) {
            list($table_alias, $table) = explode('.', $table);
        }

        $asso_table = Shop::getAssoTable($table);
        if ($asso_table === false || $asso_table['type'] != 'shop') {
            return;
        }
        $sql = (($inner_join) ? ' INNER' : ' LEFT') . ' JOIN ' . _DB_PREFIX_ . $table . '_shop ' . $table_alias . '
        ON (' . $table_alias . '.' . $identifier . ' = ' . $alias . '.' . $identifier;
        if ((int) Shop::getContextShopID()) {
            $sql .= ' AND ' . $table_alias . '.id_shop = ' . (int) Shop::getContextShopID();
        } elseif (Shop::checkIdShopDefault($table) && !$force_not_default) {
            $sql .= ' AND ' . $table_alias . '.id_shop = ' . $alias . '.id_shop_default';
        } else {
            $sql .= ' AND ' . $table_alias . '.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')';
        }
        $sql .= (($on) ? ' AND ' . $on : '') . ')';

        return $sql;
    }
}
