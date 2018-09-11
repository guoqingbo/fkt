<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * collections_model CLASS
 *
 * 采集模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date      2014-12-28
 * @author          angel_in_us
 */
class Collections_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();

    $this->rent_house_collect = 'rent_house_collect';
    $this->sell_house_collect = 'sell_house_collect';
    $this->agent_house_judge = 'agent_house_judge';
    $this->agent_reportlist = 'agent_reportlist';
    $this->city = 'city';

    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_collections_model_';
  }

  //根据城市缩写获取城市ID
  public function collect_city_byab($spell)
  {
    $where = array('spell' => $spell);
    $result = $this->get_data(array('form_name' => $this->city, 'where' => $where), 'dbback');
    return $result[0];
  }

  /**
   * 获取采集的二手房总数量
   * @date      2015/8/4
   * @author       fisher
   */
  function get_new_sell_num($database = 'dbback')
  {
    $count_num = 0;
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $mem_key = $this->_mem_key . 'new_sell_num';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $count_num = $cache['data'];
    } else {
      $time = time() - 86400;
      $where = array('createtime >=' => $time, 'city' => $city['id']);

      $sell_sum = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'select' => array('count(id) as num')), $database);
      $count_num = $sell_sum[0]['num'];

      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $count_num), 60);
    }

    return $count_num;
  }

  /**
   * 获取采集的租房总数量
   * @date      2015/8/4
   * @author       fisher
   */
  function get_new_rent_num($database = 'dbback_city')
  {
    $count_num = 0;

    $mem_key = $this->_mem_key . 'new_rent_num';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $count_num = $cache['data'];
    } else {
      $spell = $this->config->item('login_city');
      $city = $this->collect_city_byab($spell);
      $time = time() - 86400;
      $where = array('createtime >=' => $time, 'city' => $city['id']);

      $rent_sum = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'select' => array('count(id) as num')), $database);
      $count_num = $rent_sum[0]['num'];

      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $count_num), 60);
    }

    return $count_num;
  }

  /**
   * 获取采集的二手房总数量
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_sell_num($where = array(), $like = array(), $or_like = array(), $database = 'dbback')
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $sell_sum = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'like' => $like, 'or_like' => $or_like, 'select' => array('count(*) as num')), $database);
    return $sell_sum[0]['num'];
  }


  /**
   * 获取采集的租房总数量
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_rent_num($where = array(), $like = array(), $or_like = array(), $database = 'dbback')
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $rent_num = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'like' => $like, 'or_like' => $or_like, 'select' => array('count(*) as num')), $database);
    return $rent_num[0]['num'];
  }


  /**
   * 获取采集的二手房房源信息
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_house_sell($where = array(), $where_in = array(), $like = array(), $or_like = array(), $order_by = '', $offset = 0, $limit = 10, $database = 'dbback')
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'or_like' => $or_like, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 根据时间排序获取采集的二手房房源信息(边框底部滚动)
   * @date      2015-03-26
   * @author       angel_in_us
   */
  function get_house_sell_orderby($offset = 0, $limit = 3, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'order_by' => 'createtime', 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 获取最近三天的采集的二手房房源信息
   * @date      2015-03-25
   * @author       angel_in_us
   */
  function get_recent_hosue_num()
  {
    $this->dbselect('dbback_city');
    //出售房源
    $sell_sql = "SELECT COUNT(*) as sell_num FROM " . $this->sell_house_collect . " where `createtime` > " . strtotime('-3 day');
    $query = $this->db->query($sell_sql);
    $result_arr = $query->result();
    $sell_num = $result_arr[0]->sell_num;
    //出租房源
    $rent_sql = "SELECT COUNT(*) as rent_num FROM " . $this->rent_house_collect . " where `createtime` > " . strtotime('-3 day');
    $query = $this->db->query($rent_sql);
    $result_arr = $query->result();
    $rent_num = $result_arr[0]->rent_num;
    return $sell_num + $rent_num;
  }

  /**
   * 获取最近七天被浏览的房源数量
   * @date      2015-03-25
   * @author       angel_in_us
   */
  function get_recent_brower_hosue_num()
  {
    $this->dbselect('dbback_city');
    $sql = "SELECT COUNT(distinct house_id) as num FROM agent_house_judge where `createtime` > " . strtotime('-3 day');
    $query = $this->db->query($sql);
    $result_arr = $query->result();
    $num = $result_arr[0]->num;
    return $num;
  }

  /**
   * 获取采集的租房房源信息
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_house_rent($where = array(), $where_in = array(), $like = array(), $or_like = array(), $order_by = '', $offset = 0, $limit = 0, $database = 'dbback')
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $result = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'or_like' => $or_like, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }


  /**
   * 根据房源id来查询详细房源信息
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function get_housesell_byid($where = array(), $database = 'dbback')
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where), $database);
    return $result;
  }

  /**
   * 根据房源 house_id 来查询 agent_house_judge 表中 该房源被查看的次数
   * @date      2015-06-14
   * @author  angel_in_us
   */
  function get_readtimes_byid($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_house_judge, 'where' => $where), $database);
    return $result;
  }

  /**
   * 判断该号码是不是已经被举报过了
   * @date      2015-06-14
   * @author  angel_in_us
   */
  function check_reprot_tel($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_reportlist, 'where' => $where), $database);
    return $result;
  }


  /**
   * 根据房源id来查询详细房源信息
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function get_houserent_byid($where = array(), $database = 'dbback')
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $result = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where), $database);
    return $result;
  }

  /**
   * 根据经纪人编号broker_id 查询经纪人查看房源情况
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function get_agent_house($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_house_judge, 'where' => $where), $database);
    return $result;
  }


  /**
   * 把经纪人已查看的房源插入到  agent_house_judge 表中
   */
  public function add_agent_house($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->add_data($data, $database, $this->agent_house_judge);
    return $result;
  }


  /**
   * 根据房源house_id 和 经纪人broker_id 来查询 agent_house_judge 表中是否已存在
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function check_agent_house($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_house_judge, 'where' => $where), $database);
    return $result;
  }


  /**
   * 把被举报的中介信息插入到 agent_reportlist
   */
  public function agent_reportlist($data = array(), $database = 'db_city', $form_name = '')
  {
    $agent_reportlist = $this->add_data($data, $database, $this->agent_reportlist);
    return $agent_reportlist;
  }

  /**
   * 我的采集里，成功录入房源后，根据房源编号 house_id 、经纪人编号 broker_id 和 房源类型 tbl_name 改变数据表 agent_house_judge 中的 is_input 字段值为 1
   * @date      2015-04-02
   * @author       angel_in_us
   */
  function change_house_status_byid($house_id, $broker_id, $tbl_name, $database = 'db_city')
  {
    $where = array(
      'house_id' => $house_id,
      'broker_id' => $broker_id,
      'tbl_name' => $tbl_name
    );
    $data = array('is_input' => 1);
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->agent_house_judge);
    return $result;
  }

  /**
   * 采集管理-已查看列表，点击删除按钮后，根据房源编号 house_id 、经纪人编号 broker_id 和 房源类型 tbl_name 改变数据表 agent_house_judge 中的 is_del 字段值为 1
   * @date      2015-08-07
   * @author       angel_in_us
   */
  function change_del_status($where, $data)
  {
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->agent_house_judge);
    return $result;
  }


  /**
   * 采集管理，经纪人选择标记联系后，根据房源编号 house_id 、经纪人编号 broker_id 和 房源类型 tbl_name 改变数据表 agent_house_judge 中的 is_contact 字段值为 0
   * @date      2015-04-02
   * @author       angel_in_us
   */
  function update_contact_status($where = array(), $data = array(), $database = 'db_city')
  {
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->agent_house_judge);
    return $result;
  }

  /**
   * 获取已保存的搜索条件的条数
   * @date      2015-06-16
   * @author       lujun
   */
  function get_search_num($id, $type)
  {
    $where = "broker_id = " . $id . " and type = " . "'$type'";
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->from('my_search');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['num'];
  }

  /**
   * 保存搜索条件
   * @date      2015-06-16
   * @author       lujun
   */
  function save_search($param)
  {
    if ($this->db_city->insert('my_search', $param)) {
      return $this->db_city->insert_id();
    }
    return 0;
  }

  /**
   * 获取经纪人已保存的搜索条件
   * @date      2015-06-16
   * @author       lujun
   */
  function get_my_search($id, $type)
  {
    $where = "broker_id = " . $id . " and type = " . "'$type'";
    $this->dbback_city->select('*');
    $this->dbback_city->from('my_search');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->result_array();
    return $result;
  }

  /**
   * 根据ID获取搜索条件的内容
   * @date      2015-06-16
   * @author       lujun
   */
  function get_search_info_by_id($id)
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('my_search');
    $this->dbback_city->where('id', $id);
    $result = $this->dbback_city->get()->row_array();
    return $result;
  }

  /**
   * 删除保存的搜索条件
   * @date      2015-06-16
   * @author       lujun
   */
  function del_my_search_by_id($id)
  {
    $this->db_city->where('id', $id);
    $this->db_city->delete('my_search');
    $num = $this->db_city->affected_rows();
    return $num;
  }
}

/* End of file collections_model.php */
/* Location: ./application/mls/models/collections_model.php */
