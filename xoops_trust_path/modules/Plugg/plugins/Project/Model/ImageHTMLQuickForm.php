<?php
class Plugg_Project_Model_ImageHTMLQuickForm extends Plugg_Project_Model_Base_ImageHTMLQuickForm
{
    protected function _onInit(array $params)
    {
        // things that should be applied to all forms should come here (e.g., add validators)
        $extension_mimetype = array(
            'gif'  => IMAGETYPE_GIF,
            'jpg'  => IMAGETYPE_JPEG,
            'jpeg' => IMAGETYPE_JPEG,
            'png'  => IMAGETYPE_PNG,
            'bmp'  => IMAGETYPE_BMP
        );
        $maxfilesize = $params['image_max_kb'] * 1024;
        $this->setRequired('name', $this->_model->_('Please select a file to upload'));
        $this->addRule('name', $this->_model->_('Invalid file type'), 'fileextensionandmimetype', array($extension_mimetype));
        $this->addRule('name', sprintf($this->_model->_('File size must be smaller than %d kilo bytes'), $params['image_max_kb']), 'maxfilesize', $maxfilesize);
        //$this->setMaxFileSize($maxfilesize);
        $this->removeElements(array('userid', 'Project'));
        $this->setElementLabel('name', $this->_model->_('Screenshot'));
    }

    protected function _onEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to a specific entity form should come here
    }

    protected function _onFillEntity(Sabai_Model_Entity $entity)
    {
        // things that should be applied to the entity after form submit should come here
    }
}