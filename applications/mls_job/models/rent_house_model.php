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
   * 跟进表名
   * @var string
   */
  private $_follow_tbl = 'detailed_follow';


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    //初始化表名称
    $this->set_tbl($this->_rent_house_tbl);
  }

  /**
   * 处理所有相关公司的出售房源
   */
  public function change_nature_by_agency_id($agency_id, $create_time)
  {
    $agency_id = intval($agency_id);
    $create_time = intval($create_time);
    if ($agency_id && $create_time) {
      $data = array();
      $data['nature'] = 2;
      $cond_where = "agency_id = '" . $agency_id . "' and nature = 1 and createtime < '" . $create_time . "'";
      $result = parent::update_info_by_cond($data, $cond_where);
      return $result;
    }
  }

  /**
   * 处理所有非相关公司的出售房源
   */
  public function change_nature_by_agency_id2($agency_id = array(), $create_time)
  {
    $cond_where = '';
    if (!empty($agency_id)) {
      $company_str = implode(',', $agency_id);
      $cond_where = "agency_id not in (" . $company_str . ") and nature = 1 and createtime < '" . $create_time . "'";
    } else {
      $cond_where = "nature = 1 and createtime < '" . $create_time . "'";
    }
    $create_time = intval($create_time);
    if ($create_time) {
      $data = array();
      $data['nature'] = 2;
      $result = parent::update_info_by_cond($data, $cond_where);
      return $result;
    }
  }

  /**
   * 处理所有相关门店的出售房源,是否是公共数据
   */
  public function change_is_public_by_agency_id($house_id_arr)
  {
    $house_id_str = '';
    $num = 0;
    if (is_full_array($house_id_arr)) {
      $house_id_str = implode(',', $house_id_arr);
    }
    if (!empty($house_id_str)) {
      $data = array();
      $data['is_public'] = 1;
      $data['broker_id'] = 0;
      $data['broker_name'] = '';
      if (is_full_array($house_id_arr)) {
        foreach ($house_id_arr as $k => $v) {
          $where_cond = array(
            'id' => intval($v)
          );
          $house_info = parent::get_info_by_cond($where_cond);
          if (is_full_array($house_info)) {
            if ('0' == $house_info['is_public']) {
              $result = parent::update_info_by_cond($data, $where_cond);
              if ($result) {
                $num++;
                //对应的房源写跟进
                $follow_arr = array();
                $follow_arr['house_id'] = intval($v);//房源id
                $follow_arr['follow_way'] = 8;//跟进方式
                $follow_arr['follow_type'] = 3;//跟进类型
                $follow_arr['text'] = '委托人从 ' . $house_info['broker_name'] . '>> 无';//跟进内容
                $follow_arr['date'] = date('Y-m-d H:i:s');//跟进时间
                $follow_arr['type'] = 2;//类型
                $this->add_follow($follow_arr);
              }
            }
          }
        }
      }
      return $num;
    }
  }

    /**
     * 处理所有相关门店的区域公盘内出租房源,是否是公共数据
     */
    public function change_is_public_by_house_id($house_id_arr)
    {
        $house_id_str = '';
        $num = 0;
        if (is_full_array($house_id_arr)) {
            $house_id_str = implode(',', $house_id_arr);
        }
        if (!empty($house_id_str)) {
            $data = array();
            $data['is_district_public'] = 1;
            $data['district_broker_id'] = 0;
            $data['district_broker_name'] = '';
            if (is_full_array($house_id_arr)) {
                foreach ($house_id_arr as $k => $v) {
                    $where_cond = array(
                        'id' => intval($v),
                        'isshare_district' => 1
                    );
                    $house_info = parent::get_info_by_cond($where_cond);
                    if (is_full_array($house_info)) {
                        if ('0' == $house_info['is_district_public']) {
                            $result = parent::update_info_by_cond($data, $where_cond);
                            if ($result) {
                                $num++;
                                //对应的房源写跟进
                                $follow_arr = array();
                                $follow_arr['house_id'] = intval($v);//房源id
                                $follow_arr['follow_way'] = 8;//跟进方式
                                $follow_arr['follow_type'] = 3;//跟进类型
                                $follow_arr['text'] = '区域公盘内委托人从 ' . $house_info['broker_name'] . '>> 无';//跟进内容
                                $follow_arr['date'] = date('Y-m-d H:i:s');//跟进时间
                                $follow_arr['type'] = 2;//类型
                                $this->add_follow($follow_arr);
                            }
                        }
                    }
                }
            }
            return $num;
        }
    }
  /**
   * 添加跟进信息
   * @access  public
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function add_follow($data)
  {
    $this->db_city->insert($this->_follow_tbl, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

}

/* End of file sell_house_model.php */
/* Location: ./applications/mls/models/sell_house_model.php */
