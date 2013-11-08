<?php namespace Kareem3d\Link;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Kareem3d\Templating\Page;
use Kareem3d\Templating\PageRepository;

class DynamicRouter {

    /**
     * @var array
     */
    protected $routes = array();

    /**
     * @var Link
     */
    protected $links;

    /**
     * @param Link $links
     */
    public function __construct(Link $links)
    {
        $this->links = $links;
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
        $link = $this->links->getByUrl(Request::url());

        if($link and $link->page)
        {
            // If no route defined for this page then create new route to only print the page attached to it..
            if(! $route = $this->getRouteFor($link->page))
            {
                $route = DynamicRoute::factory(function() use ($link)
                {
                    // Print the page given it the link arguments
                    return $link->page->printMe(array('arguments' => $link->arguments));
                });
            }

            // Bind current link and current route for future usage
            App::instance('CurrentLink', $link);
            App::instance('CurrentPage', $link->page);
            App::instance('CurrentRoute', $route);

            $route->routeToLink($link);
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