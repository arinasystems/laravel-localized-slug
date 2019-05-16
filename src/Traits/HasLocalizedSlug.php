<?php

namespace ArinaSystems\LocalizedSlug\Traits;

use ArinaSystems\LocalizedSlug\Services\LocalizedSlugOptions;
use ArinaSystems\LocalizedSlug\Observers\LocalizedSlugObserver;

trait HasLocalizedSlug
{
    /**
     * Boot Eloquent HasLocalizedSlug trait for the model.
     *
     * @return void
     */
    public static function bootHasLocalizedSlug()
    {
        static::observe(app(LocalizedSlugObserver::class));
    }

    /**
     * Translatable slug model options
     *
     * @return array
     */
    public function localizedSlug(): array
    {
        return [];
    }

    /**
     * Get model' slug.
     *
     * @return string|null
     */
    public function getSlug($locale = null)
    {
        $slugField = LocalizedSlugOptions::get('slugField', $this);

        if (is_null($locale)) {
            $locale = config('app.locale');
        }

        $slug = $this->getOriginal($slugField);

        if (is_null($slug)) {
            return;
        }

        $slug = json_decode($slug)->{$locale} ?? null;

        return $slug;
    }

    /**
     * Find a model by its slug.
     *
     * @param  string                                                                              $slug
     * @param  array                                                                               $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|null
     */
    public static function findBySlug(string $slug, string $locale = null, array $columns = ['*'])
    {
        if (is_null($locale)) {
            $locale = config('app.locale');
        }

        $slugField = LocalizedSlugOptions::get('slugField', new static());

        return static::where("{$slugField}->{$locale}", $slug)->first($columns);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  string                                                                         $slug
     * @param  array                                                                          $columns
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    public static function findBySlugOrFail(string $slug, string $locale = null, array $columns = ['*'])
    {
        if (is_null($locale)) {
            $locale = config('app.locale');
        }

        $slugField = LocalizedSlugOptions::get('slugField', new static());

        return static::where("{$slugField}->{$locale}", $slug)->firstOrFail($columns);
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        if (LocalizedSlugOptions::get('route_binding', $this)) {
            return static::findBySlug($value);
        }

        return parent::resolveRouteBinding($value);
    }
}
