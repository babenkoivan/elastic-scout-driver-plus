<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticScoutDriverPlus\Builders\MultiMatchQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\MultiMatchQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\FlatArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class MultiMatchQueryBuilderTest extends TestCase
{
    /**
     * @var MultiMatchQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new MultiMatchQueryBuilder();
    }

    public function test_exception_is_thrown_when_fields_are_not_specified(): void
    {
        $this->expectException(QueryBuilderException::class);

        $this->builder
            ->query('this is a test')
            ->buildQuery();
    }

    public function test_exception_is_thrown_when_text_is_not_specified(): void
    {
        $this->expectException(QueryBuilderException::class);

        $this->builder
            ->fields(['subject', 'message'])
            ->buildQuery();
    }

    public function test_query_with_fields_and_text_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'query' => 'this is a test',
                'fields' => ['subject', 'message'],
            ],
        ];

        $actual = $this->builder
            ->query('this is a test')
            ->fields(['subject', 'message'])
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_type_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'type' => 'best_fields',
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->type('best_fields')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_analyzer_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'analyzer' => 'english',
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->analyzer('english')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_boost_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'boost' => 1.2,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->boost(1.2)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_operator_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'operator' => 'AND',
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->operator('AND')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_minimum_should_match_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'minimum_should_match' => 1,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->minimumShouldMatch(1)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_fuzziness_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'fuzziness' => 'AUTO',
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->fuzziness('AUTO')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_lenient_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'lenient' => true,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->lenient(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_prefix_length_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'prefix_length' => 3,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->prefixLength(3)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_max_expansions_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'max_expansions' => 50,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->maxExpansions(50)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_rewrite_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'fuzzy_rewrite' => 'constant_score',
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->fuzzyRewrite('constant_score')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_zero_terms_query_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'zero_terms_query' => 'none',
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->zeroTermsQuery('none')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_auto_generate_synonyms_phrase_query_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'auto_generate_synonyms_phrase_query' => true,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->autoGenerateSynonymsPhraseQuery(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_fuzzy_transpositions_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'fuzzy_transpositions' => true,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->fuzzyTranspositions(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_tie_breaker_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'tie_breaker' => 0.3,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->tieBreaker(0.3)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_fields_and_text_and_slop_can_be_built(): void
    {
        $expected = [
            'multi_match' => [
                'fields' => ['subject', 'message'],
                'query' => 'this is a test',
                'slop' => 0,
            ],
        ];

        $actual = $this->builder
            ->fields(['subject', 'message'])
            ->query('this is a test')
            ->slop(0)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }
}
