<?php
class Plugg_XOOPSCubeUserAuth_Plugin extends Plugg_Plugin implements Plugg_User_Authenticator_Application
{
    private $_db;

    public function userAuthGetName()
    {
        return array($this->getName() => $this->_('XOOPS Cube user authentication'));
    }

    public function userAuthGetNicename()
    {
         return $this->_('XOOPS Cube user authentication');
    }

    public function userAuthGetForm($action, $authId)
    {
        require_once 'Sabai/HTMLQuickForm.php';
        $form = new Sabai_HTMLQuickForm('', 'post', $action);
        $form->addHeader(sprintf(
            $this->_('You can use the username and password pair at <a href="%s">%s</a> to login to this site.'),
            h($this->_params['siteUrl']),
            h($this->_params['siteName'])
        ));
        $form->addElement('text', 'username', array(
            $this->_('Username'),
            sprintf($this->_('Enter your username at %s.'), h($this->_params['siteName']))
        ), array('size' => 30, 'maxlength' => 255));
        $form->addElement('password', 'password', array(
            $this->_('Password'),
            $this->_('Enter the password that accompanies your username.')
        ), array('size' => 30, 'maxlength' => 255));
        $mb_whitespace = $this->_(' ');
        $form->setRequired('username', $this->_('Username is required'), true, $mb_whitespace);
        $form->setRequired('password', $this->_('Password is required'), true, $mb_whitespace);
        $form->useToken(get_class($this));
        return $form;
    }

    public function userAuthSubmitForm(Sabai_HTMLQuickForm $form)
    {
        $values = $form->getSubmitValues();
        $mb_whitespace = $this->_(' ');
        $username = mb_trim($values['username'], $mb_whitespace);
        $password = mb_trim($values['password'], $mb_whitespace);
        if (!empty($username) && !empty($password)) {
            $db = $this->_getDB();
            $sql = sprintf(
                'SELECT email, name FROM %susers WHERE uname = %s AND pass = %s',
                $db->getResourcePrefix(),
                $db->escapeString($username),
                $db->escapeString(md5($password))
            );
            if (($rs = $db->query($sql, 1, 0)) && ($row = $rs->fetchAssoc())) {
                return array(
                    'id' => $username,
                    'display_id' => $username,
                    'username' => $username,
                    'email' => $row['email'],
                    'name' => $row['name'],
                );
            }
        }

        $error = $this->_('Invalid username or password');
        $form->setElementError('username', $error);
        $form->setElementError('password', $error);
        $form->setElementValue('password', '');
        return false;
    }

    public function userAuthRenderForm(Sabai_HTMLQuickForm $form)
    {
        return $form->toHtml();
    }

    private function _getDB()
    {
        if (!isset($this->_db)) {
            $this->loadParams(); // Load params manually so that non-cacheable ones (db* params) become accessible

            $this->_db = $this->_application->getLocator()->createService('DB', array(
                'DBConnection' => $this->_application->getLocator()->createService('DBConnection', array(
                    'scheme' => $this->_params['dbScheme'],
                    'options' => array(
                        'host' => $this->_params['dbHost'],
                        'dbname' => $this->_params['dbName'],
                        'user' => $this->_params['dbUser'],
                        'pass' => $this->_params['dbPass'],
                        'flags' => $this->_params['dbSecure'] ? MYSQL_CLIENT_SSL : 0,
                    )
                )),
                'tablePrefix' => $this->_params['dbPrefix'] . '_'
            ));
        }
        return $this->_db;
    }
}