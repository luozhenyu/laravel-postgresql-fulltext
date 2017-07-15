<?php

namespace LuoZhenyu\PostgresFullText;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;

class PostgresConnection extends BasePostgresConnection
{
    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getSchemaBuilder()
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
     * @return \Illuminate\Database\Grammar|PostgresGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new PostgresGrammar);
    }
}
