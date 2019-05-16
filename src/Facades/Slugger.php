<?php

namespace ArinaSystems\LocalizedSlug\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Slugger
 *
 * @method string slug()
 * @method string generate()
 * @method self fromString(string $string)
 * @method self setOptions(array $options)
 * @method self setSeparator(string $separator)
 * @method self setRegex(string $regex)
 * @method self uniqueWithin(Closure $callback)
 */
class Slugger extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'slugger';
    }
}
