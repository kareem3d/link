<?php namespace Kareem3d\Link;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Kareem3d\Templating\Page;

class DynamicController extends Controller {

    /**
     * @return mixed
     */
    public function callCurrentLink()
    {
        $arguments = $this->link()->arguments;

        return call_user_func_array(array($this, $this->route()->getAction()), (array) $arguments);
    }

    /**
     * @return Link
     */
    public function link()
    {
        return App::make('CurrentLink');
    }

    /**
     * @return DynamicRoute
     */
    public function route()
    {
        return App::make('CurrentRoute');
    }

    /**
     * @return Page
     */
    public function page()
    {
        return $this->link()->page;
    }
}