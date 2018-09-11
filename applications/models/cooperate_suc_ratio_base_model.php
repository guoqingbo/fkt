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
 * cooperate_suc_ratio_model CLASS
 *
 * 合作成功率记录类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Cooperate_suc_ratio_base_model extends MY_Model
{

  /**
   * 合作结果记录表
   * @var string
   */
  protected $tbl_result_record = 'cooperate_result_record';


  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 设置合作结果表名称
   *
   * @access  public
   * @param  string $tbl_name 表名称
   * @return  void
   */
  public function set_tbl($tbl_name)
  {
    $this->tbl_result_record = strip_tags($tbl_name);
  }


  /**
   * 获取合作结果日志表名称
   *
   * @access  public
   * @param  void
   * @return  string 合作表名称
   */
  public function get_tbl()
  {
    return $this->tbl_result_record;
  }


  /*
   * 添加记录
   *
   * @param array $record_arr 插入数据数组
   * @return 插入数据ID
   */
  private function _add_record($record_arr)
  {
    //日志表名称
    $tbl_name = $this->get_tbl();

    $insert_id = 0;

    if ($tbl_name != '' && !empty($record_arr)) {
      $this->db_city->insert($tbl_name, $record_arr);
      //echo $this->db_city->last_query();
      //如果插入成功，则返回插入的id
      if (($this->db_city->affected_rows()) >= 1) {
        $insert_id = $this->db_city->insert_id();
      }
    }

    return $insert_id;
  }


  /*
   * 更新记录
   *
   * @param array $record_arr 需要更新的数据
   * @param string $cond_where 更新数据条件
   * @param boolean $escape 数据更新操作时是否做数据验证
   * @return int 更新影响函数
   */
  private function _update_record($up_arr, $cond_where, $escape = TRUE)
  {
    //日志表名称
    $tbl_name = $this->get_tbl();

    if ($tbl_name == '' || empty($up_arr) || $cond_where == '') {
      return $up_num;
    }

    foreach ($up_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);
    $up_num = $this->db_city->affected_rows();

    return $up_num;
  }


  /**
   * 获取经纪人合作记录
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  array  经纪人合作记录
   */
  protected function get_data_by_cond($cond_where)
  {
    //合作附表
    $tbl_name = $this->get_tbl();

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //查询
    $arr_data = $this->dbback_city->get($tbl_name)->result_array();

    return $arr_data;
  }


  /**
   * 获取经纪人合作记录
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  array  经纪人合作记录
   */
  protected function get_data_num_by_cond($cond_where)
  {
    //合作附表
    $tbl_name = $this->get_tbl();

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //查询
    $num = $this->dbback_city->count_all_results($tbl_name);

    return intval($num);
  }


  /* 添加合作记录
   * @param int $cid 合作编号
   * @param int $broker_id_a 合作经纪人甲方
   * @param int $broker_id_b 合作经纪人乙方
   * @param int $esta 合作状态
   * @return  array 合作结果，出入数据编号ID
   */
  public function add_cooperate_record($cid, $broker_id_a, $broker_id_b, $esta = 2)
  {
    $cid = intval($cid);
    $esta = intval($esta);
    $creattime = time();

    //甲方合作记录
    $record_arr_a['cid'] = $cid;
    $record_arr_a['broker_id'] = intval($broker_id_a);
    $record_arr_a['cop_side'] = 1;
    $record_arr_a['c_esta'] = $esta;
    $record_arr_a['creattime'] = $creattime;

    //已方合作记录
    $record_arr_b['cid'] = $cid;
    $record_arr_b['broker_id'] = intval($broker_id_b);
    $record_arr_b['cop_side'] = 2;
    $record_arr_b['c_esta'] = $esta;
    $record_arr_b['creattime'] = $creattime;

    //添加甲方合作记录
    $insertid_a = $this->_add_record($record_arr_a);

    //添加乙方合作记录
    $insertid_b = $this->_add_record($record_arr_b);

    if (!empty($insertid_a) && !empty($insertid_b)) {
      $result =
        array(
          'ret' => TRUE,
          'data' => array('insertid_a' => $insertid_a, 'insertid_b' => $insertid_b)
        );
    } else {
      $result = array('ret' => FALSE, 'data' => array());
    }

    return $result;
  }


  /* 更新合作记录状态
   *
   * @param int $cid 合作编号
   * @param int $broker_id_a 合作经纪人甲方
   * @param int $broker_id_b 合作经纪人乙方
   * @param int $esta 合作状态
   * @return  array 合作结果，出入数据编号ID
   */
  public function update_cooperate_record($cid, $broker_id_a, $broker_id_b, $esta)
  {
    $cid = intval($cid);
    $esta = intval($esta);
    $endtime = time();

    //甲方合作记录
    $cond_where_a = "cid = '" . $cid . "' AND broker_id = '" . intval($broker_id_a) . "'";
    $up_arr_a['c_esta'] = $esta;
    $up_arr_a['endtime'] = $endtime;
    $up_num_a = $this->_update_record($up_arr_a, $cond_where_a);

    //已方合作记录
    $cond_where_b = "cid = '" . $cid . "' AND broker_id = '" . intval($broker_id_b) . "'";
    $up_arr_b['c_esta'] = $esta;
    $up_arr_b['endtime'] = $endtime;
    $up_num_b = $this->_update_record($up_arr_b, $cond_where_b);

    if (!empty($up_num_a) && !empty($up_num_b)) {
      $result = TRUE;
    } else {
      $result = FALSE;
    }

    return $result;
  }


  /* 计算经纪人合作成功数量
   *
   * @param int $broker_id    经纪人编号
   * @param int $cop_side     1甲方、2乙方
   * @return int 合作成功次数
   */
  public function get_broker_cop_succ_num($brokerid, $cop_side = 0)
  {
    $cond_where = "broker_id = '" . intval($brokerid) . "' ";

    if ($cop_side > 0) {
      $cond_where .= " AND cop_side = '" . intval($cop_side) . "'";
    }

    $cond_where_s = $cond_where . " AND c_esta = 7";

    //合作成功次数
    $cooperate_succ_num = $this->get_data_num_by_cond($cond_where_s);

    return intval($cooperate_succ_num);
  }


  /* 计算经纪人参与合作个数
   *
   * @param int $broker_id    经纪人编号
   * @param int $cop_side     1甲方、2乙方
   * @return int 合作参与个数
   */
  public function get_broker_cop_num($brokerid, $cop_side = 0)
  {
    $cond_where = "broker_id = '" . intval($brokerid) . "' ";

    if ($cop_side > 0) {
      $cond_where .= " AND cop_side = '" . intval($cop_side) . "'";
    }

    //合作总数
    $cooperate_num = $this->get_data_num_by_cond($cond_where);

    return intval($cooperate_num);
  }


  /* 计算经纪人合作成功率数据
   *
   * @param int $broker_id    经纪人编号
   * @param int $cop_side     1甲方、2乙方
   * @return float 合作成功率
   */
  public function get_broker_cop_succ_ratio($brokerid, $cop_side = 0)
  {
    $cop_succ_ratio = 0;    //合作成功数
    $brokerid = intval($brokerid);

    if ($brokerid > 0) {
      $cooperate_succ_num = $this->get_broker_cop_succ_num($brokerid, $cop_side);
      $cooperate_num = $this->get_broker_cop_num($brokerid, $cop_side);

      $cop_succ_ratio = $cooperate_num > 0 ? round($cooperate_succ_num / $cooperate_num, 2) * 100 : 0;
    }

    return $cop_succ_ratio;
  }


  /* 计算经纪人合作成功率相关数据
   *
   * @param int $broker_id    经纪人编号
   * @param int $cop_side     1甲方、2乙方
   * @return  array 合作成功率记录
   */
  public function get_broker_cop_succ_ratio_info($brokerid, $cop_side = 0)
  {
//    $cop_succ_ratio_info = array(); //合作成功率数组
    $cooperate_succ_num = 0;    //合作成功数
    $cooperate_num = 0;     //合作总数

    $brokerid = intval($brokerid);

    if ($brokerid > 0) {
      $cooperate_succ_num = $this->get_broker_cop_succ_num($brokerid, $cop_side);
      $cooperate_num = $this->get_broker_cop_num($brokerid, $cop_side);

      $cop_succ_ratio = $cooperate_num > 0 ? round($cooperate_succ_num / $cooperate_num, 2) * 100 : 0;
    }

    //合作成功率数据
    $cop_succ_ratio_info = array('cooperate_succ_num' => $cooperate_succ_num,
      'cooperate_num' => $cooperate_num,
      'cop_succ_ratio' => $cop_succ_ratio);

    return $cop_succ_ratio_info;
  }


  /* 合作成功总次数
   *
   * @param   int $creattime 合作开始时间默认是3个月内的合作
   * @return  float 合作成功率
   */
  public function get_total_succ_num($creattime = 0)
  {
    $count_time = $creattime > 0 ? $creattime : time() - 86400 * 90;
    $cond_where = "creattime >= '" . intval($creattime) . "' AND c_esta = '7'";

    //合作成功总数
    $cooperate_succ_num = $this->get_data_num_by_cond($cond_where);

    return intval($cooperate_succ_num);
  }


  /* 合作总次数
   *
   * @param   int $creattime 合作开始时间默认是3个月内的合作
   * @return  float 合作成功率
   */
  public function get_total_num($creattime = 0)
  {
    $count_time = $creattime > 0 ? $creattime : time() - 86400 * 90;
    $cond_where = "creattime >= '" . intval($creattime) . "' ";

    //合作总数
    $cooperate_num = $this->get_data_num_by_cond($cond_where);

    return intval($cooperate_num);
  }


  /* 获取合作平均成功率
   *
   * @param   void
   * @return  float 合作成功率
   */
  public function get_avg_succ_ratio($creattime = 0)
  {
    //合作总数
    $cooperate_num = $this->get_total_num($creattime);

    //合作成功次数
    $cooperate_succ_num = $this->get_total_succ_num($creattime);

    $cop_succ_ratio = $cooperate_num > 0 ? round($cooperate_succ_num / $cooperate_num, 2) * 100 : 0;

    return $cop_succ_ratio;
  }


  /* 更新经纪人合作成功率数据
   *
   * @param   int $broker_id 经纪人编号
   * @param   int $cop_side  1甲方、2乙方
   * @return  float 合作成功率
   */
  public function update_broker_succ_raito($broker_id, $cop_side = 0)
  {
    $update_num = 0;
    $broker_id = intval($broker_id);

    if ($broker_id > 0) {
      $cop_succ_ratio = $this->get_broker_cop_succ_ratio($broker_id, $cop_side);

      $update_data['cop_suc_ratio'] = floatval($cop_succ_ratio);
      $this->load->model('broker_info_base_model');
      $update_num = $this->broker_info_base_model->update_by_broker_id($update_data, $broker_id);

      return $update_num;
    }
  }
}

/* End of file cooperate_suc_ratio_base_model.php */
/* Location: ./application/models/cooperate_suc_ratio_base_model.php */
