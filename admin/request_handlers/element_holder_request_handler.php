<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "database/dao/element_holder_dao.php";
    require_once CMS_ROOT . "database/dao/link_dao.php";
    require_once CMS_ROOT . "database/dao/element_dao.php";
    require_once CMS_ROOT . "request_handlers/module_request_handler.php";
    require_once CMS_ROOT . "request_handlers/link_form.php";
    require_once CMS_ROOT . "request_handlers/exceptions/element_holder_contains_errors_exception.php";
    require_once CMS_ROOT . "elements/element_contains_errors_exception.php";

    abstract class ElementHolderRequestHandler extends ModuleRequestHandler {

        private $_element_dao;
        private $_link_dao;
        private $_element_holder_dao;

        public function __construct() {
            $this->_element_dao = ElementDao::getInstance();
            $this->_link_dao = LinkDao::getInstance();
            $this->_element_holder_dao = ElementHolderDao::getInstance();
        }

        public function handleGet() {
        }

        public function handlePost() {
            $element_holder = $this->getElementHolderFromPostRequest();
            if ($this->isAddElementAction())
                $this->addElement();
            else if ($this->isDeleteElementAction())
                $this->deleteElement();
            else if ($this->isAddLinkAction())
                $this->addLink($element_holder);
        }

        protected function updateElementHolder($element_holder) {
            $this->updateLinks($element_holder);
            foreach ($element_holder->getElements() as $element) {
                try {
                    $element->getRequestHandler()->handle();
                } catch (ElementContainsErrorsException $e) {
                    throw new ElementHolderContainsErrorsException($e->getMessage());
                }
            }
        }

        private function addElement() {
            $element_type = $this->getElementTypeToAdd();
            if (!is_null($element_type))
                $this->_element_dao->createElement($element_type, $_POST[EDIT_ELEMENT_HOLDER_ID]);
        }

        private function deleteElement() {
            $element_to_delete = $this->_element_dao->getElement($_POST[DELETE_ELEMENT_FORM_ID]);
            if (!is_null($element_to_delete))
                $element_to_delete->delete();
        }

        private function getElementTypeToAdd() {
            $element_type_to_add = $_POST[ADD_ELEMENT_FORM_ID];
            $element_type = $this->_element_dao->getElementType($element_type_to_add);
            return $element_type;
        }

        private function addLink($element_holder) {
            $this->_link_dao->createLink($element_holder->getId());
        }

        private function updateLinks($element_holder) {
            $links = $this->_link_dao->getLinksForElementHolder($element_holder->getId());
            foreach ($links as $link) {
                $link_form = new LinkForm($link);
                if ($link_form->isSelectedForDeletion())
                    $this->_link_dao->deleteLink($link);
                else {
                    $this->updateLink($link, $link_form);
                }
            }
        }

        private function updateLink($link, $link_form) {
            try {
                $link_form->loadFields();
                $this->_link_dao->updateLink($link);
            } catch (FormException $e) {
                $this->sendErrorMessage($this->getTextResource('link_not_saved_error'));
            }
        }

        private function getElementHolderFromPostRequest() {
            if (!isset($_POST[EDIT_ELEMENT_HOLDER_ID])) return null;
            return $this->_element_holder_dao->getElementHolder($_POST[EDIT_ELEMENT_HOLDER_ID]);
        }

        private function isAddElementAction() {
            return isset($_POST[ADD_ELEMENT_FORM_ID]) && $_POST[ADD_ELEMENT_FORM_ID] != "";
        }

        private function isDeleteElementAction() {
            return isset($_POST[DELETE_ELEMENT_FORM_ID]) && $_POST[DELETE_ELEMENT_FORM_ID] != "";
        }

        private function isAddLinkAction() {
            return isset($_POST['action']) && $_POST['action'] == 'add_link';
        }
    }

?>
