<?php	// No direct access	defined('_ACCESS') or die;		require_once "view/views/form_pulldown.php";	require_once "dao/template_dao.php";		class TemplatePicker extends Pulldown {			public function __construct($name, $label, $mandatory, $class_name, $current_template, $scope) {			$options = $this->getOptions($scope);			$current_template_id = null;			if (!is_null($current_template)) {				$current_template_id = $current_template->getId();			}						parent::__construct($name, $label, $current_template_id, $options, $mandatory, $class_name);		}			public function render() {			return parent::render();		}				private function getOptions($scope) {			$template_dao = TemplateDao::getInstance();						$options = array();			array_push($options, array("name" => "&gt; Selecteer", "value" => null));			foreach ($template_dao->getTemplatesByScope($scope) as $template) {				array_push($options, array('name' => $template->getName(), 'value' => $template->getId()));			}			return $options;		}		}?>