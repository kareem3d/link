<?php namespace Kareem3d\Link;

use Illuminate\Support\Facades\App;
use Kareem3d\Eloquent\Model;

use Illuminate\Support\Facades\URL as LaravelURL;

class URL extends LaravelURL {

    /**
     * @var
     */
    protected static $temporaryUrls;

    /**
     * @param $_pageName
     * @param Model $model
     * @return mixed
     */
    public static function page( $_pageName, Model $model = null )
    {
        /**
         * @param \Kareem3d\Link\Link $link
         */
        $link = App::make('Kareem3d\Link\Link');

        if($model)
        {
            // Unique key for this url request
            $key = $_pageName . $model->getClass() . $model->id;

            // First check if it exists in the temporary urls to speed up the process
            if(isset(static::$temporaryUrls[$key])) return static::$temporaryUrls[$key];

            // Set up a query to get this url
            static::$temporaryUrls[$key] = $link->getUrlByPageAndModel( $_pageName, $model );

            $url = static::$temporaryUrls[$key];
        }
        else
        {
            // Set up a query to get this url
            $url = $link->getUrlByPage($_pageName);
        }

        return $url ?: '#';
    }

}