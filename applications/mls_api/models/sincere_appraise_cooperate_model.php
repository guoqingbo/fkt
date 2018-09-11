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
 * Sincere_appraise_cooperate_model CLASS
 *
 * 经纪人的合作评价管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Sincere_appraise_cooperate_base_model");

class Sincere_appraise_cooperate_model extends Sincere_appraise_cooperate_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 评价
   * @param array $info 评价内容
   * @return Ambigous <number, boolean>
   */
  public function appraise($info)
  {
    $this->load->model('broker_info_model');
    $partner_trust = $this->broker_info_model->get_trust_by_broker_id($info['partner_id']);
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

/* End of file Sincere_appraise_cooperate_model.php */
/* Location: ./app/models/Sincere_appraise_cooperate_model.php */
