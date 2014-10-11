<?php		defined('_ACCESS') or die;			class ArticlesSearch extends Visual {			private static $TEMPLATE = "articles/articles/search.tpl";			private $_template_engine;		private $_current_article;		private $_article_dao;			public function __construct($current_article) {			$this->_current_article = $current_article;			$this->_template_engine = TemplateEngine::getInstance();			$this->_article_dao = ArticleDao::getInstance();		}			public function render() {			$this->_template_engine->assign("search_query_field", $this->renderSearchQueryField());			$this->_template_engine->assign("term_query_field", $this->renderTermQueryField());			$this->_template_engine->assign("search_button", $this->renderSearchButton());					return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);		}				private function renderSearchQueryField() {			$default_search_value = null;			if (isset($_GET['search_query'])) {				$default_search_value = $_GET['search_query'];			}			$search_query_field = new TextField("search_query", "Zoekterm", $default_search_value, false, false, null);			return $search_query_field->render();		}				private function renderTermQueryField() {			$term_options = array();			array_push($term_options, array('name' => '&gt; Selecteer', 'value' => NULL));			foreach ($this->_article_dao->getAllTerms() as $term) {				array_push($term_options, array('name' => $term->getName(), 'value' => $term->getId()));			}			$term = null;			if (isset($_GET['s_term']) && $_GET['s_term'] != '') {				$term = $_GET['s_term'];			}			$term_query_field = new Pulldown("s_term", "Term", $term, $term_options, false, "");			return $term_query_field->render();		}				private function renderSearchButton() {			$search_button = new Button("", "Zoeken", "document.getElementById('article_search').submit(); return false;");			return $search_button->render();		}		}	?>