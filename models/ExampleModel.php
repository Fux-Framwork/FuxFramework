<?php

class ExampleModel extends Fux\Database\Model\Model
{
    protected static $tableName = 'table_name';
    protected static $tableFields = ["field1", "field2", "field3"];
    protected static $primaryKey = ["field1"];
}
