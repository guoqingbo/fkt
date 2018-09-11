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
 * Api_broker_credit_base_model CLASS
 *
 * 经纪人增加，扣除积分接口
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Api_broker_credit_base_model extends MY_Model
{

  //获取消耗积分的途径
  private $_way = array(
    'register' => array('action' => '注册', 'score' => 100, 'type' => 1),
    'ident_cert' => array('action' => '认证', 'score' => 400, 'type' => 2),
    'sign' => array('action' => '每日登录', 'app_score' => 5, 'type' => 3),
    'rsync_fang100' => array('action' => '房源同步', 'pc_score' => 10, 'app_score' => 15, 'type' => 4, 'limit' => 10),
    'publish_cooperate_house' => array('action' => '发布合作房/客源', 'pc_score' => 20, 'app_score' => 30, 'type' => 5, 'limit' => 2),
    'accept_cooperate' => array('action' => '接受合作申请', 'pc_score' => 20, 'app_score' => 30, 'type' => 6, 'limit' => 2),
    'cooperate_confirm_deal' => array('action' => '合作确认成交', 'app_score' => 500, 'pc_score' => 500, 'type' => 7),
    'cooperate_appraise' => array('action' => '合作评价', 'pc_score' => 10, 'app_score' => 15, 'type' => 8, 'limit' => 5),
    'gift_exchange' => array('action' => '礼品兑换', 'type' => 9),
    'cooperate_activity' => array('action' => '合作大赏活动', 'type' => 10, 'chushen_score' => 1500, 'zhongshen_score' => 15000),
    'gift_raffle' => array('action' => '礼品抽奖', 'type' => 11),
    'xffx_baobei' => array('action' => '新房报备', 'pc_score' => 30, 'app_score' => 30, 'type' => 12, 'limit' => 3),
    'xffx_daikan' => array('action' => '新房带看', 'pc_score' => 500, 'app_score' => 500, 'type' => 13),
    'xffx_rengou' => array('action' => '新房认购', 'pc_score' => 800, 'app_score' => 800, 'type' => 14),
    'pinganhaofang' => array('action' => '房源同步至平安好房', 'score' => 20, 'type' => 15),
    'fang100_activity' => array('action' => '传房源、赢积分', 'score' => 20, 'type' => 16),//昆明和苏州站同步，并且房源图片完善奖励
  );

  //经纪人信息
  private $_broker = array();

  //来源
  private $_infofrom = 0;

  //返回结果数组
  private $_arrRtnResult = array('status' => 0, 'msg' => '', 'code' => '', 'score' => 0);

  /**
   * @return ['status' ： '状态'， ‘score’ ： 分值， ‘msg’ : '错误信息']
   * 状态
   * 0 代表 '失败'， 'msg' : '操作失败' code = 1
   * 0 代表 '失败'， 'msg' : '已经验证过，已签到'  code = 2
   * 0 代表 '失败'， 'msg' : '超过上限积分'  code = 3
   * 0 代表 '失败'， 'msg' : '积分不足，扣除积分失败'  code = 4
   * 1 代表 '成功'， 'score' : '返回成功增加或者扣除的积分值' code =1
   * 1 代表 '成功'， 'score' : '返回成功扣除的积分【经纪人最大可增加或者可扣除的分值】值'  code =2
   */

  /**
   *
   * @param type $insert_data
   */
  private function _insert_broker_house_record($insert_data)
  {
    $this->load->model('credit_house_base_model');
    return $this->credit_house_base_model->insert($insert_data);
  }

  /**
   *
   * @param type $broker_id
   * @param type $type
   * @return type
   */
  private function _count_broker_house_record($broker_id, $type)
  {
    //这条房源是否已经增加过积分
    $this->load->model('credit_house_base_model');
    $today_from = strtotime(date('Y-m-d') . ' 00:00:00'); //当天的起始时间
    $today_to = strtotime(date('Y-m-d') . ' 23:59:59'); //当天的结束时间
    $where = 'broker_id = ' . $broker_id . " and type = '" . $type . "'"
      . ' and create_time >=' . $today_from . ' and create_time <='
      . $today_to;
    return $this->credit_house_base_model->count_by($where);
  }

  public function _count_broker_credit_record($broker_id, $type)
  {
    //该经纪人增加过等级分值
    $this->load->model('credit_record_base_model');
    $today_from = strtotime(date('Y-m-d') . ' 00:00:00'); //当天的起始时间
    $today_to = strtotime(date('Y-m-d') . ' 23:59:59'); //当天的结束时间
    $where = 'broker_id = ' . $broker_id . " and type = '" . $type . "'"
      . ' and create_time >=' . $today_from . ' and create_time <='
      . $today_to;
    return $this->credit_record_base_model->count_by($where);
  }

  /**
   *
   * @param type $broker_id
   * @param type $tbl
   * @param type $type
   * @param type $house_id
   * @return type
   */
  private function _get_broker_house_record($broker_id, $tbl, $type, $house_id)
  {
    //这条房源是否已经增加过积分
    $this->load->model('credit_house_base_model');
    if ($broker_id != 0) {
      $where = 'broker_id = ' . $broker_id . ' and tbl =' . "'" . $tbl
        . "' and type = " . $type . ' and house_id=' . $house_id;
    } else {
      $where = 'tbl =' . "'" . $tbl . "' and type = " . $type
        . ' and house_id=' . $house_id;
    }
    return $this->credit_house_base_model->get_by($where);
  }

  public function count_broker_credit_record()
  {

  }

  /**
   * 返回积分的操作方法
   * @return array
   */
  public function get_way()
  {
    return change_to_key_array($this->_way, 'type');
  }

  /**
   *
   * @param type $broker_id
   * @param type $type
   * @return type
   */
  private function _get_broker_credit_record($broker_id, $type)
  {
    $this->load->model('credit_record_base_model');
    return $this->credit_record_base_model->get_by_broker_id_type($broker_id, $type);
  }

  /**
   *
   * @param type $broker
   * @param type $infofrom
   */
  public function set_broker_param($broker = array(), $infofrom = 0)
  {
    $this->_broker = $broker;
    //来源 不设置获取相应频道的变量
    $this->_infofrom = $infofrom == 0
      ? $this->config->item('credit_infofrom') : $infofrom;
  }

  /**
   * 类初始化
   * @param array $broker 经纪人信息
   */
  public function __construct()
  {
    parent::__construct();
    //引入积分操作类
    $this->load->model('credit_base_model', 'credit_model', '');
  }

  /**
   * 给经纪人增加积分
   * @param int $broker_id 经纪人编号
   * @param string $type 获取途迳类型
   * * @param string $infofrom 来源
   * @param int $score 分值
   * @param string $remark 备注
   * @return true or false
   */
  public function increase($broker_id, $type, $infofrom, $score, $remark = '')
  {
    return $this->credit_model->increase($broker_id, $type, $infofrom, $score, $remark);
  }

  /**
   * 给经纪人扣除积分
   * @param int $broker_id 经纪人编号
   * @param string $type 获取途迳类型
   * * @param string $infofrom 来源
   * @param int $score 分值
   * @param string $remark 备注
   * @return true or false
   */
  public function reduce($broker_id, $type, $infofrom, $score, $remark = null)
  {
    return $this->credit_model->reduce($broker_id, $type, $infofrom, $score, $remark);
  }

  /**
   * 经纪人注册 只可获取1次
   * @return array 返回结果
   */
  public function register()
  {
    $way_one = $this->_way['register'];
    //增加积分
    $result = $this->increase($this->_broker['broker_id'], $way_one['type'],
      $this->_infofrom, $way_one['score']);
    if (is_full_array($result) && $result['status'] == 1) //添加成功
    {
      $this->_arrRtnResult['status'] = 1;
      $this->_arrRtnResult['score'] = $result['score'];
      $this->_arrRtnResult['msg'] = '操作成功';
    } else {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['msg'] = '操作失败';
    }
    return $this->_arrRtnResult;
  }

  /**
   * 资料认证 认证只可获取1次
   * @return array 返回结果
   */
  public function ident_cert()
  {
    //引入认证模型
    $this->load->model('credit_cert_base_model');
    $broker_id = $this->_broker['broker_id'];
    //查找经纪人的认证记录
    $credit_cert = $this->credit_cert_base_model->get_by_broker_id($broker_id);
    if ($credit_cert['broker_cert'] == 1) //已认证
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 2;
      $this->_arrRtnResult['msg'] = '此功能已认证过';
    } else {
      $way_one = $this->_way['ident_cert'];
      $result = $this->increase($broker_id, $way_one['type'],
        $this->_infofrom, $way_one['score']);
      if (is_full_array($result) && $result['status'] == 1) {
        //更新认证记录
        $this->credit_cert_base_model->update_by_broker_id(
          array('broker_cert' => 1), $broker_id);
        $this->_arrRtnResult['status'] = 1;
        $this->_arrRtnResult['score'] = $result['score'];
        $this->_arrRtnResult['msg'] = '操作成功';
      } else {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 1;
        $this->_arrRtnResult['msg'] = '操作失败';
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * APP每日启动 每日上限1次，只限APP，增加连续登录激励
   */
  public function sign()
  {
    //引入积分操作记录模型
    $way_one = $this->_way['sign'];
    $broker_id = $this->_broker['broker_id'];
    //一、判断当天是否已经签到过
    $new_sign = $this->_get_broker_credit_record($broker_id, $way_one['type']);
    $today_from = strtotime(date('Y-m-d') . ' 00:00:00'); //当天的起始时间
    $today_to = strtotime(date('Y-m-d') . ' 23:59:59'); //当天的结束时间
    $is_today = ($new_sign['create_time'] >= $today_from
      && $new_sign['create_time'] <= $today_to)
      ? true : false;
    if ($is_today)//已经签到过，不重复增加积分
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 2;
      $this->_arrRtnResult['msg'] = '今天已经签到过了';
    } else //没有签到
    {
      /*
       * 获取最新一次签到记录，判断与当前时间是否小于1天，
       * 如果小于则增加积分数为前一天积分数+重新开始记，反之重新开始记
       **/
      $score = $way_one['app_score'];
      //小于一天
      if (is_full_array($new_sign)
        && ($today_from - strtotime(date('Y-m-d', $new_sign['create_time'])) == 86400)
      ) {
        $score = $new_sign['score'] + $score;
      }
      $result = $this->increase($broker_id, $way_one['type'],
        $this->_infofrom, $score,
        'APP连续登录' . ($score / $way_one['app_score']) . '天');
      if (is_full_array($result) && $result['status'] == 1) {
        $this->_arrRtnResult['status'] = 1;
        $this->_arrRtnResult['score'] = $result['score'];
        $this->_arrRtnResult['msg'] = '签到成功';
      } else {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 1;
        $this->_arrRtnResult['msg'] = '操作失败';
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * 同步 PC端10分，APP端15分， 每天最多10套
   * @param type $house_detail
   */
  public function rsync_fang100($house_detail, $tbl)
  {
    //每日上限XX分，同一套房源仅获得1次，APP比PC分值和上限都高，
    //房源必须是带图片（至少1张室内 1户型）
    //tbl 区分出售和出租
    /**
     * 一、判断房源是否满足加积分的要求
     * 二、这条房源是否已经增加过积分
     * 三、判断积分有没有超过上限
     */
    $this->load->model('pic_model');
    //统计室内图的数量
    $where = array('tbl' => 'sell_house', 'type' => 1, 'rowid' => $house_detail['id']);
    $num1 = $this->pic_model->count_house_pic_by_cond($where);
    //统计户型图的数量
    $where = array('tbl' => 'sell_house', 'type' => 2, 'rowid' => $house_detail['id']);
    $num2 = $this->pic_model->count_house_pic_by_cond($where);
    $broker_id = $this->_broker['broker_id'];
    $way_one = $this->_way['rsync_fang100'];
    if ($num1 > 0 && $num2 > 0) //满足条件
    {
      //这条房源是否已经增加过积分
      $credit_house = $this->_get_broker_house_record($broker_id, $tbl, $way_one['type'], $house_detail['id']);
      if (is_full_array($credit_house)) //如果已经增加过了
      {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 2;
        $this->_arrRtnResult['msg'] = '已经增加过了';
      } else {
        $count = $this->_count_broker_house_record($broker_id, $way_one['type']);
        if ($count >= $way_one['limit']) {
          $this->_arrRtnResult['status'] = 0;
          $this->_arrRtnResult['code'] = 3;
          $this->_arrRtnResult['msg'] = '超过上限';
        } else {
          if ($this->_infofrom == 1) //来源判断
          {
            $score = $way_one['pc_score'];
            $infofrom_name = 'PC';
          } else //app
          {
            $score = $way_one['app_score'];
            $infofrom_name = 'APP';
          }
          $score = $this->_infofrom == 1 ? $way_one['pc_score'] : $way_one['app_score'];
          $result = $this->increase($broker_id, $way_one['type'], $this->_infofrom, $score,
            $infofrom_name . ',房源编号：' . $house_detail['id']);
          if (is_full_array($result) && $result['status'] == 1) {
            $this->_arrRtnResult['status'] = 1;
            $this->_arrRtnResult['score'] = $result['score'];
            $this->_arrRtnResult['msg'] = '操作成功';
            //插入房源操作记录
            $insert_data = array(
              'broker_id' => $broker_id, 'type' => $way_one['type'],
              'infofrom' => $this->_infofrom, 'tbl' => $tbl,
              'house_id' => $house_detail['id'], 'score' => $result['score'],
              'create_time' => time()
            );
            $this->_insert_broker_house_record($insert_data);
          } else {
            $this->_arrRtnResult['status'] = 0;
            $this->_arrRtnResult['code'] = 1;
            $this->_arrRtnResult['msg'] = '操作失败';
          }
        }
      }
    } else //不满足条件
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 1;
      $this->_arrRtnResult['msg'] = '不满足条件';
    }
    return $this->_arrRtnResult;
  }

  public function fang100_activity($house_detail, $tbl)
  {
    $this->load->model('pic_model');
    //统计室内图的数量
    $where = array('tbl' => 'sell_house', 'type' => 1, 'rowid' => $house_detail['id']);
    $num1 = $this->pic_model->count_house_pic_by_cond($where);
    //统计户型图的数量
    $where = array('tbl' => 'sell_house', 'type' => 2, 'rowid' => $house_detail['id']);
    $num2 = $this->pic_model->count_house_pic_by_cond($where);
    if ($num1 >= 3 && $num2 >= 1) {
      //活动时间
      $nowtime = time();//现在时间
      $start_time = strtotime('2016-07-22 00:00:00');//开始时间
      $end_time = strtotime('2016-08-30 23:59:59');//结束时间
      if ($nowtime >= $start_time && $nowtime <= $end_time) {
        $way_one = $this->_way['fang100_activity'];
        //这条房源是否已经增加过积分
        $credit_house = $this->_get_broker_house_record($house_detail['broker_id'], $tbl, $way_one['type'], $house_detail['id']);
        if (is_full_array($credit_house)) //如果已经增加过了
        {
          $this->_arrRtnResult['status'] = 0;
          $this->_arrRtnResult['code'] = 2;
          $this->_arrRtnResult['msg'] = '已经增加过了';
        } else {
          if ($this->_infofrom == 1) //来源判断
          {
            $score = $way_one['pc_score'];
            $infofrom_name = 'PC';
          } else //app
          {
            $score = $way_one['app_score'];
            $infofrom_name = 'APP';
          }
          $result = $this->increase($house_detail['broker_id'], $way_one['type'], $this->_infofrom, $way_one['score'],
            $infofrom_name . ',房源编号：' . $house_detail['id']);
          if (is_full_array($result) && $result['status'] == 1) {
            $this->_arrRtnResult['status'] = 1;
            $this->_arrRtnResult['score'] = $result['score'];
            $this->_arrRtnResult['msg'] = '操作成功';
            //插入房源操作记录
            $insert_data = array(
              'broker_id' => $house_detail['broker_id'], 'type' => $way_one['type'],
              'infofrom' => $this->_infofrom, 'tbl' => $tbl,
              'house_id' => $house_detail['id'], 'score' => $result['score'],
              'create_time' => time()
            );
            $this->_insert_broker_house_record($insert_data);
          }
        }
      } else {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 1;
        $this->_arrRtnResult['msg'] = '活动已过期';
      }
    } else {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 1;
      $this->_arrRtnResult['msg'] = '不满足条件';
    }
    return $this->_arrRtnResult;
  }

  /**
   * 发布合作房客源
   * 每日上限40分，只限不同房源
   */
  public function publish_cooperate_house($house_detail, $tbl)
  {
    $way_one = $this->_way['publish_cooperate_house'];
    $broker_id = $this->_broker['broker_id'];
    //这条房源是否已经增加过积分
    $credit_house = $this->_get_broker_house_record($broker_id, $tbl,
      $way_one['type'], $house_detail['id']);
    if (is_full_array($credit_house)) //如果已经增加过了
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 2;
      $this->_arrRtnResult['msg'] = '已经增加过了';
    } else {
      $count = $this->_count_broker_house_record($broker_id, $way_one['type']);
      if ($count >= $way_one['limit']) //超过上限
      {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 3;
        $this->_arrRtnResult['msg'] = '超过上限';
      } else {
        if ($this->_infofrom == 1) //来源判断
        {
          $score = $way_one['pc_score'];
          $infofrom_name = 'PC';
        } else //app
        {
          $score = $way_one['app_score'];
          $infofrom_name = 'APP';
        }
        $tbl_name = ($tbl == 1 || $tbl == 2) ? '房源' : '客源';
        $result = $this->increase(
          $broker_id, $way_one['type'], $this->_infofrom, $score,
          $infofrom_name . ',' . $tbl_name . '编号：' . $house_detail['id']
        );
        if (is_full_array($result) && $result['status'] == 1) {
          $this->_arrRtnResult['status'] = 1;
          $this->_arrRtnResult['score'] = $result['score'];
          $this->_arrRtnResult['msg'] = '操作成功';
          //插入房源操作记录
          $insert_data = array(
            'broker_id' => $broker_id, 'type' => $way_one['type'],
            'infofrom' => $this->_infofrom, 'tbl' => $tbl,
            'house_id' => $house_detail['id'], 'score' => $result['score'],
            'create_time' => time()
          );
          $this->_insert_broker_house_record($insert_data);
        } else {
          $this->_arrRtnResult['status'] = 0;
          $this->_arrRtnResult['msg'] = '操作失败';
        }
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * 每日无上限，只限不同公司，每套房源只有第一次接受的合作申请加分 接收合作的那个人
   * @param array $cooperate_info 合作祥情信息
   */
  public function accept_cooperate($cooperate_info)
  {
    $broker_id = $this->_broker['broker_id'];
    $way_one = $this->_way['accept_cooperate'];
    $tbl = $cooperate_info['tbl'] == 'sell' ? 1 : 2;
    //这条房源是否已经增加过积分
    $credit_house = $this->_get_broker_house_record(0, $tbl,
      $way_one['type'], $cooperate_info['rowid']);
    if (is_full_array($credit_house)) //如果已经增加过了
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 2;
      $this->_arrRtnResult['msg'] = '已经接受过';
    } else {
      //查找双方所属公司
      $this->load->model('agency_base_model');
      $broker_a_company = $this->agency_base_model->get_by_id($cooperate_info['agentid_a']);
      $broker_b_company = $this->agency_base_model->get_by_id($cooperate_info['agentid_b']);
      //判断是否是同一家公司
      if (($broker_a_company['company_id'] == $broker_b_company['company_id'] && in_array($broker_a_company['agency_type'], array(0, 1)) && in_array($broker_b_company['agency_type'], array(0, 1))) || ($cooperate_info['agentid_a'] == $cooperate_info['agentid_b'])) {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['msg'] = '同一家公司';
        $result['score'] = 0;
      } else //不是同一家公司的，可以增加积分
      {
        $count = $this->_count_broker_house_record($broker_id, $way_one['type']);
        if ($count >= $way_one['limit']) //超过上限
        {
          $this->_arrRtnResult['status'] = 0;
          $this->_arrRtnResult['code'] = 3;
          $this->_arrRtnResult['msg'] = '超过上限';
        } else {
          if ($this->_infofrom == 1) //来源判断
          {
            $score = $way_one['pc_score'];
            $infofrom_name = 'PC';
          } else //app
          {
            $score = $way_one['app_score'];
            $infofrom_name = 'APP';
          }
          $result = $this->increase(
            $broker_id, $way_one['type'], $this->_infofrom, $score,
            $infofrom_name . ',交易编号：' . $cooperate_info['order_sn']
          );
          if (is_full_array($result) && $result['status'] == 1) {
            $this->_arrRtnResult['status'] = 1;
            $this->_arrRtnResult['score'] = $result['score'];
            $this->_arrRtnResult['msg'] = '操作成功';
          } else {
            $this->_arrRtnResult['status'] = 0;
            $this->_arrRtnResult['msg'] = '操作失败';
          }
        }
      }
      if (!(isset($this->_arrRtnResult['code']) && $this->_arrRtnResult['code'] == 3)) {
        //插入房源操作记录
        $insert_data = array(
          'broker_id' => $broker_id, 'type' => $way_one['type'],
          'infofrom' => $this->_infofrom, 'tbl' => $tbl,
          'house_id' => $cooperate_info['rowid'], 'score' => $result['score'],
          'create_time' => time()
        );
        $this->_insert_broker_house_record($insert_data);
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * 每套房源仅获得1次，只限不同公司，给双方加
   * @param array $cooperate_info 合作祥情信息
   */
  public function cooperate_confirm_deal($cooperate_info)
  {
    //查找双方所属公司
    $this->load->model('agency_base_model');
    $broker_a_company = $this->agency_base_model->get_by_id($cooperate_info['agentid_a']);
    $broker_b_company = $this->agency_base_model->get_by_id($cooperate_info['agentid_b']);
    //判断是否是同一家公司
    if (($broker_a_company['company_id'] == $broker_b_company['company_id'] && in_array($broker_a_company['agency_type'], array(0, 1)) && in_array($broker_b_company['agency_type'], array(0, 1))) || ($cooperate_info['agentid_a'] == $cooperate_info['agentid_b'])) {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['msg'] = '同一家公司';
    } else //可以增加积分
    {
      $way_one = $this->_way['cooperate_confirm_deal'];
      $tbl = $cooperate_info['tbl'] == 'sell' ? 1 : 2;
      //这条房源是否已经增加过积分
      $credit_house = $this->_get_broker_house_record(0, $tbl,
        $way_one['type'], $cooperate_info['rowid']);
      if (is_full_array($credit_house)) //如果已经增加过了
      {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 2;
        $this->_arrRtnResult['msg'] = '已经增加过了';
      } else {
        if ($this->_infofrom == 1) //来源判断
        {
          $score = $way_one['pc_score'];
          $infofrom_name = 'PC';
        } else //app
        {
          $score = $way_one['app_score'];
          $infofrom_name = 'APP';
        }
        //甲乙双方都加积分
        $result_a = $this->increase(
          $cooperate_info['brokerid_a'], $way_one['type'],
          $this->_infofrom, $score, $infofrom_name . ',交易编号：'
          . $cooperate_info['order_sn']
        );
        $result_b = $this->increase(
          $cooperate_info['brokerid_b'], $way_one['type'],
          $this->_infofrom, $score, $infofrom_name . ',交易编号：'
          . $cooperate_info['order_sn']
        );
        if (is_full_array($result_a) && $result_a['status'] == 1
          && is_full_array($result_b) && $result_b['status'] == 1
        ) {
          $this->_arrRtnResult['status'] = 1;
          $this->_arrRtnResult['score'] = $result_a['score'];
          $this->_arrRtnResult['msg'] = '操作成功';
          //甲乙双方插入房源操作记录
          $insert_data_a = array(
            'broker_id' => $cooperate_info['brokerid_a'],
            'type' => $way_one['type'], 'infofrom' => $this->_infofrom,
            'tbl' => $tbl, 'house_id' => $cooperate_info['rowid'],
            'score' => $result_a['score'], 'create_time' => time()
          );
          $this->_insert_broker_house_record($insert_data_a);
          $insert_data_b = array(
            'broker_id' => $cooperate_info['brokerid_b'],
            'type' => $way_one['type'], 'infofrom' => $this->_infofrom,
            'tbl' => $tbl, 'house_id' => $cooperate_info['rowid'],
            'score' => $result_b['score'], 'create_time' => time()
          );
          $this->_insert_broker_house_record($insert_data_b);
        } else {
          $this->_arrRtnResult['status'] = 0;
          $this->_arrRtnResult['msg'] = '操作失败';
        }
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * 每日上限50分，同一合作仅获得1次，只限不同公司。
   * @param array $cooperate_info 合作祥情信息
   */
  public function cooperate_appraise($cooperate_info)
  {
    //查找双方所属公司
    $this->load->model('agency_base_model');
    $broker_a_company = $this->agency_base_model->get_by_id($cooperate_info['agentid_a']);
    $broker_b_company = $this->agency_base_model->get_by_id($cooperate_info['agentid_b']);
    //判断是否是同一家公司
    if (($broker_a_company['company_id'] == $broker_b_company['company_id'] && in_array($broker_a_company['agency_type'], array(0, 1)) && in_array($broker_b_company['agency_type'], array(0, 1))) || ($cooperate_info['agentid_a'] == $cooperate_info['agentid_b'])) {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['msg'] = '同一家公司';
    } else {
      $broker_id = $this->_broker['broker_id'];
      $way_one = $this->_way['cooperate_appraise'];
      $tbl = $cooperate_info['tbl'] == 'sell' ? 1 : 2;
      $count = $this->_count_broker_house_record($broker_id, $way_one['type']);
      if ($count >= $way_one['limit']) {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 3;
        $this->_arrRtnResult['msg'] = '超过上限';
      } else {
        if ($this->_infofrom == 1) //来源判断
        {
          $score = $way_one['pc_score'];
          $infofrom_name = 'PC';
        } else //app
        {
          $score = $way_one['app_score'];
          $infofrom_name = 'APP';
        }
        $result = $this->increase(
          $broker_id, $way_one['type'], $this->_infofrom,
          $score, $infofrom_name . ',交易编号：'
          . $cooperate_info['order_sn']
        );
        if (is_full_array($result) && $result['status'] == 1) {
          $this->_arrRtnResult['status'] = 1;
          $this->_arrRtnResult['score'] = $result['score'];
          $this->_arrRtnResult['msg'] = '操作成功';
          //插入房源操作记录
          $insert_data = array(
            'broker_id' => $broker_id, 'type' => $way_one['type'],
            'infofrom' => $this->_infofrom, 'tbl' => $tbl,
            'house_id' => $cooperate_info['rowid'],
            'score' => $result['score'],
            'create_time' => time()
          );
          $this->_insert_broker_house_record($insert_data);
        } else {
          $this->_arrRtnResult['status'] = 0;
          $this->_arrRtnResult['msg'] = '操作失败';
        }
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * 礼品兑换
   * @param int $score 分值
   * @param string $remark 备注
   */
  public function gift_exchange($score, $remark)
  {
    $broker_id = $this->_broker['broker_id'];
    $way_one = $this->_way['gift_exchange'];
    $result = $this->reduce($broker_id, $way_one['type'], $this->_infofrom, $score, $remark);
    if ($result['status'] == 1) {
      $this->_arrRtnResult['status'] = 1;
      $this->_arrRtnResult['msg'] = '操作成功';
    } else {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['msg'] = '操作失败';
    }
    return $this->_arrRtnResult;
  }

  /**
   * 商品抽奖
   * @param int $score 分值
   * @param string $remark 备注
   */
  public function gift_raffle($score, $remark, $remark_add = '', $add_score = 0)
  {
    $broker_id = $this->_broker['broker_id'];
    $way_one = $this->_way['gift_raffle'];
    $result = $this->reduce($broker_id, $way_one['type'], $this->_infofrom, $score, $remark);
    if ($add_score) {
      $this->increase($broker_id, $way_one['type'], $this->_infofrom, $add_score, $remark_add);
    }
    if ($result['status'] == 1) {
      $this->_arrRtnResult['status'] = 1;
      $this->_arrRtnResult['msg'] = '操作成功';
    } else {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['msg'] = '操作失败';
    }
    return $this->_arrRtnResult;
  }

  /**
   * 合作活动积分
   * @param int $score 分值
   * @param string $remark 备注
   */
  public function activity_jifen($cooperate_info, $type)
  {

    $way_one = $this->_way['cooperate_activity'];

    if ($type == 1) {
      $score = $way_one['chushen_score'];
    } else {
      $score = $way_one['zhongshen_score'];
    }
    //甲乙双方都加积分
    $result_a = $this->increase($cooperate_info['brokerid_a'], $way_one['type'], $this->_infofrom, $score);
    $result_b = $this->increase($cooperate_info['brokerid_b'], $way_one['type'], $this->_infofrom, $score);
    if ($result_a && $result_b) {
      $this->_arrRtnResult['status'] = 1;
      $this->_arrRtnResult['msg'] = '操作成功';
    } else {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['msg'] = '操作失败';
    }
    return $this->_arrRtnResult;
  }


  /**
   * 新房报备报备成功，每日上限3次
   */
  public function xffx_baobei($post_param)
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['xffx_baobei'];
    $broker_id = $this->_broker['broker_id'];
    //一、判断当天已举报通过次数
    $count = $this->_count_broker_credit_record($broker_id, $way_one['type']);
    if ($count >= $way_one['limit']) //超过上限
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 3;
      $this->_arrRtnResult['msg'] = '超过上限';
    } else {
      if ($this->_infofrom == 1) //来源判断
      {
        $score = $way_one['pc_score'];
        $infofrom_name = 'PC';
      } else //app
      {
        $score = $way_one['app_score'];
        $infofrom_name = 'APP';
      }
      $result = $this->increase($broker_id, $way_one['type'],
        $this->_infofrom, $score, '新房报备,楼盘:' . $post_param['cm_name'] . ',报备编号:' . $post_param['cp_code']);
      if (is_full_array($result) && $result['status'] == 1) {
        $this->_arrRtnResult['status'] = 1;
        $this->_arrRtnResult['score'] = $result['score'];
        $this->_arrRtnResult['msg'] = '报备成功';
      } else {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 1;
        $this->_arrRtnResult['msg'] = '报备失败';
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * 新房带看成功，无上限
   */
  public function xffx_daikan($post_param)
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['xffx_daikan'];
    $broker_id = $this->_broker['broker_id'];
    if ($this->_infofrom == 1) //来源判断
    {
      $score = $way_one['pc_score'];
      $infofrom_name = 'PC';
    } else //app
    {
      $score = $way_one['app_score'];
      $infofrom_name = 'APP';
    }
    $result = $this->increase($broker_id, $way_one['type'],
      $this->_infofrom, $score, '新房带看,楼盘:' . $post_param['cm_name'] . ',报备编号:' . $post_param['cp_code']);
    if (is_full_array($result) && $result['status'] == 1) {
      $this->_arrRtnResult['status'] = 1;
      $this->_arrRtnResult['score'] = $result['score'];
      $this->_arrRtnResult['msg'] = '新房带看成功';
    } else {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 1;
      $this->_arrRtnResult['msg'] = '新房带看失败';
    }
    return $this->_arrRtnResult;
  }


  /**
   * 新房认购成功，无上限
   */
  public function xffx_rengou($post_param)
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['xffx_rengou'];
    $broker_id = $this->_broker['broker_id'];
    if ($this->_infofrom == 1) //来源判断
    {
      $score = $way_one['pc_score'];
      $infofrom_name = 'PC';
    } else //app
    {
      $score = $way_one['app_score'];
      $infofrom_name = 'APP';
    }
    $result = $this->increase($broker_id, $way_one['type'],
      $this->_infofrom, $score, '新房认购,楼盘:' . $post_param['cm_name'] . ',报备编号:' . $post_param['cp_code']);
    if (is_full_array($result) && $result['status'] == 1) {
      $this->_arrRtnResult['status'] = 1;
      $this->_arrRtnResult['score'] = $result['score'];
      $this->_arrRtnResult['msg'] = '新房认购成功';
    } else {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 1;
      $this->_arrRtnResult['msg'] = '新房认购失败';
    }
    return $this->_arrRtnResult;
  }


  /**
   * 同步平安好房加积分
   * @param int $score 分值
   * @param string $remark 备注
   */
  public function pinganhaofang_jifen($broker_id, $house_id)
  {
    $way_one = $this->_way['pinganhaofang'];
    //这条房源是否已经增加过积分
    $credit_house = $this->_get_broker_house_record($broker_id, 1,
      $way_one['type'], $house_id);
    if (is_full_array($credit_house)) //如果已经增加过了
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 2;
      $this->_arrRtnResult['msg'] = '已经增加过了';
    } else {
      if ($this->_infofrom == 1) //来源判断
      {
        $infofrom_name = 'PC';
      } else //app
      {
        $infofrom_name = 'APP';
      }
      //加积分
      $result = $this->increase($broker_id, $way_one['type'], $this->_infofrom, $way_one['score'], $infofrom_name . ',房源编号：' . $house_id);
      if ($result && $result['status'] == 1) {
        //插入房源操作记录
        $insert_data = array(
          'broker_id' => $broker_id, 'type' => $way_one['type'],
          'infofrom' => $this->_infofrom, 'tbl' => '1',
          'house_id' => $house_id, 'score' => $result['score'],
          'create_time' => time()
        );
        $this->_insert_broker_house_record($insert_data);
        $this->_arrRtnResult['status'] = 1;
        $this->_arrRtnResult['msg'] = '操作成功';
      } else {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['msg'] = '操作失败';
      }
    }
    return $this->_arrRtnResult;
  }

}

/* End of file Api_broker_credit_base_model.php */
/* Location: ./applications/models/Api_broker_credit_base_model.php */
