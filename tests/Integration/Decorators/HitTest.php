<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\Tests\Integration\Decorators;

use Elastic\Adapter\Search\Hit as BaseHit;
use Elastic\ScoutDriverPlus\Decorators\Hit;
use Elastic\ScoutDriverPlus\Factories\LazyModelFactory;
use Elastic\ScoutDriverPlus\Tests\App\Book;
use Elastic\ScoutDriverPlus\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Model;

/**
 * @covers \Elastic\ScoutDriverPlus\Decorators\Hit
 *
 * @uses \Elastic\ScoutDriverPlus\Searchable
 */
final class HitTest extends TestCase
{
    private Hit $hit;

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
            ->method('makeFromIndexNameAndDocumentId')
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
