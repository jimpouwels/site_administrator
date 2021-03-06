<?php

    
    defined('_ACCESS') or die;
    
    include_once CMS_ROOT . "database/mysql_connector.php";
    include_once CMS_ROOT . "core/model/element_holder.php";

    class ElementHolderDao {

        private static $myAllColumns = "e.id, e.template_id, e.title, e.published, e.scope_id,
                      e.created_at, e.created_by, e.type";

        private static $instance;
        private $_mysql_connector;

        private function __construct() {
            $this->_mysql_connector = MysqlConnector::getInstance();
        }

        public static function getInstance() {
            if (!self::$instance)
                self::$instance = new ElementHolderDao();
            return self::$instance;
        }

        public function getElementHolder($id) {
            $query = "SELECT " . self::$myAllColumns . " FROM element_holders e WHERE e.id = " . $id;
            $result = $this->_mysql_connector->executeQuery($query);
            while ($row = $result->fetch_assoc())
                return ElementHolder::constructFromRecord($row);
        }

        public function persist($element_holder) {
            $query = "INSERT INTO element_holders (template_id, title, published, scope_id, created_at, created_by, type) VALUES
                      (NULL, '" . $element_holder->getTitle() . "', 0," . $element_holder->getScopeId() . ", now(), " . $element_holder->getCreatedBy()->getId() . "
                      , '" . $element_holder->getType() . "')";
            $this->_mysql_connector->executeQuery($query);
            $element_holder->setId($this->_mysql_connector->getInsertId());
        }

        public function update($element_holder) {
            $published_value = ($element_holder->isPublished()) ? 1 : 0;
            $query = "UPDATE element_holders SET title = '" . $this->_mysql_connector->realEscapeString($element_holder->getTitle()) . "', published = " . $published_value . ",
                      scope_id = " . $element_holder->getScopeId();
            if ($element_holder->getTemplateId() != "" && !is_null($element_holder->getTemplateId())) {
                $query .= ", template_id = " . $element_holder->getTemplateId();
            }
            $query .= " WHERE id = " . $element_holder->getId();
            $this->_mysql_connector->executeQuery($query);
        }

        public function delete($element_holder) {
            $query = "DELETE FROM element_holders WHERE id = " . $element_holder->getId();                  
            $this->_mysql_connector->executeQuery($query);
        }
    }
?>