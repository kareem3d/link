<?php

use Illuminate\Support\Facades\App;
use Kareem3d\Eloquent\Model;

function url_to(Model $model)
{
    return App::make('Kareem3d\Link\Link')->getByModel( $model );
}