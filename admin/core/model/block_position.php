<?php
    defined('_ACCESS') or die;
    
    require_once CMS_ROOT . "core/model/entity.php";
    require_once CMS_ROOT . "database/dao/block_dao.php";

    class BlockPosition extends Entity {
    
        private $_name;
        private $_explanation;
        private $_block_dao;
        
        public function __construct() {
            $this->_block_dao = BlockDao::getInstance();
        }
        
        public function getName() {
            return $this->_name;
        }
        
        public function setName($name) {
            $this->_name = $name;
        }
        
        public function getExplanation() {
            return $this->_explanation;
        }
        
        public function setExplanation($explanation) {
            $this->_explanation = $explanation;
        }
        
        public function getBlocks() {
            return $this->_block_dao->getBlocksByPosition($this);
        }
        
        public static function constructFromRecord($record) {
            $position = new BlockPosition();
            $position->setId($record['id']);
            $position->setName($record['name']);
            $position->setExplanation($record['explanation']);
            
            return $position;
        }
    
    }
    
?>