<?php namespace Kareem3d\Link;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Kareem3d\Templating\XMLFactory;

class Generator {

    /**
     * @var Link
     */
    protected $links;

    /**
     * @var DynamicRouter
     */
    protected $dynamicRouter;

    /**
     * @var \Kareem3d\Templating\XMLFactory
     */
    protected $xmlFactory;

    /**
     * @param Link $links
     * @param XMLFactory $xmlFactory
     */
    public function __construct( Link $links, XMLFactory $xmlFactory )
    {
        $this->links = $links;
        $this->xmlFactory = $xmlFactory;
    }

    /**
     * @return DynamicRouter
     */
    public function dynamicRouter()
    {
        $currentLink = $this->links->getByUrl(Request::url());

        if($currentLink)
        {
            App::instance('CurrentLink', $currentLink);

            // Push this page to repository
            $page = $this->xmlFactory->pushPageToRepositories($currentLink->page_name, $currentLink->url);

            $page->share(array('link' => $currentLink));
        }

        // Return singleton instance of dynamic router giving current link
        return DynamicRouter::instance($currentLink);
    }

    /**
     * Seed generator
     */
    public function seed()
    {
        $pages = $this->xmlFactory->generatePages();

        $this->links->query()->delete();

        foreach($pages as $page)
        {
            $this->links->create(array(
                'relative_url' => $page->getIdentifier(),
                'page_name'    => $page->getIdentifier()
            ));
        }
    }

}