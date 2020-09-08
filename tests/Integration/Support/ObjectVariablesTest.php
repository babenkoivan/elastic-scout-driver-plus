<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Tests\Integration\Support;

use ElasticScoutDriverPlus\Support\ObjectVariables;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticScoutDriverPlus\Support\ObjectVariables
 */
final class ObjectVariablesTest extends TestCase
{
    use ObjectVariables;

    /**
     * @var string
     */
    private $camelCase = 'camelCase';
    /**
     * @var string
     */
    private $snake_case = 'snake_case';

    public function test_variables_can_be_received_in_original_case(): void
    {
        $vars = $this->getObjectVariables(false);

        $this->assertArrayHasKey('camelCase', $vars);
        $this->assertArrayHasKey('snake_case', $vars);

        $this->assertSame($vars['camelCase'], 'camelCase');
        $this->assertSame($vars['snake_case'], 'snake_case');
    }

    public function test_variables_can_be_received_in_snake_case(): void
    {
        $vars = $this->getObjectVariables();

        $this->assertArrayHasKey('camel_case', $vars);
        $this->assertArrayHasKey('snake_case', $vars);

        $this->assertSame($vars['camel_case'], 'camelCase');
        $this->assertSame($vars['snake_case'], 'snake_case');
    }
}
