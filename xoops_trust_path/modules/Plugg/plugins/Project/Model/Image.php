<?php
class Plugg_Project_Model_Image extends Plugg_Project_Model_Base_Image
{
    function generateThumbnails($mediaDir, $imageLibName, $imageLibIMPath = '', $imageLibNetPBM = '')
    {
        if ('' == $original_file = $this->get('original')) return false;
        $original_file_path = $mediaDir . '/' . $original_file;
        $original_file_ext = (!$file_ext_pos = strpos($original_file, '.')) ? '' : strtolower(substr($original_file, $file_ext_pos));
        require_once 'Image/Transform.php';
        foreach ((array)$imageLibName as $image_lib_name) {
            switch ($image_lib_name) {
                case 'IM':
                    if ($imageLibIMPath) define('IMAGE_TRANSFORM_IM_PATH', $imageLibIMPath);
                    $image_lib = Image_Transform::factory('IM');
                    break;
                case 'NetPBM':
                    if ($imageLibNetPBM) define('IMAGE_TRANSFORM_NETPBM_PATH', $imageLibNetPBM);
                    $image_lib = Image_Transform::factory('NetPBM');
                    break;
                case 'GD':
                default:
                    $image_lib = Image_Transform::factory('GD');
                    break;
            }
            if (PEAR::isError($image_lib)) {
                Sabai_Log::warn(sprintf('Image library %s could not be initialized. Error: %s', $image_lib_name, $image_lib->getMessage()), __FILE__, __LINE__);
                continue;
            }
            $result = $image_lib->load($original_file_path);
            if (PEAR::isError($result)) {
                Sabai_Log::warn(sprintf('Image library %s could not load image file. Error: %s', $image_lib_name, $result->getMessage()), __FILE__, __LINE__);
                continue;
            }
            $result = $image_lib->resize(100, 70);
            if (PEAR::isError($result)) {
                Sabai_Log::warn(sprintf('Image library %s could not resize image. Error: %s', $image_lib_name, $result->getMessage()), __FILE__, __LINE__);
                continue;
            }
            $file_thumbnail = sprintf('%s-thumbnail%s', basename($original_file, $original_file_ext), $original_file_ext);
            $result = $image_lib->save($mediaDir. '/' . $file_thumbnail);
            if (PEAR::isError($result)) {
                Sabai_Log::warn(sprintf('Image library %s could not save thumbnail image. Error: %s', $image_lib_name, $result->getMessage()), __FILE__, __LINE__);
                continue;
            }
            $this->set('thumbnail', $file_thumbnail);
            $image_lib->free();
            $result = $image_lib->load($original_file_path);
            if (!PEAR::isError($result)) {
                $result = $image_lib->resize(150, 105);
                if (!PEAR::isError($result)) {
                    $file_medium = sprintf('%s-medium%s', basename($original_file, $original_file_ext), $original_file_ext);
                    $result = $image_lib->save($mediaDir . '/' . $file_medium);
                    if (!PEAR::isError($result)) {
                        $this->set('medium', $file_medium);
                    } else {
                        Sabai_Log::warn(sprintf('Image library %s could not save medium image. Error: %s', $image_lib_name, $result->getMessage()), __FILE__, __LINE__);
                    }
                } else {
                    Sabai_Log::warn(sprintf('Image library %s could not resize medium image. Error: %s', $image_lib_name, $result->getMessage()), __FILE__, __LINE__);
                }
            } else {
                Sabai_Log::warn(sprintf('Image library %s could not load image file. Error: %s', $image_lib_name, $result->getMessage()), __FILE__, __LINE__);
            }
            return true;
        }
        return false;
    }
}

class Plugg_Project_Model_ImageRepository extends Plugg_Project_Model_Base_ImageRepository
{
}