<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "view/views/information_message.php";

    class BlocksList extends Panel {

        private static $TEMPLATE = "blocks/blocks/list.tpl";
        private $_template_engine;
        private $_current_block;
        private $_block_dao;

        public function __construct($current_block) {
            parent::__construct('Blokken', 'block_list');
            $this->_current_block = $current_block;
            $this->_block_dao = BlockDao::getInstance();
            $this->_template_engine = TemplateEngine::getInstance();
        }

        public function render() {
            return parent::render();
        }

        public function renderPanelContent() {
            $this->_template_engine->assign("block_lists", $this->getBlockLists());
            $this->_template_engine->assign("no_results_message", $this->renderNoResultsMessage());
            $current_block_value = null;
            if (!is_null($this->_current_block)) {
                $current_block_value = $this->toArray($this->_current_block);
            }
            $this->_template_engine->assign("current_block", $current_block_value);
            return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);
        }

        private function renderNoResultsMessage() {
            $no_result_message = new InformationMessage($this->getTextResource("blocks_no_blocks_found"));
            return $no_result_message->render();
        }

        private function getBlockLists() {
            $block_lists = array();
            // blocks with position
            foreach ($this->_block_dao->getBlockPositions() as $position) {
                $block_list = array();
                $block_list["position"] = $position->getName();
                $block_list["blocks"] = $this->getBlocks($position);
                $block_lists[] = $block_list;
            }
            // blocks without position
            $block_list = array();
            $block_list["position"] = null;
            $block_list["blocks"] = $this->getBlocks(null);
            $block_lists[] = $block_list;
            return $block_lists;
        }

        private function getBlocks($position) {
            $blocks = array();
            if (!is_null($position))
                foreach ($position->getBlocks() as $block)
                    $blocks[] = $this->toArray($block);
            else
                foreach ($this->_block_dao->getBlocksWithoutPosition() as $block)
                    $blocks[] = $this->toArray($block);
            return $blocks;
        }

        private function toArray($block) {
            $block_value = array();
            $block_value["id"] = $block->getId();
            $block_value["title"] = $block->getTitle();
            $block_value["published"] = $block->isPublished();
            return $block_value;
        }
    }
?>
