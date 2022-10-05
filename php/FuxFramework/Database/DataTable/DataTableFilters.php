<?php

namespace Fux\Database\DataTable;

use Fux\DB;
use Fux\FuxQueryBuilder;

class DataTableFilters
{

    public static function applyFilters($queryBuilder, $filterString, $alias = null)
    {
        $filters = json_decode(base64_decode($filterString), true) ?? null;
        $qb = (new FuxQueryBuilder())->select("*")->from($queryBuilder, $alias);
        foreach ($filters as $k => $f) {
            $k = DB::sanitize($k);
            $v = is_array($f['value']) ? $f['value'] : DB::sanitize($f['value']);
            switch ($f['condition']) {
                case 'equal':
                    $qb->where($k, $v);
                    break;
                case 'equal_date':
                    $qb->SQLWhere("DATE($k) = '$v'");
                    break;
                case 'greater':
                    $qb->whereGreaterThan($k, $v);
                    break;
                case 'greaterEq':
                    $qb->whereGreaterEqThan($k, $v);
                    break;
                case 'lower':
                    $qb->whereLowerThan($k, $v);
                    break;
                case 'lowerEq':
                    $qb->whereLowerEqThan($k, $v);
                    break;
                case 'between_exclusive':
                    $qb->whereGreaterThan($k, DB::sanitize($v['start']))->whereLowerThan($k, DB::sanitize($v['end']));
                    break;
                case 'between_inclusive':
                    $qb->whereGreaterEqThan($k, DB::sanitize($v['start']))->whereLowerEqThan($k, DB::sanitize($v['end']));
                    break;
                case 'between_inclusive_left':
                    $qb->whereGreaterEqThan($k, DB::sanitize($v['start']))->whereLowerThan($k, DB::sanitize($v['end']));
                    break;
                case 'between_inclusive_right':
                    $qb->whereGreaterThan($k, DB::sanitize($v['start']))->whereLowerEqThan($k, DB::sanitize($v['end']));
                    break;
                case 'contain':
                default:
                    $qb->whereLike($k, "%$v%");
            }
        }
        return $qb;
    }

}