<?php	// No direct access	defined('_ACCESS') or die;	require_once CMS_ROOT . "view/views/information_message.php";		class ArticlesList extends Visual {			private static $TEMPLATE = "articles/articles/list.tpl";		private static $SEARCH_TERM_KEY = "s_term";		private static $SEARCH_QUERY_KEY = "search_query";			private $_template_engine;		private $_article_dao;			public function __construct() {			$this->_article_dao = ArticleDao::getInstance();			$this->_template_engine = TemplateEngine::getInstance();		}			public function render() {			$this->_template_engine->assign("search_results", $this->renderSearchResults());			$this->_template_engine->assign("search_query", isset($_GET[self::$SEARCH_QUERY_KEY]) ? $_GET[self::$SEARCH_QUERY_KEY] : null);			$this->_template_engine->assign("search_term", (isset($_GET[self::$SEARCH_TERM_KEY]) && $_GET[self::$SEARCH_TERM_KEY] != "") ? $this->_article_dao->getTerm($_GET[self::$SEARCH_TERM_KEY]) : null);			$this->_template_engine->assign("no_results_message", $this->renderNoResultsMessage());					return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);		}				private function renderSearchResults() {			$search_results = array();					$articles = $this->getSearchResults();			foreach($articles as $article) {				$search_result = array();				$search_result["id"] = $article->getId();				$search_result["title"] = $article->getTitle();								$user = $article->getCreatedBy();				$search_result["created_by"] = is_null($user) ? null : $user->getUsername();				$search_result["created_at"] = $article->getCreatedAt();				$search_result["published"] = $article->isPublished();								$search_results[] = $search_result;			}			return $search_results;		}				private function getSearchResults() {			if (isset($_GET['action']) && $_GET['action'] == 'search') {				$keyword = null;				if (isset($_GET[self::$SEARCH_QUERY_KEY]) && $_GET[self::$SEARCH_QUERY_KEY] != '') {					$keyword = $_GET[self::$SEARCH_QUERY_KEY];				}				$term = null;				if (isset($_GET[self::$SEARCH_TERM_KEY]) && $_GET[self::$SEARCH_TERM_KEY] != '') {					$term = $_GET[self::$SEARCH_TERM_KEY];				}								$articles = $this->_article_dao->searchArticles($keyword, $term);			} else {				$articles = $this->_article_dao->getAllArticles();			}			return $articles;		}				private function renderNoResultsMessage() {			$message = new InformationMessage("Geen artikelen gevonden.");			return $message->render();		}		}	?>