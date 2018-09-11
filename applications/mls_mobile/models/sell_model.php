<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * lk
 *
 * sell导入验证库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */
class Sell_model extends MY_Model
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->tmp_uploads = 'tmp_uploads';
    $this->community = 'community';
  }

//    出售房源验证数组

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
    if (!empty($arr[15]) && is_numeric($arr[15])) { //售价不能为空
      $res[15] = true;
    } else {
      $res[15] = false;
    }
    if (!empty($arr[16])) { //税费不能为空
      $taxes = $data['config']['taxes'];
      if (in_array($arr[16], $taxes)) {
        $res[16] = true;
      } else {
        $res[16] = false;
      }
    } else {
      $res[16] = false;
    }
    if (!empty($arr[17])) { //钥匙不能为空
      $keys = array('有', '无');
      if (in_array($arr[17], $keys)) {
        $res[17] = true;
      } else {
        $res[17] = false;
      }
    } else {
      $res[17] = false;
    }
    if (!empty($arr[18])) { //委托类型不能为空
      $entrust = $data['config']['entrust'];
      if (in_array($arr[18], $entrust)) {
        $res[18] = true;
      } else {
        $res[18] = false;
      }
    } else {
      $res[18] = false;
    }
    if (($res[0] == true) && ($res[1] == true) && ($res[2] == TRUE) && ($res[3] == TRUE) && ($res[4] == TRUE)
      && ($res[5] == TRUE) && ($res[6] == TRUE) && ($res[7] == TRUE) && ($res[8] == TRUE)
      && ($res[9] == TRUE) && ($res[10] == TRUE) && ($res[11] == TRUE) && ($res[12] == TRUE)
      && ($res[13] == TRUE) && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
      && ($res[17] == TRUE) && ($res[18] == TRUE)
    ) {
      return true;
    } else {
      return false;
    }
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
}

?>
