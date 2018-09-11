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
 * Project_cooperate_lol_base_model CLASS
 *
 * 天下英雄共联盟
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Project_cooperate_lol_base_model extends MY_Model
{

  //合作生效表
  private $_tbl_coopeate_effect = 'project_cooperate_lol_effect';

  //交易成功表
  private $_tbl_coopeate_success = 'project_cooperate_lol_success';

  //交易成功数据表
  private $_tbl_coopeate_success_apply = 'project_cooperate_lol_sucess_applay';

  //交易成功次数表
  private $_tbl_coopeate_lucky_once = 'project_cooperate_lol_lucky_once';

  //奖品表
  private $_tbl_coopeate_reward = 'project_cooperate_lol_reward';

  //经纪人注册表
  private $_tbl_broker = 'broker';

  //城市表
  private $_tbl_city = 'city';

  //中奖名单
  private $_tbl_coopeate_win_list = 'project_cooperate_lol_win_list';

  //中奖名单
  private $_tbl_coopeate_reserve_win = 'project_cooperate_lol_reserve_win';

  //奖品内容
  private $reward = array(
    array('id' => 1, 'name' => '富光保温杯', 'writer' => '叮，拥有“温度保持”附魔效果的魔法杯突然出现在你的面前', 'num' => '1,6,8'),
    array('id' => 2, 'name' => 'IPhone6S', 'writer' => '牛顿被一个苹果砸中之后发现了万有引力，而今天你被一个咬过一口的苹果砸中了', 'num' => '2'),
    array('id' => 3, 'name' => '九阳豆浆机', 'writer' => '大师球捕获了一只建国后成精的电器妖，此妖怪可将天下所有的豆子打磨成浆', 'num' => '3,10'),
    array('id' => 4, 'name' => '格兰仕电烤箱', 'writer' => '一只猴子路过你家只是，随手扔下了一个破碎的“老君炼丹炉”，此“炼丹炉”虽然炼不了火眼金睛，但可以烤烤面包', 'num' => '4,9'),
    array('id' => 5, 'name' => '瑞士军刀双肩包', 'writer' => '恭喜你获得一个“如意乾坤袋”，专做储物之用，拥有不可思议之力', 'num' => '5,7'),
  );

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 合作发布方或申请方首次合作生效,
   * 且合作房源为出售或合作客源为求购
   * @param array $cooperate_info 合作内容
   * @param int $operate_broker_id
   */
  public function add_cooperate_effect($cooperate_info, $operate_broker_id, $city_id)
  {
    $check_time = $this->is_active_intime();
    //且合作房源为出售或合作客源为求购
    if (is_full_array($cooperate_info) && $cooperate_info['tbl'] == 'sell'
      && ($cooperate_info['agentid_a'] != $cooperate_info['agentid_b'])
      && $check_time
    ) {
      //分别查找甲乙双方有没有合作生效过的房源
      $broker_ids = array($cooperate_info['brokerid_a'], $cooperate_info['brokerid_b']);
      $this->load->model('broker_info_model');
      $this->load->model('agency_model');
      foreach ($broker_ids as $v) {
        if ($operate_broker_id == $v) {
          $cond_where = 'broker_id = ' . $v . ' and broker_id = operate_broker_id';
        } else {
          $cond_where = 'broker_id = ' . $v . ' and broker_id != operate_broker_id';
        }
        $num = $this->get_cooperate_effect_by($cond_where);
        if ($num == 0) {
          $broker_info = $this->broker_info_model->get_by_broker_id($v);
          $agency = $this->agency_model->get_by_id($broker_info['agency_id']);
          $company = $this->agency_model->get_by_id($agency['company_id']);
          $insert_data = array(
            'c_id' => $cooperate_info['id'], 'broker_id' => $v,
            'broker_name' => $broker_info['truename'],
            'phone' => $broker_info['phone'],
            'operate_broker_id' => $operate_broker_id,
            'create_time' => time(), 'city_id' => $city_id,
            'agency_name' => $agency['name'], 'company_name' => $company['name'],
            'order_sn' => $cooperate_info['order_sn'],
          );
          $this->db->insert($this->_tbl_coopeate_effect, $insert_data);
        }
      }
    }
  }

  /**
   * 取出多长时间内的首次合作生效的经纪人
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_effect_num($where = '')
  {
    if ($where) {
      //查询条件
      $this->db->where($where);
    }
    return $this->db->count_all_results($this->_tbl_coopeate_effect);
  }

  /**
   * 取出多长时间内的首次合作生效的经纪人
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_effect_list($where, $start = -1, $limit = 20,
                                            $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->db->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->db->where($where);
    }
    //排序条件
    $this->db->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->db->limit($limit, $start);
    }
    //返回结果
    return $this->db->get($this->_tbl_coopeate_effect)->result_array();
  }

  /**
   * 取出多长时间内的首次合作生效的经纪人
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_effect_by($where)
  {
    //查询条件
    $this->db->where($where);
    return $this->db->count_all_results($this->_tbl_coopeate_effect);
  }

  /**
   * 取出多长时间内的首次合作生效的经纪人
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_effect($in_time = 259200)
  {
    $end_time = time();
    $start_time = $end_time - $in_time;
    $where = 'create_time >=' . $start_time . ' and create_time <=' . $end_time;
    //查询条件
    $this->db->where($where);
    return $this->db->get($this->_tbl_coopeate_effect)->result_array();
  }

  /**
   * 取出多长时间内的首次合作生效的经纪人
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_effect_by_id($s_id, $broker_id)
  {
    $where = 'id = ' . $s_id;
    //查询条件
    $this->db->where($where);
    return $this->db->get($this->_tbl_coopeate_effect)->row_array();
  }

  /**
   * 交易成功
   * @param array $cooperate_info 合作内容
   * @param int $operate_broker_id
   */
  public function add_cooperate_success($cooperate_info, $operate_broker_id, $city_id)
  {
    if (is_full_array($cooperate_info) && $cooperate_info['tbl'] == 'sell') {
      $broker_ids = array($cooperate_info['brokerid_a'], $cooperate_info['brokerid_b']);
      $this->load->model('broker_info_model');
      $this->load->model('agency_model');
      foreach ($broker_ids as $v) {
        $broker_info = $this->broker_info_model->get_by_broker_id($v);
        $agency = $this->agency_model->get_by_id($broker_info['agency_id']);
        $company = $this->agency_model->get_by_id($agency['company_id']);
        $insert_data = array(
          'c_id' => $cooperate_info['id'],
          'order_sn' => $cooperate_info['order_sn'],
          'broker_id' => $v, 'is_applay' => 0,
          'operate_broker_id' => $operate_broker_id,
          'create_time' => time(), 'city_id' => $city_id,
          'broker_name' => $broker_info['truename'],
          'agency_name' => $agency['name'], 'company_name' => $company['name'],
          'phone' => $broker_info['phone'], 'agency_type' => $agency['agency_type'],
        );
        $this->db->insert($this->_tbl_coopeate_success, $insert_data);
      }
    }
  }

  /**
   * 获取用户可以初审的合同编号
   * return array 可以出身的合同数据
   */

  public function get_cooperate_success_list($broker_id)
  {
    $this->db->where('broker_id', $broker_id);
    $this->db->where('is_applay', 0);
    return $this->db->get($this->_tbl_coopeate_success)->result_array();
  }

  /**
   * 通过合同编号查找交易成功的双方经纪人
   * @param type $c_id
   */
  public function get_cooperate_success_by_cid($c_id, $city_id)
  {
    $this->db->where('c_id', $c_id);
    $this->db->where('city_id', $city_id);
    return $this->db->get($this->_tbl_coopeate_success)->result_array();
  }

  /**
   * 通过合同编号查找交易成功的双方经纪人
   * @param type $c_id
   */
  public function get_cooperate_success_by_city($city_id)
  {
    $this->db->where('city_id', $city_id);
    $this->db->where('is_applay', 1);
    $this->db->distinct('c_id');
    return $this->db->get($this->_tbl_coopeate_success)->result_array();
  }

  /**
   * 通过合同编号查找交易成功的双方经纪人
   * @param type $c_id
   */
  public function get_cooperate_success_by_id($s_id, $broker_id)
  {
    $this->db->where('id', $s_id);
    $this->db->where('broker_id', $broker_id);
    $this->db->where('is_applay', 0);
    return $this->db->get($this->_tbl_coopeate_success)->row_array();
  }

  public function get_cooperate_success_id($s_id)
  {
    $this->db->where('id', $s_id);
    return $this->db->get($this->_tbl_coopeate_success)->row_array();
  }

  /**
   * 更新合作成功是否可以申请
   * @param int $c_id 合作编号
   * @param int $applay 是否可以申请
   */
  public function update_cooperate_success_applay($c_id, $applay, $city_id)
  {
    $update_status = array('is_applay' => $applay);
    $this->db->where('c_id', $c_id);
    $this->db->where('city_id', $city_id);
    $this->db->update($this->_tbl_coopeate_success, $update_status);
  }

  /**
   * 提交交易成功申请数据
   * @param array $cooperate_info 合作内容
   * @param int $operate_broker_id
   */
  public function add_cooperate_success_applay($insert_data = array())
  {
    //插入提交交易成功申请数据
    $this->db->insert($this->_tbl_coopeate_success_apply, $insert_data);
    //更新交易否可以申请
    $success_data = $this->get_cooperate_success_id($insert_data['s_id']);
    $this->update_cooperate_success_applay($insert_data['c_id'], 1, $success_data['city_id']);
  }

  /**
   * 后台审核提交上来的合作信息
   * @param int $id
   * @param int $c_id
   * @param int $status
   */
  public function update_cooperate_success_applay_status($id, $c_id, $status)
  {
    $update_status = array('status' => $status);
    $this->db->where('id', $id);
    $this->db->update($this->_tbl_coopeate_success_apply, $update_status);
    $success_applay = $this->get_cooperate_success_applay_by_id($id);
    $success_data = $this->get_cooperate_success_id($success_applay['s_id']);
    if ($status == 1) //审核通过
    {
      //为经纪人增加抽奖机会
      $this->add_broker_lucky_once($c_id, $success_data['city_id']);
    } else if ($status == 2) //驳回
    {
      //更新交易否可以申请，可以再次审核
      $this->update_cooperate_success_applay($c_id, 0, $success_data['city_id']);
    }
  }

  /**
   * 增加经纪人抽奖机会
   * @param int $c_id
   */
  public function add_broker_lucky_once($c_id, $city_id)
  {
    //查找交易成功记录
    $brokers = $this->get_cooperate_success_by_cid($c_id, $city_id);
    foreach ($brokers as $broker) {
      //查找经纪人是否有记录
      $this->db->where('broker_id', $broker['broker_id']);
      $num = $this->db->count_all_results($this->_tbl_coopeate_lucky_once);
      if ($num == 0) {
        $insert_data = array('broker_id' => $broker['broker_id'], 'num' => 1);
        $this->db->insert($this->_tbl_coopeate_lucky_once, $insert_data);
      } else {
        $once = 1;
        $this->db->set('num', "num + " . $once, false);
        $this->db->where('broker_id', $broker['broker_id']);
        $this->db->update($this->_tbl_coopeate_lucky_once);
      }
    }
  }

  /**
   * 减少经纪人抽奖机会
   * @param int $c_id
   */
  public function reduce_broker_lucky_once($broker_id)
  {
    $once = 1;
    $this->db->set('num', "num - " . $once, false);
    $this->db->where('broker_id', $broker_id);
    $this->db->where('num > ', 0);
    $this->db->limit(1);
    $this->db->update($this->_tbl_coopeate_lucky_once);
    return $this->db->affected_rows();
  }

  /**
   * 获取申请资料的数据
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_success_applay_num($where = '')
  {
    if ($where) {
      //查询条件
      $this->db->where($where);
    }
    return $this->db->count_all_results($this->_tbl_coopeate_success_apply);
  }

  /**
   * 取出多长时间内的首次合作生效的经纪人
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_success_applay_list($where, $start = -1, $limit = 20,
                                                    $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->db->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->db->where($where);
    }
    //排序条件
    $this->db->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->db->limit($limit, $start);
    }
    //返回结果
    return $this->db->get($this->_tbl_coopeate_success_apply)->result_array();
  }

  public function get_cooperate_success_applay_by_id($id)
  {
    $this->db->where('id', $id);
    return $this->db->get($this->_tbl_coopeate_success_apply)->row_array();
  }

  public function get_cooperate_success_applay_by_sid($id)
  {
    $this->db->where('s_id', $id);
    return $this->db->get($this->_tbl_coopeate_success_apply)->row_array();
  }

  /**
   * 获取奖品总数
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_reward_num($where = '')
  {
    if ($where) {
      //查询条件
      $this->db->where($where);
    }
    return $this->db->count_all_results($this->_tbl_coopeate_reward);
  }

  /**
   * 取出奖品列表
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_reward_list($where, $start = -1, $limit = 20,
                                            $order_key = 'open_time', $order_by = 'asc')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->db->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->db->where($where);
    }
    //排序条件
    $this->db->order_by('open_time asc, id asc');
    if ($start >= 0 && $limit > 0) {
      $this->db->limit($limit, $start);
    }
    //返回结果
    return $this->db->get($this->_tbl_coopeate_reward)->result_array();
  }

  /**
   * 获取奖品详情
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_reward_by_id($id)
  {
    $this->db->where('id', $id);
    return $this->db->get($this->_tbl_coopeate_reward)->row_array();
  }

  /**
   * 提交奖品数据
   * @param array $cooperate_info 合作内容
   * @param int $operate_broker_id
   */
  public function add_cooperate_reward($insert_data = array())
  {
    //插入提交交易成功申请数据
    return $this->db->insert($this->_tbl_coopeate_reward, $insert_data);

  }

  /**
   * 获取有效的奖品
   * @param type $where
   */
  public function get_cooperate_reward_by($where)
  {
    $this->db->where($where);
    $this->db->order_by('open_time asc, id asc');
    $this->db->limit(1);
    return $this->db->get($this->_tbl_coopeate_reward)->row_array();
  }

  /**
   * 更新奖品数据
   * @param int $c_id 合作编号
   * @param int $applay 是否可以申请
   */
  public function update_cooperate_reward($id, $update_status)
  {
    $this->db->where('id', $id);
    $this->db->where('status', 0);
    $this->db->limit(1);
    $this->db->update($this->_tbl_coopeate_reward, $update_status);
    return $this->db->affected_rows();
  }

  /**
   * 删除奖品数据
   * @param int $c_id 合作编号
   * @param int $applay 是否可以申请
   */
  public function delete_cooperate_reward($id)
  {
    $this->db->where('id', $id);
    return $this->db->update($this->_tbl_coopeate_reward, array('valid_flag' => 0));
    //return $this->db->delete($this->_tbl_coopeate_reward);
  }

  /**
   * 获取奖品类型
   * @param int $c_id 合作编号
   * @param int $applay 是否可以申请
   */
  public function get_cooperate_reward_type()
  {
    return $this->reward;
  }

  /**
   * 取出获奖名单
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_win_list($where, $start = -1, $limit = 20,
                                         $order_key = 'create_time', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->db->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->db->where($where);
    }
    //排序条件
    $this->db->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->db->limit($limit, $start);
    }
    //返回结果
    return $this->db->get($this->_tbl_coopeate_win_list)->result_array();
  }

  public function add_cooperate_win($insert_data)
  {
    $this->db->insert($this->_tbl_coopeate_win_list, $insert_data);
  }

  //获取随机中奖的id
  public function rand_reward_id($num)
  {
    $arr_num = explode(',', $num);
    $count_num = count($arr_num);
    $rand_order = rand(0, $count_num - 1);
    return $arr_num[$rand_order];
  }

  //判断是否在活动期内
  public function is_active_intime()
  {
    $start_time = strtotime('2015-12-19 00:00:00');
    $end_time = strtotime('2016-02-01 14:00:00');
    $current_time = time();
    return ($current_time >= $start_time && $current_time <= $end_time) ? true : false;
  }

  //判断抽奖是否在活动期内
  public function is_active_intime_lottery()
  {
    $start_time = strtotime('2015-12-19 00:00:00');
    $end_time = strtotime('2016-02-01 14:00:00');
    $current_time = time();
    return ($current_time >= $start_time && $current_time <= $end_time) ? true : false;
  }

  //通过broker_id获得所有的合同编号
  public function get_all_ordersn_by_broker_id($broker_id, $c_id = "")
  {
    $this->db->select('id, order_sn');
    //返回结果
    if ($c_id) {
      $this->db->where('c_id', $c_id);
    }
    $this->db->where('broker_id', $broker_id);
    $this->db->where('is_applay', 0);
    return $this->db->get($this->_tbl_coopeate_success)->result_array();
  }

  //通过经纪人broker_id获取所在城市信息
  public function get_city_by_broker_id($broker_id)
  {
    $this->db->select("{$this->_tbl_broker}.city_id,{$this->_tbl_city}.cityname");

    $this->db->where("{$this->_tbl_broker}.id", $broker_id);
    $this->db->from($this->_tbl_broker);
    $this->db->join($this->_tbl_city, "{$this->_tbl_broker}.city_id = {$this->_tbl_city}.id");
    //返回结果
    return $this->db->get()->row_array();

  }

  /**
   * 获取中奖名单总数
   * @param int $in_time 多长时间内
   */
  public function get_cooperate_win_num($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback->where($where);
    }
    return $this->dbback->count_all_results($this->_tbl_coopeate_win_list);
  }


  /**
   * 获取有效的奖品
   * @param type $where
   */
  public function get_reserve_win_by($where)
  {
    $this->db->where($where);
    $this->db->order_by('id asc');
    $this->db->limit(1);
    return $this->db->get($this->_tbl_coopeate_reserve_win)->row_array();
  }

  /**
   * 更新奖品数据
   * @param int $c_id 合作编号
   * @param int $applay 是否可以申请
   */
  public function update_reserve_win($id, $update_status)
  {
    $this->db->where('id', $id);
    $this->db->where('status', 0);
    $this->db->limit(1);
    $this->db->update($this->_tbl_coopeate_reserve_win, $update_status);
    return $this->db->affected_rows();
  }

  /**
   * 获取申请状态
   * @param int $city_id 城市id
   * @param int $order_sn 合同编号
   * @param int $broker_id 经纪人编号
   */
  public function get_cooperate_lol_apply($c_id, $broker_id, $city_id)
  {
    $this->dbback->where('c_id', $c_id);
    $this->dbback->where('broker_id', $broker_id);
    $this->dbback->where('city_id', $city_id);
    $this->dbback->select('id,is_applay');
    return $this->dbback->get($this->_tbl_coopeate_success)->row_array();
  }

  public function get_cooperate_lol_status($s_id)
  {
    $this->dbback->where('s_id', $s_id);
    $this->dbback->select('status');
    $this->dbback->order_by('create_time', 'desc');
    $this->dbback->limit(1);
    return $this->dbback->get($this->_tbl_coopeate_success_apply)->row_array();
  }
}

/* End of file Project_cooperate_lol_base.php */
/* Location: ./applications/models/Project_cooperate_lol_base.php */
