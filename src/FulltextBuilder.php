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
            $query->whereRaw($this->toQuery(), $keywords);
        };
    }

    /**
     * Create a fulltext query string.
     *
     * @return string
     */
    protected function toQuery()
    {
        return sprintf('%s @@ %s',
            $this->to_tsvector($this->columns),
            $this->to_tsquery($this->getTextSearchConfig())
        );
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
     * @param string $config
     * @return string
     */
    protected function to_tsquery($config)
    {
        return sprintf('plainto_tsquery(\'%s\', ?)',
            $config
        );
    }
}