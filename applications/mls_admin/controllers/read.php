<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS-ADMIN
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Customer Controller CLASS
 *
 * 楼盘数据刷新管理 控制器
 *
 * @package         MLS-ADMIN
 * @subpackage      Controllers
 * @category        Controllers
 * @author          ccy
 */
class read extends MY_Controller
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('community_helper');
    $this->load->model('district_model');//区属模型类
    $this->load->model('community_model');//楼盘模型类
    $this->load->model('read_model');//excel读取类
  }

  //住宅信息入库
  public function house()
  {
    $excelData = $this->read_model->read('zz.xls');//获得数据
    $street = $this->district_model->get_street();//获得板块
    //print_r($street);die();
    //print_r($excelData);die();
    foreach ($excelData as $key => $vo) {
      if ($key > 1) {
        if (!empty($vo[0])) {
          $name_spell_s = '';
          for ($i = 0; $i < strlen($vo[0]); $i = $i + 3) {
            $strone = substr($vo[0], $i, 3);
            $name_spell_s .= getFirstCharter($strone);
          }
        }
        $name_spell = $this->read_model->encode($vo[0], 'all');

        preg_match('/\d+/', $vo[6], $str);
        $vo[6] = $str[0];

        $arr = explode(" ", $vo[3]);//所属区域

        foreach ($street as $v) {
          if ($arr[1] == $v['streetname']) {
            $arr[0] = $v['dist_id'];
            $arr[1] = $v['id'];
          }
        }
        if ($arr[0] > 0) {
        } else {
          $arr = array(0, 0);
        }

        $paramArray = array(
          'cmt_name' => trim($vo[0]),//楼盘名称
          'type' => 1,//楼盘类型
          'name_spell' => trim($name_spell),//拼音
          'name_spell_s' => trim($name_spell_s),//名称拼音首字母
          // 'alias' => trim(),//楼盘别名
          // 'alias_spell' => trim(),//别名拼音
          'dist_id' => intval($arr[0]),//区属
          'streetid' => intval($arr[1]),//板块
          'address' => trim($vo[1]),//楼盘地址
          'build_type' => trim($vo[7]),//物业类型
          //  'build_date' => trim(),//建筑年代
          'deliver_date' => trim($vo[21]),//交付日期		新数据采集
          //  'averprice' => trim(),//均价
          'buildarea' => intval($vo[10]),//建筑面积
          'coverarea' => intval($vo[11]),//占地面积
          'property_year' => intval($vo[6]),//产权年限
          //  'property_company' => trim(),//物业公司
          'developers' => trim($vo[8]),//开发商
          //   'parking' => trim(),//停车位
          'green_rate' => trim($vo[14]),//绿化率
          'plot_ratio' => trim($vo[15]),//容积率
          'property_fee' => trim($vo[20]),//物业费
          //  'build_num' => intval(),//总栋数
          'total_room' => intval($vo[13]),//总户数
          //  'floor_instruction' => trim(),//楼层状况
          'introduction' => trim($vo[17]),//楼盘简介
          //  'facilities' => trim(),//周边配套
          //   'bus_line' => trim(),//公交
          //  'subway' => trim(),//地铁
          'b_map_x' => trim($vo[18]),//百度X
          'b_map_y' => trim($vo[19]),//百度Y
          //  'primary_school' => trim(),//对应小学
          //   'high_school' => trim(),//对应中学
          'status' => 2,//楼盘状态
          'lock_correct' => 0,
          'creattime' => time(),//录入时间
          'is_upload_pic' => 1, //前台是否显示上传图片按钮
        );
        //print_r($paramArray);
        //$this->community_model->addcommunity($paramArray,'db_city');
        $is_exist = $this->community_model->getcommunity(array('cmt_name' => $paramArray['cmt_name']));
        if (is_array($is_exist) && !empty($is_exist)) {
          //echo '已存在同名楼盘';
        } else {
          $this->community_model->addcommunity($paramArray, 'db_city');
          //print_r($paramArray);
          $aa = "";
          $bb = "";
          foreach ($paramArray as $a => $b) {
            $aa .= "$a" . ",";
            $bb .= "'$b'" . ",";
          }
          $aa = substr($aa, 0, strlen($aa) - 1);
          $bb = substr($bb, 0, strlen($bb) - 1);
          $sql = "insert into community ( " . "$aa" . " ) values ( " . "$bb" . " );";
          echo $sql . "<br/>";
        }
      }
    }
  }

  //2别墅信息入库
  public function villa()
  {
    $excelData = $this->read_model->read('bs.xls');//获得数据
    $street = $this->district_model->get_street();//获得板块
    //print_r($street);die();
    //print_r($excelData);die();
    foreach ($excelData as $key => $vo) {
      if ($key > 1) {
        if (!empty($vo[0])) {
          $name_spell_s = '';
          for ($i = 0; $i < strlen($vo[0]); $i = $i + 3) {
            $strone = substr($vo[0], $i, 3);
            $name_spell_s .= getFirstCharter($strone);
          }
        }
        $name_spell = $this->read_model->encode($vo[0], 'all');

        preg_match('/\d+/', $vo[3], $str);
        $vo[3] = $str[0];

        $arr = explode(" ", $vo[2]);//所属区域

        foreach ($street as $v) {
          if ($arr[1] == $v['streetname']) {
            $arr[0] = $v['dist_id'];
            $arr[1] = $v['id'];
          }
        }
        if ($arr[0] > 0) {
        } else {
          $arr = array(0, 0);
        }

        $paramArray = array(
          'cmt_name' => trim($vo[0]),//楼盘名称
          'type' => 2,//楼盘类型
          'name_spell' => trim($name_spell),//拼音
          'name_spell_s' => trim($name_spell_s),//名称拼音首字母
          // 'alias' => trim(),//楼盘别名
          // 'alias_spell' => trim(),//别名拼音
          'dist_id' => intval($arr[0]),//区属
          'streetid' => intval($arr[1]),//板块
          'address' => trim($vo[1]),//楼盘地址
          'build_type' => trim($vo[4]),//物业类型
          'build_date' => trim($vo[5]),//建筑年代
          //  'deliver_date' => trim(),//交付日期
          //  'averprice' => trim(),//均价
          'buildarea' => intval($vo[7]),//建筑面积
          'coverarea' => intval($vo[8]),//占地面积
          'property_year' => intval($vo[3]),//产权年限
          //  'property_company' => trim(),//物业公司
          'developers' => trim($vo[6]),//开发商
          //   'parking' => trim(),//停车位
          'green_rate' => trim($vo[11]),//绿化率
          'plot_ratio' => trim($vo[12]),//容积率
          'property_fee' => trim($vo[13]),//物业费
          //  'build_num' => intval(),//总栋数
          'total_room' => intval($vo[10]),//总户数
          //  'floor_instruction' => trim(),//楼层状况
          'introduction' => trim($vo[14]),//楼盘简介
          //  'facilities' => trim(),//周边配套
          //   'bus_line' => trim(),//公交
          //  'subway' => trim(),//地铁
          'b_map_x' => trim($vo[15]),//百度X
          'b_map_y' => trim($vo[16]),//百度Y
          //  'primary_school' => trim(),//对应小学
          //   'high_school' => trim(),//对应中学
          'status' => 2,//楼盘状态
          'lock_correct' => 0,
          'creattime' => time(),//录入时间
          'is_upload_pic' => 1, //前台是否显示上传图片按钮
        );
        //print_r($paramArray);
        //$this->community_model->addcommunity($paramArray,'db_city');

        $is_exist = $this->community_model->getcommunity(array('cmt_name' => $paramArray['cmt_name']));
        if (is_array($is_exist) && !empty($is_exist)) {
          //echo '已存在同名楼盘';
        } else {
          $this->community_model->addcommunity($paramArray, 'db_city');
          //print_r($paramArray);
          $aa = "";
          $bb = "";
          foreach ($paramArray as $k => $v) {
            $aa .= "$k" . ",";
            $bb .= "'$v'" . ",";
          }
          $aa = substr($aa, 0, strlen($aa) - 1);
          $bb = substr($bb, 0, strlen($bb) - 1);
          $sql = "insert into community ( " . "$aa" . " ) values ( " . "$bb" . " );";
          echo $sql . "<br/>";
        }
      }
    }
  }

  //3商铺
  public function shop()
  {
    $excelData = $this->read_model->read('sp.xls');//获得数据
    $street = $this->district_model->get_street();//获得板块

    //print_r($excelData);die();
    foreach ($excelData as $key => $vo) {
      if ($key > 1) {
        if (!empty($vo[0])) {
          $name_spell_s = '';
          for ($i = 0; $i < strlen($vo[0]); $i = $i + 3) {
            $strone = substr($vo[0], $i, 3);
            $name_spell_s .= getFirstCharter($strone);
          }
        }
        $name_spell = $this->read_model->encode($vo[0], 'all');

        //preg_match( '/\d+/',$vo[22], $str);//哈尔滨
        //$vo[22]=$str[0];//哈尔滨

        $arr = explode(" ", $vo[1]);//所属区域

        foreach ($street as $v) {
          if ($arr[1] == $v['streetname']) {
            $arr[0] = $v['dist_id'];
            $arr[1] = $v['id'];
          }
        }
        if ($arr[0] > 0) {
        } else {
          $arr = array(0, 0);
        }

        $paramArray = array(
          'cmt_name' => trim($vo[0]),//楼盘名称
          'type' => 3,//楼盘类型
          'name_spell' => trim($name_spell),//拼音
          'name_spell_s' => trim($name_spell_s),//名称拼音首字母
          // 'alias' => trim(),//楼盘别名
          // 'alias_spell' => trim(),//别名拼音
          'dist_id' => intval($arr[0]),//区属
          'streetid' => intval($arr[1]),//板块
          'address' => trim($vo[2]),//楼盘地址
          'build_type' => trim($vo[4]),//物业类型
          'build_date' => trim($vo[9]),//建筑年代
          //  'deliver_date' => trim(),//交付日期
          //  'averprice' => trim(),//均价
          'buildarea' => intval($vo[13]),//建筑面积
          'coverarea' => intval($vo[12]),//占地面积
          // 'property_year' => intval($vo[22]),//产权年限  哈尔滨
          'property_company' => trim($vo[11]),//物业公司
          'developers' => trim($vo[8]),//开发商
          'parking' => trim($vo[15]),//停车位
          //'green_rate' => trim($vo[23]),//绿化率 哈尔滨
          //'plot_ratio' => trim($vo[24]),//容积率 哈尔滨
          'property_fee' => trim($vo[10]),//物业费
          //  'build_num' => intval(),//总栋数
          // 'total_room' => intval($vo[10]),//总户数
          //  'floor_instruction' => trim(),//楼层状况
          'introduction' => trim($vo[16]),//楼盘简介
          //  'facilities' => trim(),//周边配套
          'bus_line' => trim($vo[17]),//公交
          //  'subway' => trim(),//地铁
          'b_map_x' => trim($vo[20]),//百度X
          'b_map_y' => trim($vo[21]),//百度Y
          //  'primary_school' => trim(),//对应小学
          //   'high_school' => trim(),//对应中学
          'status' => 2,//楼盘状态
          'lock_correct' => 0,
          'creattime' => time(),//录入时间
          'is_upload_pic' => 1, //前台是否显示上传图片按钮
        );
        //print_r($paramArray);die();exit();
        //$this->community_model->addcommunity($paramArray,'db_city');

        $is_exist = $this->community_model->getcommunity(array('cmt_name' => $paramArray['cmt_name']));
        if (is_array($is_exist) && !empty($is_exist)) {
          //echo '已存在同名楼盘';
        } else {
          $this->community_model->addcommunity($paramArray, 'db_city');
          //print_r($paramArray);
          $aa = "";
          $bb = "";
          foreach ($paramArray as $k => $v) {
            $aa .= "$k" . ",";
            $bb .= "'$v'" . ",";
          }
          $aa = substr($aa, 0, strlen($aa) - 1);
          $bb = substr($bb, 0, strlen($bb) - 1);
          $sql = "insert into community ( " . "$aa" . " ) values ( " . "$bb" . " );";
          echo $sql . "<br/>";
        }
      }
    }
  }

  //4写字楼信息入库
  public function office()
  {
    $excelData = $this->read_model->read('xzl.xls');//获得数据
    $street = $this->district_model->get_street();//获得板块

    //print_r($excelData);die();
    foreach ($excelData as $key => $vo) {
      if ($key > 1) {
        if (!empty($vo[0])) {
          $name_spell_s = '';
          for ($i = 0; $i < strlen($vo[0]); $i = $i + 3) {
            $strone = substr($vo[0], $i, 3);
            $name_spell_s .= getFirstCharter($strone);
          }
        }
        $name_spell = $this->read_model->encode($vo[0], 'all');

        //preg_match( '/\d+/',$vo[28], $str);//哈尔滨
        //$vo[28]=$str[0];//哈尔滨

        $arr = explode(" ", $vo[1]);//所属区域

        foreach ($street as $v) {
          if ($arr[1] == $v['streetname']) {
            $arr[0] = $v['dist_id'];
            $arr[1] = $v['id'];
          }
        }
        if ($arr[0] > 0) {
        } else {
          $arr = array(0, 0);
        }

        $paramArray = array(
          'cmt_name' => trim($vo[0]),//楼盘名称
          'type' => 4,//楼盘类型
          'name_spell' => trim($name_spell),//拼音
          'name_spell_s' => trim($name_spell_s),//名称拼音首字母
          // 'alias' => trim(),//楼盘别名
          // 'alias_spell' => trim(),//别名拼音
          'dist_id' => intval($arr[0]),//区属
          'streetid' => intval($arr[1]),//板块
          'address' => trim($vo[2]),//楼盘地址
          'build_type' => trim($vo[4]),//物业类型
          'build_date' => trim($vo[10]),//建筑年代
          //  'deliver_date' => trim(),//交付日期
          //  'averprice' => trim(),//均价
          'buildarea' => intval($vo[15]),//建筑面积
          'coverarea' => intval($vo[14]),//占地面积
          // 'property_year' => intval($vo[28]),//产权年限	哈尔滨
          'property_company' => trim($vo[13]),//物业公司
          'developers' => trim($vo[9]),//开发商
          'parking' => trim($vo[17]),//停车位
          //  'green_rate' => trim($vo[26]),//绿化率	哈尔滨
          //  'plot_ratio' => trim($vo[27]),//容积率	哈尔滨
          'property_fee' => trim($vo[12]),//物业费
          //  'build_num' => intval(),//总栋数
          //'total_room' => intval($vo[10]),//总户数
          //  'floor_instruction' => trim(),//楼层状况
          'introduction' => trim($vo[18]),//楼盘简介
          //  'facilities' => trim(),//周边配套
          'bus_line' => trim($vo[19]),//公交
          //  'subway' => trim(),//地铁
          'b_map_x' => trim($vo[24]),//百度X
          'b_map_y' => trim($vo[25]),//百度Y
          //  'primary_school' => trim(),//对应小学
          //   'high_school' => trim(),//对应中学
          'status' => 2,//楼盘状态
          'lock_correct' => 0,
          'creattime' => time(),//录入时间
          'is_upload_pic' => 1, //前台是否显示上传图片按钮
        );
        //print_r($paramArray);
        //$this->community_model->addcommunity($paramArray,'db_city');

        $is_exist = $this->community_model->getcommunity(array('cmt_name' => $paramArray['cmt_name']));
        if (is_array($is_exist) && !empty($is_exist)) {
          //echo '已存在同名楼盘';
        } else {
          $this->community_model->addcommunity($paramArray, 'db_city');
          //print_r($paramArray);
          $aa = "";
          $bb = "";
          foreach ($paramArray as $k => $v) {
            $aa .= "$k" . ",";
            $bb .= "'$v'" . ",";
          }
          $aa = substr($aa, 0, strlen($aa) - 1);
          $bb = substr($bb, 0, strlen($bb) - 1);
          $sql = "insert into community ( " . "$aa" . " ) values ( " . "$bb" . " );";
          echo $sql . "<br/>";
        }
      }
    }
  }


  //58楼盘入库
  public function wubaloupan()
  {
    $excelData = $this->read_model->read('Content.xls');//获得数据
    $street = $this->district_model->get_street();//获得板块
    //print_r($street);die();
    //print_r($excelData);die();
    foreach ($excelData as $key => $vo) {
      if ($key > 1) {
        //楼盘名
        if (!empty($vo[0])) {
          $name_spell_s = '';
          $vo[0] = preg_replace("/\((.*)\)/", "", $vo[0]);
          for ($i = 0; $i < strlen($vo[0]); $i = $i + 3) {
            $strone = substr($vo[0], $i, 3);
            $name_spell_s .= getFirstCharter($strone);
          }
        }

        if (empty($vo[0])) {
          continue;
        } else {
          $commdata = $this->community_model->getcommunity(array('cmt_name' => $vo[0]));
        }

        $arr = explode(" ", $vo[2]);//所属区域

        foreach ($street as $v) {
          if ($arr[1] == $v['streetname']) {
            $arr[0] = $v['dist_id'];
            $arr[1] = $v['id'];
          }
        }
        if ($arr[0] > 0) {

        } else {
          $arr = array(0, 0);
        }

        $vo[16] = str_replace('暂无信息', '', $vo[16]);
        $vo[16] = $vo[16] != '' ? $vo[16] . '&nbsp;&nbsp;' : '';
        $vo[17] = str_replace('暂无信息', '', $vo[17]);
        $vo[17] = $vo[17] != '' ? $vo[17] . '&nbsp;&nbsp;' : '';
        $vo[18] = str_replace('暂无信息', '', $vo[18]);
        $vo[18] = $vo[18] != '' ? $vo[18] . '&nbsp;&nbsp;' : '';
        $vo[19] = str_replace('暂无信息', '', $vo[19]);
        $vo[19] = $vo[19] != '' ? $vo[19] . '&nbsp;&nbsp;' : '';
        $vo[20] = str_replace('暂无信息', '', $vo[20]);
        $vo[20] = $vo[20] != '' ? $vo[20] : '';
        $data = array(
          'dist_id' => intval($arr[0]),  //区属ID
          'streetid' => intval($arr[1]),  //板块ID
          'facilities' => trim($vo[16]) . trim($vo[17]) . trim($vo[18]) . trim($vo[19]) . trim($vo[20]),  //周边配套
          'bus_line' => trim($vo[14]),  //公交线路
          'subway' => trim($vo[15]),    //地铁线路
          'primary_school' => trim($vo[22])  //配套学校
        );

        //小区存在，更新，不存在插入
        if (is_full_array($commdata[0])) {
          if ($commdata[0]['b_map_x'] <= 0 && $commdata[0]['b_map_y'] <= 0 && $vo[12] > 0 && $vo[13] > 0) {
            $data['b_map_x'] = $vo[12]; //x
            $data['b_map_y'] = $vo[13]; //y
          }

          $this->community_model->modify_data(array('id' => $commdata[0]['id']), $data, 'db_city', 'community');
        } else {
          $name_spell = $this->read_model->encode($vo[0], 'all');

          $data['cmt_name'] = trim($vo[0]); //楼盘名称
          $data['type'] = 1; //楼盘类型
          $data['name_spell'] = trim($name_spell);//拼音
          $data['name_spell_s'] = trim($name_spell_s);//名称拼音首字母
          $data['address'] = trim($vo[1]);//楼盘地址
          $data['buildarea'] = intval($vo[6]);//建筑面积
          $data['coverarea'] = intval($vo[7]);//占地面积
          $data['developers'] = intval($vo[4]);//开发商
          $green_rate = intval($vo[8]);//绿化率
          $data['green_rate'] = $green_rate / 100;//绿化率
          $data['green_rate'] = trim($vo[9]);//容积率
          $data['property_fee'] = floatval($vo[10]);//物业费
          $data['introduction'] = trim($vo[11]);//楼盘简介
          $data['status'] = 2;//楼盘状态
          $data['lock_correct'] = 0;
          $data['creattime'] = time();//录入时间
          $data['is_upload_pic'] = 1;//前台是否显示上传图片按钮
          $data['b_map_x'] = $vo[12]; //x
          $data['b_map_y'] = $vo[13]; //y

          $this->community_model->addcommunity($data, 'db_city');
        }
      }
    }
  }


  public function resethousedata()
  {
    $this->load->model('sell_house_model');
    $sellarr = $this->sell_house_model->get_list_by_cond('', 0, 500);
    if (is_full_array($sellarr)) {
      foreach ($sellarr as $sell) {
        $id = $sell['id'];
        $block_id = $sell['block_id'];

        $data = array();
        $commdata = $this->community_model->getcommunity(array('id' => $block_id));
        $data['district_id'] = $commdata[0]['dist_id'];
        $data['streetid'] = $commdata[0]['streetid'];

        if ($data['dist_id'] > 0) $this->sell_house_model->update_house($data, array('id' => $id));

        echo 'sell_' . $id . '_' . $block_id . '_' . $data['district_id'] . '_' . $data['streetid'] . '<br />';
      }
    }

    $this->load->model('rent_house_model');
    $rentarr = $this->rent_house_model->get_list_by_cond('', 0, 500);
    if (is_full_array($rentarr)) {
      foreach ($rentarr as $rent) {
        $id = $rent['id'];
        $block_id = $rent['block_id'];

        $data = array();
        $commdata = $this->community_model->getcommunity(array('id' => $block_id));
        $data['district_id'] = $commdata[0]['dist_id'];
        $data['streetid'] = $commdata[0]['streetid'];

        if ($data['dist_id'] > 0) $this->rent_house_model->update_house($data, array('id' => $id));

        echo 'rent_' . $id . '_' . $block_id . '_' . $data['district_id'] . '_' . $data['streetid'] . '<br />';
      }
    }
  }
}

?>
