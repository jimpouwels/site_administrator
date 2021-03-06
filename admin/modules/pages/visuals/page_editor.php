<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "view/views/form_template_picker.php";
    require_once CMS_ROOT . "view/views/element_container.php";
    require_once CMS_ROOT . "view/views/link_editor.php";
    require_once CMS_ROOT . "view/views/block_selector.php";
    require_once CMS_ROOT . 'modules/pages/visuals/page_metadata_editor.php';

    class PageEditor extends Visual {

        private static $PAGE_EDITOR_TEMPLATE = "pages/editor.tpl";
        private static $PAGE_METADATA_TEMPLATE = "pages/metadata.tpl";

        private $_template_engine;
        private $_current_page;

        public function __construct($current_page) {
            $this->_current_page = $current_page;
            $this->_template_engine = TemplateEngine::getInstance();
        }

        public function render() {
            $this->_template_engine->assign("page_metadata", $this->renderPageMetaDataPanel());
            $this->_template_engine->assign("element_container", $this->renderElementContainerPanel());
            $this->_template_engine->assign("link_editor", $this->renderLinkEditorPanel());
            $this->_template_engine->assign("block_selector", $this->renderBlockSelectorPanel());
            return $this->_template_engine->fetch("modules/" . self::$PAGE_EDITOR_TEMPLATE);
        }

        private function renderPageMetaDataPanel() {
            $metadata_editor = new MetadataEditor($this->_current_page);
            return $metadata_editor->render();
        }

        private function renderElementContainerPanel() {
            $element_container = new ElementContainer($this->_current_page->getElements());
            return $element_container->render();
        }

        private function renderLinkEditorPanel() {
            $link_editor = new LinkEditor($this->_current_page->getLinks());
            return $link_editor->render();
        }

        private function renderBlockSelectorPanel() {
            $block_selector = new BlockSelector($this->_current_page->getBlocks(), $this->_current_page->getId());
            return $block_selector->render();
        }

    }
