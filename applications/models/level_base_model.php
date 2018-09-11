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
class level_base_model extends MY_Model
{
  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('level_record_base_model');
  }

  /**
   * 获取经纪人等级分值
   * @param int $broker_id 经纪人编号
   * @return int 经纪人等级分值值
   */
  public function get_level_by_broker_id($broker_id)
  {
    $this->load->model('broker_info_base_model');
    return $this->broker_info_base_model->get_level_by_broker_id($broker_id);
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
  function increase($broker_id, $type, $infofrom, $score, $remark = '')
  {
    $arrRtnResult = array('status' => 0, 'msg' => '');
    //检查字段
    if ($broker_id == null || $score == null
      || $type == null || $infofrom == null
    ) {
      $arrRtnResult['status'] = 0;
      $arrRtnResult['msg'] = '参数不合法';
      $arrRtnResult['type'] = 1;
      return $arrRtnResult;
    }
    $this->load->model('broker_info_base_model');
    //添加等级分值
    $update_score = $this->broker_info_base_model->update_self_level_by_broker_id($broker_id, $score);
    if ($update_score) //受影响的行数
    {
      //添加记录
      $level = $this->get_level_by_broker_id($broker_id);
      $level_record_data = array(
        'broker_id' => $broker_id, 'type' => $type,
        'infofrom' => $infofrom, 'score' => $score,
        'level' => $level, 'remark' => $remark,
        'create_time' => time(),
      );
      $this->level_record_base_model->insert($level_record_data);
      //返回结果
      $arrRtnResult['status'] = 1;
      $arrRtnResult['score'] = $score;
      $arrRtnResult['msg'] = '操作成功';
    } else {
      //返回结果
      $arrRtnResult['status'] = 0;
      $arrRtnResult['msg'] = '操作失败,请重试';
      $arrRtnResult['type'] = 2;
    }
    return $arrRtnResult;
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
  function reduce($broker_id, $type, $infofrom, $score, $remark = null)
  {
    //返回结果数组
    $arrRtnResult = array('status' => 0, 'msg' => '');
    //检查字段
    if ($broker_id == null || $score == null
      || $type == null || $infofrom == null
    ) {
      $arrRtnResult['status'] = 0;
      $arrRtnResult['msg'] = '参数不合法';
      $arrRtnResult['type'] = 1;
      return $arrRtnResult;
    }
    //获取分值
    $score = '-' . abs($score);
    //查询有没有经纪人数据
    $this->load->model('broker_info_base_model');
    $level = $this->get_level_by_broker_id($broker_id);
    if ($level < abs($score)) {
      $score = '-' . abs($level);
      $arrRtnResult['type'] = 2;//表示等级分值不足，可扣取的最大的分值
    } else {
      $score = '-' . abs($score); //正常扣分值
    }
    //减少等级分值
    $update_score = $this->broker_info_base_model->update_self_level_by_broker_id($broker_id, $score);
    $level = $this->get_level_by_broker_id($broker_id);
    if ($update_score) //受影响的行数
    {
      //添加记录
      $level_record_data = array(
        'broker_id' => $broker_id, 'type' => $type,
        'type' => $type, 'infofrom' => $infofrom, 'score' => $score,
        'level' => $level, 'remark' => $remark, 'create_time' => time(),
      );
      $this->level_record_base_model->insert($level_record_data);
      //返回结果
      $arrRtnResult['status'] = 1;
      $arrRtnResult['msg'] = '操作成功';
      $arrRtnResult['score'] = $score;
      if ($arrRtnResult['type'] != 2) {
        $arrRtnResult['type'] = 1;
      }
    } else {
      $arrRtnResult['status'] = 0;
      $arrRtnResult['msg'] = '扣除等级分值失败,请重试';
      $arrRtnResult['type'] = 2;
    }
    return $arrRtnResult;
  }

  //获取所有经纪人信息
  public function get_broker_arr()
  {
    $data = array();
    $data['form_name'] = 'broker_info';
    $data['select'] = array('broker_id', 'group_id');
    $data['where'] = array('status' => 1);
    $broker_arr = $this->get_data($data, 'dbback_city');

    return $broker_arr;
  }

  //获取同步到外网的出售房源
  public function get_sell_house_outside()
  {
    $data = array();
    $data['form_name'] = 'sell_house';
    $data['select'] = array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids');
    $data['where'] = array('status' => 1, 'is_outside' => 1);
    $house_arr = $this->get_data($data, 'dbback_city');

    return $house_arr;
  }

  //获取同步到外网的出租房源
  public function get_rent_house_outside()
  {
    $data = array();
    $data['form_name'] = 'rent_house';
    $data['select'] = array('id', 'broker_id', 'is_outside', 'status', 'pic_tbl', 'pic_ids');
    $data['where'] = array('status' => 1, 'is_outside' => 1);
    $house_arr = $this->get_data($data, 'dbback_city');

    return $house_arr;
  }

  //获取合同信息
  public function get_cooperate()
  {
    $data = array();
    $data['form_name'] = 'cooperate_chushen';
    $data['select'] = array('c_id', 'status');
    $data['where'] = array('status' => 1);
    $cooperate_arr = $this->get_data($data, 'dbback_city');

    return $cooperate_arr;
  }
}

/* End of file Api_broker_level_base_model.php */
/* Location: ./applications/models/Api_broker_level_base_model.php */
