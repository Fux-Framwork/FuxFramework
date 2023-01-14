<?php

class ExampleMultiplePKModel extends Fux\Database\Model\Model
{
    protected static $tableName = 'table_name2';
    protected static $tableFields = ["field1", "field2", "field3"];
    protected static $primaryKey = ["field1","field2"];

    public function genericRelationship()
    {
        return $this->relationship("field1", ExampleModel::class, "field1");
    }
}
