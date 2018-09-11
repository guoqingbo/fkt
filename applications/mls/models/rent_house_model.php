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
 * 出租房源信息管理类,提供增加、修改、删除、查询 出租房源信息的方法。
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
    $this->browse_rent_mess_log = 'browse_rent_mess_log';
    $this->tmp_uploads = 'tmp_uploads';
    $this->community = 'community';
    $this->rent_temporaryinfo = 'rent_temporaryinfo';
  }

  /**
   * 获取房源临时详情
   * cc 2016.1.27
   */
  public function get_temporaryinfo($house_id, $broker_id, $database = 'db_city')
  {
    $where = array('house_id' => $house_id, 'broker_id' => $broker_id);
    $result = $this->get_data(array('form_name' => $this->rent_temporaryinfo, 'where' => $where), $database);
    return $result;
  }

  /**
   * 修改房源临时详情
   * cc 2016.1.27
   */
  public function update_temporaryinfo($arr, $data, $database = 'db_city', $form_name = '')
  {
    $arr = array(
      'house_id' => $arr[0],
      'broker_id' => $arr[1]
    );
    $result = $this->modify_data($arr, $data, $database, $this->rent_temporaryinfo);
    return $result;
  }

  /**
   * 添加房源临时详情
   * cc 2016.1.27
   */
  public function add_temporaryinfo($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->add_data($data, $database, $this->rent_temporaryinfo);
    return $result;
  }

  /**
   * 添加出租信息
   *
   * @access  public
   * @param   array $data_info 出租房源信息数组
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function add_rent_house_info($data_info)
  {
    $result = parent::add_info($data_info);
    return $result;
  }

  function change_agency_id_by_borker_id($broker_id, $agency_id)
  {
    $broker_id = intval($broker_id);
    $agency_id = intval($agency_id);
    if ($broker_id && $agency_id) {
      $data = array();
      $data['agency_id'] = $agency_id;
      $cond_where = "broker_id = '$broker_id' and nature = 1 ";
      $result = parent::update_info_by_cond($data, $cond_where);
    }
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
   * 获取符合条件的客源id需求信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的出售信息条数
   */
  public function get_id_by_brokerid($broker_id)
  {
    $this->dbback_city->select("id");
    $this->dbback_city->where("broker_id =" . $broker_id);
    $this->dbback_city->from($this->_rent_house_tbl);
    return $this->dbback_city->get()->result_array();
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
    $pictbl = 'upload';

    if (!$pics) {
      return $return;
    }
    $createtime = time();
    $pic_ids = '';

    if ($pics['p_filename2']) {
      foreach ($pics['p_filename2'] as $key => $val) {
        if (empty($pics['p_fileids2'][$key])) {
          //房源图片的参数
          $insert_data_house = array(
            'tbl' => $tbl,
            'type' => '1',
            'rowid' => $house_id,
            'url' => $val,
            'block_id' => $block_id,
            'createtime' => $createtime
          );
          $picid = $this->pic_model->insert_house_pic($insert_data_house, $pictbl);
          $pic_ids .= $picid . ',';
        } else {
          $pic_ids .= $pics['p_fileids2'][$key] . ',';
        }
      }
    }
    if ($pics['p_filename1']) {
      foreach ($pics['p_filename1'] as $key => $val) {
        if (empty($pics['p_fileids1'][$key])) {
          //房源图片的参数
          $insert_data_house = array(
            'tbl' => $tbl,
            'type' => '2',
            'rowid' => $house_id,
            'url' => $val,
            'block_id' => $block_id,
            'createtime' => $createtime
          );
          $picid = $this->pic_model->insert_house_pic($insert_data_house, $pictbl);
          $pic_ids .= $picid . ',';
        } else {
          $pic_ids .= $pics['p_fileids1'][$key] . ',';
        }
      }
    }
    $result = array(
      'pic_tbl' => $pictbl,
      'pic_ids' => $pic_ids
    );
    return $result;
  }


  /**
   * 获取合作房源必要信息
   * @param type $house_id
   */
  function get_hezuo_info($house_id)
  {
    $select_fields = array('id', 'broker_id', 'block_name', 'block_id', 'district_id', 'subfloor', 'totalfloor',
      'street_id', 'title', 'address', 'telno1', 'telno2', 'telno3', 'room', 'hall', 'toilet', 'floor', 'floor_type',
      'fitment', 'forward', 'price', 'buildarea', 'buildyear', 'pic', 'commission_ratio');
    $this->set_search_fields($select_fields);
    $this->set_id($house_id);
    $house_detail = $this->get_info_by_id();

    $this->load->model('district_model');
    $house_detail['district_name'] = $this->district_model->get_distname_by_id($house_detail['district_id']);
    $house_detail['street_name'] = $this->district_model->get_streetname_by_id($house_detail['street_id']);
    $house_detail['photo'] = $house_detail['pic'];
    return $house_detail;
  }

  /**
   * 根据条件获得出售浏览记录数
   * @param array $where where字段
   * @return string 浏览记录数
   */
  public function get_brower_log_sell_num($where = array())
  {
    $comm = $this->get_data(array('form_name' => $this->browse_rent_mess_log, 'where' => $where, 'select' => array('count(*) as num')), 'dbback_city');
    return $comm[0]['num'];
  }


  /**
   * 获得浏览记录
   * @param array $where where字段
   * @return array 以客源浏览日志信息组成的多维数组
   */
  public function get_brower_log($where = array(), $offset = 0, $pagesize = 0, $order_by_array = array(), $group_by = '', $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->browse_rent_mess_log, 'group_by' => $group_by, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize, 'order_by_array' => $order_by_array), $database);
    return $comm;
  }

  /**
   * 根据条件获得浏览记录数分组总数
   * @param int $customer_id customer_id字段
   * @return string 浏览记录数
   */
  public function get_brower_log_group_num($house_id)
  {
    $this->dbselect('dbback_city');
    $sql = "";
    if (!empty($house_id)) {
      $sql = "SELECT COUNT(*) as group_num FROM ";
      $sql .= " (SELECT * FROM (`browse_rent_mess_log`) ";
      $sql .= " WHERE `house_id` = $house_id GROUP BY `broker_id`) AS NUM";
      $query = $this->db->query($sql);
      $result_arr = $query->result();
      $result = $result_arr[0]->group_num;
    }
    return $result;
  }

  /**
   * 根据条件获得当天浏览记录数
   * @param array $where where字段
   * @return string 浏览记录数
   */
  public function get_today_brower_log_num($house_id, $broker_id, $today_browertime)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    if (!empty($house_id) && !empty($broker_id) && !empty($today_browertime)) {
      $where_sql = "SELECT count( * ) AS num FROM (";
      $where_sql .= "`" . $this->browse_rent_mess_log . "`";
      $where_sql .= ")";
      $where_sql .= " WHERE house_id =$house_id";
      $where_sql .= " AND broker_id =$broker_id";
      $where_sql .= " AND browertime";
      $where_sql .= " BETWEEN $today_browertime[0]";
      $where_sql .= " AND $today_browertime[1]";
      $query = $this->db->query($where_sql);
      $result_arr = $query->result();
      $result = $result_arr[0]->num;
    }
    return $result;
  }

  /**
   * 添加一条浏览记录
   * @param array $paramlist 添加字段
   * @return insert_id or 0
   */
  function add($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db_city', $this->browse_rent_mess_log);
    return $result;
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

  public function community_info($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->community, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }


  //出租房源验证数组

  public function checkarr($arr, $broker_info, $view_import_house)
  {
    $data = array();
    $data_fail = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    if (!empty($arr[0]) && !eregi("[^\x80-\xff]", "$arr[0]")) { //楼盘名称不为空并且为中文
      $where['cmt_name'] = $arr[0];
      $community_info = $this->community_info($where);
      if ($community_info[0]['id']) {
        $res[0] = true;
      } else {
        //判断是否楼盘需要加入临时小区
        $this->load->model('district_base_model');
        if (!empty($arr[19])) {  //区属不能空
          $dist_arr = $this->district_base_model->get_district_id($arr[19]);
          if (!empty($dist_arr)) {
            $res[19] = true;
          } else {
            $res[19] = false;
            $data_fail[] = 19;
          }
        } else {
          $res[19] = false;
          $data_fail[] = 19;
        }

        if (!empty($arr[20])) {  //板块不能空
          if (!empty($dist_arr)) {
            $streetname_arr = $this->district_base_model->get_streetname_bydist($dist_arr['id']);
            //print_r($streetname_arr);exit;
            if (in_array($arr[20], $streetname_arr)) {
              $res[20] = true;
            } else {
              $res[20] = false;
              $data_fail[] = 20;
            }
          } else {
            $street_arr = $this->district_base_model->get_street_id($arr[20]);
            //print_r( $street_arr);exit;
            if (!empty($street_arr)) {
              $res[20] = true;
            } else {
              $res[20] = false;
              $data_fail[] = 20;
            }
          }
        } else {
          $res[20] = false;
          $data_fail[] = 20;
        }

        if (!empty($arr[21])) {  //地址不能空
          $res[21] = true;
        } else {
          $res[21] = false;
          $data_fail[] = 21;
        }

        if (($res[19] == true) || ($res[20] == true) || ($res[21] == true)) {
          $res[0] = true;
        } else {
          $res[0] = false;
          $data_fail[] = 0;
        }
      }
    } else {
      $res[0] = false;
      $data_fail[] = 0;
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
    if (!empty($arr[2]) && eregi("^[0-9]+$", $arr[2])) { //栋座不为空并且为数字
      $res[2] = true;
    } else {
      $res[2] = false;
      $data_fail[] = 2;
    }
    if (!empty($arr[3]) && eregi("^[0-9]+$", $arr[3])) {  //单元不为空并且为数字
      $res[3] = true;
    } else {
      $res[3] = false;
      $data_fail[] = 3;
    }
    if (!empty($arr[4]) && eregi("^[0-9]+$", $arr[4])) {  //门牌不为空并且为数字
      $res[4] = true;
    } else {
      $res[4] = false;
      $data_fail[] = 4;
    }
    if (!empty($arr[5]) && !eregi("[^\x80-\xff]", "$arr[5]")) { //业主姓名不为空并且为中文
      $res[5] = true;
    } else {
      $res[5] = false;
      $data_fail[] = 5;
    }
    if (!empty($arr[6])) { //业主电话不为空
      $tel = explode("/", $arr[6]);
      if (count($tel) < 4) {
        $isMob = "/^1[3-5,8]{1}[0-9]{9}$/";
        $isTel = "/^([0-9]{3,4})?[0-9]{7,8}$/";
        foreach ($tel as $vo => $v) {
          if (preg_match($isMob, $v) || preg_match($isTel, $v)) {
            $res[6] = true;
          } else {
            $res[6] = false;
            $data_fail[] = 6;
          }
        }
      } else {
        $res[6] = false;
        $data_fail[] = 6;
      }
    } else {
      $res[6] = false;
      $data_fail[] = 6;
    }
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
    if (in_array($arr[1], array('厂房', '仓库', '车库'))) {
      $res[9] = true;
      $res[10] = true;
      $res[11] = true;
      $res[12] = true;
    } else {
      if (!empty($arr[9])) { //户型不能为空
        $m = explode("/", $arr[9]);
        if (count($m) == 3) {
          foreach ($m as $key => $k) {
            if (eregi("^[0-9]+$", $k)) {
              $res[9] = true;
            } else {
              $res[9] = false;
              $data_fail[] = 9;
            }
          }
        } else {
          $res[9] = false;
          $data_fail[] = 9;
        }
      } else {
        $res[9] = FALSE;
        $data_fail[] = 9;
      }
      if (!empty($arr[10])) { //朝向不能为空
        $forward = $data['config']['forward'];
        if (in_array($arr[10], $forward)) {
          $res[10] = true;
        } else {
          $res[10] = false;
          $data_fail[] = 10;
        }
      } else {
        $res[10] = false;
        $data_fail[] = 10;
      }
      if (!empty($arr[11])) { //楼层不能为空
        $m = explode("/", $arr[11]);
        if (count($m) == 2) {
          foreach ($m as $key => $k) {
            if (eregi("^[0-9-]+$", $k)) {
              $res[11] = true;
            } else {
              $res[11] = false;
              $data_fail[] = 11;
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
      if (!empty($arr[12])) { //装修不能为空
        $fitment = $data['config']['fitment'];
        if (in_array($arr[12], $fitment)) {
          $res[12] = true;
        } else {
          $res[12] = false;
          $data_fail[] = 12;
        }
      } else {
        $res[12] = false;
        $data_fail[] = 12;
      }
    }
    if (!empty($arr[13])) { //房龄不能为空
      if (strlen($arr[13]) == 4 && ($arr[13] <= date('Y', time()))) {
        $res[13] = true;
      } else {
        $res[13] = false;
        $data_fail[] = 13;
      }
    } else {
      $res[13] = false;
      $data_fail[] = 13;
    }
    if (!empty($arr[14]) && is_numeric($arr[14])) { //面积不能为空
      $res[14] = true;
    } else {
      $res[14] = false;
      $data_fail[] = 14;
    }
    if (!empty($arr[15]) && is_numeric($arr[15])) { //租金不能为空
      $res[15] = true;
    } else {
      $res[15] = false;
      $data_fail[] = 15;
    }
    if (!empty($arr[16])) { //钥匙不能为空
      $keys = array('有', '无');
      if (in_array($arr[16], $keys)) {
        $res[16] = true;
      } else {
        $res[16] = false;
        $data_fail[] = 16;
      }
    } else {
      $res[16] = false;
      $data_fail[] = 16;
    }
    if (!empty($arr[17])) { //委托类型不能为空
      $rententrust = $fitment = $data['config']['rententrust'];;
      if (in_array($arr[17], $rententrust)) {
        $res[17] = true;
      } else {
        $res[17] = false;
        $data_fail[] = 17;
      }
    } else {
      $res[17] = false;
      $data_fail[] = 17;
    }
    if (!empty($arr[18])) { //房源标题不能为空
      $length = mb_strlen($arr[18]);
      if ($length <= 30 && $length > 0) {
        $res[18] = true;
      } else {
        $res[18] = false;
        $data_fail[] = 18;
      }
    } else {
      $res[18] = false;
      $data_fail[] = 18;
    }
    //判断权限
    if ($view_import_house['auth']) //有权限 --判断级别
    {
      //判断role_level
      if ($broker_info['role_level'] < 6) //公司
      {
        $view_import_house['area'] = 1;
      } else if ($broker_info['role_level'] >= 6 && $broker_info['role_level'] <= 7) //店长
      {
        $view_import_house['area'] = 2;
      } else {
        $view_import_house['area'] = 3;//本人
      }
    }
    //加载经纪人模型
    $this->load->model('broker_info_model');
    //通过电话号码查找经纪人信息
    $broker = array();
    if (!empty($arr[22])) { //电话不能为空
      $broker = $this->broker_info_model->get_one_by(array('phone' => $arr[22]));
      if ($view_import_house['area'] == 1
        && $broker['company_id'] == $broker_info['company_id']
      ) {
        $res[22] = true;
      } else if ($view_import_house['area'] == 2
        && $broker['agency_id'] == $broker_info['agency_id']
      ) {
        $res[22] = true;
      } else if ($view_import_house['area'] == 3
        && $broker['broker_id'] == $broker_info['broker_id']
      ) {
        $res[22] = true;
      } else {
        $res[22] = false;
        $data_fail[] = 22;
      }
    } else {
      $res[22] = false;
      $data_fail[] = 22;
    }

    if (($res[19] == true) || ($res[20] == true) || ($res[21] == true)) {
      if (($res[0] == true) && ($res[1] == true) && ($res[2] == TRUE) && ($res[3] == TRUE) && ($res[4] == TRUE)
        && ($res[5] == TRUE) && ($res[6] == TRUE) && ($res[7] == TRUE) && ($res[8] == TRUE)
        && ($res[9] == TRUE) && ($res[10] == TRUE) && ($res[11] == TRUE) && ($res[12] == TRUE)
        && ($res[13] == TRUE) && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
        && ($res[17] == TRUE) && ($res[18] == TRUE) && ($res[19] == TRUE) && ($res[20] == TRUE)
        && ($res[21] == TRUE) && ($res[22] == TRUE)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    } else {
      if (($res[0] == true) && ($res[1] == true) && ($res[2] == TRUE) && ($res[3] == TRUE) && ($res[4] == TRUE)
        && ($res[5] == TRUE) && ($res[6] == TRUE) && ($res[7] == TRUE) && ($res[8] == TRUE)
        && ($res[9] == TRUE) && ($res[10] == TRUE) && ($res[11] == TRUE) && ($res[12] == TRUE)
        && ($res[13] == TRUE) && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
        && ($res[17] == TRUE) && ($res[18] == TRUE) && ($res[22] == TRUE)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    }
  }

  /**
   * 根据多个house_id查询多条房源信息
   * @param  house_id字段
   * @return array
   */

  public function get_all_house($house_id)
  {
    $this->dbselect('dbback_city');
    $sql = "";
    if (!empty($house_id)) {
      $sql = " SELECT * FROM  `rent_house` WHERE id IN ($house_id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }

  /**
   * 根据多个id查询多条房源合作的信息
   * @param  id字段
   * @return array
   */

  public function get_all_isshare_by_ids($id)
  {
    $this->dbselect('dbback_city');

    if (!empty($id)) {
      $sql = " SELECT isshare FROM  `rent_house` WHERE id IN ($id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }


  /**
   *
   * @param type $house_id
   * @param type $area_auth
   * @param type $frame_id
   * @return type
   */
  public function validate_func_area($house_id, $area_auth, $frame_id = '')
  {
    $this->load->model('api_broker_permission_model');
    $this->set_search_fields(array("id, broker_id, agency_id, company_id"));
    if (!strstr($house_id, ',')) {
      $this->set_id($house_id);
      $house_info = $this->get_info_by_id();
      return array('is_auth' => 1, 'result' => $house_id);
    } else {
      $new_str = '';
      $cond_where = "id IN (0," . $house_id . ")";
      $list = $this->get_list_by_cond($cond_where);
      if (is_full_array($list)) {
        foreach ($list as $v) {
          $new_str .= $v['id'] . ',';
        }
      }
      $new_str = trim($new_str, ',');

      if ($new_str == '') {
        return array('is_auth' => 0);
      } else {
        return array('is_auth' => 1, 'result' => $new_str);
      }
    }
  }

  /**
   * 根据公司id获得当前公司合作流程结束的房源id
   * @param int $company_id 公司id
   * @return $result_arr 房源id
   */
  public function get_house_cooperate_end_by_company_id($company_id = 0)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    if (!empty($company_id)) {
      $where_sql = 'SELECT distinct rent_house.id FROM `rent_house` left join `cooperate` on rent_house.id = cooperate.rowid ';
      $where_sql .= ' where cooperate.tbl = "rent" and cooperate.esta in (5,6,8,9,10,11) ';
      $where_sql .= ' and rent_house.company_id = "' . intval($company_id) . '"';
      $query = $this->db->query($where_sql);
      $result_arr = $query->result_array();
    }
    return $result_arr;
  }

  /**
   * 获得合作生效和交易成功的房源
   * @param array $where where字段
   * @return array 房源id
   */
  public function get_house_id_esta_4_7()
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    $where_sql = 'SELECT distinct rowid FROM `cooperate` ';
    $where_sql .= ' where tbl = "rent" and apply_type=1 and (esta = 4 or esta = 7) ';
    $query = $this->db->query($where_sql);
    $result_arr = $query->result_array();
    return $result_arr;
  }

  /**
   * 根据公司id获得当前公司刚发起合作申请的房源
   * @param int $company_id 公司id
   * @return $result_arr 房源id
   */
  public function get_house_id_esta_1_by_company_id($company_id = 0)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    if (!empty($company_id)) {
      $where_sql = 'SELECT distinct rent_house.id FROM `rent_house` left join `cooperate` on rent_house.id = cooperate.rowid ';
      $where_sql .= ' where cooperate.tbl = "rent" and cooperate.step = 1 and cooperate.esta = 1 ';
      $where_sql .= 'and rent_house.company_id = "' . intval($company_id) . '"';
      $query = $this->db->query($where_sql);
      $result_arr = $query->result_array();
    }
    return $result_arr;
  }

  public function change_is_share_by_house_id($house_id_arr, $type = 0)
  {
    $house_id_str = '';
    $result = false;
    if (is_full_array($house_id_arr)) {
      foreach ($house_id_arr as $k => $v) {
        $house_id_str .= $v['id'] . ',';
      }
      $house_id_str = trim($house_id_str, ',');
      $data = array();
      $data['isshare'] = (1 == $type) ? 0 : 1;
      $old_share = (1 == $type) ? 1 : 2;
      $cond_where = "id in ($house_id_str) and isshare = '" . $old_share . "'";
      $result = parent::update_info_by_cond($data, $cond_where);
    }
    return $result;
  }

  /**
   * 根据house_id，变更合作字段
   *
   * @access  public
   * @param   $house_id_arr 房源id
   * @return  boolean 是否修改成功，TRUE-成功，FAlSE-失败。
   */
  public function change_is_share_by_not_house_id($house_id_arr, $agency_id = 0)
  {
    $house_id_str = '';
    $result = false;
    if (is_full_array($house_id_arr)) {
      foreach ($house_id_arr as $k => $v) {
        $house_id_str .= $v['rowid'] . ',';
      }
      $house_id_str = trim($house_id_str, ',');
      $data = array();
      $data['isshare'] = 0;
      $cond_where = "agency_id = '" . $agency_id . "'  and isshare = 1 and id not in ($house_id_str)";
      $result = parent::update_info_by_cond($data, $cond_where);
    }
    return $result;
  }

  //修改合作的方法 isshare 默认0是不合作 当为2的时候是审核状态
  function change_isshare_status($where, $data)
  {
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->_rent_house_tbl);
    return $result;
  }

  /**
   * 根据门店id获得合作待审核的房源id
   * @param  agency_id字段
   * @return array
   */

  public function get_isshare_2_house_id_by_agency_id($agency_id)
  {
    $this->dbselect('dbback_city');

    if (!empty($agency_id)) {
      $sql = " SELECT id FROM  `rent_house` WHERE isshare = 2 AND agency_id = '" . $agency_id . "'";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }

  public function checkarr_you($arr)
  {
    $data = array();
    $data_fail = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    if (!empty($arr[0]) && !eregi("^[\u4e00-\u9fa5a-zA-Z]+$", "$arr[0]")) { //楼盘名称不为空并且可以是中文、英文
      $where['cmt_name'] = $arr[0];
      $community_info = $this->community_info($where);
      if ($community_info[0]['id']) {
        $res[0] = true;
      } else {
        //判断是否楼盘需要加入临时小区
        $this->load->model('district_base_model');
        if (!empty($arr[19])) {  //区属不能空
          $dist_arr = $this->district_base_model->get_district_id($arr[19]);
          if (!empty($dist_arr)) {
            $res[19] = true;
          } else {
            $res[19] = false;
            $data_fail[] = 19;
          }
        } else {
          $res[19] = false;
          $data_fail[] = 19;
        }

        if (!empty($arr[20])) {  //板块不能空
          if (!empty($dist_arr)) {
            $streetname_arr = $this->district_base_model->get_streetname_bydist($dist_arr['id']);
            //print_r($streetname_arr);exit;
            if (in_array($arr[20], $streetname_arr)) {
              $res[20] = true;
            } else {
              $res[20] = false;
              $data_fail[] = 20;
            }
          } else {
            $street_arr = $this->district_base_model->get_street_id($arr[20]);
            //print_r( $street_arr);exit;
            if (!empty($street_arr)) {
              $res[20] = true;
            } else {
              $res[20] = false;
              $data_fail[] = 20;
            }
          }
        } else {
          $res[20] = false;
          $data_fail[] = 20;
        }

        if (!empty($arr[21])) {  //地址不能空
          $res[21] = true;
        } else {
          $res[21] = false;
          $data_fail[] = 21;
        }

        if (($res[19] == true) || ($res[20] == true) || ($res[21] == true)) {
          $res[0] = true;
        } else {
          $res[0] = false;
          $data_fail[] = 0;
        }
      }
    } else {
      $res[0] = false;
      $data_fail[] = 0;
    }

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

    if (!empty($arr[11])) { //楼层不能为空
      $m = explode("/", $arr[11]);
      if (count($m) == 2) {
        foreach ($m as $key => $k) {
          if (eregi("^[0-9-]+$", $k)) {
            $res[11] = true;
          } else {
            $res[11] = false;
            $data_fail[] = 11;
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
    if (!empty($arr[12])) { //装修不能为空
      $fitment = $data['config']['fitment'];
      if (in_array($arr[12], $fitment)) {
        $res[12] = true;
      } else {
        $res[12] = false;
        $data_fail[] = 12;
      }
    } else {
      $res[12] = false;
      $data_fail[] = 12;
    }

    if (!empty($arr[14]) && is_numeric($arr[14])) { //面积不能为空
      $res[14] = true;
    } else {
      $res[14] = false;
      $data_fail[] = 14;
    }
    if (!empty($arr[15])) { //租金不能为空
      $res[15] = true;
    } else {
      $res[15] = false;
      $data_fail[] = 15;
    }
    if (!empty($arr[16])) { //钥匙不能为空
      $keys = array('有', '无');
      if (in_array($arr[16], $keys)) {
        $res[16] = true;
      } else {
        $res[16] = false;
        $data_fail[] = 16;
      }
    } else {
      $res[16] = false;
      $data_fail[] = 16;
    }
    if (!empty($arr[17])) { //委托类型不能为空
      $rententrust = $fitment = $data['config']['rententrust'];;
      if (in_array($arr[17], $rententrust)) {
        $res[17] = true;
      } else {
        $res[17] = false;
        $data_fail[] = 17;
      }
    } else {
      $res[17] = false;
      $data_fail[] = 17;
    }
    if (!empty($arr[18])) { //房源标题不能为空
      $length = mb_strlen($arr[18]);
      if ($length <= 30 && $length > 0) {
        $res[18] = true;
      } else {
        $res[18] = false;
        $data_fail[] = 18;
      }
    } else {
      $res[18] = false;
      $data_fail[] = 18;
    }
    if (($res[19] == true) || ($res[20] == true) || ($res[21] == true)) {
      if (($res[0] == true) && ($res[7] == TRUE) && ($res[8] == TRUE)
        && ($res[11] == TRUE) && ($res[12] == TRUE)
        && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
        && ($res[17] == TRUE) && ($res[18] == TRUE) && ($res[19] == TRUE) && ($res[20] == TRUE)
        && ($res[21] == TRUE)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    } else {
      if (($res[0] == true) && ($res[7] == TRUE) && ($res[8] == TRUE)
        && ($res[11] == TRUE) && ($res[12] == TRUE)
        && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
        && ($res[17] == TRUE) && ($res[18] == TRUE)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    }
  }
}

/* End of file rent_house_model.php */
/* Location: ./applications/mls/models/rent_house_model.php */
