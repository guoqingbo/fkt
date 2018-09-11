<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 浏览日志累
 *
 * 业务类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * 客源、房源查看日志类
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          liuhu
 */
class View_log_model extends MY_Model
{

  /**
   * 求购客源浏览日志表
   *
   * @access private
   * @var string
   */
  private $_buy_customer_view_tbl = 'customer_view_log';


  /**
   * 求租客源浏览日志表
   *
   * @access private
   * @var string
   */
  private $_rent_customer_view_tbl = 'rent_customer_view_log';

  private $_sell_house_view_tbl = 'sell_view_log';
  private $_rent_house_view_tbl = 'rent_view_log';
  private $_collect_click_log_tbl = 'collect_click_log';//采集点击查看日志表

  /**
   * 构造函数
   */
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 获取日志表名称
   * @param string $type 日志类型
   * @return string 表名称
   */
  public function get_log_tbl($type)
  {
    $tbl_name = '';
    $type = strip_tags($type);

    switch ($type) {
      case 'buy_customer':
        $tbl_name = $this->_buy_customer_view_tbl;
        break;
      case 'rent_customer':
        $tbl_name = $this->_rent_customer_view_tbl;
        break;
      case 'sell':
        $tbl_name = $this->_sell_house_view_tbl;
        break;
      case 'rent':
        $tbl_name = $this->_rent_house_view_tbl;
        break;
    }

    return $tbl_name;
  }


  /**
   * 添加信息
   *
   * @access    protected
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  private function _add_info($type, $data_info)
  {
    $tbl_name = $this->get_log_tbl($type);

    if ($tbl_name != '' && is_array($data_info) && !empty($data_info)) {
      $this->db_city->insert($tbl_name, $data_info);
      return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
    }
  }


  /**
   * 更新信息
   *
   * @access    protected
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  private function _update_num_by_cond($type, $cond_where)
  {
    $tbl_name = $this->get_log_tbl($type);

    $msg = 0;
    if ($tbl_name != '' && $cond_where != '') {
      $this->db_city->where($cond_where);
      $this->db_city->set('num', 'num+1', FALSE);
      $this->db_city->update($tbl_name);
      $msg = ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : 0;
    }

    return $msg;
  }


  /**
   * 记录客源浏览日志
   *
   * @access    public
   * @param string $type 日志类型
   * @param string $cid 客源编号
   * @param string $department_id 客源所属门店编号
   * @param string $signatory_id 客源所属经纪人编号
   * @param string $department_id_v 查看经纪人门店编号
   * @param string $department_name_v 查看经纪人门店名称
   * @param string $signatory_id_v 查看经纪人编号
   * @param string $signatory_name_v 查看经纪人姓名
   * @param string $signatory_telno_v 查看经纪人手机号码
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function add_customer_view_log($type, $cid, $department_id, $signatory_id,
                                        $department_id_v, $department_name_v, $signatory_id_v, $signatory_name_v, $signatory_telno_v)
  {
    $log_arr = array();
    $cid = intval($cid);
    $department_id = intval($department_id);
    $signatory_id = intval($signatory_id);
    $department_id_v = intval($department_id_v);
    $signatory_id_v = intval($signatory_id_v);
    $cond_where = "c_id = '" . $cid . "' AND  department_id = '" . $department_id . "' AND "
      . "signatory_id = '" . $signatory_id . "' AND department_id_v = '" . $department_id_v . "' "
      . "AND signatory_id_v = '" . $signatory_id_v . "'";
    $log_num = $this->get_view_log_num_by_cond($type, $cond_where);

    if (intval($log_num) == 0) {
      $log_arr['c_id'] = intval($cid);
      $log_arr['department_id'] = intval($department_id);
      $log_arr['signatory_id'] = intval($signatory_id);
      $log_arr['department_id_v'] = intval($department_id_v);
      $log_arr['department_name_v'] = strip_tags($department_name_v);
      $log_arr['signatory_id_v'] = intval($signatory_id_v);
      $log_arr['signatory_name_v'] = strip_tags($signatory_name_v);
      $log_arr['signatory_telno_v'] = strip_tags($signatory_telno_v);
      $log_arr['datetime'] = time();
      $log_arr['ip'] = get_ip();
      $result = $this->_add_info($type, $log_arr);
    } else {
      $result = $this->_update_num_by_cond($type, $cond_where);
    }

    return $result;
  }

  /**
   * 记录房源浏览日志
   *
   * @access    public
   * @param string $type 日志类型
   * @param string $hid 房源编号
   * @param string $department_id 房源所属门店编号
   * @param string $signatory_id 房源所属经纪人编号
   * @param string $department_id_v 查看经纪人门店编号
   * @param string $department_name_v 查看经纪人门店名称
   * @param string $signatory_id_v 查看经纪人编号
   * @param string $signatory_name_v 查看经纪人姓名
   * @param string $signatory_telno_v 查看经纪人手机号码
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function add_house_view_log($type, $hid, $department_id, $signatory_id,
                                     $department_id_v, $department_name_v, $signatory_id_v, $signatory_name_v, $signatory_telno_v)
  {
    $log_arr = array();
    $hid = intval($hid);
    $department_id = intval($department_id);
    $signatory_id = intval($signatory_id);
    $department_id_v = intval($department_id_v);
    $signatory_id_v = intval($signatory_id_v);
    $cond_where = "h_id = '" . $hid . "' AND  department_id = '" . $department_id . "' AND "
      . "signatory_id = '" . $signatory_id . "' AND department_id_v = '" . $department_id_v . "' "
      . "AND signatory_id_v = '" . $signatory_id_v . "'";
    $log_num = $this->get_view_log_num_by_cond($type, $cond_where);

    if (intval($log_num) == 0) {
      $log_arr['h_id'] = intval($hid);
      $log_arr['department_id'] = intval($department_id);
      $log_arr['signatory_id'] = intval($signatory_id);
      $log_arr['department_id_v'] = intval($department_id_v);
      $log_arr['department_name_v'] = strip_tags($department_name_v);
      $log_arr['signatory_id_v'] = intval($signatory_id_v);
      $log_arr['signatory_name_v'] = strip_tags($signatory_name_v);
      $log_arr['signatory_telno_v'] = strip_tags($signatory_telno_v);
      $log_arr['datetime'] = time();
      $log_arr['ip'] = get_ip();
      $result = $this->_add_info($type, $log_arr);
    } else {
      $result = $this->_update_num_by_cond($type, $cond_where);
    }

    return $result;
  }


  /**
   * 根据条件获取客源、房源日志数量
   *
   * @access    protected
   * @param string $type 日志类型
   * @param string $cond_where 查询条件
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  protected function get_view_log_num_by_cond($type, $cond_where = '')
  {
    $log_num = 0;
    $tbl_name = $this->get_log_tbl($type);

    if ($tbl_name != '' && $cond_where != '') {
      $this->dbback_city->where($cond_where);
      $log_num = $this->dbback_city->count_all_results($tbl_name);
    }

    return $log_num;
  }


  /**
   * 根据条件获取客源、房源日志数据
   *
   * @access    protected
   * @param string $type 日志类型
   * @param string $cond_where 查询条件
   * @param int $offset 查询偏移量
   * @param int $limit 查询条数
   * @param string $order_key 查询排序字段
   * @param string $order_by 排序
   * @return array 日志数组
   */
  protected function get_view_log_list_by_cond($type, $cond_where = '',
                                               $offset = 0, $limit = 10, $order_key = 'datetime', $order_by = 'DESC')
  {
    $log_list = array();
    $tbl_name = $this->get_log_tbl($type);
    if ($tbl_name != '' && $cond_where != '') {
      $this->dbback_city->where($cond_where);
      $this->dbback_city->order_by($order_key, $order_by);
      $this->dbback_city->limit($limit, $offset);

      $log_list = $this->dbback_city->get($tbl_name)->result_array();
    }

    return $log_list;
  }


  //根据客源编号获取客源日志数量
  public function get_view_log_num_by_cid($type, $cid)
  {
    $log_num = 0;

    $cid = intval($cid);
    $cond_where = "c_id = '" . $cid . "'";
    $log_num = $this->get_view_log_num_by_cond($type, $cond_where);

    return $log_num;
  }

  //根据房源编号获取房源日志数量
  public function get_view_log_num_by_hid($type, $hid)
  {
    $log_num = 0;

    $hid = intval($hid);
    $cond_where = "h_id = '" . $hid . "'";
    $log_num = $this->get_view_log_num_by_cond($type, $cond_where);

    return $log_num;
  }


  /**
   * 根据客源编号获取客源日志数据
   *
   * @access    protected
   * @param string $type 日志类型
   * @param string $cond_where 查询条件
   * @param int $offset 查询偏移量
   * @param int $limit 查询条数
   * @param string $order_key 查询排序字段
   * @param string $order_by 排序
   * @return array 日志数组
   */
  public function get_view_log_list_by_cid($type, $cid, $offset = 0, $limit = 10,
                                           $order_key = 'datetime', $order_by = 'DESC')
  {
    $log_list = array();

    $cid = intval($cid);
    $cond_where = "c_id = '" . $cid . "'";
    $log_list = $this->get_view_log_list_by_cond($type, $cond_where, $offset, $limit, $order_key, $order_by);

    return $log_list;
  }


  /**
   * 根据房源编号获取房源日志数据
   *
   * @access    protected
   * @param string $type 日志类型
   * @param string $cond_where 查询条件
   * @param int $offset 查询偏移量
   * @param int $limit 查询条数
   * @param string $order_key 查询排序字段
   * @param string $order_by 排序
   * @return array 日志数组
   */
  public function get_view_log_list_by_hid($type, $hid, $offset = 0, $limit = 10,
                                           $order_key = 'datetime', $order_by = 'DESC')
  {
    $log_list = array();

    $hid = intval($hid);
    $cond_where = "h_id = '" . $hid . "'";
    $log_list = $this->get_view_log_list_by_cond($type, $cond_where, $offset, $limit, $order_key, $order_by);

    return $log_list;
  }

  /**
   * 采集详情页点击查看按钮加入日志表
   * 2016.2.28
   * cc
   */
  public function add_collect_click_log($signatory_id, $house_id, $type)
  {
    $data = array(
      'signatory_id' => $signatory_id,
      'house_id' => $house_id,
      'type' => $type,
      'createtime' => time()
    );
    $result = $this->add_data($data, 'db_city', $this->_collect_click_log_tbl); //1表示去重复插入
    return $result;
  }

}

/* End of file view_log_model.php */
/* Location: ./applications/models/view_log_model.php */
