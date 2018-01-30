<?php
require_once 'Sabai/HTMLQuickForm.php';

class Plugg_XOOPSCubeUser_EditImageForm extends Sabai_HTMLQuickForm
{ 
    public function __construct(Plugg_XOOPSCubeUser_Plugin $plugin, Sabai_User_Identity $identity, $action)
    {
        parent::Sabai_HTMLQuickForm('', 'post', $action);
        if ($image = $identity->getImage()) {
            $this->addElement('static', '', $plugin->_('Current image'), sprintf('<img src="%1$s" alt="%1$s" />', $image));
        }
        if ($plugin->getParam('allowImageUpload') && $plugin->getXoopsUploadsPath()) {
            $this->addHeader($plugin->_('Upload an image file to be used as your avatar or select one from the list.'));
            $maxsize_msg = sprintf($plugin->_('File size must be smaller than %d kilo bytes, %dx%d pixels.'), $plugin->getParam('imageMaxSizeKB'), $plugin->getParam('imageMaxWidth'), $plugin->getParam('imageMaxHeight'));
            $this->addElement('file', 'file', array($plugin->_('Upload image file'), null, $maxsize_msg), array('size' => 30));
            $extension_mimetype = array(
                'gif'  => IMAGETYPE_GIF,
                'jpg'  => IMAGETYPE_JPEG,
                'jpeg' => IMAGETYPE_JPEG,
                'png'  => IMAGETYPE_PNG,
            );
            $this->addRule('file', $plugin->_('Invalid file type'), 'fileextensionandmimetype', array($extension_mimetype));
            $this->addRule('file', $maxsize_msg, 'maxfilesize', $plugin->getParam('imageMaxSizeKB') * 1024);
            $this->addRule('file', $maxsize_msg, 'maximagedimension', array($plugin->getParam('imageMaxWidth'), $plugin->getParam('imageMaxHeight')));
        } else {
            $this->addHeader($plugin->_('Select an avatar image from the list below.'));
        }
        $db = $plugin->getXoopsDB();
        $sql = sprintf('SELECT avatar_id, avatar_file, avatar_name FROM %savatar WHERE avatar_display = 1 AND avatar_type = %s ORDER BY avatar_weight ASC', $db->getResourcePrefix(), $db->escapeString('S'));
        $avatar_options = array(0 => $plugin->_('None'));
        if ($rs = $db->query($sql)) {
            $uploads_dir = $plugin->getXoopsUrl() . '/uploads';
            $user_avatar = str_replace($uploads_dir . '/', '', $identity->getImage());
            while ($row = $rs->fetchRow()) {
                $avatar_options[$row[0]] = sprintf('<img src="%1$s/%2$s" alt="%3$s" />', $uploads_dir, $row[1], h($row[2]));
                if ($user_avatar == $row[1]) {
                    $avatar_selected = $row[0];
                }
            }
        }
        $avatars = $this->addElement('altselect', 'avatar', $plugin->_('Select image'), $avatar_options);
        $avatars->setDelimiter('&nbsp;&nbsp;');
        if (isset($avatar_selected)) $this->setDefaults(array('avatar' => $avatar_selected));
        $this->useToken(get_class($this));
    }
    
    function submit($plugin, $identity)
    {
        $db = $plugin->getXoopsDB();
        $db->beginTransaction();
        if ($plugin->getParam('allowImageUpload') &&
            $this->elementExists('file') &&
            ($uploads_dir = $plugin->getXoopsUploadsPath()) &&
            ($file = $this->getElement('file')) &&
            $file->isUploadedFile()
        ) {
            if ($avatar_file = @$file->moveUploadedFile($uploads_dir, 'uniq', 'cavt', 30)) {
                $mimes = array(
                    IMAGETYPE_GIF => 'image/gif',
                    IMAGETYPE_JPEG => 'image/jpeg',
                    IMAGETYPE_PNG => 'image/png'
                );
                // We need to supply the file path of the moved file here because
                // the one under the tmp directory has already been removed
                $avatar_mime = $mimes[$file->getImageType($uploads_dir . '/' . $avatar_file)];
                $sql = sprintf('INSERT INTO %savatar(avatar_file, avatar_name, avatar_mimetype, avatar_created, avatar_display, avatar_weight, avatar_type) VALUES(%s, %s, %s, %d, %d, %d, %s)', $db->getResourcePrefix(), $db->escapeString($avatar_file), $db->escapeString($identity->getUsername()), $db->escapeString($avatar_mime), time(), 1, 0, $db->escapeString('C'));
                if (!$db->exec($sql)) {
                    $db->rollback();
                    return false;
                }
                $avatar_id = $db->lastInsertId($db->getResourcePrefix() . 'avatar', 'avatar_id');
            }
        } elseif ($this->elementExists('avatar')) {
            if ($avatar_id = $this->getSubmitValue('avatar')) {
                $sql = sprintf('SELECT avatar_file FROM %savatar WHERE avatar_id = %d AND avatar_type = %s', $db->getResourcePrefix(), $avatar_id, $db->escapeString('S'));
                if (($rs = $db->query($sql, 1, 0)) && ($row = $rs->fetchRow())) {
                    $avatar_file = $row[0];
                }
            } else {
                // Default avatar image for XCL
                $avatar_file = 'blank.gif';
            }
        }
        if (isset($avatar_id) && !empty($avatar_file)) {
            $sql = sprintf('UPDATE %susers SET user_avatar = %s WHERE uid = %d', $db->getResourcePrefix(), $db->escapeString($avatar_file), $identity->getId());
            if (!$db->exec($sql)) {
                $db->rollback();
                return false;
            }
            $sql = sprintf('DELETE FROM %savatar_user_link WHERE user_id = %d', $db->getResourcePrefix(), $identity->getId());
            if (!$db->exec($sql, false)) {
                $db->rollback();
                return false;
            }
            
            if (!empty($avatar_id)) {
                $sql = sprintf('INSERT INTO %savatar_user_link(avatar_id, user_id) VALUES(%d, %d)', $db->getResourcePrefix(), $avatar_id, $identity->getId());
                if (!$db->exec($sql)) {
                    $db->rollback();
                    return false;
                }
            }
            $db->commit();

            // Remove unused avatar files
            $sql = sprintf('SELECT avatar_id, avatar_file FROM %savatar WHERE avatar_type = %s AND avatar_name = %s AND avatar_id != %d', $db->getResourcePrefix(), $db->escapeString('C'), $db->escapeString($identity->getUsername()), $avatar_id);
            $old_files = array();
            if ($rs = $db->query($sql)) {
                while ($row = $rs->fetchRow()) {
                    $old_files[$row[0]] = $row[1];
                }
            }
            if (!empty($old_files)) {
                $sql = sprintf('DELETE FROM %savatar WHERE avatar_id IN (%s)', $db->getResourcePrefix(), implode(',', array_keys($old_files)));
                if ($db->exec($sql)) {
                    foreach ($old_files as $old_file) {
                        unlink($uploads_dir . '/' . $old_file);
                    }
                }
            }

            return true;
        }
        $db->rollback();
        return false;
    }
}