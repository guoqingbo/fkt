<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * lk
 *
 * 读取excel文件
 *
 * @package         mls-admin
 * @copyright       Copyright (c) 2006 - 2014
 * @since           Version 1.0
 * @author          ccy
 * @filesource
 */
class Read_model extends MY_Model
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();

  }

  //获取IP
  public function getIp()
  {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
      $ip = getenv("HTTP_CLIENT_IP");
    } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
      $ip = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
      $ip = getenv("REMOTE_ADDR");
    } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
      $ip = $_SERVER['REMOTE_ADDR'];
    } else {
      $ip = "unknown";
    }
    return ($ip);
  }

  //    读取excel文件
  public function read($file_name)
  {
    //print_r($this->dbback_city);
    ini_set("memory_limit", "1024M"); //excel文件大小
    //$filename = 'temp/xq.xls';//excel文件名
    $filename = 'temp/' . $file_name;//excel文件名
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($filename);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);//指定的文件
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
    $valid_num = intval($highestRow);
    $highestColumn = $objWorksheet->getHighestColumn();// 取得总列数
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $excelData = array();
    for ($row = 0; $row <= $highestRow; $row++) {
      for ($col = 0; $col < $highestColumnIndex; $col++) {
        $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
      }
    }

    return $excelData;
  }

  /**
   * 拼音字符转换图
   * @var array
   */
  private static $_aMaps = array(
    'a' => -20319, 'ai' => -20317, 'an' => -20304, 'ang' => -20295, 'ao' => -20292,
    'ba' => -20283, 'bai' => -20265, 'ban' => -20257, 'bang' => -20242, 'bao' => -20230, 'bei' => -20051, 'ben' => -20036, 'beng' => -20032, 'bi' => -20026, 'bian' => -20002, 'biao' => -19990, 'bie' => -19986, 'bin' => -19982, 'bing' => -19976, 'bo' => -19805, 'bu' => -19784,
    'ca' => -19775, 'cai' => -19774, 'can' => -19763, 'cang' => -19756, 'cao' => -19751, 'ce' => -19746, 'ceng' => -19741, 'cha' => -19739, 'chai' => -19728, 'chan' => -19725, 'chang' => -19715, 'chao' => -19540, 'che' => -19531, 'chen' => -19525, 'cheng' => -19515, 'chi' => -19500, 'chong' => -19484, 'chou' => -19479, 'chu' => -19467, 'chuai' => -19289, 'chuan' => -19288, 'chuang' => -19281, 'chui' => -19275, 'chun' => -19270, 'chuo' => -19263, 'ci' => -19261, 'cong' => -19249, 'cou' => -19243, 'cu' => -19242, 'cuan' => -19238, 'cui' => -19235, 'cun' => -19227, 'cuo' => -19224,
    'da' => -19218, 'dai' => -19212, 'dan' => -19038, 'dang' => -19023, 'dao' => -19018, 'de' => -19006, 'deng' => -19003, 'di' => -18996, 'dian' => -18977, 'diao' => -18961, 'die' => -18952, 'ding' => -18783, 'diu' => -18774, 'dong' => -18773, 'dou' => -18763, 'du' => -18756, 'duan' => -18741, 'dui' => -18735, 'dun' => -18731, 'duo' => -18722,
    'e' => -18710, 'en' => -18697, 'er' => -18696,
    'fa' => -18526, 'fan' => -18518, 'fang' => -18501, 'fei' => -18490, 'fen' => -18478, 'feng' => -18463, 'fo' => -18448, 'fou' => -18447, 'fu' => -18446,
    'ga' => -18239, 'gai' => -18237, 'gan' => -18231, 'gang' => -18220, 'gao' => -18211, 'ge' => -18201, 'gei' => -18184, 'gen' => -18183, 'geng' => -18181, 'gong' => -18012, 'gou' => -17997, 'gu' => -17988, 'gua' => -17970, 'guai' => -17964, 'guan' => -17961, 'guang' => -17950, 'gui' => -17947, 'gun' => -17931, 'guo' => -17928,
    'ha' => -17922, 'hai' => -17759, 'han' => -17752, 'hang' => -17733, 'hao' => -17730, 'he' => -17721, 'hei' => -17703, 'hen' => -17701, 'heng' => -17697, 'hong' => -17692, 'hou' => -17683, 'hu' => -17676, 'hua' => -17496, 'huai' => -17487, 'huan' => -17482, 'huang' => -17468, 'hui' => -17454, 'hun' => -17433, 'huo' => -17427,
    'ji' => -17417, 'jia' => -17202, 'jian' => -17185, 'jiang' => -16983, 'jiao' => -16970, 'jie' => -16942, 'jin' => -16915, 'jing' => -16733, 'jiong' => -16708, 'jiu' => -16706, 'ju' => -16689, 'juan' => -16664, 'jue' => -16657, 'jun' => -16647,
    'ka' => -16474, 'kai' => -16470, 'kan' => -16465, 'kang' => -16459, 'kao' => -16452, 'ke' => -16448, 'ken' => -16433, 'keng' => -16429, 'kong' => -16427, 'kou' => -16423, 'ku' => -16419, 'kua' => -16412, 'kuai' => -16407, 'kuan' => -16403, 'kuang' => -16401, 'kui' => -16393, 'kun' => -16220, 'kuo' => -16216,
    'la' => -16212, 'lai' => -16205, 'lan' => -16202, 'lang' => -16187, 'lao' => -16180, 'le' => -16171, 'lei' => -16169, 'leng' => -16158, 'li' => -16155, 'lia' => -15959, 'lian' => -15958, 'liang' => -15944, 'liao' => -15933, 'lie' => -15920, 'lin' => -15915, 'ling' => -15903, 'liu' => -15889, 'long' => -15878, 'lou' => -15707, 'lu' => -15701, 'lv' => -15681, 'luan' => -15667, 'lue' => -15661, 'lun' => -15659, 'luo' => -15652,
    'ma' => -15640, 'mai' => -15631, 'man' => -15625, 'mang' => -15454, 'mao' => -15448, 'me' => -15436, 'mei' => -15435, 'men' => -15419, 'meng' => -15416, 'mi' => -15408, 'mian' => -15394, 'miao' => -15385, 'mie' => -15377, 'min' => -15375, 'ming' => -15369, 'miu' => -15363, 'mo' => -15362, 'mou' => -15183, 'mu' => -15180,
    'na' => -15165, 'nai' => -15158, 'nan' => -15153, 'nang' => -15150, 'nao' => -15149, 'ne' => -15144, 'nei' => -15143, 'nen' => -15141, 'neng' => -15140, 'ni' => -15139, 'nian' => -15128, 'niang' => -15121, 'niao' => -15119, 'nie' => -15117, 'nin' => -15110, 'ning' => -15109, 'niu' => -14941, 'nong' => -14937, 'nu' => -14933, 'nv' => -14930, 'nuan' => -14929, 'nue' => -14928, 'nuo' => -14926,
    'o' => -14922, 'ou' => -14921,
    'pa' => -14914, 'pai' => -14908, 'pan' => -14902, 'pang' => -14894, 'pao' => -14889, 'pei' => -14882, 'pen' => -14873, 'peng' => -14871, 'pi' => -14857, 'pian' => -14678, 'piao' => -14674, 'pie' => -14670, 'pin' => -14668, 'ping' => -14663, 'po' => -14654, 'pu' => -14645,
    'qi' => -14630, 'qia' => -14594, 'qian' => -14429, 'qiang' => -14407, 'qiao' => -14399, 'qie' => -14384, 'qin' => -14379, 'qing' => -14368, 'qiong' => -14355, 'qiu' => -14353, 'qu' => -14345, 'quan' => -14170, 'que' => -14159, 'qun' => -14151,
    'ran' => -14149, 'rang' => -14145, 'rao' => -14140, 're' => -14137, 'ren' => -14135, 'reng' => -14125, 'ri' => -14123, 'rong' => -14122, 'rou' => -14112, 'ru' => -14109, 'ruan' => -14099, 'rui' => -14097, 'run' => -14094, 'ruo' => -14092,
    'sa' => -14090, 'sai' => -14087, 'san' => -14083, 'sang' => -13917, 'sao' => -13914, 'se' => -13910, 'sen' => -13907, 'seng' => -13906, 'sha' => -13905, 'shai' => -13896, 'shan' => -13894, 'shang' => -13878, 'shao' => -13870, 'she' => -13859, 'shen' => -13847, 'sheng' => -13831, 'shi' => -13658, 'shou' => -13611, 'shu' => -13601, 'shua' => -13406, 'shuai' => -13404, 'shuan' => -13400, 'shuang' => -13398, 'shui' => -13395, 'shun' => -13391, 'shuo' => -13387, 'si' => -13383, 'song' => -13367, 'sou' => -13359, 'su' => -13356, 'suan' => -13343, 'sui' => -13340, 'sun' => -13329, 'suo' => -13326,
    'ta' => -13318, 'tai' => -13147, 'tan' => -13138, 'tang' => -13120, 'tao' => -13107, 'te' => -13096, 'teng' => -13095, 'ti' => -13091, 'tian' => -13076, 'tiao' => -13068, 'tie' => -13063, 'ting' => -13060, 'tong' => -12888, 'tou' => -12875, 'tu' => -12871, 'tuan' => -12860, 'tui' => -12858, 'tun' => -12852, 'tuo' => -12849,
    'wa' => -12838, 'wai' => -12831, 'wan' => -12829, 'wang' => -12812, 'wei' => -12802, 'wen' => -12607, 'weng' => -12597, 'wo' => -12594, 'wu' => -12585,
    'xi' => -12556, 'xia' => -12359, 'xian' => -12346, 'xiang' => -12320, 'xiao' => -12300, 'xie' => -12120, 'xin' => -12099, 'xing' => -12089, 'xiong' => -12074, 'xiu' => -12067, 'xu' => -12058, 'xuan' => -12039, 'xue' => -11867, 'xun' => -11861,
    'ya' => -11847, 'yan' => -11831, 'yang' => -11798, 'yao' => -11781, 'ye' => -11604, 'yi' => -11589, 'yin' => -11536, 'ying' => -11358, 'yo' => -11340, 'yong' => -11339, 'you' => -11324, 'yu' => -11303, 'yuan' => -11097, 'yue' => -11077, 'yun' => -11067,
    'za' => -11055, 'zai' => -11052, 'zan' => -11045, 'zang' => -11041, 'zao' => -11038, 'ze' => -11024, 'zei' => -11020, 'zen' => -11019, 'zeng' => -11018, 'zha' => -11014, 'zhai' => -10838, 'zhan' => -10832, 'zhang' => -10815, 'zhao' => -10800, 'zhe' => -10790, 'zhen' => -10780, 'zheng' => -10764, 'zhi' => -10587, 'zhong' => -10544, 'zhou' => -10533, 'zhu' => -10519, 'zhua' => -10331, 'zhuai' => -10329, 'zhuan' => -10328, 'zhuang' => -10322, 'zhui' => -10315, 'zhun' => -10309, 'zhuo' => -10307, 'zi' => -10296, 'zong' => -10281, 'zou' => -10274, 'zu' => -10270, 'zuan' => -10262, 'zui' => -10260, 'zun' => -10256, 'zuo' => -10254
  );

  /**
   * 将中文编码成拼音
   * @param string $utf8Data utf8字符集数据
   * @param string $sRetFormat 返回格式 [head:首字母|all:全拼音]
   * @return string
   */
  public static function encode($utf8Data, $sRetFormat = 'head')
  {
    $sGBK = iconv('UTF-8', 'GBK', $utf8Data);
    $aBuf = array();
    for ($i = 0, $iLoop = strlen($sGBK); $i < $iLoop; $i++) {
      $iChr = ord($sGBK{$i});
      if ($iChr > 160)
        $iChr = ($iChr << 8) + ord($sGBK{++$i}) - 65536;
      if ('head' === $sRetFormat)
        $aBuf[] = substr(self::zh2py($iChr), 0, 1);
      else
        $aBuf[] = self::zh2py($iChr);
    }
    if ('head' === $sRetFormat)
      return implode('', $aBuf);
    else
      return implode('', $aBuf);
  }

  /**
   * 中文转换到拼音(每次处理一个字符)
   * @param number $iWORD 待处理字符双字节
   * @return string 拼音
   */
  private static function zh2py($iWORD)
  {
    if ($iWORD > 0 && $iWORD < 160) {
      return chr($iWORD);
    } elseif ($iWORD < -20319 || $iWORD > -10247) {
      return '';
    } else {
      foreach (self::$_aMaps as $py => $code) {
        if ($code > $iWORD) break;
        $result = $py;
      }
      return $result;
    }
  }

  //    读取文件1
  public function read_house($model, $broker_info, $upload, $i, $type)
  {
    $this->load->model($model);
    $filename = 'temp/' . $upload['file_name'];
    $broker_id = intval($broker_info['broker_id']);
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($filename);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();
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

      //print_r($excelData);exit;
      //范例模版的标题
      if ($model == 'sell_model') {
        $example_title = Array('0' => '编号', '1' => '小区', '2' => '总价', '3' => '单价', '4' => '面积', '5' => '户型', '6' => '朝向', '7' => '年代', '8' => '性质', '9' => '状态', '10' => '跟进时间', '11' => '楼层', '12' => '门店', '13' => '经纪人', '14' => '装修', '15' => '房源类型', '16' => '备注', '17' => '业主类型', '18' => '区县', '19' => '板块', '20' => '小区地址', '21' => '街道', '22' => '门牌', '23' => '业主', '24' => '业主手机', '25' => '登记日期', '26' => '保密备注', '27' => '产权', '28' => '录入人', '29' => '经纪人电话', '30' => '来源', '31' => '交付时间', '32' => '房产证号', '33' => '车库备注', '34' => '钥匙编号', '35' => '钥匙', '36' => '结构', '37' => '车库', '38' => '客户备注', '39' => '交通状况', '40' => '支付方式', '41' => '看房方式', '42' => '价格条件');
      } else {
        $example_title = Array('0' => '编号', '1' => '小区', '2' => '月租', '3' => '单价', '4' => '面积', '5' => '户型', '6' => '朝向', '7' => '年代', '8' => '性质', '9' => '状态', '10' => '跟进时间', '11' => '楼层', '12' => '门店', '13' => '经纪人', '14' => '装修', '15' => '房源类型', '16' => '备注', '17' => '业主类型', '18' => '区县', '19' => '板块', '20' => '小区地址', '21' => '街道', '22' => '门牌', '23' => '业主', '24' => '业主手机', '25' => '登记日期', '26' => '保密备注', '27' => '产权', '28' => '录入人', '29' => '经纪人电话', '30' => '来源', '31' => '交付时间', '32' => '房产证号', '33' => '车库备注', '34' => '钥匙编号', '35' => '钥匙', '36' => '结构', '37' => '车库', '38' => '客户备注', '39' => '交通状况', '40' => '支付方式', '41' => '看房方式', '42' => '价格条件');
      }
      $example_title_flip = array_flip($example_title);
      //print_r($example_title_flip);exit;

      //print_r($example_title);exit;
      //获取文件标题数组 $type=1为房源 ，2为客源
      $excelTitle = array();
      if ($type == 1) {
        $excelTitle = $excelData[1];
        unset($excelData[1]);
      } else if ($type == 2) {
        $excelTitle = $excelData[7];
        unset($excelData[7]);
      }
      //print_r($excelData);exit;
      $excelData_new = array();
      foreach ($excelData as $array => $arr) {
        foreach ($arr as $k => $v) {
          foreach ($excelTitle as $key => $vo) {
            if ($key == $k) {
              $excelData_new[$array][$example_title_flip[$vo]] = $arr[$key];

            }
          }
        }
      }
      $excelData = $excelData_new;
      //print_r($excelData_new);exit;
      //print_r($excelData);exit;
      //物业类型
      $sell_type = $data['config']['sell_type'];
      //委托类型
      $entrust = $data['config']['entrust'];
      //装修
      $fitment = $data['config']['fitment'];
      //print_r($sell_type);exit;

      $datas = array();
      $datas_fail = array();
      foreach ($excelData as $array => $arr) {
        //物业类型转化
        if (in_array($arr[15], array('独栋别墅', '联排别墅', '叠加别墅', '双拼别墅', '大平墅'))) {
          $excelData[$array][15] = $sell_type[2];
        } else if (!in_array($arr[15], array($sell_type[3], $sell_type[4], $sell_type[5], $sell_type[6], $sell_type[7]))) {
          $excelData[$array][15] = $sell_type[1];
        }
        //状态转化
        if ($arr[9] == '正常') {
          $excelData[$array][9] = '有效';
        } else {
          $excelData[$array][9] = '无效';
        }
        //户型
        if ($arr[5]) {
          $excelData[$array][5] = str_replace('室', '/', $excelData[$array][5]);
          $excelData[$array][5] = str_replace('厅', '/', $excelData[$array][5]);
          $excelData[$array][5] = str_replace('卫', ' ', $excelData[$array][5]);
        }
        //委托类型
        if ($arr[30] == '独家') {
          $excelData[$array][30] = $entrust[1];
        } else {
          $excelData[$array][30] = $entrust[2];
        }
        //装修
        if ($arr[14] == '简单装修') {
          $excelData[$array][14] = $fitment[2];
        } else if ($arr[14] == '中等装修') {
          $excelData[$array][14] = $fitment[3];
        } else if ($arr[14] == '精装修') {
          $excelData[$array][14] = $fitment[4];
        } else if ($arr[14] == '豪华装修') {
          $excelData[$array][14] = $fitment[5];
        }
        //标题
        $excelData[$array][43] = $arr[1] . ' ' . $arr[5];
        //电话
        $excelData[$array][24] = str_replace(',', '/', $excelData[$array][24]);

        if ($this->$model->checkarr($excelData[$array]) == 'pass') {
          $datas[] = $excelData[$array];
        } else {
          $datas_fail[$array] = $this->$model->checkarr($excelData[$array]);
        }
        //print_r($excelData[$array]);exit;
      }
      //print_r($datas_fail);exit;
      //print_r($excelData);exit;
      if (!empty($datas) && empty($datas_fail)) {
        $res = array('broker_id' => $broker_id);
        $this->$model->del($res, 'db_city', 'tmp_uploads');
        $data = array('broker_id' => $broker_id,
          'content' => serialize($datas),
          'createtime' => time()
        );
        $id = $this->$model->add_data($data, 'db_city', 'tmp_uploads');
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源</p>'
        .'<input type="hidden" id=tmp_id value='.$id.'>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源。</p>'
        .'<input type="hidden" id=tmp_id value='.$id.'>';*/

        return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"> <style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_s">上传成功</span>，共上传' . count($datas) . '条信息。</p>'
        . '<input type="hidden" id=tmp_id value=' . $id . '></body></html>';
      } else {
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        $fail_html = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"> <style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，<a href="javascript:void(0);" onclick="window.parent.see_reason();" color="#227ac6">点击查看失败原因</a></p>';
        if ($type == 1) {
          $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符、楼盘名称在楼盘表中搜索不到、房源标题超过30字、新增楼盘请按照标准填写(区属、板块和地址,如区属板块没有请先自行添加后再导入)。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        } else if ($type == 2) {
          $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        }
        foreach ($datas_fail as $key => $vo) {
          $fail_html .= '<tr><td class="up_m_b_date_up" style="text-align: center">第' . $key . '行</td><td class="up_m_b_date_up" style="text-align:left;padding:0px 15px">';
          foreach ($vo as $kk => $vv) {
            foreach ($example_title as $k => $v) {
              if ($vv == $k) {
                $fail_html .= '<span class="up_e">' . $v . '</span>,';
              }
            }
          }
          $fail_html = substr($fail_html, 0, -1);
          $fail_html .= '</td></tr>';
        }
        $fail_html .= '</table></body></html>';
        return $fail_html;
      }
    } else {
      return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"> <style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，一次最多只能导入1000条记录哦！</p></body></html>';
    }

  }


  //    读取文件1
  public function read_house_taizhou($model, $broker_info, $upload, $i, $type)
  {
    set_time_limit(90);
    ini_set("memory_limit", "1024M");
    $this->load->model($model);
    //$filename = 'caiyanceshi.xlsx';
    $filename = 'temp/' . $upload['file_name'];
    $broker_id = intval($broker_info['broker_id']);
    $broker_name = $broker_info['truename'];
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($filename);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();
    if ($model == 'sell_model' || $model == 'rent_house_model') {
      //加载出售基本配置MODEL
      $this->load->model('house_config_model');
      $data['config'] = $this->house_config_model->get_config();
    } else {
      //加载求购、求租基本配置MODEL
      $this->load->model('customer_base_model');
      $data['config'] = $this->customer_base_model->get_base_conf();
    }

    //算出有效数据总行数
    $valid_num = intval($highestRow) - intval($i) + 1;
    if ($valid_num <= 10000) {
      $highestColumn = $objWorksheet->getHighestColumn();
      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
      $excelData = array();
      for ($row = $i - 1; $row <= $highestRow; $row++) {
        for ($col = 0; $col < $highestColumnIndex; $col++) {
          $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
        }
      }
      if ($model == 'sell_model') {
        $example_title = Array('0' => '楼盘', '1' => '物业类型', '2' => '栋座', '3' => '单元', '4' => '门牌', '5' => '业主姓名', '6' => '业主电话', '7' => '现状', '8' => '性质', '9' => '户型', '10' => '朝向', '11' => '楼层', '12' => '总楼层', '13' => '装修', '14' => '房龄', '15' => '车库', '16' => '建筑面积', '17' => '外网报价', '18' => '税费', '19' => '钥匙', '20' => '委托类型', '21' => '房源登记人', '22' => '区属', '23' => '区域', '24' => '楼盘地址', '25' => '标题', '31' => '登记日期', '32' => '底价');
      } elseif ($model == 'rent_house_model') {
        $example_title = Array('0' => '楼盘', '1' => '物业类型', '2' => '栋座', '3' => '单元', '4' => '门牌', '5' => '业主姓名', '6' => '业主电话', '7' => '现状', '8' => '性质', '9' => '户型', '10' => '朝向', '11' => '楼层', '12' => '总楼层', '13' => '装修', '14' => '房龄', '15' => '车库', '16' => '建筑面积', '17' => '报价', '19' => '钥匙', '20' => '委托类型', '21' => '房源登记人', '22' => '区属', '23' => '区域', '24' => '楼盘地址', '25' => '标题', '31' => '登记日期');
      } elseif ($model == 'buy_customer_model') {
        $example_title = Array('0' => '需求小区', '1' => '物业类型', '5' => '客户姓名', '6' => '客户电话', '7' => '状态', '9' => '户型', '21' => '所属经纪人', '22' => '区属', '23' => '需求方位', '24' => '楼盘地址', '26' => '面积下限', '27' => '面积上限', '28' => '价格下限', '29' => '价格上限', '30' => '需求类型', '31' => '登记时间');
      } elseif ($model == 'rent_customer_model') {
        $example_title = Array('0' => '需求小区', '1' => '物业类型', '5' => '客户姓名', '6' => '客户电话', '7' => '状态', '9' => '户型', '21' => '所属经纪人', '22' => '区属', '23' => '需求方位', '24' => '楼盘地址', '26' => '面积下限', '27' => '面积上限', '28' => '价格下限', '29' => '价格上限', '30' => '需求类型', '31' => '登记时间');
      }
      $example_title_flip = array_flip($example_title);
      //print_r($example_title_flip);exit;

      //print_r($example_title);exit;
      //获取文件标题数组 $type=1为房源 ，2为客源
      $excelTitle = array();
      if ($type == 1) {
        $excelTitle = $excelData[1];
        unset($excelData[1]);
      } else if ($type == 2) {
        $excelTitle = $excelData[1];
        unset($excelData[1]);
      }
      $excelTitle_flip = array_flip($excelTitle);
      //print_r($excelTitle);exit;
      $excelData_new = array();
      if ($model == 'sell_model') {
        foreach ($excelData as $array => $arr) {
          if ($excelData[$array][$excelTitle_flip['房源登记人']] == $broker_name) {
            foreach ($arr as $k => $v) {
              foreach ($excelTitle as $key => $vo) {
                if ($key == $k) {
                  $excelData_new[$array][$example_title_flip[$vo]] = $arr[$key];
                }
              }
            }
          }
        }
      } elseif ($model == 'rent_house_model') {
        foreach ($excelData as $array => $arr) {
          if ($excelData[$array][$excelTitle_flip['房源登记人']] == $broker_name) {
            foreach ($arr as $k => $v) {
              foreach ($excelTitle as $key => $vo) {
                if ($key == $k) {
                  $excelData_new[$array][$example_title_flip[$vo]] = $arr[$key];
                }
              }
            }
          }
        }
      } else {
        foreach ($excelData as $array => $arr) {
          if ($excelData[$array][$excelTitle_flip['所属经纪人']] == $broker_name) {
            foreach ($arr as $k => $v) {
              foreach ($excelTitle as $key => $vo) {
                if ($key == $k) {
                  $excelData_new[$array][$example_title_flip[$vo]] = $arr[$key];
                }
              }
            }
          }
        }
      }

      if ($model == 'sell_model') {
        //委托类型
        $entrust = $data['config']['entrust'];
        //物业类型
        $sell_type = $data['config']['sell_type'];
        $excelData = $excelData_new;
      } elseif ($model == 'rent_house_model') {
        //委托类型
        $entrust = $data['config']['rententrust'];
        //物业类型
        $sell_type = $data['config']['sell_type'];
        $excelData = $excelData_new;
      } elseif ($model == 'buy_customer_model') {
        //物业类型
        $sell_type = $data['config']['property_type'];
        $excelData_new_2 = array();
        foreach ($excelData_new as $key => $vo) {
          if ($vo[30] == '二手房') {
            $excelData_new_2[$key] = $vo;
          }
        }
        $excelData = $excelData_new_2;
      } elseif ($model == 'rent_customer_model') {
        //物业类型
        $sell_type = $data['config']['property_type'];
        $excelData_new_2 = array();
        foreach ($excelData_new as $key => $vo) {
          if ($vo[30] == '租房') {
            $excelData_new_2[$key] = $vo;
          }
        }
        $excelData = $excelData_new_2;
      }

      //装修
      $fitment = $data['config']['fitment'];
      //print_r($sell_type);exit;

      $datas = array();
      $datas_fail = array();

      //print_r($excelData);exit;
      foreach ($excelData as $array => $arr) {
        //物业类型转化
        if (in_array($arr[1], array('独栋别墅', '联排别墅', '叠加别墅', '双拼别墅', '大平墅'))) {
          $excelData[$array][1] = $sell_type[2];
        } else if ($arr[1] == '店面房') {
          $excelData[$array][1] = $sell_type[3];
        } else if (!in_array($arr[1], array($sell_type[3], $sell_type[4], $sell_type[5], $sell_type[6], $sell_type[7]))) {
          $excelData[$array][1] = $sell_type[1];
        }
        if ($model == 'rent_house_model') {
          //电话
          if ($arr[6]) {
            $excelData[$array][6] = str_replace(' ', '/', $excelData[$array][6]);
            $excelData[$array][6] = trim($excelData[$array][6]);
          }
          //户型
          if ($arr[9]) {
            $excelData[$array][9] = str_replace('-', '/', $excelData[$array][9]);
          }
        }
        if ($model == 'buy_customer_model' || $model == 'rent_customer_model') {
          //户型
          if ($arr[9]) {
            $excelData[$array][9] = mb_substr($excelData[$array][9], 0, 1);
          }
        }
        if ($model == 'sell_model' || $model == 'rent_house_model') {
          //合并楼层
          if ($arr[11] || $arr[12]) {
            $excelData[$array][11] = $arr[11] . '/' . $arr[12];
          }
          //装修
          if ($arr[13] == '简单装修') {
            $excelData[$array][13] = $fitment[2];
          } else if ($arr[13] == '中等装修') {
            $excelData[$array][13] = $fitment[3];
          } else if ($arr[13] == '高装') {
            $excelData[$array][13] = $fitment[4];
          } else if ($arr[13] == '豪华装修') {
            $excelData[$array][13] = $fitment[5];
          }
          //标题
          $excelData[$array][25] = $arr[0];
          //委托类型转化
          if ($arr[20] == '独家委托') {
            if ($model == 'sell_model') {
              $excelData[$array][20] = $entrust[1];
            } else {
              $excelData[$array][20] = $entrust[3];
            }
          }
        }
        //状态
        if (!$arr[7]) {
          $excelData[$array][7] = '有效';
        }
        //默认区属为海陵
        $excelData[$array][22] = '海陵';
        //登记时间
        $excelData[$array][31] = str_replace('年', '-', $excelData[$array][31]);
        $excelData[$array][31] = str_replace('月', '-', $excelData[$array][31]);
        $excelData[$array][31] = strtotime($excelData[$array][31]);
        //楼盘地址拼接
        if (!$excelData[$array][24]) {
          $excelData[$array][24] = $excelData[$array][22] . $excelData[$array][23] . $excelData[$array][0];
        }
        //$excelData[$array][24] = str_replace(',','/',$excelData[$array][24]);
        if ($this->$model->checkarr_taizhou($excelData[$array]) == 'pass') {
          $datas[] = $excelData[$array];
        } else {
          $datas_fail[$array] = $this->$model->checkarr_taizhou($excelData[$array]);
        }
        //print_r($excelData[$array]);exit;
      }
      //print_r($datas_fail);exit;
      //print_r($excelData);exit;
      if (!empty($datas) && empty($datas_fail)) {
        $res = array('broker_id' => $broker_id);
        $this->$model->del($res, 'db_city', 'tmp_uploads');
        $data = array('broker_id' => $broker_id,
          'content' => serialize($datas),
          'createtime' => time()
        );
        $id = $this->$model->add_data($data, 'db_city', 'tmp_uploads');
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源</p>'
        .'<input type="hidden" id=tmp_id value='.$id.'>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_s">上传成功</span>，共上传'.count($datas).'条房源。</p>'
        .'<input type="hidden" id=tmp_id value='.$id.'>';*/

        return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"> <style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_s">上传成功</span>，共上传' . count($datas) . '条信息。</p>'
        . '<input type="hidden" id=tmp_id value=' . $id . '></body></html>';
      } else {
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        $fail_html = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"> <style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，<a href="javascript:void(0);" onclick="window.parent.see_reason();" color="#227ac6">点击查看失败原因</a></p>';
        if ($type == 1) {
          $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符、楼盘名称在楼盘表中搜索不到、房源标题超过30字、新增楼盘请按照标准填写(区属、板块和地址,如区属板块没有请先自行添加后再导入)。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        } else if ($type == 2) {
          $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        }
        foreach ($datas_fail as $key => $vo) {
          $fail_html .= '<tr><td class="up_m_b_date_up" style="text-align: center">第' . $key . '行</td><td class="up_m_b_date_up" style="text-align:left;padding:0px 15px">';
          foreach ($vo as $kk => $vv) {
            foreach ($example_title as $k => $v) {
              if ($vv == $k) {
                $fail_html .= '<span class="up_e">' . $v . '</span>,';
              }
            }
          }
          $fail_html = substr($fail_html, 0, -1);
          $fail_html .= '</td></tr>';
        }
        $fail_html .= '</table></body></html>';
        return $fail_html;
      }
    } else {
      return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"> <style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，一次最多只能导入1000条记录哦！</p></body></html>';
    }

  }

  public function broker_read($upload, $i)
  {
    $this->load->model('broker_model');
    $filename = 'temp/' . $upload['file_name'];
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

      $excelTitle = array();

      if (is_full_array($excelData)) {
        $res = array('broker_id' => 0);
        $this->broker_model->del($res, 'db_city', 'tmp_uploads');
        $data = array('broker_id' => 0,
          'content' => serialize($excelData),
          'createtime' => time()
        );
        $id = $this->broker_model->add_data($data, 'db_city', 'tmp_uploads');

        return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_s">上传成功</span>，共上传' . count($excelData) . '条信息。</p>'
        . '<input type="hidden" id=tmp_id value=' . $id . '></body></html>';
      } else {
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        /*return '<link type="text/css" rel="stylesheet" href="'.MLS_SOURCE_URL.'/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css"><p class="up_m_b_date_up" style="text-align: center">'.$upload['client_name'].'<span class="up_e">上传失败</span>，请按照标准模板重新上传</p>';*/
        $fail_html = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，<a href="javascript:void(0);" onclick="window.parent.see_reason();" color="#227ac6">点击查看失败原因</a></p>';
        $fail_html .= '<b class="up_m_b_date_up" style="text-align: left;display:none">错误编号为导入表格的实际行数，所列错误项存在以下几种情况：数据为空、格式与模版不符、楼盘名称在楼盘表中搜索不到、房源标题超过30字、新增楼盘请按照标准填写(区属、板块和地址)。请仔细核对后再次导入！</b><table style="width:100%;display:none" border="1px" cellpadding="2" cellspacing="0"><tr><td style="text-align:center;width:15%">错误行</td><td style="text-align:center;width:320px">失败项</td>';
        $fail_html .= '</table></body></html>';
        return $fail_html;
      }
    } else {
      return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center">' . $upload['client_name'] . '<span class="up_e">上传失败</span>，一次最多只能导入1000条记录哦！</p></body></html>';
    }

  }

  public function community_read($file_name)
  {
    //print_r($this->dbback_city);
    ini_set("memory_limit", "1024M"); //excel文件大小
    //$filename = 'temp/xq.xls';//excel文件名
    $filename = UPLOADS . '/temp/' . $file_name;//excel文件名
    $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    $objReader = IOFactory::createReaderForFile($filename);
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);//指定的文件
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
    $valid_num = intval($highestRow);
    $highestColumn = $objWorksheet->getHighestColumn();// 取得总列数
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $excelData = array();
    for ($row = 0; $row <= $highestRow; $row++) {
      for ($col = 0; $col < $highestColumnIndex; $col++) {
        $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
      }
    }
    $this->load->model('community_model');
    $this->load->model('district_model');
    $fail_html = '';
    $sccessCount = 0;
    $fail_type = array(0, 0, 0, 0);
    for ($j = 7; $j <= $highestRow; $j++) {
      if ($excelData[$j]['0'] == '') {
        $fail_type[0] = 1;
      }
      if ($excelData[$j]['1'] == '') {
        $fail_type[1] = 1;
      }
      if ($excelData[$j]['2'] == '') {
        $fail_type[2] = 1;
      }
      if ($excelData[$j]['3'] == '') {
        $fail_type[3] = 1;
      }
    }
    if (in_array(1, $fail_type)) {
      $fail_html .= '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center;line-height: 34px;margin:0;">' . '<span class="up_e">上传失败</span><span>';
      if ($fail_type[0]) {
        $fail_html .= ',楼盘名称';
      }
      if ($fail_type[1]) {
        $fail_html .= ',区属';
      }
      if ($fail_type[2]) {
        $fail_html .= ',板块';
      }
      if ($fail_type[3]) {
        $fail_html .= ',楼盘地址';
      }
      $fail_html .= '不能为空。</span></p></body></html>';
      return $fail_html;
    }
    if ($fail_html === '') {
      for ($k = 7; $k <= $highestRow; $k++) {
        $dist = $this->district_model->get_district_by_district($excelData[$k][1]);
        $street = $this->district_model->get_street_by_street($excelData[$k][2]);
        $communityData = array(
          'cmt_name' => $excelData[$k][0],
          'dist_id' => $dist[0]['id'],
          'streetid' => $street[0]['id'],
          'address' => $excelData[$k][3],
          'build_type' => $excelData[$k][4],
          'build_date' => $excelData[$k][5],
          'buildarea' => $excelData[$k][6],
          'deliver_date' => $excelData[$k][7],
          'coverarea' => $excelData[$k][8],
          'property_company' => $excelData[$k][9],
          'developers' => $excelData[$k][10],
          'parking' => $excelData[$k][11],
          'green_rate' => $excelData[$k][12],
          'plot_ratio' => $excelData[$k][13],
          'property_fee' => $excelData[$k][14],
          'build_num' => $excelData[$k][15],
          'total_room' => $excelData[$k][16],
        );
        $is_exist = $this->community_model->getcommunity(array('cmt_name' => $communityData['cmt_name']));
        if (is_array($is_exist) && !empty($is_exist)) {
          //如果已存在楼盘
        } else {
          $this->community_model->add_community($communityData);
          $sccessCount++;
        }
      }
      return '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="' . MLS_SOURCE_URL . '/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css">
<style>*{ background: transparent !important;}</style></head><body style="background: transparent"><p class="up_m_b_date_up" style="text-align: center;line-height: 34px;">' . '<span class="up_s">上传成功</span>，共上传' . $sccessCount . '条信息。</p>'
      . '</body></html>';
    }
  }

}

?>
