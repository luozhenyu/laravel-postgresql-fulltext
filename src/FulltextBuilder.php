<?php

namespace LuoZhenyu\PostgresFullText;


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
     * @param $columns
     */
    public function setColumns($columns)
    {
        $this->columns = (array)$columns;
    }

    /**
     * Build a closure to search keywords.
     *
     * @param string $keywords
     * @return \Closure
     */
    public function search($keywords)
    {
        return function ($query) use ($keywords) {
            $query->whereRaw($this->makePlainTsQuery(), $keywords);
        };
    }

    /**
     * Build a closure to search using ts_query.
     *
     * @param string $keywords
     * @return \Closure
     */
    public function searchUsingTsQuery($keywords)
    {
        return function ($query) use ($keywords) {
            $query->whereRaw($this->makeTsQuery(), $keywords);
        };
    }

    /**
     * Create a fulltext query string.
     *
     * @return string
     */
    protected function makeTsQuery()
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
    protected function makePlainTsQuery()
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
    public function getTextSearchConfig()
    {
        return config('fulltext.text_search_config');
    }

    /**
     * Concatenate an array of column values into a united string.
     *
     * @param  array $columns
     * @return string
     */
    protected function concatenate(array $columns)
    {
        return implode('|| ', $columns);
    }

    /**
     * Convert an array of columns into a tsvector.
     *
     * @param array $columns
     * @return string
     */
    protected function to_tsvector(array $columns)
    {
        return sprintf('to_tsvector(\'%s\', %s)',
            $this->getTextSearchConfig(),
            $this->concatenate($columns)
        );
    }

    /**
     * Make a tsquery.
     *
     * @param string $string
     * @return string
     */
    protected function to_tsquery($string)
    {
        return sprintf('to_tsquery(\'%s\', ?)',
            $string
        );
    }

    /**
     * Convert a string into tsquery.
     *
     * @param string $string
     * @return string
     */
    protected function plainto_tsquery($string)
    {
        return sprintf('plainto_tsquery(\'%s\', ?)',
            $string
        );
    }
}