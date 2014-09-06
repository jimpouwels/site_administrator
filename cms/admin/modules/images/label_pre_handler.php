<?php	// No direct access	defined('_ACCESS') or die;		require_once "database/dao/image_dao.php";	require_once "core/data/image_label.php";	require_once "view/request_handlers/module_request_handler.php";	require_once "modules/images/label_form.php";		class LabelPreHandler extends ModuleRequestHandler {		private static $LABEL_QUERYSTRING_KEY = "label";			private $_image_dao;		private $_current_label;			public function __construct() {			$this->_image_dao = ImageDao::getInstance();		}			public function handleGet() {			$this->_current_label = $this->getCurrentLabelFromGetRequest();					}				public function handlePost() {			$this->_current_label = $this->getCurrentLabelFromPostRequest();			if ($this->isUpdateLabelAction())				$this->updateLabel();			else if ($this->isAddLabelAction())				$this->addLabel();			else if ($this->isDeleteLabelsAction())				$this->deleteLabels();		}				public function getCurrentLabel() {			return $this->_current_label;		}		private function getCurrentLabelFromGetRequest() {			$current_label = null;			if (isset($_GET[self::$LABEL_QUERYSTRING_KEY])) {				$label_id = $_GET[self::$LABEL_QUERYSTRING_KEY];				$current_label = $this->_image_dao->getLabel($label_id);			}			return $current_label;		}				private function getCurrentLabelFromPostRequest() {			$current_label = null;			if (isset($_POST["label_id"]) && $_POST["label_id"] != "") {				$current_label = $this->_image_dao->getLabel($_POST["label_id"]);			}			return $current_label;		}				private function addLabel() {			$label = $this->_image_dao->createLabel();			$label->setName("Nieuw label");			header("Location: /admin/index.php?label=" . $label->getId());		}				private function updateLabel() {			$label_form = new LabelForm($this->_current_label);			try {				$label_form->loadFields();				$this->_image_dao->updateLabel($this->_current_label);				Notifications::setSuccessMessage("Label succesvol opgeslagen");			} catch (FormException $e) {				Notifications::setFailedMessage("Label niet opgeslagen, verwerk de fouten");			}		}				private function deleteLabels() {			$labels = $this->_image_dao->getAllLabels();			foreach ($labels as $label) {				if (isset($_POST["label_" . $label->getId() . "_delete"])) {					$this->_image_dao->deleteLabel($label);				}			}			Notifications::setSuccessMessage("Label(s) succesvol verwijderd");		}				private function isUpdateLabelAction() {			return isset($_POST["action"]) && $_POST["action"] == "update_label";		}				private function isDeleteLabelsAction() {			return isset($_POST["label_delete_action"]) && $_POST["label_delete_action"] == "delete_labels";		}				private function isAddLabelAction() {			return isset($_POST["add_label_action"]) && $_POST["add_label_action"] != "";		}			}	?>