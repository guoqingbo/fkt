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
 * Api_signatory_sincere_base_model CLASS
 *
 * 经纪人增加，扣除积分接口
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Api_signatory_sincere_base_model extends MY_Model
{
  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_api_signatory_sincere_base_model_';
  }

  /**
   * 增加、扣除信用
   * @param int $signatory_id 经纪人编号
   * @param string $func_alias 功能别名
   * @param int $score 分值，默认为空
   * @return boolean 返回添加或者更新的分值
   */
  function update_trust($signatory_id, $func_alias, $score = '')
  {
    $this->load->model('sincere_trust_config_base_model');
    $trust_config = $this->sincere_trust_config_base_model->get_config();
    //操作功能别名
    $operator_trust_action = $trust_config['operator_trust_action'];
    //获取别名分值
    if ($score === '') //没有传分数
    {
      $score = $operator_trust_action[$func_alias]['score'];
    }
    //当分值为负时，相当于扣分，必须要满足是否大于
    $this->load->model('signatory_info_base_model');
    if ($score < 0) //扣分
    {
      $trust = $this->signatory_info_base_model->get_trust_by_signatory_id($signatory_id);
      if ($trust < abs($score)) {
        $score = -($trust);
      }
    }
    if ($score === '') {
      return false;
    }
    //更新分值
    $this->signatory_info_base_model->update_self_trust_by_signatory_id($signatory_id, $score);
    $trust = $this->signatory_info_base_model->get_trust_by_signatory_id($signatory_id);
    //插入记录
    $this->load->model('sincere_trust_record_base_model');
    $action_id = $operator_trust_action[$func_alias]['id'];
    $trust_record = array(
      'signatory_id' => $signatory_id, 'action_id' => $action_id,
      'score' => $score, 'trust' => $trust, 'create_time' => time(),
    );
    $this->sincere_trust_record_base_model->insert($trust_record);
    return $score;
  }

  /**
   * 获取某个经纪人的信用值和等级
   * @param int $signatory_id
   * @return {'trust' : '', 'level' : '' }
   */
  public function get_trust_level_by_signatory_id($signatory_id)
  {
    $this->load->model('signatory_info_base_model');
    $trust = $this->signatory_info_base_model->get_trust_by_signatory_id($signatory_id);
    //根据分值获取等级
    $this->load->model('sincere_trust_level_base_model');
    $trust_level = $this->sincere_trust_level_base_model->get_level_by_trust($trust);
    return array('trust' => $trust, 'level' => $trust_level['name'], 'level_id' => $trust_level['level_id']);
  }

  /**
   * 获取某个经纪人信用分
   * @param int $signatory_id
   * @return int 信用分数
   */
  public function get_trust_by_signatory_id($signatory_id)
  {
    $this->load->model('signatory_info_base_model');
    return $this->signatory_info_base_model->get_trust_by_signatory_id($signatory_id);
  }

  /**
   * 获取某个分值信用值所对应的等级 - 转换成静态数据的方法
   * @param int $trust 信用分
   */
  public function get_level_by_trust($trust)
  {
    //根据分值获取等级
    $this->load->model('sincere_trust_level_base_model');
    $trust_level = $this->sincere_trust_level_base_model->get_level_by_trust($trust);
    return $trust_level['name'];
  }

  /**
   * 获取某个分值信用值所对应的等级 - 转换成静态数据的方法
   * @param int $trust 信用分
   */
  public function get_level_id_by_trust($trust)
  {
    //根据分值获取等级
    $this->load->model('sincere_trust_level_base_model');
    $trust_level = $this->sincere_trust_level_base_model->get_level_by_trust($trust);
    return $trust_level['level_id'];
  }

  /**
   * 信用评价 好评，中评，差评
   * @param int $signatory_id 经纪人编号
   * @return array {'good' : '好评', 'medium' : '', 'bad' : '',
   * 'total' : '总数', 'good_rate' : '好评率'}
   */
  public function get_trust_appraise_count($signatory_id)
  {
    $mem_key = $this->_mem_key . 'get_trust_appraise_count_' . $signatory_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $result = $cache['data'];
    } else {
      $this->load->model('sincere_trust_count_base_model');
      $result = array('good' => 0, 'medium' => 0, 'bad' => 0, 'total' => 0, 'good_rate' => '');
      $trust_count = $this->sincere_trust_count_base_model->get_by_signatory_id($signatory_id);
      if (is_full_array($trust_count)) {
        $total_count = $trust_count['good'] + $trust_count['medium'] + $trust_count['bad'];
        $result = array(
          'good' => $trust_count['good'], 'medium' => $trust_count['medium'],
          'bad' => $trust_count['bad'], 'total' => $total_count,
          'good_rate' => $trust_count['good_rate']
        );
      }
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $result), 600);
    }
    return $result;
  }

  /**
   * 获取好评率、平均值及获取当前经纪人比
   * @param int $signatory_id
   */
  public function good_avg_rate($signatory_id)
  {
    $mem_key = $this->_mem_key . 'good_avg_rate_' . $signatory_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $result = $cache['data'];
    } else {
      $result = array('good_avg_rate' => 0, 'good_rate_avg_high' => 0, 'good_rate' => '');
      //平均好评率
      $good_avg_rate = $this->get_good_avg_rate();
      //经纪人好评率
      $good_rate = $this->get_trust_appraise_count($signatory_id);
      //经纪人高于平均好评率比例
      if ($good_avg_rate['good_rate'] != '' && $good_avg_rate['good_rate'] != 0) {
        $good_rate_avg_high = round(($good_rate['good_rate'] - $good_avg_rate['good_rate'])
          / $good_avg_rate['good_rate'] * 100, 2);
        $result['good_avg_rate'] = round($good_avg_rate['good_rate'], 2);
        $result['good_rate_avg_high'] = $good_rate_avg_high;
        $result['good_rate'] = $good_rate['good_rate'];
      }
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $result), 600);
    }
    return $result;
  }

  public function get_good_avg_rate()
  {
    $mem_key = $this->_mem_key . 'get_good_avg_rate';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $good_avg_rate = $cache['data'];
    } else {
      //平均好评率
      $this->load->model('sincere_trust_count_base_model');
      $good_avg_rate = $this->sincere_trust_count_base_model->good_avg_rate();
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $good_avg_rate), 600);
    }
    return $good_avg_rate;
  }

  /**
   * 获取经纪人动态评分基本统计信息
   * @param int $signatory 经纪人编号
   * @return array
   */
  public function get_appraise_and_avg($signatory_id)
  {
    $mem_key = $this->_mem_key . 'get_good_avg_rate_' . $signatory_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $get_appraise_and_avg = $cache['data'];
    } else {
      $this->load->model('sincere_appraise_base_model');
      //经纪人的分值
      $appraise = $this->sincere_appraise_base_model->get_by_signatory_id($signatory_id);
      //获取基本的统计信息
      $appraise_avg = $this->get_appraise_avg();
      $infomation_avg = $appraise_avg['infomation'];
      $attitude_avg = $appraise_avg['attitude'];
      $business_avg = $appraise_avg['business'];
      $get_appraise_and_avg = array(
        'infomation' => array(
          'score' => $appraise['infomation'],
          'avg' => $infomation_avg,
          'level' => $this->sincere_appraise_base_model->
          get_level_by_score($appraise['infomation']),
          'rate' => round(($appraise['infomation'] -
              $infomation_avg) / $infomation_avg * 100, 2)), /*信息真实度*/
        'attitude' => array(
          'score' => $appraise['attitude'],
          'avg' => $attitude_avg,
          'level' => $this->sincere_appraise_base_model->
          get_level_by_score($appraise['attitude']),
          'rate' => round(($appraise['attitude'] -
              $attitude_avg) / $attitude_avg * 100, 2)), /*态度满意度*/
        'business' => array(
          'score' => $appraise['business'],
          'avg' => $business_avg,
          'level' => $this->sincere_appraise_base_model->
          get_level_by_score($appraise['business']),
          'rate' => round(($appraise['business'] -
              $business_avg) / $business_avg * 100, 2)), /*业务专业度*/
      );
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $get_appraise_and_avg), 600);
    }
    return $get_appraise_and_avg;
  }

  /**
   * 获取动态评分基本统计信息
   * @return array
   */
  public function get_appraise_avg()
  {
    $mem_key = $this->_mem_key . 'get_appraise_avg';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $get_appraise_avg = $cache['data'];
    } else {
      $this->load->model('sincere_appraise_base_model');
      //平均分
      $appraise_avg = $this->sincere_appraise_base_model->get_avg_by();
      $get_appraise_avg = array(
        'infomation' => round($appraise_avg['infomation'], 1),
        'attitude' => round($appraise_avg['attitude'], 1),
        'business' => round($appraise_avg['business'], 1)
      );
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $get_appraise_avg), 600);
    }
    return $get_appraise_avg;
  }

  /**
   * 获取经纪人动态评分的分值获取其等级  几个星
   * @param int $score
   * @return string 几个星所组成的状态
   */
  public function get_appraise_level($score)
  {
    $this->load->model('sincere_appraise_base_model');
    return $this->sincere_appraise_base_model->get_level_by_score($score);
  }

  /**
   * 经纪人动态评分详细信息
   * @param int $signatory 经纪人编号
   * @return array
   */
  public function get_appraise_info($signatory_id)
  {
    $mem_key = $this->_mem_key . 'get_appraise_info_' . $signatory_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $get_appraise_info = $cache['data'];
    } else {
      //获取动态说分的有效时间
      $this->load->model('sincere_appraise_record_base_model');
      $valid_time = $this->sincere_appraise_record_base_model->get_valid_time();
      $where = 'signatory_id = ' . $signatory_id . " and score > 0 "
        . "  and create_time > " . $valid_time;
      //获取动态评分的类型
      $this->load->model('sincere_weight_base_model');
      $type_weight_config = $this->sincere_weight_base_model->get_config();
      $type_info = $type_weight_config['type_info'];
      //评价功能编号
      $this->load->model('sincere_appraise_base_model');
      $appraise_config = $this->sincere_appraise_base_model->get_config();
      //信息真实度
      $operator_infomation_action = $appraise_config['operator_infomation_action'];
      $infomation_action_id = $operator_infomation_action['signatory_appriase']['id'];
      $where_infomation = $where . ' and type_id = ' . $type_info['infomation']['type']
        . ' and action_id = ' . $infomation_action_id;
      $infomation = $this->sincere_appraise_record_base_model->
      count_score_group_by($where_infomation);
      $infomation = change_to_key_array($infomation, 'score');
      //合作满意度
      $operator_attitude_action = $appraise_config['operator_attitude_action'];
      $attitude_action_id = $operator_attitude_action['signatory_appriase']['id'];
      $where_attitude = $where . ' and type_id = ' . $type_info['attitude']['type']
        . ' and action_id = ' . $attitude_action_id;
      $attitude = $this->sincere_appraise_record_base_model->
      count_score_group_by($where_attitude);
      $attitude = change_to_key_array($attitude, 'score');
      //业务专业度
      $operator_business_action = $appraise_config['operator_business_action'];
      $business_action_id = $operator_business_action['signatory_appriase']['id'];
      $where_business = $where . ' and type_id = ' . $type_info['business']['type']
        . ' and action_id = ' . $business_action_id;;
      $business = $this->sincere_appraise_record_base_model->
      count_score_group_by($where_business);
      $business = change_to_key_array($business, 'score');
      function format_score($score_group)
      {
        if (is_full_array($score_group)) {
          foreach ($score_group as $key => $value) {
            $value['score'] = round($value['score']);
            $score_group[round($key)] = $value;
            unset($score_group[$key]);
          }
        }
        $sum = 0;
        for ($i = 1; $i <= 5; $i++) {
          if (!isset($score_group[$i])) {
            $score_group[$i] = array('score' => $i, 'count' => 0);
          }
          $sum += $score_group[$i]['count'];
        }
        return array('score_group' => $score_group, 'sum' => $sum);
      }

      $infomation_all = format_score($infomation);
      $attitude_all = format_score($attitude);
      $business_all = format_score($business);
      $get_appraise_info = array(
        'infomation' => $infomation_all['score_group'],
        'infomation_sum' => $infomation_all['sum'],
        'attitude' => $attitude_all['score_group'],
        'attitude_sum' => $attitude_all['sum'],
        'business' => $business_all['score_group'],
        'business_sum' => $business_all['sum']
      );
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $get_appraise_info), 600);
    }
    return $get_appraise_info;
  }

  /**
   * 返回此合同是否已经给经纪人评价过
   * @param int $signatory_id 经纪人编号
   * @param string $transaction_id 交易号
   */
  public function is_exist_transaction($signatory_id, $transaction_id)
  {
    $this->load->model('sincere_appraise_cooperate_base_model');
    return $this->sincere_appraise_cooperate_base_model->
    is_exist_transaction($signatory_id, $transaction_id);
  }

  /**
   * 更新信息真实度
   * @param int $signatory_id 经纪人编号
   * @param string $alias 操作类型别名
   */
  public function update_infomation($signatory_id, $alias, $score = 0)
  {
    //获取类型编号
    $this->load->model('sincere_weight_base_model');
    $type_weight_config = $this->sincere_weight_base_model->get_config();
    $type_info = $type_weight_config['type_info'];
    $type_id = $type_info['infomation']['type'];
    $this->load->model('sincere_appraise_base_model');
    $appraise_config = $this->sincere_appraise_base_model->get_config();
    //信息真实度
    $operator_infomation_action = $appraise_config['operator_infomation_action'];
    //获取功能下的分值
    if ($score == 0) {
      $score = $operator_infomation_action[$alias]['score'];
    }
    $appraise_record = array(
      'signatory_id' => $signatory_id, 'type_id' => $type_id,
      'action_id' => $operator_infomation_action[$alias]['id'],
      'score' => $score, 'create_time' => time()
    );
    $this->load->model('sincere_appraise_record_base_model');
    $this->sincere_appraise_record_base_model->insert($appraise_record);
    return $score;
  }

  /**
   * 更新合作满意度
   * @param int $signatory_id 经纪人编号
   * @param string $alias 操作类型别名
   * @return int 插入编号
   */
  public function update_attitude($signatory_id, $alias, $score = 0)
  {
    //获取类型编号
    $this->load->model('sincere_weight_base_model');
    $type_weight_config = $this->sincere_weight_base_model->get_config();
    $type_info = $type_weight_config['type_info'];
    $type_id = $type_info['attitude']['type'];
    $this->load->model('sincere_appraise_base_model');
    $appraise_config = $this->sincere_appraise_base_model->get_config();
    //合作满意度
    $operator_attitude_action = $appraise_config['operator_attitude_action'];
    //获取功能下的分值
    if ($score == 0) {
      $score = $operator_attitude_action[$alias]['score'];
    }

    $appraise_record = array(
      'signatory_id' => $signatory_id, 'type_id' => $type_id,
      'action_id' => $operator_attitude_action[$alias]['id'],
      'score' => $score, 'create_time' => time()
    );

    $this->load->model('sincere_appraise_record_base_model');
    $this->sincere_appraise_record_base_model->insert($appraise_record);
    return $score;
  }

  /**
   * 更新合作满意度
   * @param int $signatory_id 经纪人编号
   * @param string $alias 操作类型别名
   * @return int 插入编号
   */
  public function update_businsess($signatory_id, $alias, $score = 0)
  {
    //获取类型编号
    $this->load->model('sincere_weight_base_model');
    $type_weight_config = $this->sincere_weight_base_model->get_config();
    $type_info = $type_weight_config['type_info'];
    $type_id = $type_info['businsess']['type'];
    $this->load->model('sincere_appraise_base_model');
    $appraise_config = $this->sincere_appraise_base_model->get_config();
    //合作满意度
    $operator_businsess_action = $appraise_config['operator_businsess_action'];
    //获取功能下的分值
    if ($score == 0) {
      $score = $operator_businsess_action[$alias]['score'];
    }
    $appraise_record = array(
      'signatory_id' => $signatory_id, 'type_id' => $type_id,
      'action_id' => $operator_businsess_action[$alias]['id'],
      'score' => $score, 'create_time' => time()
    );
    $this->load->model('sincere_appraise_record_base_model');
    $this->sincere_appraise_record_base_model->insert($appraise_record);
    return $score;
  }

  /**
   * 处罚记录
   * @param int $signatory_id 举报人编号
   * @param int $signatoryed_id 受罚人编号
   * @param string $alias 类型名称
   * @param int $score 分值
   * @param string $number 房源编号
   * @param string $house_info 房源详细信息 序列化房源数组信息
   */
  public function punish($signatory_id, $signatoryed_id, $alias, $score, $number, $house_info)
  {
    $this->load->model('sincere_punish_base_model');
    //type score object_id, house_info
    if ($alias == 'mali_appraise' || $alias == 'no_accord_agreement_signature'
      || $alias == 'cancel_cooperate'
      || $alias == 'no_accord_agreement_trade_success'
    ) //扣信用
    {
      $description = '信用度' . $score . '分';
    } else if ($alias == 'house_info_false' || $alias == 'customer_info_false') //扣信息真实度
    {
      $description = '【系统】信息真实度' . $score . '分';
    } else if ($alias == 'whether_accept_cooperate'
      || $alias == 'whether_accept_signatoryage'
    ) //扣合作满意度
    {
      $description = '【系统】合作满意度' . $score . '分';
    }
    $punish_config = $this->sincere_punish_base_model->get_config();
    $insert_data = array(
      'signatory_id' => $signatory_id,
      'signatoryed_id' => $signatoryed_id,
      'type' => $punish_config['type_alias'][$alias],
      'score' => $score, 'number' => $number, 'house_info' => $house_info,
      'description' => $description, 'create_time' => time());
    return $this->sincere_punish_base_model->insert($insert_data);
  }

  /**
   * 合作申诉
   * @param int $signatory_id 经纪人编号
   * @param int $appraise_id 合作编号
   * @param string $photo_url 图片名称
   * @param string $photo_name 图片名称
   * @param string $reason 理由
   */
  public function appraise_appeal($signatory_id, $appraise_id, $transaction_id,
                                  $photo_url, $photo_name, $reason)
  {
    $this->load->model('signatory_info_base_model');
    $signatory_info = $this->signatory_info_base_model->get_by_signatory_id($signatory_id);
    $insert_data = array(
      'signatory_id' => $signatory_id, 'signatory_name' => $signatory_info['truename'],
      'appraise_id' => $appraise_id, 'transaction_id' => $transaction_id,
      'photo_url' => $photo_url,
      'photo_name' => $photo_name, 'reason' => $reason, 'status' => 1,
    );
    $this->load->model('sincere_appraise_appeal_base_model');
    return $this->sincere_appraise_appeal_base_model->insert($insert_data);
  }

  /**
   * 审核合作申诉
   * @param int $id 编号
   * @param int $status 状态
   */
  public function update_appraise_appeal_status($id, $status)
  {
    $this->load->model('sincere_appraise_appeal_base_model');
    $this->load->model('sincere_appraise_cooperate_base_model');
    $update_data = array('status' => $status);
    $effected_rows = $this->sincere_appraise_appeal_base_model->update_by_id($update_data, $id);
    //获取合作申诉记录
    $appraise_appeal = $this->sincere_appraise_appeal_base_model->get_by_id($id);

    if ($effected_rows && $status == 2) //申诉成功
    {
      //获取合作评价记录
      $appraise_cooperate = $this->sincere_appraise_cooperate_base_model->
      get_by_id($appraise_appeal['appraise_id']);
      $this->load->model('sincere_trust_config_base_model');
      $trust_config = $this->sincere_trust_config_base_model->get_config();
      $return_score = $trust_config['appraise_type_score'][$appraise_cooperate['trust_type_id']];
      //1、更新信用 申诉成功
      if ($return_score > 0) {
        $return_score = -abs($return_score);
      } else if ($return_score < 0) {
        $return_score = abs($return_score);
      }
      $this->update_trust($appraise_cooperate['partner_id'],
        'appeal_success', $return_score);
      //2、删除 信息真实度记录 合作满意度记录 业务专业度记录
      $delete_record_id = array(
        $appraise_cooperate['infomation_id'],
        $appraise_cooperate['attitude_id'],
        $appraise_cooperate['business_id']
      );
      $this->load->model('sincere_appraise_record_base_model');
      $this->sincere_appraise_record_base_model->delete_by_id($delete_record_id);
      //二、恶意评价罚分
      $score = $this->update_trust($appraise_cooperate['signatory_id'], 'mali_appraise');
      //三、进入罚分库
      $this->punish($appraise_cooperate['partner_id'], $appraise_cooperate['signatory_id'], 'mali_appraise', $score,
        $appraise_cooperate['transaction_id'], $appraise_cooperate['house_info']);
    }
    //更新合作表状态   、评价作废
    $this->sincere_appraise_cooperate_base_model->update_by_id($update_data, $appraise_appeal['appraise_id']);
  }
}

/* End of file Api_signatory_sincere_base_model.php */
/* Location: ./applications/models/Api_signatory_sincere_base_model.php */
