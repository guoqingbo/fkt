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
 * Sincere_appraise_base_model CLASS
 *
 * 经纪人的动态评分管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Sincere_appraise_base_model extends MY_Model
{

  //信息真实度
  private $_operator_infomation_action = array(
    'house_info_false' => array('name' => '房源信息虚假', 'score' => -0.1, 'id' => 1),
    'customer_info_false' => array('name' => '客源信息虚假', 'score' => -0.1, 'id' => 2),
    'broker_appriase' => array('name' => '经纪人评价', 'id' => 3),
  );

  //合作满意度
  private $_operator_attitude_action = array(
    'whether_accept_cooperate' => array('name' => '是否及时接受合作申请', 'score' => -0.1, 'id' => 1),
    'whether_accept_brokerage' => array('name' => '是否及时接受佣金分配', 'score' => -0.1, 'id' => 2),
    'broker_appriase' => array('name' => '经纪人评价', 'id' => 3),
  );

  //业务专业度
  private $_operator_business_action = array(
    'broker_appriase' => array('name' => '经纪人评价', 'id' => 3),
  );

  /**
   * 信用表
   * @var string
   */
  private $_tbl = 'sincere_appraise';

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
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_sincere_appraise_base_model_';
  }

  /**
   * 获取配置信息
   * @return array
   */
  public function get_config()
  {
    return array(
      'operator_infomation_action' => $this->_operator_infomation_action,
      'operator_attitude_action' => $this->_operator_attitude_action,
      'operator_business_action' => $this->_operator_business_action,
    );
  }

  /**
   * 能过获取历史记录计算出评价值
   * @param int $broker_id
   * @return array
   */
  private function _get_appraise_info($broker_id)
  {
    $this->load->model('sincere_appraise_record_base_model');
    //三个月以内的数据
    $search_time = $this->sincere_appraise_record_base_model->get_valid_time();
    $where = 'broker_id = ' . $broker_id . ' and create_time >= '
      . $search_time . ' ';
    //获取细节分值 信息真实度
    $where_partner = $where . 'and score > 0'; //人工die();
    $partner = $this->sincere_appraise_record_base_model->get_avg_by($where_partner);
    $partner = change_to_key_array($partner, 'type_id');
    $where_system = $where . 'and score < 0'; //系统
    $system = $this->sincere_appraise_record_base_model->get_sum_by($where_system);
    $system = change_to_key_array($system, 'type_id');
    //获取权重
    $this->load->model('sincere_weight_base_model');
    $weight = $this->sincere_weight_base_model->get_all_by();
    $sincere_weight_config = $this->sincere_weight_base_model->get_config();
    $sincere_weight_type = $sincere_weight_config['type_info'];
    //分值
    $arr_score = array();
    //三种细节评价的类型及分值
    foreach ($weight as $key => $value) {
      $score = 0;
      $type_id = $value['id'];
      //人工比例
      if ($value['man_made'] > 0) {
        if (!isset($partner[$type_id]['score'])) {
          $partner[$type_id]['score'] = $sincere_weight_type[$value['alias']]['init_score'];
        }
        $score += $partner[$type_id]['score'] * $value['man_made'];
      }
      //系统比例
      if ($value['system'] > 0) {
        if (!isset($system[$type_id]['score'])) {
          $system[$type_id]['score'] = 0;
        }
        $score += (($sincere_weight_type[$value['alias']]['init_score']
            + $system[$type_id]['score']) * $value['system']);
      }
      $arr_score[$value['alias']] = round($score / 100, 1);
    }
    $arr_score['update_time'] = time();
    $this->update_by_broker_id($arr_score, $broker_id);
    //更新分值，并返回相应的值
    return $arr_score;
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
   * 通过经纪人编号获取评价记录
   * @param int $broker_id 评价编号
   * @return array 评价记录组成的一维数组
   */
  public function get_by_broker_id($broker_id)
  {
    $mem_key = $this->_mem_key . 'get_by_broker_id_' . $broker_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $appraise = $cache['data'];
    } else {
      //查询条件
      $this->dbback_city->where('broker_id', $broker_id);
      $appraise = $this->dbback_city->get($this->_tbl)->row_array();
      if (!is_full_array($appraise)) {
        $this->init_appraise($broker_id);
        $appraise = $this->get_by_broker_id($broker_id);
      } else {
        //获取三个月以内的分值
        $appraise = $this->_get_appraise_info($broker_id);
      }
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $appraise), 300);
    }
    return $appraise;
  }

  /**
   * 根据分值获取其等级
   * @param int $score
   */
  public function get_level_by_score($score)
  {
    $level = '';
    for ($i = 0; $i < 5; $i++) {
      if ($score >= 1) {
        $level .= '<span class="djicon dj100"></span>';
        $score--;
      } else if ($score > 0 && $score < 1) {
        $level .= '<span class="djicon dj50"></span>';
        $score--;
      } else {
        $level .= '<span class="djicon dj0"></span>';
      }
    }
    return $level;
  }

  /**
   * 获取每个经纪人的均分
   */
  public function get_avg_by()
  {
    $mem_key = $this->_mem_key . 'get_avg_by';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $appraise_avg = $cache['data'];
    } else {
      $current_time = time();
      $this->load->model('broker_info_base_model');
      $join_tbl = $this->broker_info_base_model->get_tbl();
      $this->dbback_city->select('avg(infomation) as infomation,avg(attitude)
                     as attitude,avg(business) as business');
      $this->dbback_city->where("$join_tbl.status = 1 and $join_tbl.expiretime > " . $current_time);
      $this->dbback_city->join($join_tbl, $this->_tbl . '.broker_id = ' . $join_tbl . '.broker_id ');
      $appraise_avg = $this->dbback_city->get($this->_tbl)->row_array();
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $appraise_avg), 300);
    }
    return $appraise_avg;
  }

  /**
   * 返回初始化某个经纪人的数据
   * @param int $broker_id 经纪人编号
   */
  public function init_appraise($broker_id)
  {
    $this->load->model('sincere_weight_base_model');
    $sincere_weight_config = $this->sincere_weight_base_model->get_config();
    $type_info = $sincere_weight_config['type_info'];
    return $this->insert(array('broker_id' => $broker_id,
      'infomation' => $type_info['infomation']['init_score'],
      'attitude' => $type_info['attitude']['init_score'],
      'business' => $type_info['business']['init_score'],
      'update_time' => time()));
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
   * @param array $broker_id 经纪人编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_broker_id($update_data, $broker_id)
  {
    if (is_array($broker_id)) {
      $ids = $broker_id;
    } else {
      $ids[0] = $broker_id;
    }
    $this->db_city->where_in('broker_id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }
}

/* End of file Sincere_appraise_base_model.php */
/* Location: ./applications/models/Sincere_appraise_base_model.php */
