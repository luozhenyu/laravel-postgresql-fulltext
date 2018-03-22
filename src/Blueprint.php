<?php

namespace LuoZhenyu\PostgresFullText;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;

class Blueprint extends BaseBlueprint
{
    /**
     * The inherited tables that should be added to the table.
     *
     * @var array
     */
    protected $inheritedTables = [];

    /**
     * Get the inherited tables that should be added.
     *
     * @return array
     */
    public function getInheritedTables()
    {
        return $this->inheritedTables;
    }

    /**
     * Add a new fulltext key to the blueprint.
     *
     * @param string|array $columns
     * @param string $name
     * @param string $algorithm
     * @return \Illuminate\Support\Fluent
     */
    public function fulltext($columns, $name = null, $algorithm = 'gin')
    {
        return $this->indexCommand('fulltext', $columns, $name, $algorithm);
    }

    /**
     * Indicate that the given fulltext key should be dropped.
     *
     * @param  string|array $index
     * @return \Illuminate\Support\Fluent
     */
    public function dropFulltext($index)
    {
        return $this->dropIndexCommand('dropFullText', 'fulltext', $index);
    }

    /**
     * Add a new inherited table to the blueprint.
     *
     * @param string|array $columns
     */
    public function inherits($columns)
    {
        $columns = (array)$columns;
        foreach ($columns as $column) {
            $this->inheritedTables[] = $column;
        }
    }
}
