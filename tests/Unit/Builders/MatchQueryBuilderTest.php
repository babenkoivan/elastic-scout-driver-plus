<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Builders;

use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
 * @covers \ElasticScoutDriverPlus\Builders\MatchQueryBuilder
 *
 * @uses   \ElasticScoutDriverPlus\QueryParameters\ParameterCollection
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Transformers\GroupedArrayTransformer
 * @uses   \ElasticScoutDriverPlus\QueryParameters\Validators\AllOfValidator
 */
final class MatchQueryBuilderTest extends TestCase
{
    /**
     * @var MatchQueryBuilder
     */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new MatchQueryBuilder();
    }

    public function test_exception_is_thrown_when_field_is_not_specified(): void
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
            ->field('message')
            ->buildQuery();
    }

    public function test_query_with_field_and_text_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_analyzer_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'analyzer' => 'english',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->analyzer('english')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_auto_generate_synonyms_phrase_query_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'auto_generate_synonyms_phrase_query' => true,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->autoGenerateSynonymsPhraseQuery(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_fuzziness_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'fuzziness' => 'AUTO',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->fuzziness('AUTO')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_max_expansions_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'max_expansions' => 50,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->maxExpansions(50)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_prefix_length_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'prefix_length' => 0,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->prefixLength(0)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_fuzzy_transpositions_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'fuzzy_transpositions' => true,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->fuzzyTranspositions(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_rewrite_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'fuzzy_rewrite' => 'constant_score',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->fuzzyRewrite('constant_score')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_lenient_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'lenient' => true,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->lenient(true)
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_operator_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'operator' => 'AND',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->operator('AND')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_minimum_should_match_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'minimum_should_match' => '75%',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->minimumShouldMatch('75%')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_zero_terms_query_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'zero_terms_query' => 'none',
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->zeroTermsQuery('none')
            ->buildQuery();

        $this->assertSame($expected, $actual);
    }

    public function test_query_with_field_and_text_and_boost_can_be_built(): void
    {
        $expected = [
            'match' => [
                'message' => [
                    'query' => 'this is a test',
                    'boost' => 2,
                ],
            ],
        ];

        $actual = $this->builder
            ->field('message')
            ->query('this is a test')
            ->boost(2)
            ->buildQuery();

        $this->assertEquals($expected, $actual);
    }
}
