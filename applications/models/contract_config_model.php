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
 * Contract_config_model CLASS *
 *
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class Contract_config_model extends MY_Model
{

  public function get_config()
  {
    $config = array(
      'business_type' => array(
        1 => '房源',
        2 => '客源',
        3 => '钥匙',
        4 => '独家',
        5 => '签合同',
        6 => '转介绍',
        7 => '收房',
        8 => '勘察',
        9 => '代办贷款',
        10 => '其它'
      ),
      'money_type' => array(
        1 => '佣金',
        2 => '房款',
        3 => '税费',
        4 => '按揭费',
        5 => '评估费',
        6 => '定金',
        7 => '退款',
        8 => '代办费',
        9 => '担保费',
        10 => '其它'
      ),
      'collect_type' => array(
        1 => '客户',
        2 => '业主',
        3 => '银行',
        4 => '政府',
        5 => '内部',
        6 => '其它'
      ),
      'pay_type' => array(
        1 => '客户',
        2 => '业主',
        3 => '银行',
        4 => '政府',
        5 => '内部',
        8 => '其它'
      ),
      'price_type' => array(
        1 => '元/月',
        2 => '元/年',
        3 => '元/平米*月',
        4 => '元/平米*天'
      ),

      'payment_method' => array(
        1 => '现金',
        2 => '支票',
        3 => '转账',
        4 => '汇款',
      ),

      'buy_type_r' => array(
        1 => '月付',
        2 => '季付',
        3 => '半年付',
        4 => '年付'
      ),
      'buy_type_s' => array(
        1 => '全款',
        2 => '商贷',
        3 => '公积金',
        4 => '公积金+商贷'
      ),
      'tax_pay_type' => array(
        1 => '双方各付',
        2 => '买方支付',
        3 => '卖方支付'
      ),
      'divide_type' => array(
        1 => '房源',
        2 => '客源',
        3 => '钥匙',
        4 => '独家',
        5 => '签合同',
        6 => '转介绍',
        7 => '收房',
        8 => '勘察',
        9 => '代办贷款',
        10 => '其它'
      ),
      'type' => array(
        1 => '出售',
        2 => '出租'
      ),
      'is_check' => array(
        1 => '待审核',
        2 => '审核通过',
        3 => '审核未通过',
        4 => '作废'
      ),
      'is_check1' => array(
        1 => '待审核',
        2 => '生效',
        3 => '审核未通过'
      ),
      'status2' => array(
        1 => '待审核',
        3 => '未通过',
        5 => '履行中',
        6 => '已办结',
        4 => '终止'
      ),
      'cont_status' => array(
        1 => '待审核',
        2 => '生效',
        3 => '审核不通过',
        4 => '终止',
        5 => '已结佣',
        6 => '已结盘'
      ),
      'cont_status_r' => array(
        1 => '待审核',
        2 => '生效',
        3 => '审核不通过',
        4 => '终止',
        5 => '已结佣'
      ),
      'flow_status' => array(
        0 => '待审核',
        1 => '审核通过',
        2 => '审核未通过'
      ),
      'datetype' => array(
        1 => '签约时间',
        2 => '结佣日期',
        3 => '结盘日期'
      ),
      'datetype_r' => array(
        1 => '签约时间',
        2 => '结佣日期'
      ),
      'datetype1' => array(
        2 => '收付日期',
        1 => '签约时间'

      ),
      'docket_type' => array(
        1 => '佣金单据',
        2 => '房款收据',
        3 => '税费收据'
      ),
      'report_status' => array(
        1 => '预约中',
        2 => '预约成功',
        3 => '预约失败',
        4 => '已转正式',
        5 => '已取消',
        6 => '已删除'
      ),
      'is_cooperate' => array(
        0 => '否',
        1 => '是'
      )

    );
    return $config;
  }
}


/* End of file Contract_config_model.php */
/* Location: ./application/models/Contract_config_model.php */
