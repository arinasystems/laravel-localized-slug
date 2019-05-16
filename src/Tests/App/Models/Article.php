<?php

namespace ArinaSystems\LocalizedSlug\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use ArinaSystems\LocalizedSlug\Traits\HasLocalizedSlug;

class Article extends Model
{
    use HasLocalizedSlug;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'title' => 'array',
        'body'  => 'array',
    ];

    /**
     * Translatable slug model options.
     *
     * @return array
     */
    public function localizedSlug()
    {
        return [
            'source'        => 'title',
            'slugField'     => 'slug',
            'route_binding' => true,
            'locales'       => [
                'ar' => null,
                'en' => null,
            ],
        ];
    }

    /**
     * Get model' slug attribute
     *
     * @param  mixed    $value
     * @return string
     */
    public function getSlugAttribute($value)
    {
        return $this->getSlug();
    }

    /**
     * Get the title attribute
     *
     * @param  mixed    $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        $locale = config('app.locale');

        return json_decode($value)->{$locale};
    }
}
