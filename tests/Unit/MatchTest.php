<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit;

use ElasticAdapter\Search\Highlight;
use ElasticAdapter\Search\Hit;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Match;
use ElasticScoutDriverPlus\Tests\App\Book;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Match
 */
final class MatchTest extends TestCase
{
    /**
     * @var Match
     */
    private $match;

    protected function setUp(): void
    {
        parent::setUp();

        $model = new Book();
        $model->id = 1;
        $model->title = 'test';

        $factory = $this->createMock(LazyModelFactory::class);

        $factory->method('makeByIndexNameAndDocumentId')
            ->with('books', $model->id)
            ->willReturn($model);

        $hit = new Hit([
            '_index' => 'books',
            '_score' => 1.1,
            '_id' => (string)$model->id,
            '_source' => ['title' => $model->title],
            'highlight' => ['title' => ['<em>test</em>']],
        ]);

        $this->match = new Match($factory, $hit);
    }

    public function test_index_name_can_be_received(): void
    {
        $this->assertSame('books', $this->match->indexName());
    }

    public function test_score_can_be_received(): void
    {
        $this->assertSame(1.1, $this->match->score());
    }

    public function test_model_can_be_received(): void
    {
        /** @var Model $model */
        $model = $this->match->model();

        $this->assertSame(['id' => 1, 'title' => 'test'], $model->toArray());
    }

    public function test_document_can_be_received(): void
    {
        $document = $this->match->document();

        $this->assertSame('1', $document->getId());
        $this->assertSame(['title' => 'test'], $document->getContent());
    }

    public function test_highlight_can_be_received(): void
    {
        /** @var Highlight $highlight */
        $highlight = $this->match->highlight();

        $this->assertSame(['title' => ['<em>test</em>']], $highlight->getRaw());
    }

    public function test_match_can_be_transformed_to_array(): void
    {
        $this->assertSame([
            'model' => ['id' => 1, 'title' => 'test'],
            'index_name' => 'books',
            'document' => ['id' => '1', 'content' => ['title' => 'test']],
            'highlight' => ['title' => ['<em>test</em>']],
            'score' => 1.1,
        ], $this->match->toArray());
    }

    public function test_raw_can_be_received(): void
    {
        $this->assertSame([
            '_index' => 'books',
            '_score' => 1.1,
            '_id' => '1',
            '_source' => ['title' => 'test'],
            'highlight' => ['title' => ['<em>test</em>']],
        ], $this->match->raw());
    }
}
