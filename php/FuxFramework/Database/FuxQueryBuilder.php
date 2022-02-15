<?php

namespace Fux;

use FuxModel;

class FuxQueryBuilder
{

    private const TYPE_SELECT = 'select';
    private const TYPE_UPDATE = 'update';
    private const TYPE_DELETE = 'delete';
    private $queryType = '';
    private $selectables = [];
    private $table = "";
    private $setClause = [];
    private $joins = []; // => Array of ["type"=>"left", "table"=>"", "on"=>"where"]
    private $whereClause = [];
    private $groupBy = [];
    private $orderBy = [];
    private $limit;
    private $offset;
    private $havingClause = [];
    private $returnFoundRows = false;

    public function select($data)
    {
        $this->queryType = self::TYPE_SELECT;
        if (is_array($data)) {
            $this->selectables = $data;
        } else {
            $this->selectables = func_get_args();
        }
        return $this;
    }

    public function selectAppend($data)
    {
        $this->queryType = self::TYPE_SELECT;
        if (is_array($data)) {
            $newSelects = $data;
        } else {
            $newSelects = func_get_args();
        }
        $this->selectables = array_merge($this->selectables, $newSelects);
        return $this;
    }

    public function update($table)
    {
        $this->queryType = self::TYPE_UPDATE;
        $table = self::tableRefToString($table);
        $this->table = $table;
        return $this;
    }

    public function set($field, $value, $valueUseColumns = false)
    {
        $field = self::getStringfiedFieldName($field);
        $this->setClause[] = $value === null ? "$field = NULL" : ($valueUseColumns ? "$field = $value" : "$field = '$value'");
        return $this;
    }

    public function delete($from)
    {
        $this->queryType = self::TYPE_DELETE;
        $from = self::tableRefToString($from);
        $this->table = $from;
        return $this;
    }

    public function SQLSet($clause)
    {
        $this->setClause[] = $clause;
        return $this;
    }

    public function massiveSet($fields)
    {
        foreach ($fields as $field => $value) {
            $field = self::getStringfiedFieldName($field);
            $this->setClause[] = $value === null ? "$field = NULL" : "$field = '$value'";
        }
        return $this;
    }

    /**
     * @param string | FuxModel $table
     */
    public function from($table, $as = null)
    {
        $table = self::tableRefToString($table);
        $this->table = $table;
        if ($as) $this->table .= " as $as";
        return $this;
    }

    public function join($with, $on, $as = null)
    {
        return $this->_join("INNER", $with, $on, $as);
    }

    public function leftJoin($with, $on, $as = null)
    {
        return $this->_join("LEFT", $with, $on, $as);
    }

    public function rightJoin($with, $on, $as = null)
    {
        return $this->_join("RIGHT", $with, $on, $as);
    }

    public function fullJoin($with, $on, $as = null)
    {
        return $this->_join("FULL", $with, $on, $as);
    }

    public function _join($type, $with, $on, $as = null)
    {
        $with = self::tableRefToString($with);
        $this->joins[] = ["type" => $type, "table" => $with, "on" => $on, "as" => $as];
        return $this;
    }

    public function SQLWhere($clause)
    {
        $this->whereClause[] = $clause;
        return $this;
    }

    public function where($field, $value)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = $value === null ? "$field IS NULL" : "$field = '$value'";
        return $this;
    }

    public function whereColumn($field, $field2)
    {
        $field = self::getStringfiedFieldName($field);
        $field2 = self::getStringfiedFieldName($field2);
        $this->whereClause[] = "$field = $field2";
        return $this;
    }

    public function whereNotEqual($field, $value)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field <> '$value'";
        return $this;
    }

    public function whereGreaterThan($field, $value)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field > '$value'";
        return $this;
    }

    public function whereGreaterEqThan($field, $value)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field >= '$value'";
        return $this;
    }

    public function whereLowerThan($field, $value)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field < '$value'";
        return $this;
    }

    public function whereLowerEqThan($field, $value)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field <= '$value'";
        return $this;
    }

    public function whereNull($field)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field IS NULL";
        return $this;
    }

    public function whereNotNull($field)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field IS NOT NULL";
        return $this;
    }

    public function whereBetween($field, $from, $to)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field BETWEEN $from AND $to";
        return $this;
    }

    public function whereIn($field, $values)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field IN ('" . implode("','", $values) . "')";
        return $this;
    }

    public function whereNotIn($field, $values)
    {
        $field = self::getStringfiedFieldName($field);
        $this->whereClause[] = "$field NOT IN ('" . implode("','", $values) . "')";
        return $this;
    }

    public function massiveWhere($fields)
    {
        foreach ($fields as $fieldName => $wantedValue) {
            $fieldName = self::getStringfiedFieldName($fieldName);
            $this->whereClause[] = "$fieldName = '$wantedValue'";
        }
        return $this;
    }

    public function orderBy($field, $type)
    {
        $this->orderBy[] = [$field, $type];
        return $this;
    }

    public function groupBy()
    {
        $this->groupBy = func_get_args();
        return $this;
    }

    public function having($field, $value)
    {
        $field = self::getStringfiedFieldName($field);
        $this->havingClause[] = "$field = '$value'";
        return $this;
    }

    public function massiveHaving($fields)
    {
        foreach ($fields as $fieldName => $wantedValue) {
            $fieldName = self::getStringfiedFieldName($fieldName);
            $this->havingClause[] = "$fieldName = '$wantedValue'";
        }
        return $this;
    }

    public function SQLHaving($clause)
    {
        $this->havingClause[] = $clause;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function useFoundRows($use)
    {
        $this->returnFoundRows = $use;
        return $this;
    }

    private function _getWhereParts()
    {
        $query = [];
        if (!empty($this->whereClause)) {
            $query[] = "WHERE";
            $query[] = join(' AND ', $this->whereClause);
        }
        return $query;
    }

    private function _getGroupByParts()
    {
        $query = [];
        if (!empty($this->groupBy)) {
            $query[] = "GROUP BY";
            $query[] = join(', ', $this->groupBy);
        }
        return $query;
    }

    private function _getOrderByParts()
    {
        $query = [];
        if (!empty($this->orderBy)) {
            $query[] = "ORDER BY";
            $orderBy = [];
            foreach ($this->orderBy as $clauses) {
                $orderBy[] = $clauses[0] . " " . $clauses[1];
            }
            $query[] = implode(",", $orderBy);
        }
        return $query;
    }

    private function _getHavingParts()
    {
        $query = [];
        if (!empty($this->havingClause)) {
            $query[] = "HAVING";
            $query[] = join(' AND ', $this->havingClause);
        }
        return $query;
    }

    private function _getLimitParts()
    {
        $query = [];
        if (!empty($this->limit)) {
            $query[] = "LIMIT";
            $query[] = $this->limit;
        }
        if (!empty($this->offset)) {
            $query[] = "OFFSET";
            $query[] = $this->offset;
        }
        return $query;
    }

    private function _getSetParts()
    {
        $query = [];
        if (!empty($this->setClause)) {
            $query[] = "SET";
            $query[] = join(', ', $this->setClause);
        }
        return $query;
    }

    private function _getQueryPartsForUpdate()
    {
        $query = [];
        $query[] = "UPDATE";
        $query[] = $this->table;

        $query = array_merge($query, $this->_getSetParts());
        $query = array_merge($query, $this->_getWhereParts());
        $query = array_merge($query, $this->_getGroupByParts());
        $query = array_merge($query, $this->_getOrderByParts());
        $query = array_merge($query, $this->_getHavingParts());
        $query = array_merge($query, $this->_getLimitParts());

        return $query;
    }

    private function _getQueryPartsForDelete()
    {
        $query = [];
        $query[] = "DELETE FROM";
        $query[] = $this->table;

        $query = array_merge($query, $this->_getWhereParts());
        $query = array_merge($query, $this->_getLimitParts());

        return $query;
    }

    private function _getQueryPartsForSelect()
    {
        $query[] = "SELECT";
        if ($this->returnFoundRows) {
            $query[] = "SQL_CALC_FOUND_ROWS";
        }
        // if the selectables array is empty, select all
        if (empty($this->selectables)) {
            $query[] = "*";
        } // else select according to selectables
        else {
            $query[] = join(', ', $this->selectables);
        }

        if ($this->table) {
            $query[] = "FROM";
            $query[] = $this->table;
        }

        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                $query[] = "$join[type] JOIN";
                if ($join['table'] instanceof FuxQuery) {
                    $query[] = "($join[table])";
                } else {
                    $query[] = $join['table'];
                }
                if ($join['as']) $query[] = " as $join[as]";
                $query[] = "ON $join[on]";
            }
        }

        $query = array_merge($query, $this->_getWhereParts());
        $query = array_merge($query, $this->_getGroupByParts());
        $query = array_merge($query, $this->_getHavingParts());
        $query = array_merge($query, $this->_getOrderByParts());
        $query = array_merge($query, $this->_getLimitParts());

        return $query;
    }

    public function result()
    {
        switch ($this->queryType) {
            case self::TYPE_SELECT:
                $query = $this->_getQueryPartsForSelect();
                break;
            case self::TYPE_UPDATE:
                $query = $this->_getQueryPartsForUpdate();
                break;
            case self::TYPE_DELETE:
                $query = $this->_getQueryPartsForDelete();
                break;
        }
        return new FuxQuery(join(' ', $query));
    }

    public function execute($returnFetchAll = true)
    {
        $sql = $this->result();

        if ($this->returnFoundRows && $returnFetchAll) {
            $results = DB::multiQuery([$sql, "SELECT FOUND_ROWS() as total"]) or die(DB::ref()->error . "SQL: $sql");
            return [
                'rows' => $results[0],
                'total' => $results[1][0]['total']
            ];
        }

        $q = DB::ref()->query($sql) or die(DB::ref()->error . "SQL: $sql");
        if ($returnFetchAll && $this->queryType === self::TYPE_SELECT) {
            return $q->fetch_all(MYSQLI_ASSOC);
        }
        return $q;
    }

    public function first()
    {
        $sql = $this->result();
        $q = DB::ref()->query($sql) or die(DB::ref()->error . "SQL: $sql");
        if ($row = $q->fetch_assoc()) {
            $q->free_result();
            return $row;
        }
        return null;
    }

    public function __toString()
    {
        return (string)$this->result();
    }

    private function getStringfiedFieldName($field)
    {
        $fieldParts = explode(".", $field);
        if (count($fieldParts) > 1) {
            return "$fieldParts[0].`$fieldParts[1]`";
        }
        return "`$fieldParts[0]`";
    }


    /**
     * Trasforma una tabella di un qualunque formato in una stringa usabile nella query SQL finale
     *
     * @param FuxQueryBuilder | FuxQuery | FuxModel | string
     */
    private function tableRefToString($table)
    {
        if ($table instanceof FuxQueryBuilder) {
            return "(" . $table->result() . ")";
        } elseif ($table instanceof FuxModel) {
            return $table->getTableName();
        } elseif ($table instanceof FuxQuery) {
            return "(" . $table . ")";
        }
        return $table;
    }
}

class FuxQuery
{
    private $sql = "";

    public function __construct($sql)
    {
        $this->sql = $sql;
    }

    public function __toString()
    {
        return $this->sql;
    }
}