<?php declare(strict_types=1);

namespace ElasticScoutDriverPlus;

/**
 * Use this trait to enable custom elasticsearch routing functionality.
 * https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-routing-field.html
 */
trait ShardRouting
{
    /**
     * Override this method to specify a document field that will be used
     * to route the document to a shard.
     * By default the scout key will be used.
     */
    abstract public function getRoutingPath(): string;
}
