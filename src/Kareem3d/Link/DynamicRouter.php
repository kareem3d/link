<?php namespace Kareem3d\Link;

use Illuminate\Support\Facades\App;
use Kareem3d\Templating\Page;

class DynamicRouter {

    /**
     * @var array
     */
    protected $routes = array();

    /**
     * Current currentLink
     *
     * @var Link
     */
    protected $currentLink;

    /**
     * @var DynamicRouter
     */
    protected static $instance;

    /**
     * @param Link $currentLink
     */
    private function __construct(Link $currentLink = null)
    {
        $this->currentLink = $currentLink;
    }

    /**
     * @return DynamicRouter
     */
    public static function instance(Link $currentLink = null)
    {
        if(! static::$instance)
        {
            static::$instance = new static($currentLink);
        }

        return static::$instance;
    }

    /**
     * @param  Link $currentLink
     * @return void
     */
    public function setCurrentLink(Link $currentLink)
    {
        $this->currentLink = $currentLink;
    }

    /**
     * @return Link
     */
    public function getCurrentLink()
    {
        return $this->currentLink;
    }

    /**
     * @param $_pageName
     * @param $_route
     */
    public function attach( $_pageName, $_route )
    {
        if($_route instanceof DynamicRoute)
        {
            $this->routes[$_pageName] = $_route;
        }

        else
        {
            $this->routes[$_pageName] = DynamicRoute::factory($_route);
        }
    }

    /**
     * Launch the Dynamic route class
     */
    public function launch()
    {
        $currentLink = $this->getCurrentLink();

        // If current currentLink is defined and there's a page linking to it...
        if($currentLink && $currentLink->page)
        {
            // If no route defined for this page then create new route to only print the page attached to it..
            if(! $route = $this->getRouteFor($currentLink->page))
            {
                $route = DynamicRoute::factory(function() use($currentLink)
                {
                    // Print the page given it the currentLink arguments
                    return $currentLink->page->printMe();
                });
            }

            $route->routeToLink($currentLink);
        }
    }

    /**
     * @param $page
     * @return bool
     */
    public function hasRouteFor( Page $page )
    {
        return isset($this->routes[$page->identifier]);
    }

    /**
     * @param \Kareem3d\Templating\Page $page
     * @return DynamicRoute
     */
    public function getRouteFor( Page $page )
    {
        return $this->hasRouteFor($page) ? $this->routes[$page->identifier] : null;
    }

}