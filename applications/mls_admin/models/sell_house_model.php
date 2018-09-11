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
 * sell_house_model CLASS
 *
 * 出售房源信息管理类,提供增加、修改、删除、查询 出售房源信息的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */

//加载父类文件
load_m('House_base_model');

class Sell_house_model extends House_base_model
{

  /**
   * 表名
   *
   * @access private
   * @var string
   */
  private $_sell_house_tbl = 'sell_house';


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    //初始化表名称
    $this->set_tbl($this->_sell_house_tbl);
    $this->district = 'district';
    $this->broker_info = 'broker_info';
  }


  /**
   *  根据小区id获得该小区的房源
   *
   * @access  public
   * @param   array $data_info 出售房源信息数组
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function get_hosue_by_blockid($block_id)
  {
    $cond_where = array('block_id' => $block_id);
    if (!empty($block_id)) {
      $this->set_search_fields(array('id', 'block_id', 'block_name', 'district_id', 'street_id'));
      $house = $this->get_list_by_cond($cond_where);
      return $house;
    }
  }

  /**
   *  根据房源id获得该小区的房源
   *
   * @access  public
   * @param   array $data_info 出售房源信息数组
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function get_house_by_id($house_id)
  {
    $cond_where = array('id' => $house_id);
    if (!empty($house_id)) {
      $this->set_search_fields(array('id', 'block_id', 'block_name', 'district_id', 'street_id', 'reward_type', 'cooperate_reward'));
      $house = $this->get_list_by_cond($cond_where);
      return $house;
    }
  }

  public function update_house($update_arr, $cond_where)
  {
    if (!empty($update_arr) && is_array($update_arr) && !empty($cond_where) && is_array($cond_where)) {
        $update_res = $this->update_info_by_cond($update_arr, $cond_where);
        return $update_res;
    }
  }

  //根据房源id更新楼盘名字
  public function update_house_where_in($update_arr, $cond_where)
  {
    if (!empty($update_arr) && is_array($update_arr) && !empty($cond_where) && is_array($cond_where)) {
      $tbl_name = $this->get_tbl();
      if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
        return FALSE;
      }

      foreach ($update_arr as $key => $value) {
        $this->db_city->set($key, $value, $escape);
      }

      //设置条件
      $this->db_city->where_in('id', $cond_where);

      //更新数据
      $this->db_city->update($tbl_name);

      return $this->db_city->affected_rows();
    }
  }

  public function community_info($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->district, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }

  public function change_agency_id_by_borker_id($broker_id, $agency_id)
  {
    $broker_id = intval($broker_id);
    $agency_id = intval($agency_id);
    if ($broker_id && $agency_id) {
      $data = array();
      $data['agency_id'] = $agency_id;
      $cond_where = "broker_id = '$broker_id'";
      $result = parent::update_info_by_cond($data, $cond_where);
    }
  }

  public function change_company_id_by_borker_id($broker_id, $company_id)
  {
    $broker_id = intval($broker_id);
    $company_id = intval($company_id);
    if ($broker_id && $company_id) {
      $data = array();
      $data['company_id'] = $company_id;
      $cond_where = "broker_id = '$broker_id'";
      $result = parent::update_info_by_cond($data, $cond_where);
    }
  }


  /**
   * 取消关联
   */
  public function del_sell($sell_id)
  {
    $data = array('status' => 5);
    $where = "id = " . $sell_id;
    $rs = $this->db_city->update('sell_house', $data, $where);

    return $rs;
  }

  /**
   * 下架
   */
  public function xiajia($sell_id)
  {
    $data = array('is_outside' => 0);
    $where = "id = " . $sell_id;
    $rs = $this->db_city->update('sell_house', $data, $where);

    return $rs;
  }

  /**
   * 获取符合条件的经纪人信息
   *
   * @param $cond_where   查询条件
   * @param int $offset 偏移量
   * @param int $limit 每页查询数据条数
   * @param string $order_key 排序字段
   * @param string $order_by 排序方式（升序、降序）
   * @return array
   */
  public function broker_info($cond_where, $offset = 0, $limit = 10,
                              $order_key = 'a.id', $order_by = 'ASC')
  {
    //客源需求信息表
    $this->dbback_city->select('a.*,b.*');
    $this->dbback_city->from('broker_info as a');
    $this->dbback_city->join('agency as b', 'a.agency_id = b.id');
    //查询条件
    $this->dbback_city->where($cond_where);

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    $this->dbback_city->limit($limit, $offset);

    //查询
    $arr_data = $this->dbback_city->get()->result_array();
    return $arr_data;
  }

  /**
   * 根据基本配置获取符合条件的房源
   *
   * @param $cond_where   查询条件
   * @param int $offset 偏移量
   * @param int $limit 每页查询数据条数
   * @param string $order_key 排序字段
   * @param string $order_by 排序方式（升序、降序）
   * @return array
   */
  public function get_id_by_basic_companyid_cmtid($cond_where, $order_key = 'a.id', $order_by = 'ASC')
  {
    //客源需求信息表
    $this->dbback_city->select('a.id,a.company_id,a.block_id,a.block_name');
    $this->dbback_city->from('sell_house as a');
    $this->dbback_city->join('basic_setting as b', 'a.company_id = b.company_id');
    //查询条件
    $this->dbback_city->where($cond_where);

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);

    //查询
    $arr_data = $this->dbback_city->get()->result_array();
    return $arr_data;
  }


  /**
   * 获取符合条件的信息列表
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表数组
   */
  public function get_list_by_cond($cond_where = '', $offset = 0, $limit = 10,
                                   $order_key = 'createtime', $order_by = 'DESC', $order_key2 = '', $order_by2 = '', $order_key3 = '', $order_by3 = '')
  {
    //合作信息表
    $tbl_name = $this->get_tbl($this->_sell_house_tbl);

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();

    if (isset($select_fields) && !empty($select_fields)) {
      //查询字段
      $select_fields_str = implode(',', $select_fields);
      $this->dbback_city->select($select_fields);
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);

    //查询
    $arr_data = $this->dbback_city->get($tbl_name, $limit, $offset)->result_array();
    return $arr_data;
  }

  /**
   * 获取符合条件的房源数量
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_sell_house_num_by_cond($cond_where)
  {
    $count_num = 0;

    //出售名称
    $tbl_name = $this->get_tbl($this->_sell_house_tbl);

    //查询条件
    if ($tbl_name != '') {
      if ($cond_where != '') {
        $this->dbback_city->where($cond_where);
      }

      $count_num = $this->dbback_city->count_all_results($tbl_name);
    }

    return intval($count_num);
  }

  /**
   * 获取符合经纪人信息
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息
   */
  public function broker($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->broker_info, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }


  /**
   * 根据合作 id 查询房源信息
   * @date      2015-01-26
   * @author       angel_in_us
   */
  function get_house_info_byids($where_in = array(), $offset = 0, $limit = 10, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => 'sell_house', 'where_in' => $where_in, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 插入房源图片
   * @param type $pics
   * @param type $house_id
   * @param type $block_id
   */
  public function insert_house_pic($pics, $tbl, $house_id, $block_id, $deleteupload = 0)
  {
    //调用pic_model
    $this->load->model('pic_model');
    $return = array();

    //先删除原上传的图片，再重新添加上传图片数据，这个不知道是谁设计的
    if ($deleteupload == 1) {
      $this->pic_model->del_house_pic_by($tbl, $house_id);
    }
    if (!$pics) {
      return $return;
    }
    $createtime = time();

    if ($pics['p_filename2']) {
      if (empty($pics['add_pic2'])) {
        $pics['add_pic2'] = $pics['p_filename2'][0];
      }
      foreach ($pics['p_filename2'] as $key => $val) {
        $is_top = 0;
        if ($pics['add_pic2'] == $val) {
          $is_top = 1;
        }

        //房源图片的参数
        $insert_data_house = array(
          'tbl' => $tbl,
          'type' => '1',
          'rowid' => $house_id,
          'url' => $val,
          'block_id' => $block_id,
          'createtime' => $createtime,
          'is_top' => $is_top
        );
        $picid = $this->pic_model->insert_house_pic($insert_data_house);
      }
    }
    if ($pics['p_filename1']) {
      if (empty($pics['add_pic1'])) {
        $pics['add_pic1'] = $pics['p_filename1'][0];
      }
      foreach ($pics['p_filename1'] as $key => $val) {
        $is_top = 0;
        if ($pics['add_pic1'] == $val) {
          $is_top = 1;
        }

        //房源图片的参数
        $insert_data_house = array(
          'tbl' => $tbl,
          'type' => '2',
          'rowid' => $house_id,
          'url' => $val,
          'block_id' => $block_id,
          'createtime' => $createtime,
          'is_top' => $is_top
        );
        $picid = $this->pic_model->insert_house_pic($insert_data_house);
      }
    }
    return true;
  }

  /**
   * 根据条件获取跟进信息
   */
  public function get_follows_by_cond($condition)
  {

    $this->dbback_city->select('a.date,c.follow_name, a.text, b.owner, b.broker_name');
    $this->dbback_city->from('detailed_follow as a');
    $this->dbback_city->join('sell_house as b', 'a.house_id = b.id');
    $this->dbback_city->join('follow_up as c', 'a.follow_way = c.id');
    $this->dbback_city->where($condition);

    $rows = $this->dbback_city->get()->result_array();
    //echo $this->dbback_city->last_query();
    return $rows;
  }

  /**
   * 获取符合条件的房源需求信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_housenum_by_cond($cond_where)
  {
    $num = 0;
    $num = parent::get_count_by_cond($cond_where);
    return $num;
  }

  /**
   * 添加出售信息
   *
   * @access  public
   * @param   array $data_info 出售房源信息数组
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function add_sell_house_info($data_info)
  {
    $result = parent::add_info($data_info);
    return $result;
  }

    /**
     * 有效房源统计
     * @auth 郭庆波
     * @access  public
     * @param   broker_id 经纪人id
     * @return  int 返回经纪人有效房源数
     */
    public function stat_effective_house($broker_id)
    {
        $effective_house_num = 0;
        //客源需求信息表
        $this->dbback_city->select('id,broker_id,broker_name');
        $this->dbback_city->from('sell_house');
        //查询条件
        $cond_where = 'status = 1 and pic is not null and pic_tbl is not null and pic_ids is not null and broker_id = ' . $broker_id;
        $this->dbback_city->where($cond_where);
        //查询
        $houseList = $this->dbback_city->get()->result_array();
        if (!empty($houseList)) {
            foreach ($houseList as $key => $value) {
                //客源需求信息表
                $this->dbback_city->select('id,tbl,type,rowid');
                $this->dbback_city->from('upload');
                //查询条件
                $cond_where = 'type in (1,2) and tbl = "sell_house" and url is not null and rowid = ' . $value['id'];
                $this->dbback_city->where($cond_where);
                //查询
                $imgList = $this->dbback_city->get()->result_array();
                if (!empty($imgList)) {
                    //检查是否有三张室内图，一张户型图
                    $romImg = 0;
                    $outImg = 0;
                    foreach ($imgList as $k => $v) {
                        if ($v['type'] == 1) {
                            $romImg++;
                        }
                        if ($v['type'] == 2) {
                            $outImg++;
                        }
                    }
                    if ($outImg > 0 && $romImg > 2) {
                        $effective_house_num++;
                    }
                }
            }
        }
        return $effective_house_num;
    }

    /**
     * 经纪人房源总数量
     *
     * @access  public
     * @param  string $broker_id 经纪人id
     * @return  int   符合条件的信息条数
     */
    public function get_tatal_house_num($broker_id)
    {
        $count_num = 0;
        $cond_where = 'broker_id = ' . $broker_id;
        $this->dbback_city->where($cond_where);
        $count_num = $this->dbback_city->count_all_results('sell_house');
        return intval($count_num);
    }

}

/* End of file sell_house_model.php */
/* Location: ./applications/mls/models/sell_house_model.php */
