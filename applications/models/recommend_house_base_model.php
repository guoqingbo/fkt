<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 推荐房源类
 */
class Recommend_house_base_model extends MY_Model
{
    /**
     * 表名
     */
    private $_tbl = 'recommend_house';

    /**
     * 类初始化
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 插入
     */
    public function insert($data)
    {
        //检测推荐房源中是否已有该房源

        if ($this->db->insert($this->_tbl, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * 删除
     */
    public function delete($data)
    {
        $res = $this->db
            ->where($data)
            ->delete($this->_tbl);
        return $res;
    }

    /**
     * 获取
     */
    public function getbycond($data)
    {
        $res = $this->db
            ->where($data)
            ->get($this->_tbl)
            ->row_array();
        return $res;
    }
}