<?php

use Illuminate\Support\Facades\App;
use Kareem3d\Eloquent\Model;

function url_to($_pageName, Model $model = null)
{
    /**
     * @param \Kareem3d\Link\Link $link
     */
    $link = App::make('Kareem3d\Link\Link');

    return $model ? $link->getByPageAndModel( $_pageName, $model ) : $link->getByPage($_pageName);
}