<?php namespace Kareem3d\Link;

use Illuminate\Support\Facades\App;
use Kareem3d\Eloquent\Model;

class Argument extends Model {

    const TYPE_STRING = 0;
    const TYPE_MODEL = 1;

    /**
     * @var string
     */
    protected $table = 'ka_arguments';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param \Kareem3d\Eloquent\Model $model
     * @return string
     */
    public static function modelToString( Model $model )
    {
        return "{$model->getClass()}-{$model->id}";
    }

    /**
     * @param $string
     * @return Model|null
     */
    public static function stringToModel( $string )
    {
        $pieces = explode('-', $string);

        if($pieces == 2)
        {
            $className = $pieces[0];
            $id        = $pieces[1];

            $model = App::make($className);

            return $model ? $model->find($id) : null;
        }


        return $string;
    }

    /**
     * @param string|Model $value
     */
    public function setValueAttribute( $value )
    {
        if($value instanceof Model)
        {
            $this->attributes['value'] = static::modelToString($value);

            $this->attributes['type']  = static::TYPE_MODEL;
        }

        else
        {
            $this->attributes['value'] = $value;

            $this->type = static::TYPE_STRING;
        }
    }

    /**
     * @param $value
     * @return string|Model
     */
    public function getValueAttribute( $value )
    {
        if($this->isModelValue())
        {
            $this->attributes['value'] = $value instanceof Model ? $value : static::stringToModel( $value );
        }

        return $this->attributes['value'];
    }

    /**
     * @return bool
     */
    public function isModelValue()
    {
        return $this->type == static::TYPE_MODEL;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function link()
    {
        return $this->belongsTo(App::make('Kareem3d\Link\Link')->getClass());
    }

    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->value;
    }
}