<?php

namespace LuoZhenyu\PostgresFullText;


use Illuminate\Database\Grammar;
use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Database\Schema\Builder;

class PostgresConnection extends BasePostgresConnection
{
    /**
     * Get a schema builder instance for the connection.
     *
     * @return Builder
     */
    public function getSchemaBuilder(): Builder
    {
        $builder = parent::getSchemaBuilder();
        $builder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });
        return $builder;
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return Grammar
     */
    protected function getDefaultSchemaGrammar(): Grammar
    {
        return $this->withTablePrefix(new PostgresGrammar);
    }
}
