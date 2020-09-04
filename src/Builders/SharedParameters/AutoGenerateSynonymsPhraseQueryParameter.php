<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus\Builders\SharedParameters;

trait AutoGenerateSynonymsPhraseQueryParameter
{
    /**
     * @var bool|null
     */
    private $autoGenerateSynonymsPhraseQuery;

    public function autoGenerateSynonymsPhraseQuery(bool $autoGenerateSynonymsPhraseQuery): self
    {
        $this->autoGenerateSynonymsPhraseQuery = $autoGenerateSynonymsPhraseQuery;
        return $this;
    }
}
