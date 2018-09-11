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
 * Api_broker_level_base_model CLASS
 *
 * 经纪人增加，扣除等级分值接口
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Api_broker_level_base_model extends MY_Model
{

  //获取等级分值(成长值)的区间
  /*private $_level_score = array(
      '1 , 'down' => 0, 'up' => 400),
      '2 , 'down' => 400, 'up' => 1000),
      '3 , 'down' => 1000, 'up' => 1800),
      '4 , 'down' => 1800, 'up' => 2800),
      '5 , 'down' => 2800, 'up' => 4000),
      '6 , 'down' => 4000, 'up' => 5600),
      '7 , 'down' => 5600, 'up' => 7200),
      '8 , 'down' => 7200, 'up' => 8800),
      '9 , 'down' => 8800, 'up' => 10800),
      '10 , 'down' => 10800, 'up' => 13000),
      '11 , 'down' => 13000, 'up' => 15400),
      '12 , 'down' => 15400, 'up' => 18000),
      '13 , 'down' => 18000, 'up' => 20800),
      '14 , 'down' => 20800, 'up' => 23800),
      '15 , 'down' => 23800, 'up' => 27000),
      '16 , 'down' => 27000, 'up' => 30400),
      '17 , 'down' => 30400, 'up' => 34000),
      '18 , 'down' => 34000, 'up' => 37800),
      '19 , 'down' => 37800, 'up' => 41800),
      '20 , 'down' => 41800, 'up' => 46000),
      '21 , 'down' => 46000, 'up' => 50400),
      '22 , 'down' => 50400, 'up' => 55000),
      '23 , 'down' => 55000, 'up' => 59800),
      '24 , 'down' => 59800, 'up' => 64800),
      '25 , 'down' => 64800, 'up' => 70000),
      '26 , 'down' => 70000, 'up' => 75400),
      '27 , 'down' => 75400, 'up' => 81000),
      '28 , 'down' => 81000, 'up' => 86800),
      '29 , 'down' => 86800, 'up' => 92800),
      '30 , 'down' => 92800, 'up' => 99000),
      '31 , 'down' => 99000, 'up' => 105400),
      '32 , 'down' => 105400, 'up' => 112000),
      '33 , 'down' => 112000, 'up' => 118800),
      '34 , 'down' => 118800, 'up' => 125800),
      '35 , 'down' => 125800, 'up' => 133000),
      '36 , 'down' => 133000, 'up' => 140400),
      '37 , 'down' => 140400, 'up' => 148000),
      '38 , 'down' => 148000, 'up' => 155800),
      '39 , 'down' => 155800, 'up' => 163800),
      '40 , 'down' => 163800, 'up' => 172000),
      '41 , 'down' => 172000, 'up' => 180400),
      '42 , 'down' => 180400, 'up' => 189000),
      '43 , 'down' => 189000, 'up' => 197800),
      '44 , 'down' => 197800, 'up' => 206800),
      '45 , 'down' => 206800, 'up' => 216000),
      '46 , 'down' => 216000, 'up' => 225400),
      '47 , 'down' => 225400, 'up' => 235000),
      '48 , 'down' => 235000, 'up' => 244800),
      '49 , 'down' => 244800, 'up' => 254800),
      '50 , 'down' => 254800, 'up' => 265000),
  );*/
  private $_level_score =
    array('1' => 400, '2' => 1000, '3' => 1800, '4' => 2800, '5' => 4000, '6' => 5600, '7' => 7200, '8' => 8800, '9' => 10800, '10' => 13000, '11' => 15400, '12' => 18000, '13' => 20800, '14' => 23800, '15' => 27000, '16' => 30400, '17' => 34000, '18' => 37800, '19' => 41800, '20' => 46000, '21' => 50400, '22' => 55000, '23' => 59800, '24' => 64800, '25' => 70000, '26' => 75400, '27' => 81000, '28' => 86800, '29' => 92800, '30' => 99000, '31' => 105400, '32' => 112000, '33' => 118800, '34' => 125800, '35' => 133000, '36' => 140400, '37' => 148000, '38' => 155800, '39' => 163800, '40' => 172000, '41' => 180400, '42' => 189000, '43' => 197800, '44' => 206800, '45' => 216000, '46' => 225400, '47' => 235000, '48' => 244800, '49' => 254800, '50' => 265000);

  //获取消耗等级分值的途径
  private $_way = array(
    'register' => array('action' => '注册', 'score' => 100, 'type' => 1),
    'ident_cert' => array('action' => '认证', 'score' => 200, 'type' => 2),
    'sign' => array('action' => '每日登录', 'score' => 5, 'type' => 3),
    'rsync_fang100' => array('action' => '房源同步', 'score' => 5, 'pc_score' => 5, 'app_score' => 5, 'type' => 4, 'limit' => 4),
    'publish_cooperate_house' => array('action' => '发布合作房/客源', 'score' => 10, 'pc_score' => 10, 'app_score' => 10, 'type' => 5, 'limit' => 2),
    'accept_cooperate' => array('action' => '接受合作申请', 'score' => 10, 'pc_score' => 10, 'app_score' => 10, 'type' => 6, 'limit' => 2),
    'cooperate_confirm_deal' => array('action' => '合作确认成交', 'score' => 300, 'app_score' => 300, 'pc_score' => 300, 'type' => 7),
    'cooperate_appraise' => array('action' => '合作评价', 'score' => 5, 'pc_score' => 5, 'app_score' => 5, 'type' => 8, 'limit' => 5),
    'blacklist' => array('action' => '采集举报', 'score' => 2, 'pc_score' => 2, 'app_score' => 2, 'type' => 9, 'limit' => 10),
    'grab' => array('action' => '抢拍房源', 'score' => 5, 'pc_score' => 5, 'app_score' => 5, 'type' => 10, 'limit' => 5),
    'abroad_report' => array('action' => '海外报备', 'score' => 10, 'pc_score' => 10, 'app_score' => 10, 'type' => 11, 'limit' => 2),
    'tourism_report' => array('action' => '旅游报备', 'score' => 10, 'pc_score' => 10, 'app_score' => 10, 'type' => 12, 'limit' => 2),
    'xffx_baobei' => array('action' => '新房报备', 'score' => 10, 'pc_score' => 10, 'app_score' => 10, 'type' => 13, 'limit' => 3),
    'xffx_daikan' => array('action' => '新房带看', 'score' => 100, 'pc_score' => 100, 'app_score' => 100, 'type' => 14),
    'xffx_rengou' => array('action' => '新房认购', 'score' => 300, 'pc_score' => 300, 'app_score' => 300, 'type' => 15)
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
   * 0 代表 '失败'， 'msg' : '超过上限等级分值'  code = 3
   * 0 代表 '失败'， 'msg' : '等级分值不足，扣除等级分值失败'  code = 4
   * 1 代表 '成功'， 'score' : '返回成功增加或者扣除的等级分值值' code =1
   * 1 代表 '成功'， 'score' : '返回成功扣除的等级分值【经纪人最大可增加或者可扣除的分值】值'  code =2
   */

  /**
   *
   * @param type $insert_data
   */
  private function _insert_broker_house_record($insert_data)
  {
    $this->load->model('level_house_base_model');
    return $this->level_house_base_model->insert($insert_data);
  }

  /**
   *
   * @param type $broker_id
   * @param type $type
   * @return type
   */
  private function _count_broker_house_record($broker_id, $type)
  {
    //这条房源是否已经增加过等级分值
    $this->load->model('level_house_base_model');
    $today_from = strtotime(date('Y-m-d') . ' 00:00:00'); //当天的起始时间
    $today_to = strtotime(date('Y-m-d') . ' 23:59:59'); //当天的结束时间
    $where = 'broker_id = ' . $broker_id . " and type = '" . $type . "'"
      . ' and create_time >=' . $today_from . ' and create_time <='
      . $today_to;
    return $this->level_house_base_model->count_by($where);
  }

  private function _count_broker_house_record_shua($broker_id, $type)
  {
    //这条房源是否已经增加过等级分值
    $this->load->model('level_house_base_model');
    $where = 'broker_id = ' . $broker_id . " and type = '" . $type . "'";
    return $this->level_house_base_model->count_by($where);
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
    //这条房源是否已经增加过等级分值
    $this->load->model('level_house_base_model');
    if ($broker_id != 0) {
      $where = 'broker_id = ' . $broker_id . ' and tbl =' . "'" . $tbl
        . "' and type = " . $type . ' and house_id=' . $house_id;
    } else {
      $where = 'tbl =' . "'" . $tbl . "' and type = " . $type
        . ' and house_id=' . $house_id;
    }

    return $this->level_house_base_model->get_by($where);
  }

  public function _count_broker_level_record($broker_id, $type)
  {
    //该经纪人增加过等级分值
    $this->load->model('level_record_base_model');
    $today_from = strtotime(date('Y-m-d') . ' 00:00:00'); //当天的起始时间
    $today_to = strtotime(date('Y-m-d') . ' 23:59:59'); //当天的结束时间
    $where = 'broker_id = ' . $broker_id . " and type = '" . $type . "'"
      . ' and create_time >=' . $today_from . ' and create_time <='
      . $today_to;
    return $this->level_record_base_model->count_by($where);
  }

  /**
   * 返回等级分值的操作方法
   * @return array
   */
  public function get_way()
  {
    return change_to_key_array($this->_way, 'type');
  }

  public function get_way_app()
  {
    return $this->_way;
  }

  /**
   *
   * @param type $broker_id
   * @param type $type
   * @return type
   */
  private function _get_broker_level_record($broker_id, $type)
  {
    $this->load->model('level_record_base_model');
    return $this->level_record_base_model->get_by_broker_id_type($broker_id, $type);
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
    $this->_infofrom = $infofrom == 0 ? $this->config->item('level_infofrom') : $infofrom;
  }

  /**
   * 类初始化
   * @param array $broker 经纪人信息
   */
  public function __construct()
  {
    parent::__construct();
    //引入等级分值操作类
    $this->load->model('level_base_model', 'level_model', '');
  }

  /**
   * 给经纪人增加等级分值
   * @param int $broker_id 经纪人编号
   * @param string $type 获取途迳类型
   * * @param string $infofrom 来源
   * @param int $score 分值
   * @param string $remark 备注
   * @return true or false
   */
  public function increase($broker_id, $type, $infofrom, $score, $remark = '')
  {
    return $this->level_model->increase($broker_id, $type, $infofrom, $score, $remark);
  }

  /**
   * 给经纪人扣除等级分值
   * @param int $broker_id 经纪人编号
   * @param string $type 获取途迳类型
   * * @param string $infofrom 来源
   * @param int $score 分值
   * @param string $remark 备注
   * @return true or false
   */
  public function reduce($broker_id, $type, $infofrom, $score, $remark = null)
  {
    return $this->level_model->reduce($broker_id, $type, $infofrom, $score, $remark);
  }

  /**
   * 经纪人注册 只可获取1次
   * @return array 返回结果
   */
  public function register()
  {
    //引入认证模型
    $this->load->model('level_register_base_model');
    $broker_id = $this->_broker['broker_id'];
    //查找经纪人的注册记录
    $level_register = $this->level_register_base_model->get_by_broker_id($broker_id);
    if (is_full_array($level_register)) //已注册
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 2;
      $this->_arrRtnResult['msg'] = '此功能已注册过';
    } else {
      $way_one = $this->_way['register'];
      //增加等级分值
      $result = $this->increase($this->_broker['broker_id'], $way_one['type'],
        $this->_infofrom, $way_one['score'], '注册');
      if (is_full_array($result) && $result['status'] == 1) //添加成功
      {
        $this->_arrRtnResult['status'] = 1;
        $this->_arrRtnResult['score'] = $result['score'];
        $this->_arrRtnResult['msg'] = '操作成功';
        //插入注册记录
        $this->load->model('broker_base_model');
        $this->broker_base_model->add_user_record($broker_id);
      } else {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['msg'] = '操作失败';
      }
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
    $this->load->model('level_cert_base_model');
    $broker_id = $this->_broker['broker_id'];
    //查找经纪人的认证记录
    $level_cert = $this->level_cert_base_model->get_by_broker_id($broker_id);
    if ($level_cert['broker_cert'] == 1) //已认证
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 2;
      $this->_arrRtnResult['msg'] = '此功能已认证过';
    } else {
      $way_one = $this->_way['ident_cert'];
      $result = $this->increase($broker_id, $way_one['type'],
        $this->_infofrom, $way_one['score'], '认证');
      if (is_full_array($result) && $result['status'] == 1) {
        //更新认证记录
        $this->level_cert_base_model->update_by_broker_id(
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
   * 每日启动 每日上限1次
   */
  public function sign()
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['sign'];
    $broker_id = $this->_broker['broker_id'];
    //一、判断当天是否已经签到过
    $new_sign = $this->_get_broker_level_record($broker_id, $way_one['type']);
    //print_r($new_sign);die;
    $today_from = strtotime(date('Y-m-d') . ' 00:00:00'); //当天的起始时间
    $today_to = strtotime(date('Y-m-d') . ' 23:59:59'); //当天的结束时间
    $is_today = ($new_sign['create_time'] >= $today_from
      && $new_sign['create_time'] <= $today_to)
      ? true : false;
    if ($is_today)//已经签到过，不重复增加等级分值
    {
      $this->_arrRtnResult['status'] = 0;
      $this->_arrRtnResult['code'] = 2;
      $this->_arrRtnResult['msg'] = '今天已经签到过了';
    } else //没有签到
    {
      /*
       * 获取最新一次签到记录，判断与当前时间是否小于1天，
       * 如果小于则增加等级分值数为前一天等级分值数+重新开始记，反之重新开始记
       **/
      $score = $way_one['score'];
      //小于一天
      /*if (is_full_array($new_sign)
          && ($today_from - strtotime(date('Y-m-d', $new_sign['create_time'])) == 86400))
      {
          $score = $new_sign['score'] + $score;
      }*/
      $result = $this->increase($broker_id, $way_one['type'],
        $this->_infofrom, $score,
        '连续登录' . ($score / $way_one['score']) . '天');
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
     * 一、判断房源是否满足加等级分值的要求
     * 二、这条房源是否已经增加过等级分值
     * 三、判断等级分值有没有超过上限
     */
    $this->load->model('pic_model');
    $picinfo = $this->pic_model->find_house_pic_by_ids($house_detail['pic_tbl'], $house_detail['pic_ids']);
    //按照原 sell_house 表中的 pic_ids 字段来展示房源图片
    $id_str = substr($house_detail['pic_ids'], 0, strlen($house_detail['pic_ids']) - 1);
    $arr = explode(',', $id_str);
    $room_picinfo = array();#室内图
    //房源图片数据重构
    foreach ($arr as $v) {
      if (is_full_array($picinfo)) {
        foreach ($picinfo as $key => $value) {
          if ($value['id'] == $v && $value['type'] == 1) {
            $room_picinfo[] = $value;
          } else if ($value['id'] == $v && $value['type'] == 2) {
            $outside_info[] = $value;
          }
        }
      }
    }
    $broker_id = $this->_broker['broker_id'];
    $way_one = $this->_way['rsync_fang100'];
    if (count($room_picinfo) > 0 && count($outside_info) > 0) //满足条件
    {
      //这条房源是否已经增加过等级分值
      $level_house = $this->_get_broker_house_record($broker_id, $tbl,
        $way_one['type'], $house_detail['id']);
      if (is_full_array($level_house)) //如果已经增加过了
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

  /**
   * 同步 PC端10分，APP端15分， 每天最多10套(刷数据用)
   * @param type $house_detail
   */
  public function rsync_fang100_shua($house_detail, $tbl)
  {
    //每日上限XX分，同一套房源仅获得1次，APP比PC分值和上限都高，
    //房源必须是带图片（至少1张室内 1户型）
    //tbl 区分出售和出租
    /**
     * 一、判断房源是否满足加等级分值的要求
     * 二、这条房源是否已经增加过等级分值
     * 三、判断等级分值有没有超过上限
     */
    $this->load->model('pic_model');
    $picinfo = $this->pic_model->find_house_pic_by_ids($house_detail['pic_tbl'], $house_detail['pic_ids']);
    //按照原 sell_house 表中的 pic_ids 字段来展示房源图片
    $id_str = substr($house_detail['pic_ids'], 0, strlen($house_detail['pic_ids']) - 1);
    $arr = explode(',', $id_str);
    $room_picinfo = array();#室内图
    //房源图片数据重构
    foreach ($arr as $v) {
      if (is_full_array($picinfo)) {
        foreach ($picinfo as $key => $value) {
          if ($value['id'] == $v && $value['type'] == 1) {
            $room_picinfo[] = $value;
          } else if ($value['id'] == $v && $value['type'] == 2) {
            $outside_info[] = $value;
          }
        }
      }
    }
    $broker_id = $this->_broker['broker_id'];
    $way_one = $this->_way['rsync_fang100'];
    if (count($room_picinfo) > 0 && count($outside_info) > 0) //满足条件
    {
      //这条房源是否已经增加过等级分值
      $level_house = $this->_get_broker_house_record($broker_id, $tbl,
        $way_one['type'], $house_detail['id']);
      if (is_full_array($level_house)) //如果已经增加过了
      {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 2;
        $this->_arrRtnResult['msg'] = '已经增加过了';
      } else {
        $count = $this->_count_broker_house_record_shua($broker_id, $way_one['type']);
        if ($count > 100) {
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

  /**
   * 发布合作房客源
   * 每日上限40分，只限不同房源
   */
  public function publish_cooperate_house($house_detail, $tbl)
  {
    $way_one = $this->_way['publish_cooperate_house'];
    $broker_id = $this->_broker['broker_id'];
    //这条房源是否已经增加过等级分值
    $level_house = $this->_get_broker_house_record($broker_id, $tbl,
      $way_one['type'], $house_detail['id']);
    if (is_full_array($level_house)) //如果已经增加过了
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
    //这条房源是否已经增加过等级分值
    $level_house = $this->_get_broker_house_record(0, $tbl,
      $way_one['type'], $cooperate_info['rowid']);
    if (is_full_array($level_house)) //如果已经增加过了
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
      } else //不是同一家公司的，可以增加等级分值
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
    } else //可以增加等级分值
    {
      $way_one = $this->_way['cooperate_confirm_deal'];
      $tbl = $cooperate_info['tbl'] == 'sell' ? 1 : 2;
      //这条房源是否已经增加过等级分值
      $level_house = $this->_get_broker_house_record(0, $tbl,
        $way_one['type'], $cooperate_info['rowid']);
      if (is_full_array($level_house)) //如果已经增加过了
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
        //甲乙双方都加等级分值
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
   * 采集举报，每日上限5次
   */
  public function blacklist($report_info)
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['blacklist'];
    $broker_id = $this->_broker['broker_id'];
    //一、判断当天已举报通过次数
    $count = $this->_count_broker_level_record($broker_id, $way_one['type']);
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
        $this->_infofrom, $score, '采集举报电话:' . $report_info['r_tel']);
      if (is_full_array($result) && $result['status'] == 1) {
        $this->_arrRtnResult['status'] = 1;
        $this->_arrRtnResult['score'] = $result['score'];
        $this->_arrRtnResult['msg'] = '举报成功';
      } else {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 1;
        $this->_arrRtnResult['msg'] = '举报失败';
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * 抢拍房源，每日上限5次
   */
  public function grab($grab_info, $tab)
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['grab'];
    $broker_id = $this->_broker['broker_id'];
    //一、判断当天已举报通过次数
    $count = $this->_count_broker_level_record($broker_id, $way_one['type']);
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
      switch ($tab) {
        case 'ent_sell':
          $remark = '委托出售房源';
          break;
        case 'ent_rent':
          $remark = '委托出租房源';
          break;
        case 'seek_sell':
          $remark = '求购客源';
          break;
        case 'seek_rent':
          $remark = '求租客源';
          break;
      }
      $result = $this->increase($broker_id, $way_one['type'],
        $this->_infofrom, $score, $infofrom_name . ',抢拍' . $remark . ',编号:' . $grab_info['id']);
      if (is_full_array($result) && $result['status'] == 1) {
        $this->_arrRtnResult['status'] = 1;
        $this->_arrRtnResult['score'] = $result['score'];
        $this->_arrRtnResult['msg'] = '抢拍成功';
      } else {
        $this->_arrRtnResult['status'] = 0;
        $this->_arrRtnResult['code'] = 1;
        $this->_arrRtnResult['msg'] = '抢拍失败';
      }
    }
    return $this->_arrRtnResult;
  }

  /**
   * 海外报备审核，每日上限2次
   */
  public function abroad_report($abroad)
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['abroad_report'];
    $broker_id = $this->_broker['broker_id'];
    //一、判断当天已举报通过次数
    $count = $this->_count_broker_level_record($broker_id, $way_one['type']);
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
        $this->_infofrom, $score, '海外报备,国家:' . $abroad['country_name'] . ',楼盘:' . $abroad['block_name'] . ',房源编号:' . $abroad['house_id']);
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
   * 旅游报备审核，每日上限2次
   */
  public function tourism_report($tourism)
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['tourism_report'];
    $broker_id = $this->_broker['broker_id'];
    //一、判断当天已举报通过次数
    $count = $this->_count_broker_level_record($broker_id, $way_one['type']);
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
        $this->_infofrom, $score, '旅游报备,客户城市:' . $tourism['user_city_name'] . ',楼盘:' . $tourism['block_name'] . ',房源编号:' . $tourism['house_id']);
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
   * 新房报备报备成功，每日上限3次
   */
  public function xffx_baobei($post_param)
  {
    //引入等级分值操作记录模型
    $way_one = $this->_way['xffx_baobei'];
    $broker_id = $this->_broker['broker_id'];
    //一、判断当天已举报通过次数
    $count = $this->_count_broker_level_record($broker_id, $way_one['type']);
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
        $this->_infofrom, $score, '新房报备,楼盘:' . $post_param['lp_name'] . ',报备编号:' . $post_param['cp_code']);
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
      $this->_infofrom, $score, '新房带看,楼盘:' . $post_param['lp_name'] . ',报备编号:' . $post_param['cp_code']);
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
      $this->_infofrom, $score, '新房认购,楼盘:' . $post_param['lp_name'] . ',报备编号:' . $post_param['cp_code']);
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
   * 获取当前经纪人成长等级_PC
   */
  public function get_level($level_score)
  {
    $result = array();
    $proArr = $this->_level_score;
    //概率数组的总概率精度
    $proSum = array_sum($proArr);
    //概率数组循环
    foreach ($proArr as $key => $proCur) {
      if ($level_score < $proCur) {
        $result['level'] = $key - 1;
        break;
      } else {
        $proSum -= $proCur;
      }
    }
    $result['level_score'] = $level_score;
    if ($level_score >= $proArr[50]) {
      $key = 50;
      $result['level'] = $key;
      $result['score_max'] = $proArr[$key] - $proArr[$key - 1];
      $result['score_now'] = $proArr[$key] - $proArr[$key - 1];
    } else {
      if ($key > 1) {
        $result['score_max'] = $proArr[$key] - $proArr[$key - 1];
        $result['score_now'] = $level_score - $proArr[$key - 1];
      } else {
        $result['score_max'] = $proArr[$key];
        $result['score_now'] = $level_score;
      }
    }
    unset ($proArr);
    return $result;
  }

  /**
   * 获取当前经纪人成长等级_APP
   */
  public function get_level_app($level_score)
  {
    $result = array();
    $proArr = $this->_level_score;
    //概率数组的总概率精度
    $proSum = array_sum($proArr);
    //概率数组循环
    foreach ($proArr as $key => $proCur) {
      if ($level_score < $proCur) {
        $result['level'] = $key - 1;
        break;
      } else {
        $proSum -= $proCur;
      }
    }
    $result['level_score'] = $level_score;
    if ($level_score >= $proArr[50]) {
      $key = 50;
      $result['level'] = $key;
      $result['score_next'] = $proArr[$key];
      $result['score_last'] = $proArr[$key - 1];
    } else {
      if ($key > 1) {
        $result['score_next'] = $proArr[$key + 1];
        $result['score_last'] = $proArr[$key - 1];
      } else {
        $result['score_next'] = $proArr[$key + 1];
        $result['score_last'] = 0;
      }
    }
    unset ($proArr);
    return $result;
  }
}

/* End of file Api_broker_level_base_model.php */
/* Location: ./applications/models/Api_broker_level_base_model.php */
