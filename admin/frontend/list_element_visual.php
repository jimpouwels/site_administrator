<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "frontend/frontend_visual.php";

    class ListElementFrontendVisual extends FrontendVisual {

        private $_template_engine;
        private $_list_element;

        public function __construct($current_page, $list_element) {
            parent::__construct($current_page);
            $this->_template_engine = TemplateEngine::getInstance();
            $this->_list_element = $list_element;
        }

        public function render() {
            $element_holder = $this->_list_element->getElementHolder();
            $this->_template_engine->assign("title", $this->toHtml($this->_list_element->getTitle(), $element_holder));
            $this->_template_engine->assign("items", $this->renderListItems($element_holder));
            return $this->_template_engine->fetch(FRONTEND_TEMPLATE_DIR . "/" . $this->_list_element->getTemplate()->getFileName());
        }

        private function renderListItems($element_holder) {
            $list_items = array();
            foreach ($this->_list_element->getListItems() as $list_item)
                $list_items[] = $this->toHtml($list_item->getText(), $element_holder);
            return $list_items;
        }
    }