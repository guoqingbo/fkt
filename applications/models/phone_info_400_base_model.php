<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * 400电话业务类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Phone_info_400_base_model CLASS
 *
 * 400电话数据管理相关功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          yuan
 */
class Phone_info_400_base_model extends MY_Model
{
  /**
   * 表名称
   * @var string
   */
  //400电话关联表
  private $_tbl = 'phone_info_400';
  //通话记录
  private $_tb2 = 'phone_recoder_400';
  //查询转接号码接口日志
  private $_tb3 = 'phone_400_api_log_1';
  //通话记录接口日志
  private $_tb4 = 'phone_400_api_log_2';


  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';


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
   * @return  string 区属表名称
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

    $this->select_fields = $select_fields;
  }

  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->select_fields;
  }

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  //添加数据
  public function insert_data($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db->insert($this->_tbl, $paramlist);//插入数据

      if (($this->db->affected_rows()) >= 1) {
        $result = $this->db->insert_id();//如果插入成功，则返回插入的id
      } else {
        $result = 0;    //如果插入失败,返回0
      }
    } else {
      $result = 0;
    }

    return $result;
  }

  //修改数据
  function update_data($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->get_tbl();

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db->set($key, $value, $escape);
    }

    //设置条件
    $this->db->where($cond_where);

    //更新数据
    $this->db->update($tbl_name);

    return $this->db->affected_rows();
  }

  //选择一定短号范围内的一条随机未分配数据
  function get_one_by_num_group($start_num = 0, $end_num = 0)
  {
    $result = false;
    if ($start_num > 0 && $end_num > 0) {
      $rand_num = rand($start_num, $end_num);
      $sql = 'select id from ' . $this->_tbl . ' where status = "1" and num_group >= ' . $rand_num . ' limit 1';
      $result_data = $this->dbback->query($sql)->result_array();
      if (is_full_array($result_data)) {
        $result = $result_data[0];
      }
    }
    return $result;
  }

  //根据条件，获得数据
  function get_data_by_cond($where_cond = array())
  {

    if (is_full_array($where_cond)) {
      //房源需求信息表
      $tbl = $this->get_tbl();
      $this->dbback->where($where_cond);
      return $this->dbback->get($tbl)->result_array();
    } else {
      return false;
    }
  }

  //通话记录，添加数据
  function insert_data_tb2($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db->insert($this->_tb2, $paramlist);//插入数据

      if (($this->db->affected_rows()) >= 1) {
        $result = $this->db->insert_id();//如果插入成功，则返回插入的id
      } else {
        $result = 0;    //如果插入失败,返回0
      }
    } else {
      $result = 0;
    }

    return $result;
  }

  //查询号码接口日志
  function insert_data_tb3($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db->insert($this->_tb3, $paramlist);//插入数据

      if (($this->db->affected_rows()) >= 1) {
        $result = $this->db->insert_id();//如果插入成功，则返回插入的id
      } else {
        $result = 0;    //如果插入失败,返回0
      }
    } else {
      $result = 0;
    }

    return $result;
  }

  //通话记录接口日志
  function insert_data_tb4($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db->insert($this->_tb4, $paramlist);//插入数据

      if (($this->db->affected_rows()) >= 1) {
        $result = $this->db->insert_id();//如果插入成功，则返回插入的id
      } else {
        $result = 0;    //如果插入失败,返回0
      }
    } else {
      $result = 0;
    }

    return $result;
  }

  //根据条件，获得接口2日志
  function get_log_data_by_cond($where_cond = array())
  {

    if (is_full_array($where_cond)) {
      $this->dbback->where($where_cond);
      return $this->dbback->get($this->_tb4)->result_array();
    } else {
      return false;
    }
  }


  /**
   * 获取总数
   */
  function get_phone_num($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback->where($where);
    }
    return $this->dbback->count_all_results($this->_tbl);
  }

  /**
   * 获得
   */
  public function get_phone($where = array(), $offset = -1, $pagesize = 20)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->dbback->where($where);
    }
    if ($offset >= 0 && $pagesize > 0) {
      $this->dbback->limit($pagesize, $offset);
    }
    //返回结果
    return $this->dbback->get($this->_tbl)->result_array();
  }

  /**
   * 修改
   */
  function modify_phone($id, $paramlist = array(), $database = 'db')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->_tbl);
    return $result;
  }


}


/* End of file block_base_model.php */
/* Location: ./models/block_base_model.php */
