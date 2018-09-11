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
 * Product_manage_base_model CLASS
 *
 * 商品添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Gift_manage_base_model extends MY_Model
{

  /**
   * 商品管理表
   * @var string
   */
  private $_tbl = 'gift_manage';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
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
   * 获取礼品列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->order_by('order', 'ASC');
    $this->dbback_city->order_by('release_time', 'DESC');
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  public function get_one_new_by($where, $start = -1, $limit = 1,
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
   * 获取抽奖商品排序order数值数组
   * @param int $gift_id
   * @return array
   */
  public function get_raffle_order()
  {
    $where = 'type = 2 and status = 1';
    $gift_array = $this->get_all_by($where, 0, 0);
    $order_array = array();
    if (is_full_array($gift_array)) {
      foreach ($gift_array as $key => $vo) {
        $order_array[] = $vo['order'];
      }
    }
    return $order_array;
  }

  /**
   * 获取有效抽奖商品中奖率总和
   * @param int $gift_id
   * @return array
   */
  public function get_raffle_rate()
  {
    $where = 'type = 2 and status = 1';
    $gift_array = $this->get_all_by($where, 0, 0);
    $rate = 0;
    if (is_full_array($gift_array)) {
      foreach ($gift_array as $key => $vo) {
        $rate += $vo['rate'];
      }
    }
    return $rate;
  }

  /**
   * 通过礼品ID获取记录
   * @param int $gift_id
   * @return array
   */
  public function get_by_id($gift_id)
  {
    $gift = array();
    //查询字段
    if ($this->_select_fields) {
      $this->db_city->select($this->_select_fields);
    }
    if (is_array($gift_id)) {
      $this->db_city->where_in('id', $gift_id);
      $gift = $this->db_city->get($this->_tbl)->result_array();
    } else if (intval($gift_id) > 0) {
      $this->db_city->where('id', $gift_id);
      $gift = $this->db_city->get($this->_tbl)->row_array();
    }
    return $gift;
  }

  /**
   * 通过礼品编号获取商品信息
   * @param int $gift_id
   * @return array
   */
  public function get_by_product_serial_num($product_serial_num)
  {
    $gift = array();
    //查询字段
    $this->db_city->select('id');
    if (trim($product_serial_num)) {
      $where = "product_serial_num like '%" . $product_serial_num . "%'";
      $this->db_city->where($where);
      $gift = $this->db_city->get($this->_tbl)->result_array();
    }
    return $gift;
  }

  /**
   * 添加商品管理数据
   */
  function add_product_data($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->_tbl);
    return $result;
  }

  /**
   * 修改礼品信息
   */
  function update_by_id($id, $paramlist = array(), $database = 'db_city')
  {
    $where = 'stock > 0';
    $this->db_city->where($where);
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->_tbl);
    return $result;
  }

  //后台跟新上下架
  function update_status_by_id($id, $paramlist = array(), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->_tbl);
    return $result;
  }

  /**
   * 根据礼品ID删除对应礼品数据
   * @param int $id ID
   * @return boolean true 成功 false 失败
   */
  public function delete_by_id($id)
  {
    $this->db_city->where('id', $id);
    //$this->db_city->delete($this->_tbl);
    $this->db_city->update($this->_tbl, array('status' => 3));
    return $this->db_city->affected_rows() > 0 ? true : false;
  }

  public function count_data_by_cond($where = '')
  {
    $this->dbback_city->select('count(*) as nums');
    $this->dbback_city->from($this->_tbl);
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['nums'];
  }

  public function get_data_by_cond($where, $start = 0, $limit = 20,
                                   $order_key = 'id', $order_by = 'ASC')
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from($this->_tbl);
    $this->dbback_city->where($where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 礼品竞换
   * @param int $broker_id 经纪人编号
   * @param int $gift_id 礼品编号
   * @return array
   */
  public function exchange($broker_id, $gift_id)
  {
    $arr_result = array('status' => 0, 'code' => 0, 'msg' => '');
    //一、判断商品是否存在$gift_id   code = 1
    $gift = $this->get_by_id($gift_id);
    if (!is_full_array($gift) || $gift['status'] == 2) {
      $arr_result['status'] = 0;
      $arr_result['code'] = 1;
      $arr_result['msg'] = '礼品不存在，或已下架';
      return $arr_result;
    }
    //二、兑换奖品给经纪人扣除相应的积分 2
    $this->load->model('broker_info_model');
    $credit = $this->broker_info_model->get_credit_by_broker_id($broker_id);
    if ($credit < abs($gift['score'])) //积分不足
    {
      $arr_result['status'] = 0;
      $arr_result['code'] = 2;
      $arr_result['msg'] = '积分不足，兑换失败';
      return $arr_result;
    }
    //三、兑换礼品
    $this->load->model('api_broker_credit_base_model');
    $this->api_broker_credit_base_model->set_broker_param(array('broker_id' => $broker_id));
    $exchange_result = $this->api_broker_credit_base_model->
    gift_exchange($gift['score'], '兑换' . $gift['product_name']);
    if ($exchange_result['status'] != 1) //竞换失败
    {
      $arr_result['status'] = 0;
      $arr_result['code'] = 3;
      $arr_result['msg'] = '兑换失败,请重试';
      return $arr_result;
    }
    //三、竞换成功后插入到商品记录里
    $this->load->model('gift_exchange_record_base_model');
    $new = $this->gift_exchange_record_base_model->get_one_new_by(
      $where = '');
    if (empty($new)) {
      $new_data['id'] = 1;
    } else {
      $new_data['id'] = $new[0]['id'] + 1;
    }
    $insert_data = array(
      'broker_id' => $broker_id, 'order' => date('Ymd') . sprintf("%05d", $new_data['id']),
      'gift_id' => $gift_id, 'score' => $gift['score'],
      'create_time' => time()
    );
    $_id = $this->gift_exchange_record_base_model->insert($insert_data);
    $arr_result['status'] = 1;
    $arr_result['msg'] = '操作成功';
    $arr_result['id'] = $_id;
    return $arr_result;
  }

  /**
   * 礼品抽奖
   * @param int $broker_id 经纪人编号
   * @param int $gift_id 礼品编号
   * @return array
   */
  public function raffle($broker_id, $gift_id, $add_score = 0)
  {
    $arr_result = array('status' => 0, 'code' => 0, 'msg' => '');
    //一、判断商品是否存在$gift_id   code = 1
    $gift = $this->get_by_id($gift_id);
    if (!is_full_array($gift) || $gift['status'] == 2) {
      $arr_result['status'] = 0;
      $arr_result['code'] = 1;
      $arr_result['msg'] = '商品不存在，或已下架';
      return $arr_result;
    }
    //二、抽奖奖品给经纪人扣除相应的积分 2
    $this->load->model('broker_info_model');
    $credit = $this->broker_info_model->get_credit_by_broker_id($broker_id);
    if ($credit < abs($gift['score'])) //积分不足
    {
      $arr_result['status'] = 0;
      $arr_result['code'] = 2;
      $arr_result['msg'] = '您当前的积分不足！<br />攒够积分下次再来抽奖吧。';
      return $arr_result;
    }
    //三、抽奖礼品
    $this->load->model('api_broker_credit_base_model');
    $this->api_broker_credit_base_model->set_broker_param(array('broker_id' => $broker_id));
    if ($add_score) {
      $exchange_result = $this->api_broker_credit_base_model->gift_raffle(500, '抽奖-500积分', '抽奖+' . $gift['product_name'], $add_score);
    } else {
      $exchange_result = $this->api_broker_credit_base_model->gift_raffle($gift['score'], '抽奖-' . $gift['product_name']);
    }
    if ($exchange_result['status'] != 1) //竞换失败
    {
      $arr_result['status'] = 0;
      $arr_result['code'] = 3;
      $arr_result['msg'] = '抽奖失败,请重试';
      return $arr_result;
    }
    //三、抽奖成功后插入到商品记录里
    $this->load->model('gift_exchange_record_base_model');
    $new = $this->gift_exchange_record_base_model->get_one_new_by(
      $where = '');
    if (empty($new)) {
      $new_data['id'] = 1;
    } else {
      $new_data['id'] = $new[0]['id'] + 1;
    }
    $insert_data = array(
      'broker_id' => $broker_id, 'order' => date('Ymd') . sprintf("%05d", $new_data['id']),
      'gift_id' => $gift_id, 'score' => $gift['score'],
      'create_time' => time(), 'type' => 2
    );
    $_id = $this->gift_exchange_record_base_model->insert($insert_data);
    $arr_result['status'] = 1;
    $arr_result['msg'] = '操作成功';
    $arr_result['id'] = $_id;
    return $arr_result;
  }


  /**
   * 获取奖品类型
   */
  public function get_reward_type()
  {
    $where = 'type = 2 and status = 1';
    $gift_array = $this->get_all_by($where, 0, 10);
    $gift_array_new = array();
    if (is_full_array($gift_array)) {
      foreach ($gift_array as $key => $vo) {
        $gift_array_new[$vo['order']] = array('id' => $vo['order'], 'name' => $vo['product_name'], 'writer' => '恭喜中奖！', 'num' => $vo['order'], 'rate' => $vo['rate'], 'gift_id' => $vo['id'], 'score' => $vo['score']);
      }
    }
    //给未中奖分配id与num值(转盘0点位置按格数开始)
    $get_raffle_rate = 1 - ($this->get_raffle_rate());
    $gift_array_new[3]['rate'] = ($get_raffle_rate / 2);
    $gift_array_new[8]['rate'] = ($get_raffle_rate / 2);
    return $gift_array_new;
  }


  //判断抽奖是否在活动期内
  public function is_active_intime_lottery()
  {
    $start_time = strtotime('2015-04-20 00:00:00');
    //$end_time = strtotime('2016-02-01 14:00:00');
    $current_time = time();
    //return ($current_time >= $start_time && $current_time <= $end_time) ? true : false;
    return ($current_time >= $start_time) ? true : false;
  }
}

/* End of file agency_base_model.php */
/* Location: ./applications/models/agency_base_model.php */
