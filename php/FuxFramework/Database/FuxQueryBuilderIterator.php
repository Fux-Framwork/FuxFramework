<?php

namespace Fux;


/**
 * Questa classe viene inizializzata con un query builder e permette di attraversare il poteziale result set
 * con il cambio interno dei parametri di limit e offset
*/
class FuxQueryBuilderIterator
{

    /** @property FuxQueryBuilder $builder */
    private $builder;
    private $offset;

    /**
     * @param FuxQueryBuilder $builder
     * @param int $initialOffset
    */
    public function __construct($builder, $initialOffset = 0){
        $this->builder = $builder;
        $this->offset = $initialOffset;
    }

    public function next($rowNum){
        $data = $this->builder->offset($this->offset)->limit($rowNum)->execute();
        $this->offset += $rowNum;
        return $data;
    }

}