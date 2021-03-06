<?php
    defined('_ACCESS') or die;

    require_once CMS_ROOT . "database/dao/scope_dao.php";

    class TemplateEditor extends Panel {

        private static $TEMPLATE_EDITOR_TEMPLATE = "templates/template_editor.tpl";

        private $_template;
        private $_template_engine;
        private $_scope_dao;

        public function __construct($template) {
            parent::__construct('Template bewerken', 'template_editor_fieldset');
            $this->_template = $template;
            $this->_template_engine = TemplateEngine::getInstance();
            $this->_scope_dao = ScopeDao::getInstance();
        }

        public function render() {
            return parent::render();
        }

        public function renderPanelContent() {
            $this->_template_engine->assign("template_id", $this->_template->getId());
            $this->assignEditFields();
            return $this->_template_engine->fetch("modules/" . self::$TEMPLATE_EDITOR_TEMPLATE);
        }

        private function assignEditFields() {
            $name_field = new TextField("name", "Naam", $this->_template->getName(), true, false, null);
            $this->_template_engine->assign("name_field", $name_field->render());
            $filename_field = new TextField("file_name", "Bestandsnaam", $this->_template->getFileName(), false, false, null);
            $this->_template_engine->assign("filename_field", $filename_field->render());
            $upload_field = new UploadField("template_file", "Template", false, "");
            $this->_template_engine->assign("upload_field", $upload_field->render());
            $this->_template_engine->assign("scopes_field", $this->renderScopesField());
        }

        private function renderScopesField() {
            $scopes_name_value_pair = array();
            foreach ($this->_scope_dao->getScopes() as $scope) {
                array_push($scopes_name_value_pair, array("name" => $scope->getName(), "value" => $scope->getId()));
            }
            $current_scope = $this->_template->getScope();
            $scopes_field = new PullDown("scope", "Scope", (is_null($current_scope) ? null : $current_scope->getId()), $scopes_name_value_pair, 200, true);
            return $scopes_field->render();
        }

    }
