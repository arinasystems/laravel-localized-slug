<?php

namespace ArinaSystems\LocalizedSlug\Services;

use Closure;

class Slugger
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $options = [
        'unique'     => false,
        'is_unique'  => null,
        'separator'  => '-',
        'lowercase'  => true,
        'strip_tags' => true,
        'regex'      => null,
    ];

    /**
     * Create a new slugger service instance.
     *
     * @return void
     */
    public function __construct($options = [])
    {
        $this->mergeOptions($options);
    }

    /**
     * Static function to generate a slug from given string.
     *
     * @param  string     $string
     * @param  array|null $options
     * @return string
     */
    public function slug(string $string, array $options = null)
    {
        $this->mergeOptions($options);
        $this->setSlug($string);

        return $this->generate();
    }

    /**
     * Generate and return a slug from string.
     *
     * @return string
     */
    public function generate()
    {
        // remove all html tags
        $this->stripTags()
             // remove all special characters
             ->stripSpecialCharacters()
             // convert string to lowercase
             ->convertToLowercase()
             // Remove all duplicate whitespace
             ->removeDuplicateWhitespace()
             // Strip whitespace from the beginning and end
             ->trimWhitespace()
             // replace all whitespaces with separator
             ->replaceWhitespacesWithSeparator()
             // make sure the slug is unique
             ->makeUnique();

        return $this->slug;
    }

    /**
     * Determine the string that must be a slug.
     *
     * @param  string $string
     * @return self
     */
    public function fromString(string $string)
    {
        $this->setSlug($string);
        return $this;
    }

    /**
     * Override slugging options.
     *
     * @param  array  $options
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->mergeOptions($options);
        return $this;
    }

    /**
     * Determine the slug' separator.
     *
     * @param  string $separator
     * @return self
     */
    public function setSeparator(string $separator)
    {
        $this->mergeOptions([
            'separator' => $separator,
        ]);

        return $this;
    }

    /**
     * Change the regular expression pattern.
     *
     * @param  string $regex
     * @return self
     */
    public function setRegex(string $regex)
    {
        $this->mergeOptions([
            'regex' => $regex,
        ]);

        return $this;
    }

    /**
     * Determine whether the slug should be lowercase.
     *
     * @param  boolean $withlowercase
     * @return self
     */
    public function withLowercase(bool $withlowercase = true)
    {
        $this->mergeOptions([
            'lowercase' => $withlowercase,
        ]);

        return $this;
    }

    /**
     * The callback function to determine whether the slug is unique.
     *
     * @param  \Closure $callback
     * @return self
     */
    public function uniqueWithin(Closure $callback)
    {
        $this->mergeOptions([
            'unique'    => true,
            'is_unique' => $callback,
        ]);

        return $this;
    }

    /**
     * Mearging given options with current options.
     *
     * @param  array  $options
     * @return void
     */
    protected function mergeOptions($options = [])
    {
        $options = $options ?? [];

        $this->options = array_merge($this->options, $options);
    }

    /**
     * Setting slug attribute.
     *
     * @param  $slug
     * @return self
     */
    protected function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Remove all HTML tags from string.
     *
     * @return self
     */
    protected function stripTags()
    {
        if ($this->options['strip_tags']) {
            $slug = strip_tags($this->slug);
            $this->setSlug($slug);
        }

        return $this;
    }

    /**
     * Remove all special characters from string.
     *
     * @return self
     */
    protected function stripSpecialCharacters()
    {
        $regex = '/[^\w\s]+/u';

        if (!is_null($customRegex = $this->options['regex']) && is_string($customRegex)) {
            $regex = $customRegex;
        }

        $slug = preg_replace($regex, '', $this->slug);
        $this->setSlug($slug);

        return $this;
    }

    /**
     * Make a string lowercase with character encoding.
     *
     * @return self
     */
    protected function convertToLowercase()
    {
        if ($this->options['lowercase']) {
            $slug = mb_strtolower($this->slug, 'UTF-8');
            $this->setSlug($slug);
        }

        return $this;
    }

    /**
     * Remove duplicate whitespace in string.
     *
     * @return self
     */
    protected function removeDuplicateWhitespace()
    {
        $slug = preg_replace('!\s+!', ' ', $this->slug);
        $this->setSlug($slug);

        return $this;
    }

    /**
     * Strip whitespace from the beginning and end of a string
     *
     * @return self
     */
    protected function trimWhitespace()
    {
        $slug = trim($this->slug);
        $this->setSlug($slug);

        return $this;
    }

    /**
     * Replace all whitespaces with separator in string.
     *
     * @return self
     */
    protected function replaceWhitespacesWithSeparator()
    {
        $slug = preg_replace("/[\s_]/", $this->options['separator'], $this->slug);
        $this->setSlug($slug);

        return $this;
    }

    /**
     * Make the slug unique if it's not, by adding a suffix.
     * 
     * @return self
     */
    protected function makeUnique()
    {
        $suffix = 0;

        while (!$this->isUnique()) {
            $suffix += 1;

            $slug = $this->slug;

            if ($suffix > 1) {
                $slug = explode($this->options['separator'], $slug);
                array_pop($slug);
                $slug = implode($this->options['separator'], $slug);
            }

            $this->setSlug(
                $slug . $this->options['separator'] . $suffix
            );
        }

        return $this;
    }

    /**
     * Check if the slug is unique.
     * 
     * @return boolean
     * 
     * @throws Exception
     */
    protected function isUnique()
    {
        if ($this->options['unique'] instanceof Closure) {
            return $this->options['unique']($this->slug);
        }

        if (is_bool($this->options['unique']) && $this->options['unique']) {

            if (!$this->options['is_unique'] instanceof Closure) {
                throw new \Exception("You have to set a is_unique as callback function");
            }

            return $this->options['is_unique']($this->slug);
        }

        return true;
    }
}
