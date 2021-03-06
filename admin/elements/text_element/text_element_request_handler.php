<?php
    
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "request_handlers/http_request_handler.php";
    require_once CMS_ROOT . "database/dao/element_dao.php";
    require_once CMS_ROOT . "elements/text_element/text_element_form.php";

    class TextElementRequestHandler extends HttpRequestHandler {

        private $_text_element;
        private $_element_dao;
        private $_text_element_form;

        public function __construct($text_element) {
            $this->_text_element = $text_element;
            $this->_element_dao = ElementDao::getInstance();
            $this->_text_element_form = new TextElementForm($this->_text_element);
        }

        public function handleGet() {
        }

        public function handlePost() {
            $this->_text_element_form->loadFields();
            $this->_element_dao->updateElement($this->_text_element);
        }
    }
?>