<?php namespace Kareem3d\Link;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Kareem3d\Eloquent\Model;
use Kareem3d\Templating\Page;

class Link extends Model {

    /**
     * @var string
     */
    protected $table = 'ka_links';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected static $dontDuplicate = array('url');

    /**
     * @param $_pageName
     * @param Model $model
     * @return Link
     */
    public static function getByPageAndModel( $_pageName, Model $model )
    {
        return static::getByPageQuery($_pageName, static::getByModelQuery($model))->first();
    }

    /**
     * @param $_pageName
     * @return Link|null
     */
    public static function getByPage( $_pageName )
    {
        return static::getByPageQuery($_pageName)->first();
    }

    /**
     * @param Model $model
     * @param null $query
     * @return Link|null
     */
    public static function getByModel( Model $model)
    {
        return static::getByModelQuery($model)->first();
    }

    /**
     * @param \Kareem3d\Eloquent\Model $model
     * @param $query
     * @return Builder
     */
    public static function getByModelQuery( Model $model, $query = null )
    {
        $query = $query ?: static::query();

        return $query->where('linkable_type', $model->getClass())
                     ->where('linkable_id', $model->id);
    }

    /**
     * @param $_pageName
     * @param $query
     * @return Builder
     */
    public static function getByPageQuery( $_pageName, $query = null )
    {
        $query = $query ?: static::query();

        return $query->where('page_name', $_pageName);
    }

    /**
     * @param $url
     * @return \Kareem3d\URL\URL
     */
    public static function getByUrl( $url )
    {
        // Check trimed and untrimed url to be sure
        $trimedUrl   = trim($url, '/');
        $untrimedUrl = $url . '/';

        return static::where('url', $trimedUrl)->orWhere('url', $untrimedUrl)->first();
    }

    /**
     * @param Model $model
     */
    public function attachTo( Model $model )
    {
        $this->linkable_type = $model->getClass();
        $this->linkable_id   = $model->id;

        $this->save();
    }

    /**
     * @param $url
     * @return bool
     */
    public function samePath( $url )
    {
        $url = parse_url($url);

        return $this->path === $url['path'];
    }

    /**
     * @param $url
     * @return bool
     */
    public function sameHost( $url )
    {
        $url = parse_url($url);

        return $this->host === $url['host'];
    }

    /**
     * @param $url
     * @return bool
     */
    public function sameUrl( $url )
    {
        return $this->url === $url;
    }

    /**
     * @param $path
     */
    public function setRelativeUrlAttribute( $path )
    {
        $this->url = URL::to('') . '/' . trim($this->removeWebsiteUrl($path), '/');
    }

    /**
     * @return string
     */
    public function getRelativeUrlAttribute()
    {
        return trim($this->removeWebsiteUrl($this->url), '/');
    }

    /**
     * @param $url
     * @return mixed
     */
    public function removeWebsiteUrl( $url )
    {
        return str_replace(URL::to(''), '', $url);
    }

    /**
     * @param $path
     * @return void
     */
    public function setPathAttribute( $path )
    {
        $this->url = rtrim($this->host, '/') . '/' . trim($path, '/');
    }

    /**
     * @param $host
     */
    public function setHostAttribute( $host )
    {
        $this->url = rtrim($host, '/') . '/' . trim($this->path);
    }

    /**
     * @return string
     */
    public function getPathAttribute()
    {
        $url = parse_url($this->relativeUrl);

        return $url['path'];
    }

    /**
     * @return mixed
     */
    public function getHostAttribute()
    {
        $url = parse_url($this->url);

        return $url['host'];
    }

    /**
     * @return
     */
    public function getPageAttribute()
    {
        // First try to get it by url
        if($page = App::make('Kareem3d\Templating\PageRepository')->find($this->url)) return $page;

        // Not found then get by page name..
        return App::make('Kareem3d\Templating\PageRepository')->find($this->page_name);
    }

    /**
     * @param Page|string $_page
     */
    public function setPageAttribute( $_page )
    {
        $this->attributes['page_name'] = $_page instanceof Page ? $_page->name : $_page;
    }

    /**
     * Synchronize arguments from the given array
     *
     * @param array $arguments
     * @return void
     */
    public function setArgumentsAttribute( array $arguments )
    {
        // First make sure it's saved if it doesn't exists
        ! $this->exists and $this->save();

        $this->arguments()->delete();

        foreach($arguments as $key => $value)
        {
            $this->arguments()->save(
                App::make('Kareem3d\Link\Argument')->newInstance(array('key' => $key, 'value' => $value))
            );
        }
    }

    /**
     * Get arguments as array
     *
     * @return array
     */
    public function getArgumentsAttribute()
    {
        $rows = array();

        foreach($this->arguments()->get() as $argument)
        {
            $rows[$argument->key] = $argument->value;
        }

        return $rows;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function arguments()
    {
        return $this->hasMany(App::make('Kareem3d\Link\Argument')->getClass());
    }

    /**
     * @param $key
     * @return string
     */
    public function argument( $key )
    {
        return (string) $this->arguments()->where('key', $key)->first();
    }

    /**
     * @return string
     */
    public function getArgumentsString()
    {
        return http_build_query($this->getArgumentsAttribute());
    }

    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->url;
    }
}