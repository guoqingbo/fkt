<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

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
 * buy_customer_model CLASS
 *
 * 求购客户信息管理类,提供增加、修改、删除、查询 求购客户信息的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
//加载父类文件
load_m('Customer_base_model');

class Buy_customer_model extends Customer_base_model
{

  /**
   * 信息录入经纪人编号
   *
   * @access private
   * @var string
   */
  private $_buy_customer_tbl = 'buy_customer';

  /**
   * 跟进表名
   * @var string
   */
  private $_follow_tbl = 'detailed_follow';

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    //初始化表名称
    $this->set_tbl($this->_buy_customer_tbl);
  }

  /**
   * 处理所有相关公司的求购客源
   */
  public function change_nature_by_agency_id($agency_id, $create_time)
  {
    $agency_id = intval($agency_id);
    $create_time = intval($create_time);
    if ($agency_id && $create_time) {
      $data = array();
      $data['public_type'] = 2;
      $cond_where = "agency_id = '" . $agency_id . "' and public_type = 1 and creattime < '" . $create_time . "'";
      $result = parent::update_info_by_cond($data, $cond_where);
      return $result;
    }
  }

  /**
   * 处理所有非相关公司的出售房源
   */
  public function change_nature_by_agency_id2($agency_id = array(), $create_time)
  {
    $cond_where = '';
    if (!empty($agency_id)) {
      $company_str = implode(',', $agency_id);
      $cond_where = "agency_id not in (" . $company_str . ") and public_type = 1 and creattime < '" . $create_time . "'";
    } else {
      $cond_where = "public_type = 1 and creattime < '" . $create_time . "'";
    }
    $create_time = intval($create_time);
    if ($create_time) {
      $data = array();
      $data['public_type'] = 2;
      $result = parent::update_info_by_cond($data, $cond_where);
      return $result;
    }
  }

  /**
   * 处理所有相关门店的求购客源,是否是公共数据
   */
  public function change_is_public_by_agency_id($customer_id_arr)
  {
    $customer_id_str = '';
    $num = 0;
    if (is_full_array($customer_id_arr)) {
      $customer_id_str = implode(',', $customer_id_arr);
    }
    if (!empty($customer_id_str)) {
      $data = array();
      $data['is_public'] = 1;
      $data['broker_id'] = 0;
      $data['broker_name'] = '';
      if (is_full_array($customer_id_arr)) {
        foreach ($customer_id_arr as $k => $v) {
          $where_cond = array(
            'id' => intval($v)
          );
          $customer_info = parent::get_info_by_cond($where_cond);
          if (is_full_array($customer_info)) {
            if ('0' == $customer_info['is_public']) {
              $result = parent::update_info_by_cond($data, $where_cond);
              if ($result) {
                $num++;
                //对应的房源写跟进
                $follow_arr = array();
                $follow_arr['customer_id'] = intval($v);//客源id
                $follow_arr['follow_way'] = 12;//跟进方式
                $follow_arr['follow_type'] = 3;//跟进类型
                $follow_arr['text'] = '委托人从 ' . $customer_info['broker_name'] . '>> 无';//跟进内容
                $follow_arr['date'] = date('Y-m-d H:i:s');//跟进时间
                $follow_arr['type'] = 3;//类型
                $this->add_follow($follow_arr);
              }
            }
          }
        }
      }
      return $num;
    }
  }

  /**
   * 添加跟进信息
   * @access  public
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function add_follow($data)
  {
    $this->db_city->insert($this->_follow_tbl, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

}

/* End of file buy_customer_model.php */
/* Location: ./applications/mls/models/buy_customer_model.php */
