<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
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
 * Cooperate_model CLASS
 *
 * 房客源合作业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
load_m("Cooperate_base_model");

class Cooperate_model extends Cooperate_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();

    //设置表名称
    $this->set_table('cooperate');
    $this->set_attached_tbl('cooperate_attached');
    $this->set_log_tbl('cooperate_log');
  }


  /**
   * 根据条件获取成交合作记录条数
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  int   符合条件成交房源条数
   */
  public function get_transaction_records_num_by_cond($cond_where = '')
  {
    $cooperate_num = 0;
    $cond_where = trim(strip_tags($cond_where));

    $cond_where = !empty($cond_where) ? $cond_where . " AND esta = 7" : "esta = 7";
    $cooperate_num = parent::get_cooperate_num_by_cond($cond_where);

    return $cooperate_num;
  }


  /**
   * 根据条件获取成交合作记录
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值dateline最后更新时间
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   成交合作列表数组
   */
  public function get_transaction_records_by_cond($cond_where = '', $offset = 0, $limit = 10,
                                                  $order_key = 'dateline', $order_by = 'DESC')
  {
    $cooperate_list = array();
    $cond_where = trim(strip_tags($cond_where));

    $cond_where = !empty($cond_where) ? $cond_where . " AND esta = 7" : "esta = 7";
    $cooperate_list = parent::get_list_by_cond($cond_where, $offset, $limit, $order_key, $order_by);

    return $cooperate_list;
  }


  /**
   * 提交真实交易总价
   *
   * @access  public
   * @param  int $cid 合同编号
   * @param  float $real_price 真实总价
   * @return  int 更新影响行数
   */
  public function sub_transaction_real_price($cid, $real_price)
  {
    $up_num = 0;

    $up_arr = array();
    $up_arr['real_price'] = floatval($real_price);
    $up_arr['dateline'] = time();

    $cond_where = "id = '" . $cid . "'";
    $up_num = parent::update_info_by_cond($up_arr, $cond_where);

    return $up_num;
  }


  /**
   * 获取合同操作日志
   *
   * @access    public
   * @param    int $cid 合同编号
   * @return    array 合同日志
   */
  public function get_cooperation_log_by_cid($cid)
  {
    $log_arr = array();
    $cid = intval($cid);

    if ($cid > 0) {
      $tbl_name = $this->get_log_tbl();
      $cond_where = "cid = " . $cid;
      $this->dbback_city->where($cond_where);
      $log_arr = $this->dbback_city->get($tbl_name)->result_array();
    }

    return $log_arr;


  }

}



/* End of file cooperate_model.php */
/* Location: ./app/models/cooperate_model.php */
