<?php

namespace ArinaSystems\LocalizedSlug\Tests;

use Slugger;

class SluggerFacadeTest extends TestCase
{
    /**
     * @var string
     */
    protected $slug = '';

    /**
     * @var array
     */
    protected $slugs = [];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function generate_a_slug_from_a_given_string()
    {
        $string = 'Hello, World! Please slug me. مرحبًا بكم';

        $this->slug = Slugger::fromString($string)
            ->generate();

        $this->assertEquals('hello-world-please-slug-me-مرحبا-بكم', $this->slug);
    }

    /**
     * @test
     */
    public function change_a_separator_option()
    {
        $string = 'Hello, World! Please slug me.';

        $options = ['separator' => '/'];

        $this->slug = Slugger::fromString($string)
            ->setOptions($options)
            ->generate();

        $this->assertEquals('hello/world/please/slug/me', $this->slug);

        $this->slug = Slugger::fromString($string)
            ->setSeparator('*')
            ->generate();

        $this->assertEquals('hello*world*please*slug*me', $this->slug);
    }

    /**
     * @test
     */
    public function allow_uppercase_option()
    {
        $string = 'Hello, World! Please slug me.';

        $options = ['lowercase' => false];

        $this->slug = Slugger::fromString($string)
            ->setOptions($options)
            ->generate();

        $this->assertEquals('Hello-World-Please-slug-me', $this->slug);

        $this->slug = Slugger::fromString($string)
            ->withLowercase(false)
            ->generate();

        $this->assertEquals('Hello-World-Please-slug-me', $this->slug);
    }

    /**
     * @test
     */
    public function remove_html_tags()
    {
        $string = '<a>Hello, World! Please slug me.</a>';

        $this->slug = Slugger::fromString($string)
            ->generate();

        $this->assertEquals('hello-world-please-slug-me', $this->slug);
    }

    /**
     * @test
     */
    public function custom_regex_option()
    {
        $string = 'Hello, World! Please slug me. مرحبًا بكم';

        $options = ['regex' => '/[^a-zA-Z0-9\/_|+ -]/'];

        $this->slug = Slugger::fromString($string)
            ->setOptions($options)
            ->generate();

        $this->assertEquals('hello-world-please-slug-me', $this->slug);

        $this->slug = Slugger::fromString($string)
            ->setRegex('/[^a-zA-Z0-9\/_|+ -]/')
            ->generate();

        $this->assertEquals('hello-world-please-slug-me', $this->slug);
    }

    /**
     * @test
     */
    public function generate_unique_slug()
    {
        $string = 'Hello, World! Please slug me.';

        $unique_fun = function ($slug) {
            return !in_array($slug, $this->slugs);
        };

        $this->slugs[] = $this->slug = Slugger::fromString($string)
            ->uniqueWithin($unique_fun)
            ->generate();
        $this->assertEquals('hello-world-please-slug-me', $this->slug);

        $this->slugs[] = $this->slug = Slugger::fromString($string)
            ->uniqueWithin($unique_fun)
            ->generate();
        $this->assertEquals('hello-world-please-slug-me-1', $this->slug);

        $this->slugs[] = $this->slug = Slugger::fromString($string)
            ->uniqueWithin($unique_fun)
            ->generate();
        $this->assertEquals('hello-world-please-slug-me-2', $this->slug);

        $this->slugs[] = $this->slug = Slugger::fromString($string)
            ->uniqueWithin($unique_fun)
            ->generate();
        $this->assertEquals('hello-world-please-slug-me-3', $this->slug);
    }
}
