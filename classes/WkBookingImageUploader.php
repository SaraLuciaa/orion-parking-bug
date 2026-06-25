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

class WkBookingImageUploader
{
    public $m;
    public $cache_data;
    public $cache_download_content;
    protected $options = [
        'limit' => null,
        'maxSize' => null,
        'extensions' => null,
        'required' => false,
        'uploadDir' => 'image_path',
        'title' => ['auto', 10],
        'removeFiles' => true,
        'perms' => null,
        'replace' => true,
        'onCheck' => null,
        'onError' => null,
        'onSuccess' => null,
        'onUpload' => null,
        'onComplete' => null,
        'onRemove' => null,
    ];

    private $field = [];
    private $data = [
        'hasErrors' => false,
        'hasWarnings' => false,
        'isSuccess' => false,
        'isComplete' => false,
        'data' => [
            'files' => [],
            'metas' => [],
        ],
    ];

    public function __construct()
    {
        // __construct function
        $this->m = Module::getInstanceByName('psbooking');
        $this->cache_data = $this->data;
    }

    private function getErrorMsg($key)
    {
        $errorMessages = [
            1 => $this->m->l('Uploaded file exceeds upload_max_filesize in php.ini', 'WkBookingImageUploader'),
            2 => $this->m->l('Uploaded file exceeds MAX_FILE_SIZE directive', 'WkBookingImageUploader'),
            3 => $this->m->l('Uploaded file was only partially uploaded.', 'WkBookingImageUploader'),
            4 => $this->m->l('No file was uploaded.', 'WkBookingImageUploader'),
            6 => $this->m->l('Missing a temporary folder.', 'WkBookingImageUploader'),
            7 => $this->m->l('Failed to write file to disk.', 'WkBookingImageUploader'),
            8 => $this->m->l('A PHP extension stopped the file upload.', 'WkBookingImageUploader'),
            'accept_file_types' => $this->m->l('Filetype not allowed', 'WkBookingImageUploader'),
            'file_uploads' => $this->m->l('File uploading option disabled in php.ini', 'WkBookingImageUploader'),
            'post_max_size' => $this->m->l('Uploaded file exceeds post_max_size in php.ini', 'WkBookingImageUploader'),
            'max_file_size' => $this->m->l('File is too big', 'WkBookingImageUploader'),
            'max_number_of_files' => $this->m->l('Maximum number of files exceeded', 'WkBookingImageUploader'),
            'required_and_no_file' => $this->m->l('No file was choosed. Please select one.', 'WkBookingImageUploader'),
            'no_download_content' => $this->m->l('File could not be download.', 'WkBookingImageUploader'),
        ];
        if (!empty($key)) {
            return $errorMessages[$key];
        }

        return $errorMessages;
    }

    /**
     * upload method.
     * Return the initialize method
     *
     * @param $field {Array, String}
     * @param $options {Array, null}
     *
     * @return array
     */
    public function upload($field, $options = null)
    {
        $this->data = $this->cache_data;

        return $this->initialize($field, $options);
    }

    /**
     * initialize method.
     * Initialize field values and properties.
     * Merge options
     * Prepare files
     *
     * @param $field {Array, String}
     * @param $options {Array, null}
     */
    private function initialize($field, $options)
    {
        if (is_array($field) && in_array($field, $_FILES)) {
            $this->field = $field;
            $this->field['Field_Name'] = array_search($field, $_FILES);
            $this->field['Field_Type'] = 'input';

            if (!is_array($this->field['name'])) {
                $this->field = array_merge(
                    $this->field,
                    ['name' => [
                        $this->field['name'], ],
                        'tmp_name' => [$this->field['tmp_name']],
                        'type' => [$this->field['type']],
                        'error' => [$this->field['error']],
                        'size' => [$this->field['size']],
                    ],
                );
            }

            foreach ($this->field['name'] as $key => $value) {
                if (empty($value)) {
                    unset($this->field['name'][$key]);
                    unset($this->field['type'][$key]);
                    unset($this->field['tmp_name'][$key]);
                    unset($this->field['error'][$key]);
                    unset($this->field['size'][$key]);
                }
            }

            $this->field['length'] = count($this->field['name']);
        } elseif (is_string($field) && $this->isURL($field)) {
            $this->field = ['name' => [$field], 'size' => [], 'type' => [], 'error' => []];
            $this->field['Field_Type'] = 'link';
            $this->field['length'] = 1;
        } else {
            return [];
        }

        if ($options != null) {
            $this->setOptions($options);
        }

        return $this->prepareFiles();
    }

    /**
     * setOptions method.
     * Merge options
     *
     * @param $options {Array}
     */
    private function setOptions($options)
    {
        if (!is_array($options)) {
            return false;
        }
        $this->options = array_merge($this->options, $options);
    }

    /**
     * validation method.
     * Check the field and files
     *
     * @return bool
     */
    private function validate($file = null)
    {
        $field = $this->field;
        $errors = [];
        $options = $this->options;

        if ($file == null) {
            $ini = [
                ini_get('file_uploads'),
                (int) ini_get('upload_max_filesize'),
                (int) ini_get('post_max_size'),
                (int) ini_get('max_file_uploads'),
            ];

            if (!isset($field) || empty($field)) {
                return false;
            }
            if (!$ini[0]) {
                $errors[] = $this->getErrorMsg('file_uploads');
            }

            if ($options['required'] && $field['length'] == 0) {
                $errors[] = $this->getErrorMsg('required_and_no_file');
            }
            if (($options['limit'] && $field['length'] > $options['limit']) || $field['length'] > $ini[3]) {
                $errors[] = $this->getErrorMsg('max_number_of_files');
            }
            if ($field['Field_Type'] == 'input') {
                $totalSize = 0;
                foreach ($this->field['size'] as $value) {
                    $totalSize += $value;
                }
                $totalSize = $totalSize / 1048576;
                if ($options['maxSize'] && $totalSize > $options['maxSize']) {
                    $errors[] = $this->getErrorMsg('max_file_size');
                }

                if ($ini[1] != 0 && $totalSize > $ini[1]) {
                    $errors[] = $this->getErrorMsg(1);
                }
                if ($ini[2] != 0 && $totalSize > $ini[2]) {
                    $errors[] = $this->getErrorMsg('post_max_size');
                }
            }
        } else {
            if (@$field['error'][$file['index']] > 0
                && array_key_exists($field['error'][$file['index']], $this->getErrorMsg(false))
            ) {
                $errors[] = $this->getErrorMsg($field['error'][$file['index']]);
            }
            if ($options['extensions'] && !in_array(Tools::strtolower($file['extension']), $options['extensions'])) {
                $errors[] = $this->getErrorMsg('accept_file_types');
            }
            if ($file['type'][0] == 'image' && @!is_array(getimagesize($file['tmp']))) {
                $errors[] = $this->getErrorMsg('accept_file_types');
            }
            if (is_array($file['size'])) {
                $totalSize = $file['size'][0] / 1048576;
            } else {
                $totalSize = $file['size'] / 1048576;
            }
            if ($options['maxSize'] && $totalSize > $options['maxSize']) {
                $errors[] = $this->getErrorMsg('max_file_size');
            }
            if ($field['Field_Type'] == 'link' && empty($this->cache_download_content)) {
                $errors[] = '';
            }
        }

        $custom = $this->onCheck($file);
        if ($custom) {
            $errors = array_merge($errors, $custom);
        }

        if (!empty($errors)) {
            $this->data['hasErrors'] = true;
            if (!isset($this->data['errors'])) {
                $this->data['errors'] = [];
            }

            $this->data['errors'][] = $errors;
            $custom = $this->onError($errors, $file);

            return false;
        } else {
            return true;
        }
    }

    /**
     * prepareFiles method.
     * Prepare files for upload/download and generate meta infos
     */
    private function prepareFiles()
    {
        $field = $this->field;
        $validate = $this->validate();

        if ($validate) {
            $files = [];
            $removedFiles = $this->removeFiles();
            $isAddMoreMode = count(preg_grep('/^(\d+)\:\/\/(.*)/i', $removedFiles)) > 0;
            $addMoreMatches = [];

            for ($i = 0; $i < count($field['name']); ++$i) {
                $metas = [];
                if ($field['Field_Type'] == 'input') {
                    $tmpName = $field['tmp_name'][$i];
                } elseif ($field['Field_Type'] == 'link') {
                    $link = $this->downloadFile($field['name'][0], false, true);

                    $tmpName = $field['name'][0];
                    $field['name'][0] = pathinfo($field['name'][0], PATHINFO_BASENAME);
                    $field['type'][0] = $link['type'];
                    $field['size'][0] = $link['size'];
                    $field['error'][0] = 0;
                } else {
                    $tmpName = '';
                }

                $metas['extension'] = Tools::substr(strrchr($field['name'][$i], '.'), 1);
                $metas['type'] = preg_split('[/]', $field['type'][$i]);
                $metas['extension'] = ($field['Field_Type'] == 'link' && empty($metas['extension']))
                ? $metas['type'][1] : $metas['extension'];
                $metas['old_name'] = $field['name'][$i];
                $metas['size'] = $field['size'][$i];
                $metas['size2'] = $this->formatSize($metas['size']);

                $metas['name'] = Tools::passwdGen(6) . '.' . $metas['extension'];

                $metas['file'] = $this->options['uploadDir'] . $metas['name'];
                $metas['replaced'] = file_exists($metas['file']);
                $metas['date'] = date('r');

                $isFileRemoved = in_array($field['name'][$i], $removedFiles);
                if ($isAddMoreMode) {
                    $addMoreMatches[$field['name'][$i]][] = $i;
                    $matches = preg_grep(
                        '/^' . (count($addMoreMatches[$field['name'][$i]]) - 1) . '\:\/\/' . $field['name'][$i] . '/i',
                        $removedFiles,
                    );
                    if (count($matches) == 1) {
                        $isFileRemoved = true;
                    }
                }

                if (!$isFileRemoved
                    && $this->validate(array_merge($metas, ['index' => $i, 'tmp' => $tmpName]))
                    && ($idImage = $this->uploadFile($tmpName, $metas['file']))
                ) {
                    if ($this->options['perms']) {
                        @chmod($metas['file'], $this->options['perms']);
                    }

                    $custom = $this->onUpload($metas, $this->field);
                    if ($custom && is_array($custom)) {
                        $metas = array_merge($custom, $metas);
                    }

                    ksort($metas);

                    $files[] = $metas['file'];
                    $this->data['data']['metas'][] = $metas;
                    $this->data['data']['id_image'] = $idImage;
                }
            }

            $this->data['isSuccess'] = count($field['name']) - count($removedFiles) == count($files);
            $this->data['data']['files'] = $files;

            if ($this->data['isSuccess']) {
                $custom = $this->onSuccess($this->data['data']['files'], $this->data['data']['metas']);
            }

            $this->data['isComplete'] = true;
            $custom = $this->onComplete($this->data['data']['files'], $this->data['data']['metas']);
        }

        return $this->data;
    }

    /**
     * uploadFile method.
     * Upload/Download files to server
     *
     * @return bool
     */
    private function uploadFile($source, $destination)
    {
        if ($this->field['Field_Type'] == 'input') {
            return WkBookingProductInformation::updatePsProductImage($this->options['id_product'], $source);
        } elseif ($this->field['Field_Type'] == 'link') {
            return $this->downloadFile($source, $destination);
        }

        return false;
    }

    /**
     * removeFiles method.
     * Remove files or cancel upload for them
     *
     * @return array
     */
    private function removeFiles()
    {
        $removedFiles = [];
        if ($this->options['removeFiles'] !== false) {
            $files = array_keys($_POST);
            foreach ($files as $value) {
                preg_match(
                    is_string($this->options['removeFiles']) ?
                    $this->options['removeFiles'] : '/jfiler-items-exclude-' . $this->field['Field_Name'] . '-(\d+)/',
                    $value,
                    $matches,
                );
                if (!empty($matches)) {
                    $input = Tools::getValue($matches[0]);
                    if ($this->isJson($input)) {
                        $removedFiles = json_decode($input, true);
                    }

                    $custom = $this->onRemove($removedFiles, $this->field);
                    if ($custom && is_array($custom)) {
                        $removedFiles = $custom;
                    }
                }
            }
        }

        return $removedFiles;
    }

    /**
     * downloadFile method.
     * Download file to server
     */
    private function downloadFile($source, $destination, $info = false)
    {
        set_time_limit(80);

        $forInfo = [
            'size' => 1,
            'type' => 'text/plain',
        ];

        $httpResponseHeader = null;
        if (!isset($this->cache_download_content)) {
            $fileContent = Tools::file_get_contents($source);
            if ($info) {
                $headers = implode(' ', $httpResponseHeader);
                if (preg_match('/Content-Length: (\d+)/', $headers, $matches)) {
                    $forInfo['size'] = $matches[1];
                }
                if (preg_match('/Content-Type: (\w+\/\w+)/', $headers, $matches)) {
                    $forInfo['type'] = $matches[1];
                }

                $this->cache_download_content = $fileContent;

                return $forInfo;
            }
        } else {
            $fileContent = $this->cache_download_content;
        }

        $downloadedFile = @fopen($destination, 'w');
        $written = @fwrite($downloadedFile, $fileContent);
        @fclose($downloadedFile);

        return $written;
    }

    /**
     * isJson method.
     * Check if string is a valid json
     *
     * @return bool
     */
    private function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * isURL method.
     * Check if string $url is a link
     *
     * @return bool
     */
    private function isURL($url)
    {
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }

    /**
     * formatSize method.
     * Convert file size
     */
    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes > 0) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    private function onCheck()
    {
        $arguments = func_get_args();

        return $this->options['onCheck'] != null && function_exists($this->options['onCheck']) ?
        $this->options['onCheck'](@$arguments[0]) : null;
    }

    private function onSuccess()
    {
        $arguments = func_get_args();

        if (is_callable($this->options['onSuccess'])) {
            return call_user_func($this->options['onSuccess'], @$arguments[0], @$arguments[1]);
        }

        return null;
    }

    private function onError()
    {
        $arguments = func_get_args();

        return ($this->options['onError'] && function_exists($this->options['onError'])) ?
         $this->options['onError'](@$arguments[0], @$arguments[1]) : null;
    }

    private function onUpload()
    {
        $arguments = func_get_args();

        return ($this->options['onUpload'] && function_exists($this->options['onUpload'])) ?
        $this->options['onUpload'](@$arguments[0], @$arguments[1]) : null;
    }

    private function onComplete()
    {
        $arguments = func_get_args();

        return ($this->options['onComplete'] != null && function_exists($this->options['onComplete'])) ?
        $this->options['onComplete'](@$arguments[0], @$arguments[1]) : null;
    }

    private function onRemove()
    {
        $arguments = func_get_args();

        return ($this->options['onRemove'] && function_exists($this->options['onRemove'])) ?
         $this->options['onRemove'](@$arguments[0], @$arguments[1]) : null;
    }
}
