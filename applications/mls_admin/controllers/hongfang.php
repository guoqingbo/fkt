<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of hongfang
 *
 * @author ccy
 */
class hongfang extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('hongfang_model');
    $this->load->model('read_model');
    $this->load->model('community_model');//楼盘模型类
    $this->load->model('broker_info_model');
    $this->load->model('agency_model');
    $this->load->model('district_model');
    //加载客户MODEL
    $this->load->model('sell_house_model');
  }

  public function index()
  {
    $arr_data = $this->hongfang_model->get_hongfang_data(100);
    $arr = unserialize($arr_data[0]['content']);
    print_r($arr);
  }

  //导入接口
  public function import($lasthid = 1)
  {
    /****
     * if ($_GET['test']) {die();}
     * //洪房请求数据地址 -- 看数据量多不多，是否需要批量插入
     * $url = 'http://www.hongfang.cc/hfdb/HFBigDataService.asmx/HF_GetHouseBigData4WS2?token=F100&password=FBED10FE173EFAC9&pagesize=20&lasthid=' . $lasthid . '&hids=';
     * $this->load->library('log/Log');
     * Log::record('1、请求地址：' . '/hongfang/import/'.$lasthid, array(), 'hongfang');
     *
     * //一、通过curl获取数据，返回数据
     * $this->load->library('Curl');
     * $json_data = $this->curl->vget($url);
     * $json_data = str_replace('
     * ', '', strip_tags($json_data));
     * //$json_data = str_replace('\\', '', $json_data);
     * echo $json_data;
     * $arr_data = json_decode($json_data, true);
     * print_r($arr_data);
     * die();
     * $hongfang_data = array(
     * 'url' => '/hongfang/import/'.$lasthid, 'content' => serialize($arr_data)
     * );
     * $this->hongfang_model->add_hongfang_data($hongfang_data);**/

    die();
    if ($lasthid >= 342) {
      echo 'over';
      die();
    }
    $arr_hongfang_data = $this->hongfang_model->get_hongfang_data($lasthid);

    if (!is_full_array($arr_hongfang_data)) {
      sleep(1);
      $lasthid = $lasthid + 1;
      echo "<script>window.location.href='/hongfang/import/" . $lasthid . "';</script>";
      die();
    }
    $this->hongfang_model->update_hongfang_data($lasthid);

    //die();
    $arr_data = unserialize($arr_hongfang_data[0]['content']);
    //二、判断是否有数据，如果有，继续，反之导入结束
    if (is_full_array($arr_data) && $arr_data['LPtotalnum'] > 0) {
      //三、导入楼盘数据
      foreach ($arr_data['rows'] as $block) {
        $block_fields = array();
        $block_fields['HID'] = $block['HID']; //主键
        $block_fields['AID'] = $block['AID']; //区域ID
        $block_fields['east'] = $block['east']; //东临
        $block_fields['west'] = $block['west']; //西临
        $block_fields['south'] = $block['south']; //南临

        $block_fields['north'] = $block['north']; //北临
        $block_fields['name'] = $block['name']; //楼盘名称
        $block_fields['byname'] = $block['byname']; //别名
        $block_fields['spellShort'] = $block['spellShort']; //拼音简写
        $block_fields['code'] = $block['code']; //楼盘编号

        $block_fields['address'] = $block['address']; //地址
        $block_fields['useID'] = $block['useID']; //用途
        $block_fields['propertyformID'] = $block['propertyformID']; //产权形式
        $block_fields['landno'] = $block['landno']; //地号
        $block_fields['drawnNo'] = $block['drawnNo']; //图号

        $block_fields['landusepermit'] = $block['landusepermit']; //国有土地使用证
        $block_fields['landstarttime'] = $block['landstarttime']; //土地起始日期
        $block_fields['landyears'] = $block['landyears']; //土地年限
        $block_fields['buildingtypeID'] = $block['buildingtypeID']; //主建筑类型
        $block_fields['plotratio'] = $block['plotratio']; //容积率

        $block_fields['greenratio'] = $block['greenratio']; //绿化率
        $block_fields['floorarea'] = $block['floorarea']; //占地面积
        $block_fields['buildarea'] = $block['buildarea']; //建筑面积
        $block_fields['completiondate'] = $block['completiondate']; //竣工日期
        $block_fields['opentime'] = $block['opentime']; //开盘时间

        $block_fields['starttime'] = $block['starttime']; //开工时间
        $block_fields['price'] = $block['price']; //项目均价
        $block_fields['totalnum'] = $block['totalnum']; //总栋数
        $block_fields['totalhouseholds'] = $block['totalhouseholds']; //总套数
        $block_fields['parkNum'] = $block['parkNum']; //车位数

        $block_fields['developer'] = $block['developer']; //开发商
        $block_fields['greenID'] = $block['greenID']; //小区绿化
        $block_fields['environmentID'] = $block['environmentID']; //小区环境
        $block_fields['estatemanagement'] = $block['estatemanagement']; //物业管理(有或无)
        $block_fields['estatecompany'] = $block['estatecompany']; //物业公司

        $block_fields['estatetypeID'] = $block['estatetypeID']; //物业类(外)型
        $block_fields['estatemoney'] = $block['estatemoney']; //物管费
        $block_fields['estatephone'] = $block['estatephone']; //物管电话
        $block_fields['ProjectDescription'] = $block['ProjectDescription']; //项目概况
        $block_fields['delflag'] = $block['delflag']; //删除标识
        $block_fields['Subtime'] = $block['Subtime']; //创建时间

        $block_fields['AreaName'] = $block['AreaName']; //用途ID-对应的中文
        $block_fields['propertyformID_Text'] = $block['propertyformID_Text'];
        //物业管理(有或无)ID-对应的中文
        $block_fields['greenID_Text'] = $block['greenID_Text']; //小区绿化ID-对应的中文
        $block_fields['environmentID_Text'] = $block['environmentID_Text']; //小区环境ID-对应的中文
        $block_fields['estatetypeID_Text'] = $block['estatetypeID_Text']; //物业类(外)型ID-对应的中文
        $block_fields['estatemanagement_Text'] = $block['estatemanagement_Text']; //物业管理(有或无)ID-对应的中文
        $block_fields['useID_Text'] = $block['useID_Text']; //用途ID-对应的中文

        $block_fields['buildingtypeID_Text'] = $block['buildingtypeID_Text']; //用途ID-对应的中文

        $block_fields['Lati_Longitude'] = $block['Lati_Longitude']; //用途ID-对应的中文

        $this->hongfang_model->add_block($block_fields);

        //四、导入楼栋
        if ($block['LDtotalnum'] > 0) //楼栋
        {
          foreach ($block['rows'] as $door) {
            $door_fields = array();
            $door_fields['BID'] = $door['BID'];//主键
            $door_fields['HID'] = $door['HID'];//关链楼盘ID
            $door_fields['name'] = $door['name'];//楼栋名称
            $door_fields['byname'] = $door['byname'];//别名
            $door_fields['builderTypeID'] = $door['builderTypeID'];//建筑类型

            $door_fields['builderStruct'] = $door['builderStruct'];//建筑结构
            $door_fields['completionDate'] = $door['completionDate'];//竣工日期
            $door_fields['saledate'] = $door['saledate'];//销售时间
            $door_fields['saleprice'] = $door['saleprice'];//销售均价

            $door_fields['unitNumber'] = $door['unitNumber'];//单元数
            $door_fields['totalFloors'] = $door['totalFloors'];//总层数
            $door_fields['Startstopfloor'] = $door['Startstopfloor'];//起止楼层
            $door_fields['totalhouseholds'] = $door['totalhouseholds'];//总套数
            $door_fields['buildarea'] = $door['buildarea'];//建筑面积

            $door_fields['blift'] = $door['blift'];//是否有电梯
            $door_fields['lifttohome'] = $door['lifttohome'];//梯户比
            $door_fields['exteriorWallsID'] = $door['exteriorWallsID'];//外墙装修
            $door_fields['saleno'] = $door['saleno'];//预售证号
            $door_fields['locationID'] = $door['locationID'];//位置

            $door_fields['layer'] = $door['layer'];//地下层数
            $door_fields['addfacilities'] = $door['addfacilities'];//附属设施
            $door_fields['weighting'] = $door['weighting'];//价格权重系数
            $door_fields['explain'] = $door['explain'];//备注
            $door_fields['delflag'] = $door['delflag'];//删除标识

            $door_fields['builderTypeID_Text'] = $door['builderTypeID_Text'];//建筑类型
            $door_fields['builderStruct_Text'] = $door['builderStruct_Text'];//附属设施
            $door_fields['exteriorWallsID_Text'] = $door['exteriorWallsID_Text'];//价格权重系数
            $door_fields['locationID_Text'] = $door['locationID_Text'];//备注
            $door_fields['addfacilities_Text'] = $door['addfacilities_Text'];//删除标识

            $this->hongfang_model->add_door($door_fields);
            //五、导入户型
            if ($door['HXtotalnum'] > 0) {
              foreach ($door['rows'] as $room) {
                $room_fields = array();
                $room_fields['TID'] = $room['TID']; //主键
                $room_fields['BID'] = $room['BID']; //关链楼栋ID
                $room_fields['Realname'] = $room['Realname']; //房号名称
                $room_fields['currentfloor'] = $room['currentfloor']; //所在楼层
                $room_fields['roomno'] = $room['roomno']; //所在室号

                $room_fields['floorarea'] = $room['floorarea']; //建筑面积
                $room_fields['transmissionarea'] = $room['transmissionarea']; //套内面积
                $room_fields['housetypeID'] = $room['housetypeID']; //户型
                $room_fields['structureID'] = $room['structureID']; //户型结构
                $room_fields['totalprice'] = $room['totalprice']; //总价

                $room_fields['price'] = $room['price']; //单价
                $room_fields['TowardsID'] = $room['TowardsID']; //朝向
                $room_fields['landscapeID'] = $room['landscapeID']; //景观
                $room_fields['useID'] = $room['useID']; //用途
                $room_fields['floorheight'] = $room['floorheight']; //层高

                $room_fields['venlightID'] = $room['venlightID']; //通风采光
                $room_fields['weighting'] = $room['weighting']; //价格权重系数
                $room_fields['bjudge'] = $room['bjudge']; //可估价
                $room_fields['delflag'] = $room['delflag']; //删除标识
                $room_fields['bpass'] = $room['bpass']; //是否审批通过

                $room_fields['Subtime'] = $room['Subtime']; //创建时间
                $room_fields['unitno'] = $room['unitno']; //单元号

                $room_fields['housetypeID_Text'] = $room['housetypeID_Text']; //户型ID-对应的文字
                //户型结构ID-对应的中文
                $room_fields['structureID_Text'] = $room['structureID_Text'];

                $room_fields['TowardsID_Text'] = $room['TowardsID_Text']; //朝向ID-对应的中文
                $room_fields['landscapeID_Text'] = $room['landscapeID_Text']; //景观ID-对应的中文
                $room_fields['useID_Text'] = $room['useID_Text']; //用途ID-对应的中文
                //通风采光ID-对应的中文
                $room_fields['venlightID_Text'] = $room['venlightID_Text'];
                $this->hongfang_model->add_room($room_fields);
              }
            }
          }
        }
        //print_r($block);
        //最后一次楼盘ID
        //$lasthid = $block_fields['HID'];
      }
    } else {
      echo 'over';
      die();
    }
    $lasthid = $lasthid + 1;
    //Log::record('请求地址2：' . '/hongfang/import/'.$lasthid, array(), 'hongfang');
    //die();
    //六、获取最后一轮lastid，继续下一轮的导入
    sleep(1);
    echo "<script>window.location.href='/hongfang/import/" . $lasthid . "';</script>";

  }


  public function fast_comminuty($lasthid = 1)
  {
    //die();
    if ($lasthid >= 342) {
      echo 'over';
      die();
    }
    $arr_hongfang_data = $this->hongfang_model->get_hongfang_data($lasthid);

    if (!is_full_array($arr_hongfang_data)) {
      //sleep(1);
      $lasthid = $lasthid + 1;
      echo "<script>window.location.href='/hongfang/fast_comminuty/" . $lasthid . "';</script>";
      die();
    }
    $this->hongfang_model->update_hongfang_data($lasthid);
    $arr_data = unserialize($arr_hongfang_data[0]['content']);
    //二、判断是否有数据，如果有，继续，反之导入结束
    if (is_full_array($arr_data) && $arr_data['LPtotalnum'] > 0) {
      //三、导入楼盘数据
      $districts = array(
        '13' => '1',  //武昌
        '16' => '2',  //洪山
        '19' => '12',  //青山
        '20' => '8',  //汉阳
        '21' => '6',  //江汉
        '22' => '10',  //硚口
        '23' => '7',  //蔡甸
        '24' => '11',  //江夏
        '26' => '4',  //江岸
        '27' => '14',  //汉南
        '30' => '5',  //东西湖
        '31' => '13', //新洲
        '33' => '3' //黄陂
      );
      foreach ($arr_data['rows'] as $block) {
        $block_update_data = array();
        $block_insert_data = array();
        $block_id = '';
        //如果区属对不上就跳过
        if (!isset($districts[$block['AID']])) {
          continue;
        }
        //判断小区名称在是否存在，存在新更新，不存在更新
        $old_block = $this->community_model->get_cmtinfo_by_cmtname($block['name']);
        //要更新或者插入的数据
        if (is_full_array($old_block)) //存在, 更新
        {
          //区属
          $block_update_data['dist_id'] = $districts[$block['AID']];
          //小区名称
          $block_update_data['cmt_name'] = $block['name'];
          //别名
          if (!empty($block['alias'])) {
            $block_update_data['alias'] = $block['alias'];
          }
          //地址
          if (!empty($block['address'])) {
            $block_update_data['address'] = $block['address'];
          }
          //容积率
          if (!empty($block['plotratio'])) {
            $block_update_data['plot_ratio'] = $block['plotratio'];
          }
          //绿化率
          if (!empty($block['plotratio'])) {
            $block_update_data['green_rate'] = $block['plotratio'];
          }
          //占地面积
          if (!empty($block['plotratio'])) {
            $block_update_data['coverarea'] = $block['floorarea'];
          }
          //建筑面积
          if (!empty($block['buildarea'])) {
            $block_update_data['buildarea'] = $block['buildarea'];
          }
          //均价
          if (!empty($block['price'])) {
            $block_update_data['averprice'] = $block['price'];
          }
          //总栋数
          if (!empty($block['totalnum'])) {
            $block_update_data['build_num'] = $block['totalnum'];
          }
          //停车位
          if (!empty($block['parkNum'])) {
            $block_update_data['parking'] = $block['parkNum'];
          }
          //开发商
          if (!empty($block['developer'])) {
            $block_update_data['developers'] = $block['developer'];
          }
          //物业公司
          if (!empty($block['estatecompany'])) {
            $block_update_data['property_company'] = $block['estatecompany'];
          }
          //物业公司
          if (!empty($block['estatemoney'])) {
            $block_update_data['property_fee'] = $block['estatemoney'];
          }
          $baidu_map = explode('|', $block['Lati_Longitude']);
          if (count($baidu_map) > 0 && $baidu_map[0] > 0) {
            $block_update_data['b_map_x'] = $baidu_map[0];
            $block_update_data['b_map_y'] = $baidu_map[1];
          } else {
            $block_update_data['b_map_x'] = 0;
            $block_update_data['b_map_y'] = 0;
          }
          $block_update_data['h_id'] = $block['HID'];
          //要更新block_id
          $block_id = $old_block[0]['id'];
          $modifyResult = $this->community_model->modifycommunity($block_id, $block_update_data);
        } else //新增
        {
          $block_insert_data = array(
            'dist_id' => $districts[$block['AID']], 'cmt_name' => $block['name'],
            'name_spell_s' => $this->read_model->encode($block['name'], 'head'),
            'name_spell' => $this->read_model->encode($block['name'], 'all'),
            'alias' => $block['byname'], 'address' => $block['address'],
            'plot_ratio' => $block['plotratio'], 'green_rate' => $block['greenratio'],
            'coverarea' => $block['floorarea'], 'buildarea' => $block['buildarea'],
            'averprice' => $block['price'], 'build_num' => $block['totalnum'],
            'parking' => $block['parkNum'], 'developers' => $block['developer'],
            'property_company' => $block['estatecompany'], 'property_fee' => $block['estatemoney'],
            'h_id' => $block['HID'], 'status' => 2,//楼盘状态
            'creattime' => time(),//录入时间
          );
          $baidu_map = explode('|', $block['Lati_Longitude']);
          if (count($baidu_map) > 0 && $baidu_map[0] > 0) {
            $block_insert_data['b_map_x'] = $baidu_map[0];
            $block_insert_data['b_map_y'] = $baidu_map[1];
          } else {
            $block_insert_data['b_map_x'] = 0;
            $block_insert_data['b_map_y'] = 0;
          }
          //楼盘表数据入库
          $block_id = $this->community_model->addcommunity($block_insert_data, 'db_city');
        }
        if (intval($block_id) > 0) {

        } else {
          $this->load->library('log/Log');
          Log::record('1、请求地址：' . $block['name'], array(), 'hongfang');
        }
        //四、导入楼栋
        if ($block['LDtotalnum'] > 0 && false) //楼栋
        {
          $arr_dong = array();
          foreach ($block['rows'] as $door) {
            if (empty($door['name'])) {
              continue;
            }
            //$door['name']返回小区block_id =
            $arr_dong['name'] = $door['name'];
            $arr_dong['cmt_id'] = $block_id;
            $door_id = $this->community_model->add_dong($arr_dong);
            //五、导入单元号
            if ($door['HXtotalnum'] > 0) {
              foreach ($door['rows'] as $room) {
                $realname = $room['Realname'];
                if (empty($realname)) {
                  continue;
                }
                $arr_realname = explode('-', $realname);
                if (count($arr_realname) == 3) {
                  $unitname = $arr_realname[0];
                } else {
                  $unitname = '一单元';
                }
                $arr_unit = array('name' => $unitname, 'dong_id' => $door_id, 'cmt_id' => $block_id);
                $unit = $this->community_model->get_unit($arr_unit);
                if (!is_full_array($unit)) {
                  $unit_id = $this->community_model->add_unit($arr_unit);
                } else {
                  $unit_id = $unit[0]['id'];
                }
                //Realname
                //以-分开如果2个值则默认‘一单元’
                //查询相应的单元有没有，如果没有则创建
                //并返回单元号
                //$unit_id = 3;
                //创建门牌号
                $arr_door = array('name' => $room['currentfloor'] . '-' . $room['roomno'],
                  'unit_id' => $unit_id, 'dong_id' => $door_id, 'cmt_id' => $block_id);
                $this->community_model->add_door($arr_door);
              }
            }
          }
        }
      }
    }
    $lasthid = $lasthid + 1;
    //Log::record('请求地址2：' . '/hongfang/import/'.$lasthid, array(), 'hongfang');
    //die();
    //六、获取最后一轮lastid，继续下一轮的导入
    //sleep(1);
    echo "<script>window.location.href='/hongfang/fast_comminuty/" . $lasthid . "';</script>";
  }


  //导入
  public function comminuty($lasthid = 1)
  {
    $this->load->model('community_model');
    //1、获取洪房未删除的数据
    $blocks = $this->hongfang_model->get_block($lasthid);
    if (is_full_array($blocks)) {
      //区属对应判断 key洪房编号，value
      $districts = array(
        '13' => '1',  //武昌
        '16' => '2',  //洪山
        '19' => '12',  //青山
        '20' => '8',  //汉阳
        '21' => '6',  //江汉
        '22' => '10',  //硚口
        '23' => '7',  //蔡甸
        '24' => '11',  //江夏
        '26' => '4',  //江岸
        '27' => '14',  //汉南
        '30' => '5',  //东西湖
        '31' => '13', //新洲
        '33' => '3' //黄陂
      );
      foreach ($blocks as $v) {
        //如果区属对不上就跳过
        if (!isset($districts[$v['AID']])) {
          continue;
        }

        //判断小区名称在是否存在，存在新更新，不存在更新
        $block = $this->community_model->get_cmtinfo_by_cmtname($v['name']);
        if (is_full_array($block)) //存在, 更新
        {

        } else //新增
        {

        }
      }
    }

  }


  public function import_house()
  {
    $i = 3;
    $city_id = $_SESSION['esfdatacenter']['city_id'];
    $filename = 'temp/house_1.xlsx';
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($filename);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();
    //算出有效数据总行数
    $valid_num = intval($highestRow) - intval($i) + 1;
    if ($valid_num <= 1000) {
      $highestColumn = $objWorksheet->getHighestColumn();
      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
      $excelData = array();
      for ($row = $i - 1; $row <= $highestRow; $row++) {
        for ($col = 0; $col < $highestColumnIndex; $col++) {
          $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
        }
      }
    }
    if (is_full_array($excelData)) {
      foreach ($excelData as $v) {
        $block = $this->community_model->get_cmtinfo_by_cmtname($v[2]);
        //小区名称绝对匹配方式获取基本信息
        //如果有设置区属和板块，没有的话查找区属、查找板块、
        $district_id = 0;
        $street_id = 0;
        $block_id = 0;
        $block_name = '';
        $block_address = '';
        if (is_full_array($block)) {
          $district_id = $block[0]['dist_id'];
          $street_id = $block[0]['streetid'];
          $block_id = $block[0]['id'];
          $block_name = $block[0]['cmt_name'];
          $block_address = $block[0]['address'];
        } else {
          //查找区属
          $dist = $this->district_model->get_district_id($v[4]);
          if (is_full_array($dist)) {
            $district_id = $dist['id'];
          } else {
            //创建区属
            $paramArray = array(
              'district' => trim($v[4]),
              'city_id' => $city_id,
              'order' => 0,
              'is_show' => 1,
            );
            $district_id = $this->district_model->add_district($paramArray);
          }
          //查找板块
          $street = $this->district_model->get_street_id($v[5]);
          if (is_full_array($street)) {
            $street_id = $street['id'];
          } else {
            //创建板块
            $paramArray = array(
              'streetname' => trim($v[5]),
              'name_spell' => trim($this->read_model->encode($v[5], 'all')),
              'dist_id' => intval($district_id),
              'order' => 0,
              'is_show' => 1,
            );
            $street_id = $this->district_model->add_street($paramArray);
          }
          //创建小区
          $block_data = array(
            'dist_id' => $district_id, 'cmt_name' => $v[2],
            'name_spell_s' => $this->read_model->encode($v[2], 'head'),
            'name_spell' => $this->read_model->encode($v[2], 'all'),
            'streetid' => $street_id, 'status' => 2,//楼盘状态
            'creattime' => time(),//录入时间
          );
          //楼盘表数据入库
          $block_id = $this->community_model->addcommunity($block_data, 'db_city');
        }
        //房源基本参数
        $house_data = array();
        //通过门店编号查找公司编号
        $agency_info = $this->agency_model->get_by_id($v[23]);
        if ($v[24] > 0) //经纪人编号大于0
        {

          $broker_info = $this->broker_info_model->get_by_id($v[24]);
          $house_data['broker_id'] = $broker_info['broker_id'];
          $house_data['broker_name'] = $broker_info['truename'];
          //查不到经纪人的记录

        } else {
          $house_data['agency_id'] = $agency_info['id'];
          $house_data['company_id'] = $agency_info['company_id'];
        }
        $house_data['createtime'] = $v[21];
        $house_data['ip'] = get_ip();
        $house_data['telno1'] = $v[1];//房东电话

        $house_data['sell_type'] = $v[15];
        $house_data['block_name'] = $block_name;
        $house_data['block_id'] = $block_id;
        $house_data['district_id'] = $district_id;
        $house_data['street_id'] = $street_id;
        $house_data['address'] = $block_address;
        $house_data['dong'] = $v[6];
        $house_data['unit'] = $v[7];
        $house_data['door'] = $v[8];
        $house_data['owner'] = $v[0];
        $house_data['idcare'] = '';
        if ($house_data['sell_type'] > 2) {
          $house_data['room'] = 0;
          $house_data['hall'] = 0;
          $house_data['toilet'] = 0;
        } else {
          //室-厅-卫-阳台
          $rooms = explode('-', $v[11]);
          $house_data['room'] = $rooms[0];
          $house_data['hall'] = $rooms[1];
          $house_data['toilet'] = $rooms[2];
        }
        $house_data['kitchen'] = 0;
        $house_data['balcony'] = $rooms[3];

        $house_data['floor'] = $v[9];
        $house_data['totalfloor'] = $v[10];

        if ($house_data['sell_type'] < 5) {
          $house_data['forward'] = $v[13];
          $house_data['fitment'] = $v[12];
        }
        $house_data['buildyear'] = $v[14];
        $house_data['buildarea'] = $v[17];

        $house_data['price'] = $v[18];
        $house_data['lowprice'] = $v[20];
        $house_data['avgprice'] = $v[19];

        $house_data['status'] = $v[26];

        $house_data['updatetime'] = $v[22];

        $house_data['title'] = $v[3];
        $house_data['remark'] = $v[16];

        $house_id = $this->sell_house_model->add_sell_house_info($house_data);
        die();
      }
    }
  }
}
