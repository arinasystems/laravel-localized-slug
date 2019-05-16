<?php

namespace ArinaSystems\LocalizedSlug\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use ArinaSystems\LocalizedSlug\Traits\HasLocalizedSlug;

class Blog extends Model
{
    use HasLocalizedSlug;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'en_title',
        'es_title',
        'en_body',
        'es_body',
    ];

    /**
     * Translatable slug model options.
     *
     * @return array
     */
    public function localizedSlug()
    {
        return [
            'slugField' => 'slug',
            'locales'   => [
                'en' => 'en_title',
                'es' => 'es_title',
            ],
        ];
    }

    /**
     * Get model' slug attribute
     *
     * @param  mixed  $value
     * @return string
     */
    public function getSlugAttribute($value)
    {
        return $this->getSlug();
    }
}
