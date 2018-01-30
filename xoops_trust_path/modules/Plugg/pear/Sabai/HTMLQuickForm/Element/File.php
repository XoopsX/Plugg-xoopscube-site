<?php
require_once 'HTML/QuickForm/file.php';

class Sabai_HTMLQuickForm_Element_File extends HTML_QuickForm_file
{
    function getFileName()
    {
        $value = $this->getValue();
        return $value['name'];
    }

    function getFileSize()
    {
        $value = $this->getValue();
        return @filesize($value['tmp_name']);
    }

    function getFileExtension($noFirstDot = false)
    {
        return self::fileExtension($this->getFileName(), $noFirstDot);
    }

    function fileExtension($fileName, $noFirstDot = false)
    {
        if (!$file_ext_pos = strpos($fileName, '.')) {
            return '';
        }
        return strtolower(substr($fileName, $file_ext_pos + intval($noFirstDot)));
    }

    function checkExtensionAndMimeType($file, $extensionToMimeTypes = array())
    {
        // There must be allowed extensions defined for additional security
        if (empty($extensionToMimeTypes)) {
            return false;
        }

        // Check file extension
        if ('' == $file_ext = self::fileExtension($file['name'], true)) {
            return false;
        }
        if (!array_key_exists($file_ext , $extensionToMimeTypes)) {
            return false;
        }

        // Return true if no associated mime type for the file extension
        if (empty($extensionToMimeTypes[$file_ext])) return true;

        // Check if the file mime type corresponds with the allowed mime types for the file extension
        foreach ((array)$extensionToMimeTypes[$file_ext] as $allowed_mime_type) {
            if (is_int($allowed_mime_type)) {
                if (!isset($image_type)) {
                    $image_type = self::imageType($file['tmp_name']);
                }
                if ($image_type && $image_type == $allowed_mime_type) {
                    return true;
                }
            } else {
                if (!isset($file_mime)) {
                    $file_mime = $file['type'];
                    if (function_exists('finfo_open')) {
                        if ($finfo = @finfo_open(FILEINFO_MIME)) {
                            $file_finfo_mime = finfo_file($finfo, $file['tmp_name']);
                            finfo_close($finfo);
                            if ($file_finfo_mime) {
                                $file_mime = $file_finfo_mime;
                            }
                        }
                    }
                }
                if (preg_match('#' . str_replace('#', '\#', $allowed_mime_type) . '#', $file_mime)) {
                    return true;
                }
            }
        }
        return false;
    }

    function checkMaxImageDimension($file, $maxImageWidth = null, $maxImageHeight = null)
    {
        if (!$image_size = getimagesize($file['tmp_name'])) return false;
        if (!empty($maxImageWidth) && $image_size[0] > $maxImageWidth) return false;
        if (!empty($maxImageHeight) && $image_size[1] > $maxImageHeight) return false;
        return true;
    }

    function moveUploadedFile($dest, $mode, $prefix = '', $length = null)
    {
        switch ($mode) {
            case 'uniq':
                $name = md5(uniqid(mt_rand(), true)) . $this->getFileExtension();
                break;
            case 'safe':
                $file_name = $this->getFileName();
                $file_ext = $this->getFileExtension();
                $name = md5(basename($file_name, $file_ext)) . $file_ext;
                break;
            case 'real':
                $name = $this->getFileName();
                break;
            default:
                $name = $mode;
                break;
        }
        $name = $prefix . $name;
        if (!empty($length)) $name = $this->_truncateFileName($name, $length);
        return parent::moveUploadedFile($dest, $name) ? $name : false;
    }

    function _truncateFileName($fileName, $length)
    {
        if (strlen($fileName) <= $length) return $fileName;
        $file_ext = $this->getFileExtension();
        $file_ext_len = strlen($file_ext);
        return substr(substr($fileName, 0, -1 * $file_ext_len), 0, $length - $file_ext_len) . $file_ext;
    }

    function imageType($file)
    {
        if (function_exists('exif_imagetype')) {
            return exif_imagetype($file);
        }
        if ($image_size = @getimagesize($file)) {
            return $image_size[2];
        }
        return false;
    }

    function getImageType($file = null)
    {
        if (!isset($file)) {
            $value = $this->getValue();
            $file = $value['tmp_name'];
        }
        return self::imageType($file);
    }
}