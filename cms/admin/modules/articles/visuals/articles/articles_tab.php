<?php	// No direct access	defined('_ACCESS') or die;	require_once "modules/articles/visuals/articles/article_editor.php";	require_once "modules/articles/visuals/articles/articles_list.php";	require_once "modules/articles/visuals/articles/articles_search.php";		class ArticleTab extends Visual {			private static $TEMPLATE = "articles/articles/root.tpl";			private $_template_engine;		private $_current_article;			public function __construct($current_article) {			$this->_current_article = $current_article;			$this->_template_engine = TemplateEngine::getInstance();		}			public function render() {			$this->_template_engine->assign("search", $this->renderArticlesSearch());			if (!is_null($this->_current_article)) {				$this->_template_engine->assign("editor", $this->renderArticleEditor());			} else {				$this->_template_engine->assign("list", $this->renderArticlesList());			}					return $this->_template_engine->fetch("modules/" . self::$TEMPLATE);		}				private function renderArticlesSearch() {			$articles_search_field = new ArticlesSearch($this->_current_article);			return $articles_search_field->render();		}				private function renderArticlesList() {			$articles_list = new ArticlesList();			return $articles_list->render();		}				private function renderArticleEditor() {			$article_editor = new ArticleEditor($this->_current_article);			return $article_editor->render();		}		}	?>