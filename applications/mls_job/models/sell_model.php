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

  public function checkarr($arr, $broker_info, $view_import_house)
  {
    $data = array();
    $data_fail = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();
    //if(!empty($arr[0]) && !eregi("[^\x80-\xff]","$arr[0]")){ //楼盘名称不为空并且为中文
    if (!empty($arr[0]) && !eregi("^[\u4e00-\u9fa5a-zA-Z]+$", "$arr[0]")) { //楼盘名称不为空并且可以是中文、英文
      $where['cmt_name'] = $arr[0];
      $community_info = $this->community_info($where);
      if ($community_info[0]['id']) {
        $res[0] = true;
      } else {
        //判断是否楼盘需要加入临时小区
        $this->load->model('district_base_model');
        if (!empty($arr[20])) {  //区属不能空
          $dist_arr = $this->district_base_model->get_district_id($arr[20]);
          if (!empty($dist_arr)) {
            $res[20] = true;
          } else {
            $res[20] = false;
            $data_fail[] = 20;
          }
        } else {
          $res[20] = false;
          $data_fail[] = 20;
        }

        if (!empty($arr[21])) {  //板块不能空
          if (!empty($dist_arr)) {
            $streetname_arr = $this->district_base_model->get_streetname_bydist($dist_arr['id']);
            //print_r($streetname_arr);exit;
            if (in_array($arr[21], $streetname_arr)) {
              $res[21] = true;
            } else {
              $res[21] = false;
              $data_fail[] = 21;
            }
          } else {
            $street_arr = $this->district_base_model->get_street_id($arr[21]);
            //print_r( $street_arr);exit;
            if (!empty($street_arr)) {
              $res[21] = true;
            } else {
              $res[21] = false;
              $data_fail[] = 21;
            }
          }
        } else {
          $res[21] = false;
          $data_fail[] = 21;
        }

        if (!empty($arr[22])) {  //地址不能空
          $res[22] = true;
        } else {
          $res[22] = false;
          $data_fail[] = 22;
        }

        if (($res[20] == true) || ($res[21] == true) || ($res[22] == true)) {
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
    if (!empty($arr[15]) && is_numeric($arr[15])) { //售价不能为空
      $res[15] = true;
    } else {
      $res[15] = false;
      $data_fail[] = 15;
    }
    if (!empty($arr[16])) { //税费不能为空
      $taxes = $data['config']['taxes'];
      if (in_array($arr[16], $taxes)) {
        $res[16] = true;
      } else {
        $res[16] = false;
        $data_fail[] = 16;
      }
    } else {
      $res[16] = false;
      $data_fail[] = 16;
    }
    if (!empty($arr[17])) { //钥匙不能为空
      $keys = array('有', '无');
      if (in_array($arr[17], $keys)) {
        $res[17] = true;
      } else {
        $res[17] = false;
        $data_fail[] = 17;
      }
    } else {
      $res[17] = false;
      $data_fail[] = 17;
    }
    if (!empty($arr[18])) { //委托类型不能为空
      $entrust = $data['config']['entrust'];
      if (in_array($arr[18], $entrust)) {
        $res[18] = true;
      } else {
        $res[18] = false;
        $data_fail[] = 18;
      }
    } else {
      $res[18] = false;
      $data_fail[] = 18;
    }
    if (!empty($arr[19])) { //房源标题不能为空
      $length = mb_strlen($arr[19]);
      if ($length <= 30 && $length > 0) {
        $res[19] = true;
      } else {
        $res[19] = false;
        $data_fail[] = 19;
      }
    } else {
      $res[19] = false;
      $data_fail[] = 19;
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
    if (!empty($arr[23])) { //电话不能为空
      $broker = $this->broker_info_model->get_one_by(array('phone' => $arr[23]));
      if ($view_import_house['area'] == 1
        && $broker['company_id'] == $broker_info['company_id']
      ) {
        $res[23] = true;
      } else if ($view_import_house['area'] == 2
        && $broker['agency_id'] == $broker_info['agency_id']
      ) {
        $res[23] = true;
      } else if ($view_import_house['area'] == 3
        && $broker['broker_id'] == $broker_info['broker_id']
      ) {
        $res[23] = true;
      } else {
        $res[23] = false;
        $data_fail[] = 23;
      }
    } else {
      $res[23] = false;
      $data_fail[] = 23;
    }
    if (($res[20] == true) || ($res[21] == true) || ($res[22] == true)) {
      if (($res[0] == true) && ($res[1] == true) && ($res[2] == TRUE) && ($res[3] == TRUE) && ($res[4] == TRUE)
        && ($res[5] == TRUE) && ($res[6] == TRUE) && ($res[7] == TRUE) && ($res[8] == TRUE)
        && ($res[9] == TRUE) && ($res[10] == TRUE) && ($res[11] == TRUE) && ($res[12] == TRUE)
        && ($res[13] == TRUE) && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
        && ($res[17] == TRUE) && ($res[18] == TRUE) && ($res[19] == TRUE) && ($res[20] == true)
        && ($res[21] == true) && ($res[22] == true) && ($res[23] == true)
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
        && ($res[17] == TRUE) && ($res[18] == TRUE) && ($res[19] == TRUE) && ($res[23] == true)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    }

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

  /**
   * 根据房源编号 house_id 更新 sell_house 表中的 is_publish 字段值为 1
   * @param array $where where字段
   * @data array 需要更新的字段
   * @author angel_in_us
   * @date 2015-06-10
   */
  public function update_ispub_by_houseid($type, $house_id, $database = 'db_city')
  {
    if ($type == 'sell') {
      $form_name = 'sell_house';
    } else if ($type == 'rent') {
      $form_name = 'rent_house';
    }
    $where = array('id' => $house_id);
    $data = array('is_publish' => 1);
    $comm = $this->modify_data($where, $data, $database = 'db_city', $form_name);
    return $comm;
  }


  //房友导入
  public function checkarr_you($arr)
  {
    $data = array();
    $data_fail = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();
    //if(!empty($arr[0]) && !eregi("[^\x80-\xff]","$arr[0]")){ //楼盘名称不为空并且为中文
    if (!empty($arr[0]) && !eregi("^[\u4e00-\u9fa5a-zA-Z]+$", "$arr[0]")) { //楼盘名称不为空并且可以是中文、英文
      $where['cmt_name'] = $arr[0];
      $community_info = $this->community_info($where);
      if ($community_info[0]['id']) {
        $res[0] = true;
      } else {
        //判断是否楼盘需要加入临时小区
        $this->load->model('district_base_model');
        if (!empty($arr[20])) {  //区属不能空
          $dist_arr = $this->district_base_model->get_district_id($arr[20]);
          if (!empty($dist_arr)) {
            $res[20] = true;
          } else {
            $res[20] = false;
            $data_fail[] = 20;
          }
        } else {
          $res[20] = false;
          $data_fail[] = 20;
        }

        if (!empty($arr[21])) {  //板块不能空
          if (!empty($dist_arr)) {
            $streetname_arr = $this->district_base_model->get_streetname_bydist($dist_arr['id']);
            //print_r($streetname_arr);exit;
            if (in_array($arr[21], $streetname_arr)) {
              $res[21] = true;
            } else {
              $res[21] = false;
              $data_fail[] = 21;
            }
          } else {
            $street_arr = $this->district_base_model->get_street_id($arr[21]);
            //print_r( $street_arr);exit;
            if (!empty($street_arr)) {
              $res[21] = true;
            } else {
              $res[21] = false;
              $data_fail[] = 21;
            }
          }
        } else {
          $res[21] = false;
          $data_fail[] = 21;
        }

        if (!empty($arr[22])) {  //地址不能空
          $res[22] = true;
        } else {
          $res[22] = false;
          $data_fail[] = 22;
        }

        if (($res[20] == true) || ($res[21] == true) || ($res[22] == true)) {
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
    if (!empty($arr[15])) { //售价不能为空
      $res[15] = true;
    } else {
      $res[15] = false;
      $data_fail[] = 15;
    }
    if (!empty($arr[16])) { //税费不能为空
      $taxes = $data['config']['taxes'];
      if (in_array($arr[16], $taxes)) {
        $res[16] = true;
      } else {
        $res[16] = false;
        $data_fail[] = 16;
      }
    } else {
      $res[16] = false;
      $data_fail[] = 16;
    }
    if (!empty($arr[17])) { //钥匙不能为空
      $keys = array('有', '无');
      if (in_array($arr[17], $keys)) {
        $res[17] = true;
      } else {
        $res[17] = false;
        $data_fail[] = 17;
      }
    } else {
      $res[17] = false;
      $data_fail[] = 17;
    }
    if (!empty($arr[18])) { //委托类型不能为空
      $entrust = $data['config']['entrust'];
      if (in_array($arr[18], $entrust)) {
        $res[18] = true;
      } else {
        $res[18] = false;
        $data_fail[] = 18;
      }
    } else {
      $res[18] = false;
      $data_fail[] = 18;
    }
    if (!empty($arr[19])) { //房源标题不能为空
      $length = mb_strlen($arr[19]);
      if ($length <= 30 && $length > 0) {
        $res[19] = true;
      } else {
        $res[19] = false;
        $data_fail[] = 19;
      }
    } else {
      $res[19] = false;
      $data_fail[] = 19;
    }

    if (($res[20] == true) || ($res[21] == true) || ($res[22] == true)) {
      if (($res[0] == true) && ($res[7] == TRUE) && ($res[8] == TRUE)
        && ($res[11] == TRUE) && ($res[12] == TRUE)
        && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
        && ($res[17] == TRUE) && ($res[18] == TRUE) && ($res[19] == TRUE) && ($res[20] == true)
        && ($res[21] == true) && ($res[22] == true)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    } else {
      if (($res[0] == true) && ($res[7] == TRUE) && ($res[8] == TRUE)
        && ($res[11] == TRUE) && ($res[12] == TRUE)
        && ($res[14] == TRUE) && ($res[15] == TRUE) && ($res[16] == TRUE)
        && ($res[17] == TRUE) && ($res[18] == TRUE) && ($res[19] == TRUE)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    }

  }
}

?>
