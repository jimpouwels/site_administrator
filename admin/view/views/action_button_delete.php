<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . 'view/views/action_button.php';

    class ActionButtonDelete extends ActionButton {

        public function __construct($id) {
            parent::__construct($this->getTextResource('action_button_delete'), $id, 'icon_delete');
        }
    
    }