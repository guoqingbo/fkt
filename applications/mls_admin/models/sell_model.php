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
    $data_fail = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();
    //if(!empty($arr[0]) && !eregi("[^\x80-\xff]","$arr[0]")){ //楼盘名称不为空并且为中文
    if (!empty($arr[1]) && !eregi("^[\u4e00-\u9fa5a-zA-Z]+$", "$arr[1]")) { //楼盘名称不为空并且可以是中文、英文
      $where['cmt_name'] = $arr[1];
      $community_info = $this->community_info($where);
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
      $community_info = $this->community_info($where);
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
    //if(!empty($arr[4]) && eregi("^[0-9-]+$",$arr[4])){  //门牌不为空并且为数字
    /*if(!empty($arr[4])){  //门牌不为空并且为数字
        $res[4] = true;
    }else{
        $res[4] = false;
        $data_fail[] = 4;
    }
    if(!empty($arr[5])){ //业主姓名不为空并且为中文
        $res[5] = true;
    }else{
        $res[5] = false;
        $data_fail[] = 5;
    }*/
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
            }
            if(!empty($arr[11])){ //楼层不能为空
                $m = explode("/", $arr[11]);
                if(count($m) == 2){
                    foreach($m as $key=>$k){
                        if(eregi("^[0-9-]+$",$k)){ //(-[0-9]+)?
                            $res[11] = true;
                        }else{
                            $res[11] = false;
                            //$data_fail[] = 11;
                        }
                    }
                }else{
                   $res[11] = FALSE;
                   $data_fail[] = 11;
                }
            }else{
                $res[11] = FALSE;
                $data_fail[] = 11;
            }*/
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
            $entrust = $data['config']['entrust'];
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
}

?>
