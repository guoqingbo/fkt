<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-个人资料
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Entrust extends MY_Controller
{
  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('entrust_model');
  }

  public function index()
  {
    $data = array();
    $data['user_menu'] = $this->user_menu;

    $post_url = 'http://192.168.105.241/api/esf/web/esfsell/index.php';

    $broker_info = $this->user_arr;
    $city = $broker_info['city_spell'];
    //获取区属
    $str = $post_url . '?method=block.getDistrict&city=' . $city;
    $district = json_decode(curl_get_contents($str), true);
    if ($district['result'] == 1) {
      $data['district'] = $district['data'];
    } else {
      $data['district'] = '';
    }

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    if ($post_param['district']) {
      $url = $post_url . '?method=block.getStreet&city=' . $city . '&district=' . $post_param['district'];
      $street = json_decode(curl_get_contents($url), true);
      if ($street['result'] == 1) {
        $data['street'] = $street['data'];
      } else {
        $data['street'] = '';
      }
    }
    //print_r($data['street']);exit;
    $post_param['method'] = 'sell.sellList';
    $post_param['city'] = $city;
    $post_param['type'] = '1';
    $post_param['pagesize'] = '15';
    if (empty($post_param['page'])) {
      $post_param['page'] = 1;
    }
    $entrusts = json_decode(vpost($post_url, $post_param), true);
    if ($entrusts['result'] == 1) {
      $entrust_total = $entrusts['data']['total'];
      $data['entrust_list'] = $entrusts['data']['data'];
    } else {
      $entrust_total = 0;
      $data['entrust_list'] = '';
    }
    //$data['entrust_list'] = $this->entrust_model->get_all_entrust_by($where);
    if (is_full_array($data['entrust_list'])) {
      foreach ($data['entrust_list'] as $key => $value) {
        $house_info = $this->entrust_model->get_entrust_id_by_houseid($value['id']);
        if (is_full_array($house_info)) {
          $data['entrust_list'][$key]['num'] = $house_info['num'];
          $data['entrust_list'][$key]['remain_num'] = 10 - $house_info['num'];
        } else {
          $data['entrust_list'][$key]['num'] = 0;
          $data['entrust_list'][$key]['remain_num'] = 10;
        }

        /*$pic_arr = $this->entrust_model->get_entrust_pic_by_houseid($value['id']);
        $picstr = '';
        if(is_full_array($pic_arr)){
            foreach($pic_arr as $v){
                $picstr .= $v['picurl'];
            }
        }
        $data['entrust_list'][$key]['pics'] = $value['pics'].$picstr;*/

      }
    }
    unset($house_info);
    //print_r($data['entrust_list']);exit;
    $params = array(
      'total_rows' => $entrust_total, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $post_param['page'],//当前页数
      'list_rows' => $post_param['pagesize'],//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '委托房源列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/backspace.js');
    $this->view('entrust/entrust', $data);
  }

  /*
   * 已抢房源
   */
  public function my_entrust()
  {
    $data = array();
    $post_param = array();
    $pagesize = 15;
    $data['user_menu'] = $this->user_menu;

    $broker_info = $this->user_arr;
    $city = $broker_info['city_spell'];

    $page = $this->input->post('page', TRUE);
    if (empty($page)) {
      $page = 1;
    }
    $start = ($page - 1) * $pagesize;
    $broker_id = $broker_info['broker_id'];
    $post_url = 'http://192.168.105.241/api/esf/web/esfsell/index.php';
    $post_param['method'] = 'sell.getHouseInfo';
    $post_param['city'] = $city;
    $my_entrust_total = $this->entrust_model->entrust_count_by('brokerid = ' . $broker_id);
    $data['my_entrust_list'] = $this->entrust_model->get_my_entrust_by($broker_id, $start, $pagesize);

    if (is_full_array($data['my_entrust_list'])) {
      foreach ($data['my_entrust_list'] as $key => $value) {
        $post_param['fid'] = $value['houseid'];
        $house_info = $this->entrust_model->get_entrust_id_by_houseid($value['houseid']);
        if ($house_info && $house_info['num']) {
          $data['my_entrust_list'][$key]['num'] = $house_info['num'];
        } else {
          $data['my_entrust_list'][$key]['num'] = 0;
        }
        $data['my_entrust_list'][$key]['appraise_total'] = $this->entrust_model->appraise_count_by('houseid = ' . $value['houseid']);
        $my_entrusts = json_decode(vpost($post_url, $post_param), true);
        if ($my_entrusts['result'] == 1) {
          $data['my_entrust_list'][$key]['district_street'] = $my_entrusts['data']['district_street'];
          $data['my_entrust_list'][$key]['blockname'] = $my_entrusts['data']['blockname'];
          $data['my_entrust_list'][$key]['housetype'] = $my_entrusts['data']['housetype'];
          $data['my_entrust_list'][$key]['buildarea'] = $my_entrusts['data']['buildarea'];
          $data['my_entrust_list'][$key]['price'] = $my_entrusts['data']['price'];
          $data['my_entrust_list'][$key]['pics'] = $my_entrusts['data']['pics'];
          $data['my_entrust_list'][$key]['status'] = $my_entrusts['data']['status'];
        }
      }
    }
    //print_r($data['my_entrust_list']);exit;
    $params = array(
      'total_rows' => $my_entrust_total, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $pagesize,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');
    //页面标题
    $data['page_title'] = '已抢房源';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/house.js,mls/js/v1.0/openWin.js,mls/js/v1.0/personal_center.js,mls/js/v1.0/backspace.js');

    $this->view('entrust/my_entrust', $data);
  }

  //已委托房源详情
  public function entrust_detail($house_id)
  {
    $data = array();
    $pic_arr = array();
    /*$pic_arr1 = array();
    $pic_arr2 = array();*/
    $pagesize = 2;
    $data['user_menu'] = $this->user_menu;

    $broker_info = $this->user_arr;
    $city = $broker_info['city_spell'];
    $broker_id = $broker_info['broker_id'];

    $page = $this->input->post('page', TRUE);
    if (empty($page)) {
      $page = 1;
    }
    $start = ($page - 1) * $pagesize;

    $post_url = 'http://192.168.105.241/api/esf/web/esfsell/index.php';
    $post_param['method'] = 'sell.getHouseInfo';
    $post_param['city'] = $city;
    $post_param['fid'] = $house_id;
    $my_entrust_detail = json_decode(vpost($post_url, $post_param), true);
    if ($my_entrust_detail['result'] == 1) {
      $data['entrust_detail'] = $my_entrust_detail['data'];
      /*if($data['entrust_detail']['pics']){
          $pic_arr1 = explode(',',$data['entrust_detail']['pics']);
      }*/
      if ($data['entrust_detail']['pics']) {
        $pic_arr = explode(',', $data['entrust_detail']['pics']);
      }
    } else {
      $data['entrust_detail'] = '';
    }
    /*$pic_arr = $this->entrust_model->get_entrust_pic_by_houseid($house_id);
    if(is_full_array($pic_arr)){
        $pics = '';
        foreach($pic_arr as $v){
            $pics .= $v['picurl'];
        }
        $pic_arr2 = explode(',',$pics);
    }
    $data['pic_arr'] = array_filter(array_merge($pic_arr1,$pic_arr2));*/
    //print_r($data['pic_arr']);exit;
    $data['pic_arr'] = array_filter($pic_arr);
    $mypic_arr = $this->entrust_model->get_entrust_mypic($house_id, $broker_id);
    if (is_full_array($mypic_arr)) {
      $data['mypic_arr'] = array_filter(explode(',', $mypic_arr['picurl']));
    }

    if (is_full_array($data['pic_arr'])) {
      $data['pic_len'] = count($data['pic_arr']);
//            $data['pic_default'] = str_replace('thumb/','',str_replace('_130x100','',$data['pic_arr'][0]));
      $data['pic_default'] = changepic(str_replace('_130x100', '', $data['pic_arr'][0]));
    } else {
      $data['pic_len'] = 0;
      $data['pic_default'] = MLS_SOURCE_URL . '/mls/images/v1.0/w_pic.png';
    }
    //echo $data['pic_len'];exit;
    $data['houseid'] = $house_id;
    $house_info = $this->entrust_model->get_entrust_id_by_houseid($house_id);
    if (is_full_array($house_info)) {
      $data['entrust_detail']['num'] = $house_info['num'];
      $data['entrust_detail']['remain_num'] = 10 - $house_info['num'];
    } else {
      $data['entrust_detail']['num'] = 0;
      $data['entrust_detail']['remain_num'] = 10;
    }
    unset($house_info);

    $receive = $this->entrust_model->get_entrust_receive_by_houseid($house_id, $broker_id);
    //是否是已抢拍房源
    if (is_full_array($receive)) {
      $data['entrust_detail']['id'] = $receive['id'];
      $data['entrust_detail']['receive'] = $receive['receive'];
    } else {
      $data['entrust_detail']['id'] = 0;
    }

    $data['entrust_total'] = $this->entrust_model->entrust_count_by('houseid = ' . $house_id);
    $data['broker_list'] = $this->entrust_model->get_entrust_broker_by_houseid($house_id);
    $data['appraise_total'] = $this->entrust_model->appraise_count_by('houseid = ' . $house_id);
    $appraise_list = $this->entrust_model->get_entrust_appraise_by_houseid($house_id, $start, $pagesize);
    if (is_full_array($appraise_list)) {
      $this->load->model('broker_info_model');
      $this->broker_info_model->set_select_fields(array('truename', 'photo'));
      foreach ($appraise_list as $key => $value) {
        $brokerinfo_arr = $this->broker_info_model->get_by_broker_id($value['brokerid']);
        $appraise_list[$key]['truename'] = $brokerinfo_arr['truename'];
        if ($brokerinfo_arr['photo']) {
          $appraise_list[$key]['photo'] = $brokerinfo_arr['photo'];
        } else {
          $appraise_list[$key]['photo'] = MLS_SOURCE_URL . '/mls/images/v1.0/grzx/grtx.gif';
        }
      }
    }
    $data['my_appraise'] = $this->entrust_model->get_entrust_appraise_by_houseid_brokerid($house_id, $broker_id);
    //print_r($data['my_appraise']);exit;
    $data['appraise_list'] = $appraise_list;

    $params = array(
      'total_rows' => $data['appraise_total'], //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page,//当前页数
      'list_rows' => $pagesize,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '已抢房源';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/openWin.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js,mls/js/v1.0/Marquee.js');

    $this->view('entrust/entrust_detail', $data);
  }

  //板块
  public function street($dist_name)
  {
    if ($dist_name) {
      $broker_info = $this->user_arr;
      $city = $broker_info['city_spell'];

      $str = 'http://192.168.105.241/api/esf/web/esfsell/index.php?method=block.getStreet&city=' . $city . '&district=' . $dist_name;
      $street = json_decode(curl_get_contents($str), true);
      if ($street['result'] == 1) {
        if (is_full_array($street['data'])) {
          echo json_encode($street['data']);
        }
      }
    }
  }

  //录入房源评价
  public function appraise()
  {
    $houseid = $this->input->post('houseid', TRUE);
    $brokerid = $this->user_arr['broker_id'];
    $insert_data = array();
    if (is_numeric($houseid)) {
      $my_appraise = $this->entrust_model->get_entrust_appraise_by_houseid_brokerid($houseid, $brokerid);
      if (is_full_array($my_appraise)) {
        echo '{"status":"failed","msg":"您已经对该房源评价过了，不能再次评价"}';
        exit;
      } else {
        $insert_data['houseid'] = $houseid;
        $insert_data['brokerid'] = $brokerid;
        $insert_data['dateline'] = time();
        //post参数
        $insert_data['appraise'] = $this->input->post('appraise', TRUE);
        $appraise_id = $this->entrust_model->set_entrust_appraise($insert_data);
        if ($appraise_id) {
          echo '{"status":"success","msg":"房源评价录入成功"}';
        } else {
          echo '{"status":"failed","msg":"房源评价录入失败"}';
        }
      }
    } else {
      echo '{"status":"failed","msg":"参数非法"}';
      exit;
    }
  }

  //上传房源图片
  public function entrust_pic()
  {
    $houseid = $this->input->post('houseid', TRUE);
    if (is_numeric($houseid)) {
      $photo_url = $this->input->post('photo_url', TRUE);
      $photo_len = substr_count($photo_url, ',');
      if ($photo_len > 10) {
        echo '{"status":"failed","msg":"图片超过10张,请筛选后上传"}';
      } else {
        $brokerid = $this->user_arr['broker_id'];
        $my_pic = $this->entrust_model->get_entrust_mypic($houseid, $brokerid);
        if (is_full_array($my_pic)) {
          $update_data = array();
          $update_data['dateline'] = time();
          //post参数
          $update_data['picurl'] = $photo_url;
          $rows = $this->entrust_model->update_entrust_pic($update_data, $houseid, $brokerid);
          if ($rows) {
            echo '{"status":"success","msg":"图片录入成功"}';
          } else {
            echo '{"status":"failed","msg":"图片更新失败"}';
          }
        } else {
          $insert_data = array();
          $insert_data['houseid'] = $houseid;
          $insert_data['brokerid'] = $brokerid;
          $insert_data['dateline'] = time();
          //post参数
          $insert_data['picurl'] = $photo_url;
          $pic_id = $this->entrust_model->set_entrust_pic($insert_data);
          if ($pic_id) {
            echo '{"status":"success","msg":"图片录入成功"}';
          } else {
            echo '{"status":"failed","msg":"图片录入失败"}';
          }
        }
      }
    } else {
      echo '{"status":"failed","msg":"参数非法"}';
      exit;
    }
  }

  //抢拍房源
  public function add_entrust_broker()
  {
    $houseid = $this->input->post('houseid', TRUE);
    $insert_data = array();
    if (is_numeric($houseid)) {
      $entrust_house = $this->entrust_model->get_entrust_id_by_houseid($houseid);
      if (is_full_array($entrust_house) && $entrust_house['num'] < 10) {
        $insert_data['houseid'] = $houseid;
        $insert_data['brokerid'] = $this->user_arr['broker_id'];
        $insert_data['dateline'] = time();
        //post参数
        $insert_data['receive'] = $this->input->post('receive', TRUE);
        $broker_id = $this->entrust_model->set_entrust_broker($insert_data);//插入已抢拍表
        if ($broker_id) {
          //判断此委托房源本库里有没有：true：更新num抢拍次数 false：增加记录num为1
          $house_arr = $this->entrust_model->get_entrust_id_by_houseid($houseid);
          if ($house_arr) {
            $this->entrust_model->update_entrust_house_by_id(array('num' => $house_arr['num'] + 1), $house_arr['id']);
          } else {
            $this->entrust_model->set_entrust_house(array('houseid' => $houseid, 'state' => 1, 'num' => 1, 'dateline' => $insert_data['dateline']));
          }
          echo '{"status":"success","msg":"抢拍成功"}';
        } else {
          echo '{"status":"failed","msg":"抢拍失败"}';
        }
      } else {
        echo '{"status":"failed","msg":"已经被抢光了，您下次早点来"}';
        exit;
      }
    } else {
      echo '{"status":"failed","msg":"参数非法"}';
      exit;
    }
  }

  //更新房源评价
  public function appraise_update()
  {
    $houseid = $this->input->post('houseid', TRUE);
    $brokerid = $this->user_arr['broker_id'];
    $update_data = array();
    if (is_numeric($houseid)) {
      $update_data['dateline'] = time();
      //post参数
      $update_data['appraise'] = $this->input->post('appraise_update', TRUE);
      $appraise_row = $this->entrust_model->update_entrust_appraise($update_data, $houseid, $brokerid);
      if ($appraise_row) {
        echo '{"status":"success","msg":"房源评价修改成功"}';
      } else {
        echo '{"status":"failed","msg":"房源评价修改失败"}';
      }
    } else {
      echo '{"status":"failed","msg":"参数非法"}';
      exit;
    }
  }

  //更新抢拍-认领房源
  public function update_entrust_broker()
  {
    $id = $this->input->post('id', TRUE);
    $update_data = array();
    if (is_numeric($id)) {
      //post参数
      $update_data['receive'] = $this->input->post('receive', TRUE);
      $broker_id = $this->entrust_model->update_entrust_broker_by_id($update_data, $id);
      if ($broker_id) {
        if ($update_data['receive'] == 1) {
          echo '{"status":"success","msg":"认领成功"}';
        } else {
          echo '{"status":"success","msg":"取消认领成功"}';
        }
      } else {
        echo '{"status":"failed","msg":"操作失败"}';
      }
    } else {
      echo '{"status":"failed","msg":"参数非法"}';
      exit;
    }
  }
}
/* End of file entrust.php */
/* Location: ./applications/mls/controllers/entrust.php */
