<?php declare(strict_types=1);

namespace Elastic\ScoutDriverPlus\QueryParameters\Shared;

trait AutoGenerateSynonymsPhraseQueryParameter
{
    public function autoGenerateSynonymsPhraseQuery(bool $autoGenerateSynonymsPhraseQuery): self
    {
        $this->parameters->put('auto_generate_synonyms_phrase_query', $autoGenerateSynonymsPhraseQuery);
        return $this;
    }
}
