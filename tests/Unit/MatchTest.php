<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
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

        $document = new Document((string)$model->id, ['title' => $model->title]);
        $highlight = new Highlight(['title' => ['<em>test</em>']]);

        $this->match = new Match($factory, 'books', $document, $highlight, 1.1);
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
}
