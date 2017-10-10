<?php

namespace Frontend\Modules\Faq\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Faq\Engine\Model as FrontendFaqModel;

/**
 * This is the category-action
 */
class Category extends FrontendBaseBlock
{
    /**
     * @var array
     */
    private $questions;

    /**
     * @var array
     */
    private $record;

    public function execute(): void
    {
        parent::execute();

        $this->template->assignGlobal('hideContentTitle', true);
        $this->getData();
        $this->loadTemplate();
        $this->parse();
    }

    private function getData(): void
    {
        // validate incoming parameters
        if ($this->url->getParameter(1) === null) {
            $this->redirect(FrontendNavigation::getUrl(FrontendModel::ERROR_PAGE_ID));
        }

        // get by URL
        $this->record = FrontendFaqModel::getCategory($this->url->getParameter(1));

        // anything found?
        if (empty($this->record)) {
            $this->redirect(FrontendNavigation::getUrl(FrontendModel::ERROR_PAGE_ID));
        }

        $this->record['full_url'] = FrontendNavigation::getUrlForBlock('Faq', 'Category') . '/' . $this->record['url'];
        $this->questions = FrontendFaqModel::getAllForCategory($this->record['id']);
    }

    private function parse(): void
    {
        $this->breadcrumb->addElement($this->record['title']);
        $this->header->setPageTitle($this->record['title']);

        // assign category and questions
        $this->template->assign('category', $this->record);
        $this->template->assign('questions', $this->questions);
    }
}
