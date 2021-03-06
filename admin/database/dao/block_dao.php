<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "authentication/authenticator.php";
    require_once CMS_ROOT . "database/mysql_connector.php";
    require_once CMS_ROOT . "database/dao/element_dao.php";
    require_once CMS_ROOT . "database/dao/element_holder_dao.php";
    require_once CMS_ROOT . "database/dao/template_dao.php";
    require_once CMS_ROOT . "core/model/block.php";
    require_once CMS_ROOT . "core/model/block_position.php";
    require_once CMS_ROOT . "database/dao/authorization_dao.php";

    class BlockDao {

        private $_element_holder_dao;
        private $_mysql_connector;

        public static $myAllColumns = "e.id, e.template_id, e.title, e.published, e.scope_id, 
                      e.created_at, e.created_by, e.type, b.position_id";

        private static $instance;

        private function __construct() {
            $this->_element_holder_dao = ElementHolderDao::getInstance();
            $this->_mysql_connector = MysqlConnector::getInstance();
        }

        public static function getInstance() {
            if (!self::$instance)
                self::$instance = new BlockDao();
            return self::$instance;
        }

        public function getAllBlocks() {
            $query = "SELECT " . self::$myAllColumns . " FROM element_holders e, blocks b WHERE e.id = b.element_holder_id";
            $result = $this->_mysql_connector->executeQuery($query);
            $blocks = array();
            while ($row = $result->fetch_assoc())
                $blocks[] = Block::constructFromRecord($row);
            return $blocks;
        }

        public function getBlocksByPosition($position) {
            $statement = $this->_mysql_connector->prepareStatement("SELECT " . self::$myAllColumns . " FROM
                                                                    element_holders e, blocks b WHERE b.position_id = ?
                                                                    AND e.id = b.element_holder_id");
            $position_id = $position->getId();
            $statement->bind_param("i", $position_id);
            $result = $this->_mysql_connector->executeStatement($statement);
            $blocks = array();
            while ($row = $result->fetch_assoc())
                $blocks[] = Block::constructFromRecord($row);
            return $blocks;
        }

        public function getBlocksWithoutPosition() {
            $query = "SELECT " . self::$myAllColumns . " FROM element_holders e, blocks b WHERE b.position_id IS  
                     NULL AND e.id = b.element_holder_id";
            $result = $this->_mysql_connector->executeQuery($query);
            $blocks = array();
            while ($row = $result->fetch_assoc())
                $blocks[] = Block::constructFromRecord($row);
            return $blocks;
        }

        public function getBlocksByPageAndPosition($page, $position_name) {
            $query = "SELECT " . self::$myAllColumns . 
                     " FROM element_holders e, blocks b, blocks_pages bp, block_positions bps" . 
                     " WHERE e.id = b.element_holder_id" . 
                     " AND bp.block_id = e.id" . 
                     " AND bp.page_id = " . $page->getId() .
                     " AND bps.name = '" . $position_name . "'" . 
                     " AND b.position_id = bps.id";
            $result = $this->_mysql_connector->executeQuery($query);
            $blocks = array();
            while ($row = $result->fetch_assoc())
                $blocks[] = Block::constructFromRecord($row);
            return $blocks;
        }

        public function getBlocksByPage($page) {
            $query = "SELECT " . self::$myAllColumns . " FROM element_holders e, blocks b, blocks_pages bps WHERE e.id = b.element_holder_id
                      AND bps.page_id = " . $page->getId() . " AND bps.block_id = e.id";
            $result = $this->_mysql_connector->executeQuery($query);
            $blocks = array();

            while ($row = $result->fetch_assoc())
                $blocks[] = Block::constructFromRecord($row);
            return $blocks;
        }

        public function getBlockPositions() {
            $query = "SELECT * FROM block_positions ORDER BY name";
            $result = $this->_mysql_connector->executeQuery($query);
            $positions = array();
            while ($row = $result->fetch_assoc())
                $positions[] = BlockPosition::constructFromRecord($row);
            return $positions;
        }

        public function getBlockPosition($position_id) {
            if (!is_null($position_id) && $position_id != '') {
                $query = "SELECT * FROM block_positions WHERE id = " . $position_id;
                $result = $this->_mysql_connector->executeQuery($query);
                while ($row = $result->fetch_assoc())
                    return BlockPosition::constructFromRecord($row);
            }
        }

        public function getBlockPositionByName($position_name) {
            if (!is_null($position_name) && $position_name != '') {
                $query = "SELECT * FROM block_positions WHERE name = '" . $position_name . "'";
                $result = $this->_mysql_connector->executeQuery($query);
                while ($row = $result->fetch_assoc())
                    return BlockPosition::constructFromRecord($row);
            }
        }

        public function getBlock($id) {
            $query = "SELECT " . self::$myAllColumns . " FROM element_holders e, blocks b WHERE e.id = " . $id
                     . " AND e.id = b.element_holder_id";
            $result = $this->_mysql_connector->executeQuery($query);
            while ($row = $result->fetch_assoc())
                return Block::constructFromRecord($row);
        }

        public function createBlock() {
            $new_block = new Block();
            $new_block->setPublished(false);
            $new_block->setTitle('Nieuw block');
            $new_block->setCreatedById(Authenticator::getCurrentUser()->getId());
            $new_block->setType(ELEMENT_HOLDER_BLOCK);
            
            $new_block->setScopeId(6);
            $this->persistBlock($new_block);
            return $new_block;
        }

        private function persistBlock($block) {
            $this->_element_holder_dao->persist($block);
            $query = "INSERT INTO blocks (position_id, element_holder_id) VALUES (NULL, " . $block->getId() . ")";
            $this->_mysql_connector->executeQuery($query);
        }

        public function updateBlock($block) {
            $query = "UPDATE blocks SET";
            if ($block->getPositionId() != '' && !is_null($block->getPositionId()))
                $query = $query . " position_id = " . $block->getPositionId();
            else
                $query = $query . " position_id = NULL";
            $query .= " WHERE element_holder_id = " . $block->getId();
            $this->_mysql_connector->executeQuery($query);
            $this->_element_holder_dao->update($block);
        }

        public function deleteBlock($block) {
            $this->_element_holder_dao->delete($block);
        }

        public function createBlockPosition() {
            $new_position = new BlockPosition();
            $postfix = 1;
            while (!is_null($this->getBlockPositionByName($new_position->getName()))) {
                $new_position->setName("nieuwe_positie_" . $postfix);
                $postfix++;
            }
            $this->persistBlockPosition($new_position);
            return $new_position;
        }

        private function persistBlockPosition($position) {
            $query = "INSERT INTO block_positions (name, explanation) VALUES  ('" . $position->getName() . "', NULL)";
            $this->_mysql_connector->executeQuery($query);
            $position->setId($this->_mysql_connector->getInsertId());
        }

        public function updateBlockPosition($position) {
            $query = "UPDATE block_positions SET name = '" . $position->getName() . "'
                     , explanation = '" . $position->getExplanation() . "' WHERE id = " . $position->getId();
            $this->_mysql_connector->executeQuery($query);
        }

        public function deleteBlockPosition($position) {
            $query = "DELETE FROM block_positions WHERE id = " . $position->getId();
            $this->_mysql_connector->executeQuery($query);
        }

        public function addBlockToPage($block_id, $page) {
            $query = "INSERT INTO blocks_pages (page_id, block_id) VALUES (" . $page->getId() . ", " . $block_id . ")";
            $this->_mysql_connector->executeQuery($query);
        }

        public function deleteBlockFromPage($block_id, $page) {
            $query = "DELETE FROM blocks_pages WHERE page_id = " . $page->getId() ."
                      AND block_id = " . $block_id;
            $this->_mysql_connector->executeQuery($query);
        }
    }
?>