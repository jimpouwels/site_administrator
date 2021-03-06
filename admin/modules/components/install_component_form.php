<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "request_handlers/form.php";

    class InstallComponentForm extends Form {

        private $_file_path;
        private $_file_name;

        public function loadFields() {
            $this->_file_path = $this->getMandatoryUploadFilePath('upload_field', 'Selecteer een component');
            $this->_file_name = $this->getUploadedFileName('upload_field');
            if ($this->hasErrors())
                throw new FormException($this->getError('upload_field'));
        }

        public function getFilePath() {
            return $this->_file_path;
        }

        public function getFileName() {
            return $this->_file_name;
        }
    }