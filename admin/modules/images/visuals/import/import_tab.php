<?php
    ('_ACCESS') or die;

    require_once CMS_ROOT . "view/views/visual.php";
    require_once CMS_ROOT . "database/dao/image_dao.php";

    class ImportTab extends Panel {

        private static $TEMPLATE = "images/import/root.tpl";
        private $_template_engine;
        private $_image_dao;

        public function __construct() {
            parent::__construct('Importeren');
            $this->_template_engine = TemplateEngine::getInstance();
            $this->_image_dao = ImageDao::getInstance();
        }

        public function render() {
            return parent::render();
        }

        public function renderPanelContent() {
            $this->_template_engine->assign("upload_field", $this->renderUploadField());
            $this->_template_engine->assign("labels_pulldown", $this->renderLabelPullDown());

            return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);
        }

        private function renderUploadField() {
            $upload_field = new UploadField("import_zip_file", "ZIP bestand", false, "");
            return $upload_field->render();
        }

        private function renderLabelPullDown() {
            $labels_name_value_pair = $this->getLabelsValuePair();
            $pulldown = new PullDown("import_label", "Label", null, $labels_name_value_pair, 200, false);
            return $pulldown->render();
        }

        private function getLabelsValuePair() {
            $labels_name_value_pair = array();
            array_push($labels_name_value_pair, array("name" => "&gt; Selecteer", "value" => null));
            foreach ($this->_image_dao->getAllLabels() as $label) {
                array_push($labels_name_value_pair, array("name" => $label->getName(), "value" => $label->getId()));
            }
            return $labels_name_value_pair;
        }

    }
