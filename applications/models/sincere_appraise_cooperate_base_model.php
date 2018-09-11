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
 * Sincere_appraise_cooperate_base_model CLASS
 *
 * 经纪人的合作评价管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Sincere_appraise_cooperate_base_model extends MY_Model
{

  /**
   * 信用表
   * @var string
   */
  private $_tbl = 'sincere_appraise_cooperate';

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
   * 获取合作评价记录列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = 'id', $order_by = 'desc')
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
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 判断此交易合同是否已经评论过
   * @param int $broker_id 经纪人编号
   * @param string $transaction_id 交易号
   * @return int 返回的行数
   */
  public function is_exist_transaction($broker_id, $transaction_id)
  {
    //查询条件
    $this->db_city->where('broker_id', $broker_id);
    $this->db_city->where('transaction_id', $transaction_id);
    return $this->db_city->count_all_results($this->_tbl);
  }

  /**
   * 通过评价编号获取记录
   * @param int $id 编号
   * @return array 记录组成的一维数组
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 插入评价数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的评价id 失败 false
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
   * 更新评价数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 评价编号
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
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 评价
   * @param array $info 评价内容
   * @return Ambigous <number, boolean>
   */
  public function appraise($info)
  {
    $this->load->model('broker_info_base_model');
    $partner_trust = $this->broker_info_base_model->get_trust_by_broker_id($info['partner_id']);
    $info['partner_trust'] = $partner_trust;
    $info['create_time'] = time();
    if ($info['trade_type'] == 1) //交易成功
    {
      $trust_func = 'trade_success';
    } else if ($info['trade_type'] == 2) //交易失败
    {
      $trust_func = 'trade_failure';
    } else if ($info['trade_type'] == 3) //交易逾期
    {
      $trust_func = 'trade_overdue';
    } else if ($info['trade_type'] == 4) //已成交终止合同
    {
      $trust_func = 'agree_cancel_cooperate';
    } else if ($info['trade_type'] == 5) //合作生效后取消合作
    {
      $trust_func = 'cancel_cooperate_signature';
    } else if ($info['trade_type'] == 6) //合作生效后取消合作
    {
      $trust_func = 'frozen_cooperate_signature';
    }
    //信用的类型和功能
    $trust_func = $trust_func . '_' . $info['trust_type'];
    //给合作伙伴加信用
    $this->load->model('api_broker_sincere_model');
    //返回相应类型的分值
    $update_trust_score = $this->api_broker_sincere_model->
    update_trust($info['partner_id'], $trust_func);
    //给合作伙伴增加信用统计
    $this->load->model('sincere_trust_config_model');
    $trust_config = $this->sincere_trust_config_model->get_config();
    $trust_type_id = $trust_config['appraise_type'][$info['trust_type']];
    $info['trust_type_id'] = $trust_type_id;
    $this->load->model('sincere_trust_count_model');
    $this->sincere_trust_count_model->count_appraise($info['partner_id'], $info['trust_type']);
    //删除trust_type
    unset($info['trust_type']);
    //获取各个细节评份的类型
    $this->load->model('sincere_weight_model');
    $type_weight_config = $this->sincere_weight_model->get_config();
    $type_info = $type_weight_config['type_info'];
    //评价功能编号
    $this->load->model('sincere_appraise_model');
    $appraise_config = $this->sincere_appraise_model->get_config();
    //信息真实度
    $operator_infomation_action = $appraise_config['operator_infomation_action'];
    //给合作伙伴评分
    $this->load->model('sincere_appraise_record_model');
    $appraise_infomation_record = array(
      'broker_id' => $info['partner_id'], 'type_id' => $type_info['infomation']['type'],
      'action_id' => $operator_infomation_action['broker_appriase']['id'],
      'score' => $info['infomation'], 'create_time' => time()
    );
    //插入后的编号
    $infomation_id = $this->sincere_appraise_record_model->insert($appraise_infomation_record);
    //合作满意度
    $operator_attitude_action = $appraise_config['operator_attitude_action'];
    $appraise_attitude_record = array(
      'broker_id' => $info['partner_id'], 'type_id' => $type_info['attitude']['type'],
      'action_id' => $operator_attitude_action['broker_appriase']['id'],
      'score' => $info['attitude'], 'create_time' => time()
    );
    //插入后的编号
    $attitude_id = $this->sincere_appraise_record_model->insert($appraise_attitude_record);
    //业务专业度
    $operator_business_action = $appraise_config['operator_business_action'];
    $appraise_business_record = array(
      'broker_id' => $info['partner_id'], 'type_id' => $type_info['business']['type'],
      'action_id' => $operator_business_action['broker_appriase']['id'],
      'score' => $info['business'], 'create_time' => time()
    );
    //插入后的编号
    $business_id = $this->sincere_appraise_record_model->insert($appraise_business_record);
    $info['infomation_id'] = $infomation_id;
    $info['attitude_id'] = $attitude_id;
    $info['business_id'] = $business_id;
    //插入合作记录
    $info['house_info'] = serialize($info['house_info']);
    return $this->insert($info);
  }
}

/* End of file Sincere_appraise_cooperate_base_model.php */
/* Location: ./applications/models/Sincere_appraise_cooperate_base_model.php */
