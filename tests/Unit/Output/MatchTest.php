<?php
declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Unit\Output;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticScoutDriverPlus\Factories\LazyModelFactoryInterface;
use ElasticScoutDriverPlus\Output\Match;
use ElasticScoutDriverPlus\Tests\App\Book;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Output\Match
 */
final class MatchTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->createMock(LazyModelFactoryInterface::class);
    }

    public function test_model_can_be_received(): void
    {
        $model = new Book();
        $model->id = 1;
        $model->title = 'test';

        $this->factory->expects($this->once())
            ->method('makeById')
            ->with($model->id)
            ->willReturn($model);

        $document = new Document((string)$model->id, ['title' => $model->title]);

        $match = new Match($this->factory, $document);

        $this->assertSame($match->model(), $model);
    }

    public function test_document_can_be_received(): void
    {
        $document = new Document('1', ['title' => 'test']);

        $match = new Match($this->factory, $document);

        $this->assertSame($match->document(), $document);
    }

    public function test_highlight_can_be_received(): void
    {
        $document = new Document('1', ['title' => 'test']);
        $highlight = new Highlight(['title' => ['<em>test</em>']]);

        $match = new Match($this->factory, $document, $highlight);

        $this->assertSame($match->highlight(), $highlight);
    }
}
