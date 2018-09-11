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
 * Sincere_trust_config_base_model CLASS
 *
 * 经纪人的信用配置信息功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Sincere_trust_config_base_model extends MY_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 记录操作方式别名
   * @var array  action
   */
  private $_operator_trust_action = array(
    'complete_photo' => array('name' => '完成头像认证', 'score' => 1, 'id' => 1),
    'trade_success_good' => array('name' => '交易成功好评', 'score' => 1, 'id' => 2),
    'trade_success_medium' => array('name' => '交易成功中评', 'score' => 0, 'id' => 3),
    'trade_success_bad' => array('name' => '交易成功差评', 'score' => -1, 'id' => 4),
    'trade_failure_good' => array('name' => '交易失败好评', 'score' => 1, 'id' => 5),
    'trade_failure_medium' => array('name' => '交易失败中评', 'score' => 0, 'id' => 6),
    'trade_failure_bad' => array('name' => '交易失败差评', 'score' => -1, 'id' => 7),
    'trade_overdue_good' => array('name' => '交易逾期好评', 'score' => 1, 'id' => 8),
    'trade_overdue_medium' => array('name' => '交易逾期中评', 'score' => 0, 'id' => 9),
    'trade_overdue_bad' => array('name' => '交易逾期差评', 'score' => -1, 'id' => 10),
    'agree_cancel_cooperate_good' => array('name' => '已成交合作终止好评', 'score' => 1, 'id' => 11),
    'agree_cancel_cooperate_medium' => array('name' => '已成交合作终止中评', 'score' => 0, 'id' => 12),
    'agree_cancel_cooperate_bad' => array('name' => '已成交合作终止差评', 'score' => -1, 'id' => 13),
    'appeal_success' => array('name' => '申诉成功', 'id' => 14),
    'mali_appraise' => array('name' => '恶意评价', 'score' => -1, 'id' => 15),
    'cancel_cooperate_signature' => array('name' => '合作生效后取消合作', 'score' => -1, 'id' => 16),
    'no_accord_agreement_signature' => array('name' => '合作生效后不按协议履行合同，被举报成功', 'score' => -1, 'id' => 17),
    'no_accord_agreement_trade_success' => array('name' => '交易成功后不按协议履行合同，被举报成功', 'score' => -1, 'id' => 18),
    'cancel_cooperate_signature_good' => array('name' => '合作生效后取消合作好评', 'score' => 1, 'id' => 19),
    'cancel_cooperate_signature_medium' => array('name' => '合作生效后取消合作中评', 'score' => 0, 'id' => 20),
    'cancel_cooperate_signature_bad' => array('name' => '合作生效后差评', 'score' => -1, 'id' => 21),
    'frozen_cooperate_signature_good' => array('name' => '合作生效后冻结好评', 'score' => 1, 'id' => 22),
    'frozen_cooperate_signature_medium' => array('name' => '合作生效后冻结中评', 'score' => 0, 'id' => 23),
    'frozen_cooperate_signature_bad' => array('name' => '合作生效后冻结差评', 'score' => -1, 'id' => 24),
  );

  /**
   * 评价的类型
   */
  private $_appraise_type = array(
    'good' => 1, 'medium' => 2, 'bad' => 3
  );

  /**
   * 评价的类型的分值
   */
  private $_appraise_type_score = array(
    '1' => 1, '2' => 0, '3' => -1,
  );

  /**
   * 评价类型的别名
   * @var string
   */
  private $_appraise_type_description = array(
    '1' => '好评', '2' => '中评', '3' => '差评',
  );

  /**
   * 获取配置信息
   * @return array
   */
  public function get_config()
  {
    return array(
      'operator_trust_action' => $this->_operator_trust_action,
      'appraise_type' => $this->_appraise_type,
      'appraise_type_score' => $this->_appraise_type_score,
      'appraise_type_description' => $this->_appraise_type_description,
    );
  }

  /**
   * 根据功能编号获取其代表的类型及功能
   * @param int $action_id 功能编号
   * @return array
   */
  public function get_by_action_id($action_id)
  {
    $operator_trust_action = change_to_key_array($this->_operator_trust_action, 'id');
    return $operator_trust_action[$action_id];
  }
}

/* End of file Sincere_trust_config_base_model.php */
/* Location: ./applications/models/Sincere_trust_config_base_model.php */
