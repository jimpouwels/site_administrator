<?php	// No direct access	defined('_ACCESS') or die;		require_once CMS_ROOT . "view/template_engine.php";	require_once CMS_ROOT . "database/mysql_connector.php";		class Configuration extends Visual {			private static $CONFIGURATION_TEMPLATE = "modules/database/configuration.tpl";		private $_template_engine;		private $_mysql_connector;			public function __construct() {			$this->_template_engine = TemplateEngine::getInstance();			$this->_mysql_connector = MysqlConnector::getInstance();		}			public function render() {			$this->_template_engine->assign("hostname", $this->_mysql_connector->getHostName());			$this->_template_engine->assign("database_name", $this->_mysql_connector->getDatabaseName());			$this->_template_engine->assign("database_type", $this->_mysql_connector->getDatabaseType());			$this->_template_engine->assign("database_version", $this->_mysql_connector->getDatabaseVersion());			return $this->_template_engine->fetch(self::$CONFIGURATION_TEMPLATE);		}	}	?>