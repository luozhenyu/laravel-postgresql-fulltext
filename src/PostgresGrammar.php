<?php

namespace LuoZhenyu\PostgresFullText;


use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar as BasePostgresGrammar;
use Illuminate\Support\Fluent;

class PostgresGrammar extends BasePostgresGrammar
{
    /**
     * Compile a create table command.
     *
     * @param \Illuminate\Database\Schema\Blueprint $blueprint
     * @param  \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileCreate(BaseBlueprint $blueprint, Fluent $command): string
    {
        $expression = parent::compileCreate($blueprint, $command);
        if ($blueprint instanceof Blueprint) {
            $inheritedTables = $blueprint->getInheritedTables();
            $expression = sprintf('%s inherits (%s)', $expression, $this->columnize($inheritedTables));
        }
        return $expression;
    }

    /**
     * Compile a fulltext index command.
     *
     * @param \Illuminate\Database\Schema\Blueprint $blueprint
     * @param  \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileFulltext(BaseBlueprint $blueprint, Fluent $command): string
    {
        return sprintf('create index %s on %s%s (%s)',
            $this->wrap($command->index),
            $this->wrapTable($blueprint),
            $command->algorithm ? ' using ' . $command->algorithm : '',
            $this->to_tsvector($command->columns)
        );
    }

    /**
     * Convert an array of columns into a tsvector.
     *
     * @param array $columns
     * @return string
     */
    public function to_tsvector(array $columns): string
    {
        return sprintf('to_tsvector(\'%s\', %s)',
            $this->getTextSearchConfig(),
            $this->concatenate($columns)
        );
    }

    /**
     * Get the text search configuration.
     *
     * @return string
     */
    public function getTextSearchConfig(): string
    {
        return config('fulltext.text_search_config');
    }

    /**
     * Concatenate an array of column values into a united string.
     *
     * @param  array $columns
     * @return string
     */
    public function concatenate(array $columns): string
    {
        return implode('|| ', array_map([$this, 'wrap'], $columns));
    }

    /**
     * Compile a drop fulltext key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint $blueprint
     * @param  \Illuminate\Support\Fluent $command
     * @return string
     */
    public function compileDropFulltext(BaseBlueprint $blueprint, Fluent $command): string
    {
        $index = $this->wrap($command->index);
        return sprintf('drop index %s',
            $index
        );
    }
}
