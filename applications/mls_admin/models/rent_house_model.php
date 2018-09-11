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
 * rent_house_model CLASS
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

class Rent_house_model extends House_base_model
{

  /**
   * 表名
   *
   * @access private
   * @var string
   */
  private $_rent_house_tbl = 'rent_house';


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    //初始化表名称
    $this->set_tbl($this->_rent_house_tbl);
    $this->district = 'district';
    $this->broker_info = 'broker_info';
    $this->tmp_uploads = 'tmp_uploads';
    $this->community = 'community';
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

  public function update_house($update_arr, $cond_where)
  {
    if (!empty($update_arr) && is_array($update_arr) && !empty($cond_where) && is_array($cond_where)) {
      return $this->update_info_by_cond($update_arr, $cond_where);
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
    $rs = $this->db_city->update('rent_house', $data, $where);

    return $rs;
  }

  /**
   * 下架
   */
  public function xiajia($sell_id)
  {
    $data = array('is_outside' => 0);
    $where = "id = " . $sell_id;
    $rs = $this->db_city->update('rent_house', $data, $where);

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
    $this->dbback_city->from('rent_house as a');
    $this->dbback_city->join('basic_setting as b', 'a.company_id = b.company_id');
    //查询条件
    $this->dbback_city->where($cond_where);

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);

    //查询
    $arr_data = $this->dbback_city->get()->result_array();
    return $arr_data;
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
  public function get_list_by_cond($cond_where, $offset = 0, $limit = 10,
                                   $order_key = 'createtime', $order_by = 'DESC')
  {
    //合作信息表
    $tbl_name = $this->get_tbl($this->_rent_house_tbl);

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
  public function get_rent_house_num_by_cond($cond_where)
  {
    $count_num = 0;

    //出售名称
    $tbl_name = $this->get_tbl($this->_rent_house_tbl);

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
    $result = $this->get_data(array('form_name' => 'rent_house', 'where_in' => $where_in, 'offset' => $offset, 'limit' => $limit), $database);
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
    $this->dbback_city->join('rent_house as b', 'a.house_id = b.id');
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
   * 获取临时表数据
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 临时表的多维数组
   */
  public function get_tmp($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->tmp_uploads, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }

  public function community_info_new($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->community, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }

  //出租房源验证数组

  public function checkarr($arr)
  {
    $data = array();
    $data_fail = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();
    //if(!empty($arr[0]) && !eregi("[^\x80-\xff]","$arr[0]")){ //楼盘名称不为空并且为中文
    if (!empty($arr[1]) && !eregi("^[\u4e00-\u9fa5a-zA-Z]+$", "$arr[1]")) { //楼盘名称不为空并且可以是中文、英文
      $where['cmt_name'] = $arr[1];
      $community_info = $this->community_info_new($where);
      if ($community_info[0]['id']) {
        $res[1] = true;
      } else {
        //判断是否楼盘需要加入临时小区
        $this->load->model('district_base_model');
        if (!empty($arr[18])) {  //区属不能空
          $dist_arr = $this->district_base_model->get_district_id($arr[18]);
          if (!empty($dist_arr)) {
            $res[18] = true;
          } else {
            $res[18] = false;
            $data_fail[] = 18;
          }
        } else {
          $res[18] = false;
          $data_fail[] = 18;
        }

        if (!empty($arr[19])) {  //板块不能空
          if (!empty($dist_arr)) {
            $streetname_arr = $this->district_base_model->get_streetname_bydist($dist_arr['id']);
            //print_r($streetname_arr);exit;
            if (in_array($arr[19], $streetname_arr)) {
              $res[19] = true;
            } else {
              $res[19] = false;
              $data_fail[] = 19;
            }
          } else {
            $street_arr = $this->district_base_model->get_street_id($arr[19]);
            //print_r( $street_arr);exit;
            if (!empty($street_arr)) {
              $res[19] = true;
            } else {
              $res[19] = false;
              $data_fail[] = 19;
            }
          }
        } else {
          $res[19] = false;
          $data_fail[] = 19;
        }

        if (!empty($arr[20])) {  //地址不能空
          $res[20] = true;
        } else {
          $res[20] = false;
          $data_fail[] = 20;
        }

        if (($res[18] == true) || ($res[19] == true) || ($res[20] == true)) {
          $res[1] = true;
        } else {
          $res[1] = false;
          $data_fail[] = 1;
        }
      }
    } else {
      $res[1] = false;
      $data_fail[] = 1;
    }

    if (!empty($arr[15])) { //物业类型不能为空
      $sell_type = $data['config']['sell_type'];
      if (in_array($arr[15], $sell_type)) {
        $res[15] = true;
      } else {
        $res[15] = false;
        $data_fail[] = 15;
      }
    } else {
      $res[15] = false;
      $data_fail[] = 15;
    }
    //if(!empty($arr[22]) && eregi("^[0-9-]+$",$arr[22])){  //门牌不为空并且为数字
    if (!empty($arr[22])) {  //门牌不为空并且为数字
      $res[22] = true;
    } else {
      $res[22] = false;
      $data_fail[] = 22;
    }
    if (!empty($arr[23])) { //业主姓名不为空并且为中文
      $res[23] = true;
    } else {
      $res[23] = false;
      $data_fail[] = 23;
    }
    if (!empty($arr[24])) { //业主电话不为空
      $tel = explode("/", $arr[24]);
      if (count($tel) < 4) {
        $isMob = "/^1[3-5,8]{1}[0-9]{9}$/";
        $isTel = "/^([0-9]{3,4})?[0-9]{7,8}$/";
        $isTel1 = "/^[0-9]{6,8}$/";
        foreach ($tel as $vo => $v) {
          if (preg_match($isMob, $v) || preg_match($isTel, $v) || preg_match($isTel1, $v)) {
            $res[24] = true;
          } else {
            $res[24] = false;
            $data_fail[] = 24;
          }
        }
      } else {
        $res[24] = false;
        $data_fail[] = 24;
      }
    } else {
      $res[24] = false;
      $data_fail[] = 24;
    }
    if (!empty($arr[8])) { //性质不能为空
      $nature = $data['config']['nature'];
      if (in_array($arr[8], $nature)) {
        $res[8] = true;
      } else {
        $res[8] = false;
        $data_fail[] = 8;
      }
    } else {
      $res[8] = false;
      $data_fail[] = 8;
    }
    if (!empty($arr[9])) { //状态不能为空
      $nature = array('有效', '预定', '成交', '无效', '注销', '暂不售（租）');
      if (in_array($arr[9], $nature)) {
        $res[9] = true;
      } else {
        $res[9] = false;
        $data_fail[] = 9;
      }
    } else {
      $res[9] = false;
      $data_fail[] = 9;
    }

    if (in_array($arr[15], array('厂房', '仓库', '车库'))) {
      //$res[5] = true;
      $res[11] = true;
      $res[14] = true;
    } else {
      /*if(!empty($arr[5])){ //户型不能为空
                $m = explode("/", $arr[5]);
                if(count($m) == 3){
                    foreach($m as $key=>$k){
                        if(eregi("^[0-9]+$",$k)){
                            $res[5] = true;
                        }else{
                            $res[5] = false;
                            $data_fail[] = 5;
                        }
                    }
                }else{
                    $res[5] = false;
                    $data_fail[] = 5;
                }
            }else{
                $res[5] = FALSE;
                $data_fail[] = 5;
            }*/
      if (!empty($arr[11])) { //楼层不能为空
        $m = explode("/", $arr[11]);
        if (count($m) == 2) {
          foreach ($m as $key => $k) {
            if (eregi("^[0-9-]+$", $k)) { //(-[0-9]+)?
              $res[11] = true;
            } else {
              $res[11] = false;
              //$data_fail[] = 11;
            }
          }
        } else {
          $res[11] = FALSE;
          $data_fail[] = 11;
        }
      } else {
        $res[11] = FALSE;
        $data_fail[] = 11;
      }
      if (!empty($arr[14])) { //装修不能为空
        $fitment = $data['config']['fitment'];
        if (in_array($arr[14], $fitment)) {
          $res[14] = true;
        } else {
          $res[14] = false;
          $data_fail[] = 14;
        }
      } else {
        $res[14] = false;
        $data_fail[] = 14;
      }
    }
    if (!empty($arr[4]) && is_numeric($arr[4])) { //面积不能为空
      $res[4] = true;
    } else {
      $res[4] = false;
      $data_fail[] = 4;
    }
    if (!empty($arr[2]) && is_numeric($arr[2])) { //售价不能为空
      $res[2] = true;
    } else {
      $res[2] = false;
      $data_fail[] = 2;
    }

    if (!empty($arr[30])) { //委托类型不能为空
      $entrust = $data['config']['entrust'];
      if (in_array($arr[30], $entrust)) {
        $res[30] = true;
      } else {
        $res[30] = false;
        $data_fail[] = 30;
      }
    } else {
      $res[30] = false;
      $data_fail[] = 30;
    }

    if (($res[18] == true) || ($res[19] == true) || ($res[20] == true)) {
      if (($res[1] == true) && ($res[15] == true) && ($res[22] == TRUE)
        && ($res[23] == TRUE) && ($res[24] == TRUE) && ($res[8] == TRUE)
        //&& ($res[5] == TRUE)
        && ($res[11] == TRUE) && ($res[14] == TRUE)
        && ($res[4] == TRUE) && ($res[2] == TRUE) && ($res[30] == TRUE)
        && ($res[18] == true) && ($res[19] == true) && ($res[20] == true)
        && ($res[9] == true)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    } else {
      if (($res[1] == true) && ($res[15] == true) && ($res[22] == TRUE)
        && ($res[23] == TRUE) && ($res[24] == TRUE) && ($res[8] == TRUE)
        //&& ($res[5] == TRUE)
        && ($res[11] == TRUE) && ($res[14] == TRUE)
        && ($res[4] == TRUE) && ($res[2] == TRUE) && ($res[30] == TRUE)
        && ($res[9] == true)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    }

  }


  //出租房源验证数组

  public function checkarr_taizhou($arr)
  {
    $data = array();
    $data_fail = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();
    //if(!empty($arr[0]) && !eregi("[^\x80-\xff]","$arr[0]")){ //楼盘名称不为空并且为中文
    if (!empty($arr[0]) && !eregi("^[\u4e00-\u9fa5a-zA-Z]+$", "$arr[0]")) { //楼盘名称不为空并且可以是中文、英文
      $where['cmt_name'] = $arr[0];
      $community_info = $this->community_info_new($where);
      if ($community_info[0]['id']) {
        $res[0] = true;
        //判断此楼盘有无板块没有的话添加上去
        if (!$community_info[0]['streetid'] && $arr[23]) {
          $this->load->model('district_base_model');
          $this->load->model('community_model');
          $street_arr = $this->district_base_model->get_street_id($arr[23]);
          if (!empty($street_arr)) {
            $modify_result = $this->community_model->modifycommunity($community_info[0]['id'], array('streetid' => $street_arr['id']));//楼盘数据入库
          }
        }
      } else {
        //判断是否楼盘需要加入临时小区
        $this->load->model('district_base_model');
        if (!empty($arr[22])) {  //区属不能空
          $dist_arr = $this->district_base_model->get_district_id($arr[22]);
          if (!empty($dist_arr)) {
            $res[22] = true;
          } else {
            $res[22] = false;
            $data_fail[] = 22;
          }
        } else {
          $res[22] = false;
          $data_fail[] = 22;
        }

        if (!empty($arr[23])) {  //板块不能空
          if (!empty($dist_arr)) {
            $streetname_arr = $this->district_base_model->get_streetname_bydist($dist_arr['id']);
            //print_r($streetname_arr);exit;
            if (in_array($arr[23], $streetname_arr)) {
              $res[23] = true;
            } else {
              $res[23] = false;
              $data_fail[] = 23;
            }
          } else {
            $street_arr = $this->district_base_model->get_street_id($arr[23]);
            //print_r( $street_arr);exit;
            if (!empty($street_arr)) {
              $res[23] = true;
            } else {
              $res[23] = false;
              $data_fail[] = 23;
            }
          }
        } else {
          $res[23] = false;
          $data_fail[] = 23;
        }

        if (!empty($arr[24])) {  //地址不能空
          $res[24] = true;
        } else {
          $res[24] = false;
          $data_fail[] = 24;
        }

        if (($res[22] == true) || ($res[23] == true) || ($res[24] == true)) {
          $res[0] = true;
        } else {
          $res[0] = false;
          $data_fail[] = 0;
        }
      }
    } else {
      $res[0] = false;
      $data_fail[] = 1;
    }

    if (!empty($arr[1])) { //物业类型不能为空
      $sell_type = $data['config']['sell_type'];
      if (in_array($arr[1], $sell_type)) {
        $res[1] = true;
      } else {
        $res[1] = false;
        $data_fail[] = 1;
      }
    } else {
      $res[1] = false;
      $data_fail[] = 1;
    }
    /*if(!empty($arr[6])){ //业主电话不为空
       $tel = explode("/", $arr[6]);
       if(count($tel) < 4){
            $isMob="/^1[3-5,8]{1}[0-9]{9}$/";
            $isTel="/^([0-9]{3,4})?[0-9]{7,8}$/";
            $isTel1="/^[0-9]{6,8}$/";
            foreach($tel as $vo => $v){
                if(preg_match($isMob,$v) || preg_match($isTel,$v) || preg_match($isTel1,$v)){
                   $res[6] = true;
                }else{
                   $res[6] = false;
                   $data_fail[] = 6;
                }
            }
       }else{
            $res[6] = false;
            $data_fail[] = 6;
       }
    }else{
        $res[6] = false;
        $data_fail[] = 6;
    }*/
    /*if(!empty($arr[8])){ //性质不能为空
            $nature = $data['config']['nature'];
            if(in_array($arr[8],$nature)){
                $res[8] = true;
            }else{
                $res[8] = false;
                $data_fail[] = 8;
            }
        }else{
            $res[8] = false;
            $data_fail[] = 8;
        }*/
    if (!empty($arr[7])) { //状态不能为空
      $nature = array('有效', '预定', '成交', '无效', '注销', '暂不售（租）');
      if (in_array($arr[7], $nature)) {
        $res[7] = true;
      } else {
        $res[7] = false;
        $data_fail[] = 7;
      }
    } else {
      $res[7] = false;
      $data_fail[] = 7;
    }

    if (in_array($arr[1], array('厂房', '仓库', '车库'))) {
      //$res[5] = true;
      $res[11] = true;
      $res[13] = true;
    } else {
      /*if(!empty($arr[13])){ //装修不能为空
                $fitment = $data['config']['fitment'];
                if(in_array($arr[13],$fitment)){
                    $res[13] = true;
                }else{
                    $res[13] = false;
                    $data_fail[] = 13;
                }
            }else{
                $res[13] = false;
                $data_fail[] = 13;
            }*/
    }
    /*if(!empty($arr[16]) && is_numeric($arr[16])){ //面积不能为空
            $res[16] = true;
        }else{
            $res[16] = false;
            $data_fail[] = 16;
        }
        if(!empty($arr[17]) && is_numeric($arr[17])){ //售价不能为空
            $res[17] = true;
        }else{
            $res[17] = false;
            $data_fail[] = 17;
        }*/

    /*if(!empty($arr[20])){ //委托类型不能为空
            $entrust = $data['config']['rententrust'];
            if(in_array($arr[20],$entrust)){
                $res[20] = true;
            }else{
                $res[20] = false;
                $data_fail[] = 20;
            }
        }else{
            $res[20] = false;
            $data_fail[] = 20;
        }*/

    if (($res[22] == true) || ($res[23] == true) || ($res[24] == true)) {
      if (($res[7] == TRUE)
        && ($res[22] == true) && ($res[23] == true) && ($res[24] == true)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    } else {
      if (($res[7] == TRUE)) {
        return 'pass';
      } else {
        return $data_fail;
      }
    }

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
        $this->dbback_city->from('rent_house');
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
                $cond_where = 'type in (1,2) and tbl = "rent_house" and url is not null and rowid = ' . $value['id'];
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
        $count_num = $this->dbback_city->count_all_results('rent_house');
        return intval($count_num);
    }
}

/* End of file rent_house_model.php */
/* Location: ./applications/mls/models/rent_house_model.php */
