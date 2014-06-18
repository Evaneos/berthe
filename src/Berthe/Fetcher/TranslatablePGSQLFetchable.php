<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;

abstract class TranslatablePGSQLFetchable extends AbstractPGSQLFetchable
{
    public function setLanguage($language) {
        $this->language = $language;
    }

    public function setTranslatableColumns(array $translatableColumns) {
        $this->translatableColumns = $translatableColumns;
    }

    protected function getSelectedColumnsjoins($column, $mainTableAlias, array $selectedColumns = array(), $selectedJoin = array(0, -1), $queryJoin = '')
    {
        $columns = $this->getColumns();
        if (array_key_exists($column, $columns) || !array_key_exists($column, $this->translatableColumns)) {
            return parent::getSelectedColumnsjoins($column, $mainTableAlias, $selectedColumns, $selectedJoin, $queryJoin);
        }
        return array($selectedColumns, $selectedJoin, $queryJoin);
    }

    protected function getQueryParameters(Fetcher $fetcher = null, $mainTableAlias='ref')
    {
        list($select, $join) = parent::getQueryParameters($fetcher, $mainTableAlias);
        $columns = array_unique(array_merge(array_keys($fetcher->getSorts()), $fetcher->getFilterColumns()));
        foreach ($columns as $key) {
            if (!array_key_exists($key, $this->translatableColumns)) {
                continue;
            }
            $column = $this->translatableColumns[$key];
            $select .= ', '.$key.'.name as '.$key;
            $join .= "LEFT JOIN translations $key ON {$mainTableAlias}.{$column} = {$key}.id
              INNER JOIN languages l ON {$key}.language_id = l.id AND l.iso2 = '{$this->language}' \n";
        }
        return array($select, $join);
    }
}