<?php	// No direct access	defined("_ACCESS") or die;		require_once "view/request_handlers/module_request_handler.php";	require_once "database/dao/authorization_dao.php";	include_once "libraries/utilities/password_utility.php";	require_once "libraries/system/notifications.php";	require_once "libraries/validators/form_validator.php";	require_once "libraries/handlers/form_handler.php";		class AuthorizationPreHandler extends ModuleRequestHandler {		private $_authorization_dao;		private $_current_user;				public function __construct() {			$this->_authorization_dao = AuthorizationDao::getInstance();		}			public function handleGet() {			$this->getCurrentUserFromGetRequest();		}				public function handlePost() {			$this->_current_user = $this->getCurrentUserFromPostRequest();			if ($this->isUpdateUserAction())				$this->updateUser();			if ($this->isAddUserAction())			    $this->addUser();			if ($this->isDeleteUserAction())				$this->deleteUser();		}				public function getCurrentUser() {			return $this->_current_user;		}				private function addUser() {				$new_user = $this->_authorization_dao->createUser();			$password = PasswordUtility::generatePassword();			$new_user->setUuid(uniqid());			$new_user->setPassword($password);			$this->_authorization_dao->updateUser($new_user);			Notifications::setSuccessMessage("Gebruiker aangemaakt, met wachtwoord: " . $password);			header("Location: /admin/index.php?user=" . $new_user->getId());			exit();		}				private function deleteUser() {			$this->_authorization_dao->deleteUser($this->_current_user->getId());			Notifications::setSuccessMessage("Gebruiker succesvol verwijderd");			header("Location: /admin/index.php");			exit();		}				private function updateUser() {			global $errors;						$username = FormValidator::checkEmpty("user_username", "Gebruikersnaam is verplicht");			$check_user = $this->_authorization_dao->getUser($username);			if (!is_null($check_user) && ($check_user->getId() != $this->_current_user->getId())) {				$errors["user_username_error"] = "Er bestaat al een gebruiker met deze gebruikersnaam";;			}						$first_name = FormValidator::checkEmpty("user_firstname", "Voornaam is verplicht");			$last_name = FormValidator::checkEmpty("user_lastname", "Voornaam is verplicht");			$prefix = FormHandler::getFieldValue("user_prefix");			$email = FormValidator::checkEmailAddress("user_email", false, "Email adres is verplicht");						$password1 = FormHandler::getFieldValue("user_new_password_first");			$password2 = FormHandler::getFieldValue("user_new_password_second");			$password_value = "";			if (!is_null($password1) && $password1 != "" || !is_null($password2) && $password2 != "") {				$password_value = FormValidator::checkPassword("user_new_password_first", "user_new_password_second");			}						if (count($errors) == 0) {				$this->_current_user->setUsername($username);				$this->_current_user->setFirstName($first_name);				$this->_current_user->setLastName($last_name);				$this->_current_user->setPrefix($prefix);				$this->_current_user->setEmailAddress($email);				if ($password_value != "") {					$this->_current_user->setPassword($password_value);				}				$this->_authorization_dao->updateUser($this->_current_user);				Notifications::setSuccessMessage("Gebruiker succesvol opgeslagen");			} else {				Notifications::setFailedMessage("Gebruiker niet opgeslagen, verwerk de fouten");			}		}				private function getCurrentUserFromGetRequest() {			if (isset($_GET["user"])) {				$this->_current_user = $this->getUserFromDatabase($_GET["user"]);			} else {				$this->_current_user = $this->_authorization_dao->getUser($_SESSION["username"]);			}		}				private function getCurrentUserFromPostRequest() {			return $this->getUserFromDatabase($_POST["user_id"]);		}				private function getUserFromDatabase($user_id) {			return $this->_authorization_dao->getUserById($user_id);		}					private function isUpdateUserAction() {			return isset($_POST["action"]) && $_POST["action"] == "update_user";		}					private function isAddUserAction() {			return isset($_POST["action"]) && $_POST["action"] == "add_user";		}					private function isDeleteUserAction() {			return isset($_POST["action"]) && $_POST["action"] == "delete_user";		}			}	?>