<?php


namespace App\Model;


class Common extends Model
{
    public function list(array $where = [], int $start = 1, int $end = 10)
    {
        return $this->where($where)->forPage($start, $end);
    }

    /**
     * 查询单挑记录
     * @param $where
     * @param  string  $field
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function findOne($where, $field = '*')
    {
        return $this->where($where)->select($field)->first();
    }

    public function findAll($where, $field = '*')
    {
        return $this->where($where)->select($field);
    }

    public function addOne($data)
    {
        return $this->insert($data);
    }
}
