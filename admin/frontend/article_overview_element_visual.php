<?php

    defined('_ACCESS') or die;

    require_once CMS_ROOT . "frontend/frontend_visual.php";
    require_once CMS_ROOT . "utilities/date_utility.php";

    class ArticleOverviewElementFrontendVisual extends FrontendVisual {

        private $_template_engine;
        private $_article_overview_element;

        public function __construct($current_page, $article_overview_element) {
            parent::__construct($current_page);
            $this->_template_engine = TemplateEngine::getInstance();
            $this->_article_overview_element = $article_overview_element;
        }

        public function render() {
            $element_holder = $this->_article_overview_element->getElementHolder();
            $this->_template_engine->assign("title", $this->toHtml($this->_article_overview_element->getTitle(), $element_holder));
            $this->_template_engine->assign("articles", $this->getArticles());
            return $this->_template_engine->fetch(FRONTEND_TEMPLATE_DIR . "/" . $this->_article_overview_element->getTemplate()->getFileName());
        }

        private function getArticles() {
            $articles = $this->_article_overview_element->getArticles();
            $articles_arr = array();
            foreach ($articles as $article) {
                if (!$this->isPublished($article)) continue;
                $article_item = array();
                $article_item["id"] = $article->getId();
                $article_item["title"] = $article->getTitle();
                $article_item["url"] = $this->getArticleUrl($article);
                $article_item["description"] = $this->toHtml($article->getDescription(), $article);
                $article_item["publication_date"] = DateUtility::mysqlDateToString($article->getPublicationDate(), '-');
                $article_item["sort_date_in_past"] = strtotime($article->getSortDate()) < strtotime(date('Y-m-d H:i:s', strtotime('00:00:00')));
                $article_item["sort_date"] = DateUtility::mysqlDateToString($article->getSortDate(), '-');
                $article_item["image"] = $this->getArticleImage($article);
                $articles_arr[] = $article_item;
            }
            return $articles_arr;
        }

        private function getArticleImage($article) {
            $image = null;
            if (!is_null($article->getImage())) {
                $image = array();
                $image["title"] = $article->getImage()->getTitle();
                $image["url"] = $this->getImageUrl($article->getImage());
            }
            return $image;
        }

        private function isPublished($article) {
            return $article->isPublished() && strtotime($article->getPublicationDate()) < strtotime('now');
        }
    }

?>
