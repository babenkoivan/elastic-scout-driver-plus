<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait FuzzinessParameter
{
    /**
     * @var string|null
     */
    private $fuzziness;

    public function fuzziness(string $fuzziness): self
    {
        $this->fuzziness = $fuzziness;
        return $this;
    }
}
