<?php

namespace ArinaSystems\LocalizedSlug\Tests;

use Illuminate\Support\Facades\Route;
use ArinaSystems\LocalizedSlug\Tests\App\Models\Blog;
use ArinaSystems\LocalizedSlug\Tests\App\Models\Article;

class HasLocalizedSlugTest extends TestCase
{
    /**
     * @return mixed
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['router']->group(['middleware' => 'bindings'], function () {
            Route::get('articles/{article}', function (Article $article) {
                return $article->id;
            });
        });
    }

    /**
     * @test
     */
    public function generate_slug_from_multiple_attributes_by_locale()
    {
        $blog = factory(Blog::class)->create([
            'en_title' => 'Hello, World!',
            'es_title' => '¡Hola Mundo!',
        ]);

        $expectedSlug = [
            'en' => 'hello-world',
            'es' => 'hola-mundo',
        ];

        $this->assertJson($blog->getOriginal('slug'));
        $this->assertJsonStringEqualsJsonString(json_encode($expectedSlug), $blog->getOriginal('slug'));
    }

    /**
     * @test
     */
    public function generate_slug_from_single_attribute()
    {
        $article = factory(Article::class)->create([
            'title' => [
                'ar' => 'مرحبًا بالعالم',
                'en' => 'Hello, World!',
            ],
        ]);

        $expectedSlug = [
            'ar' => 'مرحبا-بالعالم',
            'en' => 'hello-world',
        ];

        $this->assertJson($article->getOriginal('slug'));
        $this->assertJsonStringEqualsJsonString(json_encode($expectedSlug), $article->getOriginal('slug'));
    }

    /**
     * @test
     */
    public function retrieve_slug_by_app_locale()
    {
        $article = factory(Article::class)->create([
            'title' => [
                'ar' => 'مرحبًا بالعالم',
                'en' => 'Hello, World!',
            ],
        ]);

        config(['app.locale' => 'en']);
        $this->assertEquals('hello-world', $article->slug);

        config(['app.locale' => 'ar']);
        $this->assertEquals('مرحبا-بالعالم', $article->slug);
    }

    /**
     * @test
     */
    public function retrieve_slug_by_specific_locale()
    {
        $article = factory(Article::class)->create([
            'title' => [
                'ar' => 'مرحبًا بالعالم',
                'en' => 'Hello, World!',
            ],
        ]);

        $this->assertEquals('hello-world', $article->getSlug('en'));
        $this->assertEquals('مرحبا-بالعالم', $article->getSlug('ar'));
    }

    /**
     * @test
     */
    public function find_by_slug()
    {
        $article_one = factory(Article::class)->create([
            'title' => [
                'ar' => 'مرحبًا بالعالم 1',
                'en' => 'Hello, World! 1',
            ],
        ]);

        $article_two = factory(Article::class)->create([
            'title' => [
                'ar' => 'مرحبًا بالعالم 2',
                'en' => 'Hello, World! 2',
            ],
        ]);

        config(['app.locale' => 'en']);
        $model = Article::findBySlug('hello-world-1');
        $this->assertEquals($article_one->id, $model->id);

        $model = Article::findBySlug('مرحبا-بالعالم-2', 'ar');
        $this->assertEquals($article_two->id, $model->id);

        $model = Article::findBySlug('invalid-slug');
        $this->assertEquals(null, $model);
    }

    /**
     * @test
     */
    public function find_by_slug_or_fail()
    {
        $article_one = factory(Article::class)->create([
            'title' => [
                'ar' => 'مرحبًا بالعالم 1',
                'en' => 'Hello, World! 1',
            ],
        ]);

        $article_two = factory(Article::class)->create([
            'title' => [
                'ar' => 'مرحبًا بالعالم 2',
                'en' => 'Hello, World! 2',
            ],
        ]);

        config(['app.locale' => 'ar']);
        $model = Article::findBySlugOrFail('مرحبا-بالعالم-2');
        $this->assertEquals($article_two->id, $model->id);

        $model = Article::findBySlugOrFail('hello-world-1', 'en');
        $this->assertEquals($article_one->id, $model->id);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $model = Article::findBySlugOrFail('invalid-slug');
    }

    /**
     * @test
     */
    public function model_route_binding()
    {
        $article = factory(Article::class)->create([
            'title' => [
                'ar' => 'مرحبًا بالعالم',
                'en' => 'Hello, World!',
            ],
        ]);

        $this->get('articles/' . $article->slug)
             ->assertSuccessful()
             ->assertSee($article->id);

        $this->get('articles/invalid-slug')
             ->assertStatus(404);
    }

}
