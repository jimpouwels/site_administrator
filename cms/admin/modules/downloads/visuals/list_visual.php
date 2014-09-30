<?php

    // No direct access
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "/view/views/visual.php";
    require_once CMS_ROOT . "/database/dao/download_dao.php";

    class ListVisual extends Visual {

        private static $TEMPLATE = "downloads/list.tpl";
        private $_download_dao;
        private $_template_engine;

        public function __construct() {
            $this->_download_dao = DownloadDao::getInstance();
            $this->_template_engine = TemplateEngine::getInstance();
        }

        public function render() {
            $this->_template_engine->assign("search_results", $this->getSearchResults());
            return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);
        }

        private function getSearchResults() {
            $downloads = array();
            foreach ($this->_download_dao->getAllDownloads() as $download) {
                $download_value = array();
                $download_value["id"] = $download->getId();
                $download_value["title"] = $download->getTitle();
                $download_value["published"] = $download->isPublished();
                $download_value["created_at"] = $download->getCreatedAt();
                $created_by = $download->getCreatedBy();
                if (!is_null($created_by))
                    $download_value["created_by"] = $created_by->getUsername();
                $downloads[] = $download_value;
            }
            return $downloads;
        }
    }