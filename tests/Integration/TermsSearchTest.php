<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration;

use ElasticScoutDriverPlus\Tests\App\Book;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\TermsQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
 * @covers \ElasticScoutDriverPlus\QueryDsl
 * @covers \ElasticScoutDriverPlus\Engine
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Collection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\NullValidator
 * @uses   \ElasticScoutDriverPlus\Factories\LazyModelFactory
 * @uses   \ElasticScoutDriverPlus\Factories\SearchResultFactory
 * @uses   \ElasticScoutDriverPlus\QueryMatch
 * @uses   \ElasticScoutDriverPlus\SearchResult
 * @uses   \ElasticScoutDriverPlus\Support\ModelScope
 */
final class TermsSearchTest extends TestCase
{
    public function test_models_can_be_found_using_terms(): void
    {
        // additional mixin
        factory(Book::class)
            ->state('belongs_to_author')
            ->create(['tags' => ['bestseller', 'discount']]);

        $target = factory(Book::class)
            ->state('belongs_to_author')
            ->create(['tags' => ['available', 'new']]);

        $found = Book::termsSearch()
            ->terms('tags', ['available', 'new'])
            ->execute();

        $this->assertCount(1, $found->models());
        $this->assertEquals($target->toArray(), $found->models()->first()->toArray());
    }
}
