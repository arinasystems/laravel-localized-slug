<?php

namespace ArinaSystems\LocalizedSlug\Observers;

use Illuminate\Database\Eloquent\Model;
use ArinaSystems\LocalizedSlug\Services\LocalizedSlug;

class LocalizedSlugObserver
{
    /**
     * @var \ArinaSystems\LocalizedSlug\Services\LocalizedSlug
     */
    protected $service;

    /**
     * @param LocalizedSlug $service
     */
    public function __construct(LocalizedSlug $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the model "creating" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function creating(Model $model)
    {
        $this->service->generateSlugOnCreate($model);
    }

    /**
     * Handle the model "saving" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function saving(Model $model)
    {
        $this->service->generateSlugOnUpdate($model);
    }
}
