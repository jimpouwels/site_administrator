<?php	// No direct access	defined("_ACCESS") or die;		require_once FRONTEND_REQUEST . "database/dao/image_dao.php";	require_once FRONTEND_REQUEST . "core/data/settings.php";		class ImportPreHandler extends ModuleRequestHandler {		private static $ZIP_FILE_ID = "import_zip_file";		private $_image_dao;			public function __construct() {			$this->_image_dao = ImageDao::getInstance();		}			public function handleGet() {				}				public function handlePost() {			if (isset($_FILES[self::$ZIP_FILE_ID]) && is_uploaded_file($_FILES[self::$ZIP_FILE_ID]["tmp_name"])) {				$number_imported = 0;				$zip = zip_open($_FILES["import_zip_file"]["tmp_name"]);				if ($zip) {					$upload_dir = Settings::find()->getUploadDir();									while ($zip_entry = zip_read($zip)) {											$file_entry_name = zip_entry_name($zip_entry);						$splits = explode(".", $file_entry_name);						$extension = $splits[count($splits) - 1];												if ($extension == "JPEG" || $extension == "jpeg" || $extension == "JPG" || $extension == "jpg" 							|| $extension == "GIF" || $extension == "gif" || $extension == "PNG" || $extension == "png") {							$new_image = NULL;							$new_image = $this->_image_dao->createImage();							$new_image->setTitle($file_entry_name);							$new_image->setPublished(0);							$new_file_name = "UPLIMG-00" . $new_image->getId() . "00" . $file_entry_name;														$zip_filesize = zip_entry_filesize($zip_entry);														if (empty($zip_filesize)) continue;							$file_contents = zip_entry_read($zip_entry, $zip_filesize);							$new_file = fopen($upload_dir . "/" . $new_file_name, "w");							fwrite($new_file, $file_contents);							fclose($new_file);							zip_entry_close($zip_entry);													$new_image->setFileName($new_file_name);											$thumb_file_name = "THUMB-" . $new_file_name;													FileUtility::saveThumb($new_file_name, $upload_dir, $thumb_file_name, 50, 50);													$new_image->setThumbFileName($thumb_file_name);														// save the label							if (isset($_POST["import_label"]) && $_POST["import_label"] != "") {								$this->_image_dao->addLabelToImage($_POST["import_label"], $new_image);							}														$this->_image_dao->updateImage($new_image);														$number_imported += 1;						}					}				}				zip_close($zip);								if ($number_imported == 0) {					Notifications::setFailedMessage("Geen afbeeldingen gevonden in ZIP bestand");				} else {					Notifications::setSuccessMessage($number_imported . " afbeeldingen geimporteerd");				}			}		}			}	?>