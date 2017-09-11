<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\HeadBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;

class HookListener
{

    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Modify the page object
     *
     * @param \PageModel $page
     * @param \LayoutModel $layout
     * @param \PageRegular $pageRegular
     */
    public function generatePage(\PageModel $page, \LayoutModel $layout, \PageRegular $pageRegular)
    {
        $pageRegular->Template->meta = implode("\n", \System::getContainer()->get('huh.head.tag_manager')->getTags());
    }

    /**
     * Modify the page layout
     *
     * @param \PageModel $page
     * @param \LayoutModel $layout
     * @param \PageRegular $pageRegular
     */
    public function getPageLayout(\PageModel $page, \LayoutModel $layout, \PageRegular $pageRegular)
    {
        global $objPage;

        \System::getContainer()->get('huh.head.tag.meta_charset')->setContent(\Config::get('characterSet'));
        \System::getContainer()->get('huh.head.tag.base')->setContent(\Environment::get('base'));

        // Fall back to the default title tag
        if ($layout->titleTag == '')
        {
            $layout->titleTag = '{{page::pageTitle}} - {{page::rootPageTitle}}';
        }

        \System::getContainer()->get('huh.head.tag.title')->setContent(\StringUtil::stripInsertTags(\Controller::replaceInsertTags($layout->titleTag)));

        \System::getContainer()->get('huh.head.tag.meta_language')->setContent(\System::getContainer()->get('translator')->getLocale());
        \System::getContainer()->get('huh.head.tag.meta_description')->setContent(str_replace(["\n", "\r", '"'], [' ', '', ''], $objPage->description));
        \System::getContainer()->get('huh.head.tag.meta_robots')->setContent($objPage->robots ?: 'index,follow');
    }
}