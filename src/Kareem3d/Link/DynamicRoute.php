<?php namespace Kareem3d\Link;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class DynamicRoute {

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var \Closure|null
     */
    protected $closure;

    /**
     * @param string $controller
     * @param string $action
     * @param callable $closure
     */
    public function __construct($controller = '', $action = '', \Closure $closure = null)
    {
        $this->controller = $controller;
        $this->action     = $action;
        $this->closure    = $closure;
    }

    /**
     * @param mixed $_route
     * @return \Kareem3d\Link\DynamicRoute
     */
    public static function factory( $_route )
    {
        // If given route is string
        if(is_string($_route))
        {
            if(strpos($_route, '@') !== false)
            {
                $pieces = explode('@', $_route);

                return new DynamicRoute($pieces[0], $pieces[1]);
            }
        }

        // If give route is closure
        elseif( $_route instanceof \Closure )
        {
            return new DynamicRoute('', '', $_route);
        }
    }

    /**
     * @param Link $link
     * @throws DynamicRouteException
     */
    public function routeToLink( Link $link )
    {
        if($this->closure)
        {
            Route::get($link->path, $this->closure);
        }

        elseif(is_subclass_of($this->getController(), 'Kareem3d\Link\DynamicController'))
        {
            Route::get($link->path, $this->getController().'@callCurrentLink');
        }

        else
        {
            throw new DynamicRouteException("Extend DynamicController if you want to use dynamic routing");
        }
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param callable $closure
     */
    public function setClosure( \Closure $closure )
    {
        $this->closure = $closure;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return callable|null
     */
    public function getClosure()
    {
        return $this->closure;
    }
}