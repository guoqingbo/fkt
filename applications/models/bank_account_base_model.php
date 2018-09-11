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
 * Agency_base_model CLASS
 *
 * 银行卡、银行卡添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Bank_account_base_model extends MY_Model
{

  /**
   * 字典表
   * @var string
   */
  private $_tbl = 'bank_account';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    //$this->load->model('newhouse_sync_account_base_model');
//        $this->load->model('department_access_area_base_model');
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
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
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
   * 获取银行卡列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条银行卡记录组成的二维数组
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
  //获取所有银行账号
    public function get_all_list()
    {
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }

        $this->dbback_city->where("status = 1");
        //排序条件
        $this->dbback_city->order_by("id", "ASC");
        //返回结果
        return $this->dbback_city->get($this->_tbl)->result_array();
    }
  /**
   * 根据银行卡id获取类型数据
   * @param string $bank_account_id_str 字典id
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条银行卡记录组成的二维数组
   */
  public function get_all_by_bank_account_id($bank_account_id_str, $order_key = 'id', $order_by = 'ASC')
  {
    $bank_account_fileds = array('id', 'bank_name', 'card_name', 'card_no');
    $this->set_select_fields($bank_account_fileds);
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($bank_account_id_str) {
      if (!empty($bank_account_id_str)) {
        $where_cond = 'id in (' . $bank_account_id_str . ')';
        //查询条件
        $this->dbback_city->where($where_cond);
      }
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据查询条件返回一条银行卡表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的银行卡表记录
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
   * 通过银行卡获取银行卡记录
   * @param int $bank_account_id 银行卡
   * @return array 银行卡记录组成的一维数组
   */
  public function get_by_id($bank_account_id)
  {
    $bank_accounts = array();
    //查询字段
      $bank_account_fileds = array('id', 'bank_name', 'card_name', 'card_no', 'bank_deposit');
      $this->set_select_fields($bank_account_fileds);
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if (is_array($bank_account_id)) {
      $this->dbback_city->where_in('id', $bank_account_id);
      $bank_accounts = $this->dbback_city->get($this->_tbl)->result_array();
    } else if (intval($bank_account_id) >= 0) {
      $this->dbback_city->where('id', $bank_account_id);
      $bank_accounts = $this->dbback_city->get($this->_tbl)->row_array();
    }
    return $bank_accounts;
  }

  /**
   * 设置银行卡的状态
   * @param int $bank_account_id 银行卡编号
   * @param int $status 银行卡状态
   */
  public function set_esta($bank_account_id, $status)
  {
    $data = array();
    $data['status'] = $status;
    $this->update_by_id($data, $bank_account_id);
  }

  /**
   * 查找所有有效银行卡
   * @return array
   */
  function get_bank_account_by()
  {
    $this->dbback_city->where('status', 1);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 添加数据
   * @param $key 键（Key）
   * @param $name 值（Value）
   * @param $name_abbr 值缩写（Value）
   * @param $desc 描述
   * @param int $status 状态
   * @param int $bank_account_type_id 类型
   * @return int 插入成功的编号
   */
  public function add_bank_account($card_no, $card_name, $bank_id, $bank_name, $bank_deposit, $status = 1, $bank_deposit_id = 0, $area_code = '', $area = '')
  {
    $data = array();
    $data['card_no'] = $card_no;
    $data['card_name'] = $card_name;
    $data['bank_id'] = $bank_id;
    $data['bank_name'] = $bank_name;
    $data['bank_deposit'] = $bank_deposit;
    $data['bank_deposit_id'] = $bank_deposit_id;
    $data['area_code'] = $area_code;
    $data['area'] = $area;
    $data['status'] = $status;
    $data['createtime'] = time();
    $result = $this->insert($data);

    return $result;
  }

  /**
   * 修改数据
   * @param $key 键（Key）
   * @param $name 值（Value）
   * @param $name_abbr 值缩写（Value）
   * @param $desc 描述
   * @param int $status 状态
   * @param int $bank_account_type_id 类型
   * @return int 插入成功的编号
   */
  public function update_bank_account($card_no, $card_name, $bank_id, $bank_name, $bank_deposit, $status = 1, $bank_account_type_id = 0, $bank_deposit_id = 0, $area_code = '', $area = '')
  {
    $data = array();
    $data['card_no'] = $card_no;
    $data['card_name'] = $card_name;
    $data['bank_id'] = $bank_id;
    $data['bank_name'] = $bank_name;
    $data['bank_deposit'] = $bank_deposit;
    $data['bank_deposit_id'] = $bank_deposit_id;
    $data['area_code'] = $area_code;
    $data['area'] = $area;
    $data['updatetime'] = time();
    $result = $this->update_by_id($data, $bank_account_type_id);

    return $result;
  }

  /**
   * 插入公司数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的公司id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新数据字典
   * @param array $update_data 更新的数据源数组
   * @param array $dictionary_id 数据字典数组
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $bank_account_id)
  {
    if (is_array($bank_account_id)) {
      $ids = $bank_account_id;
    } else {
      $ids[0] = $bank_account_id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 根据关键字获取银行卡名称
   * @param string $keyword 银行卡名称
   * @param int $limit 显示数量
   * @param array $status 楼盘状态
   * @param string $order_key 排序字段
   * @param string $order 升序降序
   * @return array 小区信息数组
   */
  public function get_bank_account_info_by_kw($keyword = '', $search_arr = 0, $limit = 10, $order_key = 'id', $order = 'ASC')
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($keyword));

    if ($keyword != '' && is_full_array($search_arr)) {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }

      $cond_where = "(concat(`card_name`, `card_no`, `bank_name`, `bank_deposit`) LIKE '%" . $keyword . "%')";

      //设置查询条件
      $this->dbback_city->where($cond_where);
      $this->dbback_city->where('status', 1);
      //全银行卡范围
      if (in_array($search_arr['role_level'], array(1, 2, 3, 4))) {
        //查询
        $cmt_info = $this->dbback_city->get($this->_tbl, $limit)->result_array();
      } else {
        return $cmt_info;
      }

      //echo $this->dbback_city->last_query();
    }

    return $cmt_info;
  }
}

/* End of file department_base_model.php */
/* Location: ./applications/models/department_base_model.php */
