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
 * group_refresh_base_model CLASS
 *
 * 群发群发
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class Group_refresh_base_model extends MY_Model
{
  private $group_refresh_tbl = 'group_refresh_log';

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }


  public function add_refresh_log($data_info, $database = 'db_city')
  {
    return $this->add_data($data_info, $database, $this->group_refresh_tbl);
  }

  public function get_refresh_log($data_info)
  {

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
    return $this->dbback_city->count_all_results($this->group_refresh_tbl);
  }

  /**
   * 根据经纪人分组统计符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function group_broker_count_by($where)
  {
    //查询字段
    $this->dbback_city->select(array('count(distinct(broker_id)) as num'));
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $arr_num = $this->dbback_city->get($this->group_refresh_tbl)->row_array();
    return $arr_num['num'];
  }

  /**
   * 获取统计总数列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司审核记录组成的二维数组
   */
  public function get_all_by($where, $start = 0, $limit = 20,
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
    return $this->dbback_city->get($this->group_refresh_tbl)->result_array();
  }

  /**
   * 根据查询条件返回一条统计数的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的公司审核表记录
   */
  public function get_one_by($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->group_refresh_tbl)->row_array();
  }

  /**
   * 通过编号获取统计总数记录
   * @param int $agency_id 公司审核编号
   * @return array 公司审核记录组成的一维数组
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->group_refresh_tbl)->row_array();
  }

  /**
   * 插入公司审核数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的公司审核id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->group_refresh_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->group_refresh_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新公司审核数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 编号数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->group_refresh_tbl, $update_data);
    } else {
      $this->db_city->update($this->group_refresh_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 更新某条房源需求信息
   *
   * @access  protected
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_info_by_cond($update_arr, $cond_where, $escape = TRUE)
  {

    if ($this->group_refresh_tbl == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($this->group_refresh_tbl);

    return $this->db_city->affected_rows();
  }

  /* 群发刷新统计日志
     * 1 58同城 2 58网邻通 3 赶集 4 赶集vip 5 安居客 6房天下 7 365淘房
     */
  public function info_count($broker_info, $state, $house = array(), $is_log = 0)
  {
    $count_num_info = $this->get_one_by('broker_id = ' . $broker_info['broker_id'] . ' and YMD = ' . "'" . date('Y-m-d') . "'");

    if (is_full_array($count_num_info)) {
      //修改数据
      switch ($state) {
        case 1://58同城
          $update_data = array(
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'wuba_num' => $count_num_info['wuba_num'] + 1
          );
          break;
        case 2://58网邻通
          $update_data = array(
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'wuba_vip_num' => $count_num_info['wuba_vip_num'] + 1
          );
          break;
        case 3://赶集
          $update_data = array(
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'ganji_num' => $count_num_info['ganji_num'] + 1
          );
          break;
        case 4://赶集vip
          $update_data = array(
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'ganji_vip_num' => $count_num_info['ganji_vip_num'] + 1
          );
          break;
        case 5://安居客
          $update_data = array(
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'anjuke_num' => $count_num_info['anjuke_num'] + 1
          );
          break;
        case 6://房天下
          $update_data = array(
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'fang_num' => $count_num_info['fang_num'] + 1
          );
          break;
        case 7://365淘房
          $update_data = array(
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'taofang_num' => $count_num_info['taofang_num'] + 1
          );
          break;
      }
      $row = $this->update_by_id($update_data, $count_num_info['id']);
      if ($row) {
        return 'success';
      } else {
        return 'error';
      }
    } else {
      if (!isset($broker_info['agency_id'])) {
        $filter = array('broker_id' => $broker_info['broker_id']);
        $tempInfo = $this->dbback_city->where($filter)->get('broker_info')->row_array();
        $broker_info['agency_id'] = $tempInfo['agency_id'];
        $broker_info['company_id'] = $tempInfo['company_id'];
      }
      //添加数据
      switch ($state) {
        case 1://58同城
          $insert_num_data = array(
            'company_id' => $broker_info['company_id'],
            'agency_id' => $broker_info['agency_id'],
            'broker_id' => $broker_info['broker_id'],
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'wuba_num' => 1
          );
          break;
        case 2://58网邻通
          $insert_num_data = array(
            'company_id' => $broker_info['company_id'],
            'agency_id' => $broker_info['agency_id'],
            'broker_id' => $broker_info['broker_id'],
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'wuba_vip_num' => 1
          );
          break;
        case 3://赶集
          $insert_num_data = array(
            'company_id' => $broker_info['company_id'],
            'agency_id' => $broker_info['agency_id'],
            'broker_id' => $broker_info['broker_id'],
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'ganji_num' => 1
          );
          break;
        case 4://赶集vip
          $insert_num_data = array(
            'company_id' => $broker_info['company_id'],
            'agency_id' => $broker_info['agency_id'],
            'broker_id' => $broker_info['broker_id'],
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'ganji_vip_num' => 1
          );
          break;
        case 5://安居客
          $insert_num_data = array(
            'company_id' => $broker_info['company_id'],
            'agency_id' => $broker_info['agency_id'],
            'broker_id' => $broker_info['broker_id'],
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'anjuke_num' => 1
          );
          break;
        case 6://房天下
          $insert_num_data = array(
            'company_id' => $broker_info['company_id'],
            'agency_id' => $broker_info['agency_id'],
            'broker_id' => $broker_info['broker_id'],
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'fang_num' => 1
          );
          break;
        case 7://365淘房
          $insert_num_data = array(
            'company_id' => $broker_info['company_id'],
            'agency_id' => $broker_info['agency_id'],
            'broker_id' => $broker_info['broker_id'],
            'dateline' => time(),
            'YMD' => date('Y-m-d'),
            'taofang_num' => 1
          );
          break;
      }
      $insert_num_id = $this->insert($insert_num_data);
      if ($insert_num_id) {
        return 'success';
      } else {
        return 'error';
      }
    }
  }

  //群发 刷新 成功 or 失败 日志  :wjy
  public function add_message_log($param)
  {
    $this->db_city->insert('group_refresh_msg', $param);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }
}

/* End of file group_refresh_base_model.php */
/* Location: ./application/models/group_refresh_base_model.php */
