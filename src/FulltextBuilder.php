<?php

namespace LuoZhenyu\PostgresFullText;


use Closure;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class FulltextBuilder
{
    /**
     * The columns to be searched.
     *
     * @var null
     */
    protected $columns = null;

    /**
     * FulltextBuilder constructor.
     *
     * @param string|array $columns
     */
    function __construct($columns)
    {
        $this->setColumns($columns);
    }

    /**
     * Set the columns to be searched.
     *
     * @param string|array $columns
     */
    public function setColumns($columns)
    {
        $this->columns = array_wrap($columns);
    }

    /**
     * Build a closure to search keywords.
     *
     * @param string $keywords
     * @return Closure
     */
    public function search(string $keywords): Closure
    {
        return function ($query) use ($keywords) {
            $query->whereRaw($this->makePlainTsQuery(), $keywords);
        };
    }

    /**
     * Build a closure to search using ts_query.
     *
     * @param string $keywords
     * @return Closure
     */
    public function searchUsingTsQuery(string $keywords): Closure
    {
        return function ($query) use ($keywords) {
            $query->whereRaw($this->makeTsQuery(), $keywords);
        };
    }

    /**
     * Build a expression to rank query results.
     *
     * @param string $keywords
     * @return Expression
     */
    public function rank(string $keywords): Expression
    {
        return DB::raw(sprintf('ts_rank(%s, %s)',
            $this->to_tsvector($this->columns),
            $this->plainto_tsquery($this->getTextSearchConfig(), $keywords)
        ));
    }

    /**
     * Create a fulltext query string.
     *
     * @return string
     */
    protected function makeTsQuery(): string
    {
        return sprintf('%s @@ %s',
            $this->to_tsvector($this->columns),
            $this->to_tsquery($this->getTextSearchConfig())
        );
    }

    /**
     * Create a plain fulltext query string.
     *
     * @return string
     */
    protected function makePlainTsQuery(): string
    {
        return sprintf('%s @@ %s',
            $this->to_tsvector($this->columns),
            $this->plainto_tsquery($this->getTextSearchConfig())
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
    protected function concatenate(array $columns): string
    {
        return implode('|| ', $columns);
    }

    /**
     * Convert an array of columns into a tsvector.
     *
     * @param array $columns
     * @return string
     */
    protected function to_tsvector(array $columns): string
    {
        return sprintf('to_tsvector(\'%s\', %s)',
            $this->getTextSearchConfig(),
            $this->concatenate($columns)
        );
    }

    /**
     * Make a tsquery.
     *
     * @param string $config
     * @return string
     */
    protected function to_tsquery(string $config): string
    {
        return sprintf('to_tsquery(\'%s\', ?)',
            $config
        );
    }

    /**
     * Convert a string into tsquery.
     *
     * @param string $config
     * @param string|null $query
     * @return string
     */
    protected function plainto_tsquery(string $config, $query = null): string
    {
        if (is_null($query)) {
            return sprintf('plainto_tsquery(\'%s\', ?)',
                $config
            );
        }

        return sprintf('plainto_tsquery(\'%s\', \'%s\')',
            $config,
            pg_escape_string($query)
        );
    }
}
