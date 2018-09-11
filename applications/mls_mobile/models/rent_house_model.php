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
    $select_fields = array('id', 'block_name', 'block_id', 'district_id', 'street_id',
      'address', 'title', 'telno1', 'telno2', 'telno3', 'room', 'hall', 'toilet', 'fitment',
      'forward', 'price', 'buildarea', 'buildyear', 'pic');
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
    $comm = $this->get_data(array('form_name' => $this->browse_rent_mess_log, 'where' => $where, 'select' => array('count(*) as num')), 'db_city');
    return $comm[0]['num'];
  }


  /**
   * 获得浏览记录
   * @param array $where where字段
   * @return array 以客源浏览日志信息组成的多维数组
   */
  public function get_brower_log($where = array(), $offset = 0, $pagesize = 0, $order_by_array = array(), $group_by = '', $database = 'db_city')
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
    $this->dbselect('db_city');
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
    $this->dbselect('db_city');
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
  public function get_tmp($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'db_city')
  {
    $comm = $this->get_data(array('form_name' => $this->tmp_uploads, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }

  public function community_info($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'db_city')
  {
    $comm = $this->get_data(array('form_name' => $this->community, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }


  //出租房源验证数组

  public function checkarr($arr)
  {
    $data = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    if (!empty($arr[0]) && !eregi("[^\x80-\xff]", "$arr[0]")) { //楼盘名称不为空并且为中文
      $res[0] = true;
    } else {
      $res[0] = false;
    }

    if (!empty($arr[1])) { //物业类型不能为空
      $sell_type = $data['config']['sell_type'];
      if (in_array($arr[1], $sell_type)) {
        $res[1] = true;
      } else {
        $res[1] = false;
      }
    } else {
      $res[1] = false;
    }
    if (!empty($arr[2]) && eregi("^[0-9]+$", $arr[2])) { //栋座不为空并且为数字
      $res[2] = true;
    } else {
      $res[2] = false;
    }
    if (!empty($arr[3]) && eregi("^[0-9]+$", $arr[3])) {  //单元不为空并且为数字
      $res[3] = true;
    } else {
      $res[3] = false;
    }
    if (!empty($arr[4]) && eregi("^[0-9]+$", $arr[4])) {  //门牌不为空并且为数字
      $res[4] = true;
    } else {
      $res[4] = false;
    }
    if (!empty($arr[5]) && !eregi("[^\x80-\xff]", "$arr[5]")) { //业主姓名不为空并且为中文
      $res[5] = true;
    } else {
      $res[5] = false;
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
          }
        }
      } else {
        $res[6] = false;
      }
    } else {
      $res[6] = false;
    }
    if (!empty($arr[7])) { //性质不能为空
      $nature = $data['config']['nature'];
      if (in_array($arr[7], $nature)) {
        $res[7] = true;
      } else {
        $res[7] = false;
      }
    } else {
      $res[7] = false;
    }
    if (!empty($arr[8])) { //合作不能为空
      $nature = array('是', '否');
      if (in_array($arr[8], $nature)) {
        $res[8] = true;
      } else {
        $res[8] = false;
      }
    } else {
      $res[8] = false;
    }
    if (!empty($arr[9])) { //户型不能为空
      $m = explode("/", $arr[9]);
      if (count($m) == 3) {
        foreach ($m as $key => $k) {
          if (eregi("^[0-9]+$", $k)) {
            $res[9] = true;
          } else {
            $res[9] = false;
          }
        }
      } else {
        $res[9] = false;
      }
    } else {
      $res[9] = FALSE;
    }
    if (!empty($arr[10])) { //楼层不能为空
      $m = explode("/", $arr[10]);
      if (count($m) == 2) {
        foreach ($m as $key => $k) {
          if (eregi("^[0-9]+$", $k)) {
            $res[10] = true;
          } else {
            $res[10] = false;
          }
        }
      } else {
        $res[10] = FALSE;
      }
    } else {
      $res[10] = FALSE;
    }
    if (!empty($arr[11])) { //朝向不能为空
      $forward = $data['config']['forward'];
      if (in_array($arr[11], $forward)) {
        $res[11] = true;
      } else {
        $res[11] = false;
      }
    } else {
      $res[11] = false;
    }
    if (!empty($arr[12])) { //装修不能为空
      $fitment = $data['config']['fitment'];
      if (in_array($arr[12], $fitment)) {
        $res[12] = true;
      } else {
        $res[12] = false;
      }
    } else {
      $res[12] = false;
    }
    if (!empty($arr[13])) { //房龄不能为空
      if (strlen($arr[13]) == 4 && ($arr[13] <= date('Y', time()))) {
        $res[13] = true;
      } else {
        $res[13] = false;
      }
    } else {
      $res[13] = false;
    }
    if (!empty($arr[14]) && is_numeric($arr[14])) { //面积不能为空
      $res[14] = true;
    } else {
      $res[14] = false;
    }
    if (!empty($arr[15]) && is_numeric($arr[15])) { //租金不能为空
      $res[15] = true;
    } else {
      $res[15] = false;
    }
    if (!empty($arr[16])) { //钥匙不能为空
      $keys = array('有', '无');
      if (in_array($arr[16], $keys)) {
        $res[16] = true;
      } else {
        $res[16] = false;
      }
    } else {
      $res[16] = false;
    }
    if (!empty($arr[17])) { //委托类型不能为空
      $rententrust = $fitment = $data['config']['rententrust'];;
      if (in_array($arr[17], $rententrust)) {
        $res[17] = true;
      } else {
        $res[17] = false;
      }
    } else {
      $res[17] = false;
    }
    if (($res[0] == true) && ($res[1] == true) && ($res[2] == TRUE) && ($res[3] == TRUE) && ($res[4] == TRUE)
      && ($res[5] == TRUE) && ($res[6] == TRUE) && ($res[7] == TRUE) && ($res[8] == TRUE)
      && ($res[9] == TRUE) && ($res[10] == TRUE) && ($res[11] == TRUE) && ($res[12] == TRUE)
      && ($res[13] == TRUE) && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
      && ($res[17] == TRUE)
    ) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * 根据多个house_id查询多条房源信息
   * @param  house_id字段
   * @return array
   */

  public function get_all_house($house_id)
  {
    $this->dbselect('db_city');
    $sql = "";
    if (!empty($house_id)) {
      $sql = " SELECT * FROM  `rent_house` WHERE id IN ($house_id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }
}

/* End of file rent_house_model.php */
/* Location: ./applications/mls/models/rent_house_model.php */
