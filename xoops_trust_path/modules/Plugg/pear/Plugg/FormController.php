<?php
require_once 'Sabai/Token.php';

abstract class Plugg_FormController extends Sabai_Application_Controller
{
    protected $_submitable = true;
    protected $_confirmable = true;
    protected $_submitElementName = '_form_submit_submit';
    protected $_confirmElementName = '_form_submit_confirm';
    protected $_submitPhrase, $_confirmPhrase;
    protected $_tokenId = __CLASS__;
    protected $_tokenName = SABAI_TOKEN_NAME;

    protected function _doExecute(Sabai_Application_Context $context)
    {
        if (!$init_result = $this->_init($context)) {
            if (!$context->response->isError()) {
                // No error set, so set it here
                $context->response->setError($context->plugin->_('Invalid request'));
            }

            return;
        }

        // Initialize form
        $form = $this->_getForm($context);
        $form->useToken($this->_tokenId, $this->_tokenName);

        // Add submit buttons
        if ($this->_confirmable) {
            $form->addSubmitButtons(array(
                $this->_confirmElementName => isset($this->_confirmPhrase) ? $this->_confirmPhrase : $context->plugin->_('Confirm'),
                $this->_submitElementName => isset($this->_submitPhrase) ? $this->_submitPhrase : $context->plugin->_('Submit')
            ));
        } else {
            $form->addSubmitButtons(array(
                $this->_submitElementName => isset($this->_submitPhrase) ? $this->_submitPhrase : $context->plugin->_('Submit')
            ));
        }

        // Notify that the form is built
        $this->_application->dispatchEvent('PluggFormBuilt', array($context, $form));

        // Validate form and submit
        if ($this->_submitable && $form->validate()) {

            // Notify that the form is validated
            $this->_application->dispatchEvent('PluggFormValidated', array($context, $form));

            if ($this->_confirmable && $form->getSubmitValue($this->_confirmElementName)) {
                $form->freeze();
                $form->addSubmitButtons(array(
                    $context->plugin->_('Back'),
                    $this->_submitElementName => $context->plugin->_('Submit')
                ));

                // Notify that the form is being confirmed
                $this->_application->dispatchEvent('PluggFormConfirm', array($context, $form));

                $this->_confirmForm($context, $form);
            } elseif ($form->getSubmitValue($this->_submitElementName)) {

                // Notify that the form submit has been submitted
                $this->_application->dispatchEvent('PluggFormSubmit', array($context, $form));

                if ($this->_submitForm($context, $form)) {
                    // Notify that the form submit was success
                    $this->_application->dispatchEvent('PluggFormSubmitSuccess', array($context, $form));

                    return;
                }

                // Notify that the form submit has failed
                $this->_application->dispatchEvent('PluggFormSubmitFail', array($context, $form));

                // If error is set, do not display the form
                if ($context->response->isError()) return;
            }
        }

        $this->_viewForm($context, $form);

        // Notify that the form is being rendered
        $this->_application->dispatchEvent('PluggFormRender', array($context, $form));

        $this->_application->setData(array(
            'form' => $form,
            'form_html' => $form->toHtml()
        ));
    }

    protected function _confirmForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form){}

    protected function _submitForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form)
    {
        return true;
    }

    protected function _viewForm(Sabai_Application_Context $context, Sabai_HTMLQuickForm $form){}

    abstract protected function _init(Sabai_Application_Context $context);
    abstract protected function _getForm(Sabai_Application_Context $context);
}