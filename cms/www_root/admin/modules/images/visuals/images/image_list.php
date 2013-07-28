<?php	// No direct access	defined('_ACCESS') or die;		require_once "libraries/system/template_engine.php";	require_once "core/visual/information_message.php";		class ImageList extends Visual {			private static $TEMPLATE = "images/images/list.tpl";			private $_template_engine;		private $_current_image;		private $_image_dao;		private $_images_pre_handler;			public function __construct($current_image, $images_pre_handler) {			$this->_current_image = $current_image;			$this->_images_pre_handler = $images_pre_handler;			$this->_image_dao = ImageDao::getInstance();			$this->_template_engine = TemplateEngine::getInstance();		}			public function render() {			$this->_template_engine->assign("search_results", $this->getSearchResults());			$this->_template_engine->assign("no_results_message", $this->renderNoResultsMessage());			$this->_template_engine->assign("current_search_title", $this->_images_pre_handler->getCurrentSearchTitleFromGetRequest());			$this->_template_engine->assign("current_search_filename", $this->_images_pre_handler->getCurrentSearchFilenameFromGetRequest());			$this->_template_engine->assign("current_search_label", $this->getCurrentSearchLabel());						return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);		}				private function getCurrentSearchLabel() {			$current_label_search_id = $this->_images_pre_handler->getCurrentSearchLabelFromGetRequest();			if (!is_null($current_label_search_id)) {				return $this->_image_dao->getLabel($current_label_search_id)->getName();			}		}				private function renderNoResultsMessage() {			$no_result_message = new InformationMessage("Geen afbeeldingen gevonden");			return $no_result_message->render();		}				private function getSearchResults() {			$images = null;			if ($this->isSearchAction()) {				$images = $this->searchImages();			} else if ($this->isNoLabelsSearchAction()) {				$images = $this->_image_dao->getAllImagesWithoutLabel();			} else {				$images = $this->_image_dao->getAllImages();			}			return $this->toArray($images);		}				private function isSearchAction() {			return isset($_GET["action"]) && $_GET["action"] == "search";		}				private function isNoLabelsSearchAction() {			return isset($_GET["no_labels"]) && $_GET["no_labels"] == "true";		}				private function searchImages() {			$keyword = $this->_images_pre_handler->getCurrentSearchTitleFromGetRequest();			$filename = $this->_images_pre_handler->getCurrentSearchFilenameFromGetRequest();			$label = $this->_images_pre_handler->getCurrentSearchLabelFromGetRequest();			return $this->_image_dao->searchImages($keyword, $filename, $label);		}				private function toArray($images) {			$image_values = array();			foreach ($images as $image) {				$image_value = array();				$image_value["id"] = $image->getId();				$image_value["title"] = $image->getTitle();				$image_value["published"] = $image->isPublished();				$image_value["created_at"] = $image->getCreatedAt();				$image_value["thumb"] = $image->getThumbUrl();				$created_by = $image->getCreatedBy();				if (!is_null($created_by)) {					$image_value["created_by"] = $created_by->getUsername();				}				$image_values[] = $image_value;			}			return $image_values;		}		}	?>