<?php

namespace ArinaSystems\LocalizedSlug\Services;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class LocalizedSlugOptions
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->mergeModelOptions();
    }

    /**
     * Check if on creating option is enabled.
     *
     * @return boolean
     */
    public function isOnCreatingEnabled()
    {
        if (!is_bool($this->options['onCreate'])) {
            throw new \Exception("Please make sure your localized slug' (onCreate) option is true or false.");
        }

        return $this->options['onCreate'] == true;
    }

    /**
     * Check if on updating option is enabled.
     *
     * @return boolean
     */
    public function isOnUpdatingEnabled()
    {
        if (!is_bool($this->options['onUpdate'])) {
            throw new \Exception("Please make sure your localized slug' (onUpdate) option is true or false.");
        }

        return $this->options['onUpdate'] == true;
    }

    /**
     * Retrieve source field name.
     *
     * @return string
     */
    public function getSourceField()
    {
        return $this->options['source'];
    }

    /**
     * Mearging the given model options if the LocalizedSlug method exists.
     *
     * @return void
     */
    protected function mergeModelOptions()
    {
        $model_options = method_exists($this->model, 'localizedSlug')
        ? $this->model->localizedSlug()
        : [];

        $this->options = array_merge(config('localized-slug'), $model_options);
    }

    /**
     * Retrieve all slug' locales.
     *
     * @return array
     */
    public function getLocales()
    {
        return array_keys($this->options['locales']);
    }

    /**
     * Retrieve locale source field name
     *
     * @return string
     */
    public function getLocaleSourceField($locale)
    {
        if (!is_null($localeSource = $this->options['locales'][$locale])) {
            return $localeSource;
        }

        return $this->getSourceField();
    }

    /**
     * Retrieve slugger options.
     *
     * @return array
     */
    public function getSluggerOptions()
    {
        $is_unique = ['is_unique' => function ($slug) {
            return is_null($this->model->findBySlug($slug));
        }];

        return array_merge($this->options['slugger'], $is_unique);
    }

    /**
     * Retrieve slug field name.
     *
     * @return string
     */
    public function getSlugField()
    {
        return $this->options['slugField'];
    }

    /**
     * Get any option value by key.
     *
     * @param  $key
     * @param  $model
     * @return string|array
     */
    public static function get($key, $model)
    {
        $options = (new static($model))->options;
        return Arr::get($options, $key);
    }
}
