<?php	defined('_ACCESS') or die;	require_once CMS_ROOT . "view/views/object_picker.php";		class PagePicker extends ObjectPicker {				public function __construct($label, $value, $backing_field_id, $button_label, $opener_submit_id, $button_id) {			parent::__construct($label, $value, $backing_field_id, $button_label, $opener_submit_id, $button_id);		}				public function getType() {			return Search::$PAGES;		}		}?>