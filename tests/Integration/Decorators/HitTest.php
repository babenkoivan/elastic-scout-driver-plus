<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Decorators;

use ElasticAdapter\Search\Hit as BaseHit;
use ElasticScoutDriverPlus\Decorators\Hit;
use ElasticScoutDriverPlus\Factories\LazyModelFactory;
use ElasticScoutDriverPlus\Tests\App\Book;
use ElasticScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Model;

/**
 * @covers \ElasticScoutDriverPlus\Decorators\Hit
 */
final class HitTest extends TestCase
{
    /**
     * @var Hit
     */
    private $hit;

    protected function setUp(): void
    {
        parent::setUp();

        $baseHit = new BaseHit([
            '_id' => '1',
            '_index' => 'test',
            '_source' => ['title' => 'foo'],
            '_score' => 1.1,
            'highlight' => ['title' => [' <em>foo</em> ']],
        ]);

        $model = new Book([
            'id' => 1,
            'title' => 'foo',
        ]);

        $lazyModelFactory = $this->createMock(LazyModelFactory::class);

        $lazyModelFactory->expects($this->any())
            ->method('makeByIndexNameAndDocumentId')
            ->with('test', '1')
            ->willReturn($model);

        $this->hit = new Hit($baseHit, $lazyModelFactory);
    }

    public function test_model_can_be_retrieved(): void
    {
        /** @var Model $model */
        $model = $this->hit->model();

        $this->assertSame([
            'id' => 1,
            'title' => 'foo',
        ], $model->toArray());
    }

    public function test_array_casting(): void
    {
        $this->assertSame([
            'model' => ['id' => 1, 'title' => 'foo'],
            'index_name' => 'test',
            'document' => ['id' => '1', 'content' => ['title' => 'foo']],
            'highlight' => ['title' => [' <em>foo</em> ']],
            'score' => 1.1,
        ], $this->hit->toArray());
    }
}
