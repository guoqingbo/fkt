<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Cooperate_base_model CLASS
 *
 * 房客源合作基类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Cooperate_district_base_model extends MY_Model
{
    /**
     * 合作朋友圈好友列表表名
     * @var string
     */
    protected $tbl_district = 'cooperate_district';
    /**
     * 合作朋友圈好友列表表名
     * @var string
     */
    protected $tbl_pass = 'cooperate_district_join';

    /**
     * 合作朋友圈好友申请列表表名
     * @var string
     */
    protected $tbl_apply = 'cooperate_district_apply';

    /**
     * 合作朋友圈提醒消息关联表
     * @var string
     */
    protected $tbl_message = 'message_district';


    /**
     * 类初始化
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 设置表名称
     *
     * @access  public
     * @param  string $tblname 表名称
     * @return  void
     */
    public function set_tbl($tblname)
    {
        $this->_tbl = trim(strip_tags($tblname));
    }

    /**
     * 获取表名称
     *
     * @access  public
     * @param  void
     * @return  string
     */
    public function get_tbl()
    {
        return $this->_tbl;
    }

    /**
     * 设置需要查询的字段
     * @param array $select_fields
     */
    public function set_select_fields($select_fields)
    {
        $select_fields_str = '';

        if (isset($select_fields) && !empty($select_fields)) {
            $select_fields_str = implode(',', $select_fields);
        }

        $this->_select_fields = $select_fields_str;
    }

    /**
     * 返回需要查询的字段
     * @param void
     * @return string 查询字段
     */
    public function get_search_fields()
    {
        return $this->_select_fields;
    }

    /**
     * 获取所有
     * @param void
     * @return string 查询字段
     */
    public function get_all_by($where, $start = -1, $limit = 20,
                               $order_key = 'id', $order_by = 'DESC')
    {
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }
        if ($where) {
            //查询条件
            $this->dbback_city->where($where);
        }
        //排序条件
        $this->dbback_city->order_by($order_key, $order_by);
        if ($start >= 0 && $limit > 0) {
            $this->dbback_city->limit($limit, $start);
        }
        //返回结果
        return $this->dbback_city->get($this->_tbl)->result_array();
    }

    /**
     * 根据查询条件返回一条记录
     * @param string $where 查询条件
     * @return array 返回一条一维数组的记录
     */
    public function get_one_by($where = '')
    {
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }
        //查询条件
        $this->dbback_city->where($where);
        return $this->dbback_city->get($this->_tbl)->row_array();
    }

    /**
     * 根据门店id，获取其加入的区域公盘
     * @param string $where 查询条件
     * @return array 返回一条一维数组的记录
     */
    public function get_one_by_agency_id($agency_id)
    {
        $this->set_tbl("cooperate_district_join");
        $conf_where = "agency_id = {$agency_id} and status = 1";
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }
        //查询条件
        $this->dbback_city->where($conf_where);
        return $this->dbback_city->get($this->_tbl)->row_array();
    }

    /**
     * 根据门店id，获取其加入的区域公盘
     * @param string $where 查询条件
     * @return array 返回一条一维数组的记录
     */
    public function get_district($conf_where)
    {
        $this->set_tbl("cooperate_district");
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }
        //查询条件
        $this->dbback_city->where($conf_where);
        return $this->dbback_city->get($this->_tbl)->row_array();
    }
    /**
     * 根据门店id，获取其加入的区域公盘内所有的门店id
     * @param string $where 查询条件
     * @return array 返回一条一维数组的记录
     */
    public function get_agency_by_agency_id($agency_id)
    {
        $district_public_arr = $this->get_one_by_agency_id($agency_id);
        $district_agency_where = "status = 1 and cooperate_district_id = {$district_public_arr['cooperate_district_id']} ";
        $district_agency_arr = $this->get_all_by($district_agency_where);
        $agency_ids = '';
        foreach ($district_agency_arr as $key => $vo) {
            $agency_id_arr[] = $vo['agency_id'];
        }
        $agency_id_arr[] = 0;
        $agency_ids = implode(',', $agency_id_arr);
        return $agency_ids;
    }

    /**
     * 符合条件的行数
     * @param string $where 查询条件
     * @return int
     */
    public function count_by($where = '')
    {
        if ($where) {
            //查询条件
            $this->dbback_city->where($where);
        }
        return $this->dbback_city->count_all_results($this->_tbl);
    }

    /**
     * 添加
     * @param void
     * @return string 查询字段
     */
    public function insert($arr)
    {
        $tbl_name = $this->_tbl;
        $insert_id = 0;
        if (is_array($arr) && !empty($arr)) {
            $this->db_city->insert($tbl_name, $arr);
            //如果插入成功，则返回插入的id
            if (($this->db_city->affected_rows()) >= 1) {
                $insert_id = $this->db_city->insert_id();
            }
        }

        return $insert_id;
    }

    /**
     * 更新
     *
     * @access  public
     * @param  array $update_arr 需要更新字段的键值对
     * @param  string $cond_where 更新条件
     * @param  boolean $escape 是否转义更新字段的值
     * @return  int 更新影响行数
     */
    public function update_by_id($id, $update_data)
    {
        $this->db_city->where('id', $id);
        $this->db_city->update($this->_tbl, $update_data);
        return $this->db_city->affected_rows();
    }

    /**
     * 删除权限模块数据
     * @param int $id 编号
     * @return boolean true 成功 false 失败
     */
    public function delete_by_id($id)
    {
        //多条删除
        if (is_array($id)) {
            $ids = $id;
        } else {
            $ids[0] = $id;
        }
        if ($ids) {
            $this->db_city->where_in('id', $ids);
            $this->db_city->delete($this->_tbl);
        }
        if ($this->db_city->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}

/* End of file cooperate_base_model.php */
/* Location: ./application/models/cooperate_base_model.php */
