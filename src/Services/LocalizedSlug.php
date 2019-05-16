<?php

namespace ArinaSystems\LocalizedSlug\Services;

use Illuminate\Database\Eloquent\Model;

class LocalizedSlug
{
    /**
     * @var \ArinaSystems\LocalizedSlug\Services\LocalizedSlugOptions
     */
    protected $options;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Build slug attribute' value.
     *
     * @param  null|string|array<string> $forLocale
     * @param  boolean                   $force
     * @return string
     */
    public function build($forLocale = null, bool $force = false)
    {
        $locales = $forLocale ?? $this->options->getLocales();

        if (is_string($locales)) {
            $locales = (array) $locales;
        }

        $slug = $this->getCurrentSlug('array');

        foreach ($locales as $locale) {
            app()->setLocale($locale);

            if (!is_null($slug[$locale] ?? null) && !$force) {
                continue;
            }

            if ($this->model->{$this->options->getLocaleSourceField($locale)}) {
                $slug[$locale] = $this->generateFrom($this->options->getLocaleSourceField($locale));
            }
        }

        return json_encode($slug);
    }

    /**
     * Generate slug from given field.
     *
     * @param  string   $attribute
     * @return string
     */
    public function generateFrom($attribute)
    {
        $string = $this->model->{$attribute};

        return $this->getSlugBySlugger($string);
    }

    /**
     * Generate slug on create event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function generateSlugOnCreate(Model $model)
    {
        $this->setOptions($model);

        if (!$this->options->isOnCreatingEnabled()) {
            return;
        }

        $this->addSlug();
    }

    /**
     * Generate slug on update event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function generateSlugOnUpdate(Model $model)
    {
        $this->setOptions($model);

        $force = $this->options->isOnUpdatingEnabled();

        $this->addSlug(null, $force);
    }

    /**
     * Adding slug field to model.
     *
     * @param  null|string|array<string> $forLocale
     * @param  boolean                   $force
     * @return void
     */
    protected function addSlug($forLocale = null, bool $force = false)
    {
        $this->model->{$this->options->getSlugField()} = $this->build($forLocale, $force);
    }

    /**
     * Convert string into slug by slugger class.
     *
     * @param  string     $string
     * @param  array|null $options
     * @return string
     */
    public function getSlugBySlugger(string $string, array $options = null)
    {
        if (is_null($options)) {
            $options = $this->options->getSluggerOptions();
        }

        return (new Slugger())->slug($string, $options);
    }

    /**
     * Filling the given model options if the LocalizedSlug method exists.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected function setOptions(Model $model)
    {
        $this->model = $model;
        $this->options = new LocalizedSlugOptions($model);
    }

    /**
     * @param $type
     */
    public function getCurrentSlug($type = 'array')
    {
        if ($type != 'json' && $type != 'array') {
            $locale = $type;
            unset($type);
        }

        $slug = $this->model->getOriginal($this->options->getSlugField());

        if (isset($locale) || (isset($type) && $type != 'json')) {
            $slug = json_decode($slug, true);
        }

        if (isset($locale)) {
            return $slug[$locale] ?? null;
        }

        return $slug;
    }
}
