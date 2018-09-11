<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 积分--商品管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Gift_manage extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('gift_manage_model');
    $this->load->helper('page_helper');
    $this->load->model('city_model');
  }

  //商品管理页
  public function index($type)
  {
    $data_view = array();
    $pg = $this->input->post('pg');
    //商品类型
    //$type = $this->input->post('type');
    //商品名称及编号的查询条件
    $search_where = $this->input->post('search_where');
    $search_value = trim($this->input->post('search_value'));
    //上下架
    $status = $this->input->post('status');
    //时间的查询
    $search_where_time = $this->input->post('search_where_time');
    $time_s = strtotime($this->input->post('time_s'));
    $time_e = strtotime($this->input->post('time_e')) + 86399;
    $where = 'id > 0 and status != 3 ';
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
    }
    if ($type) {
      $where .= ' and type =' . $type;
    }
    if ($status) {
      $where .= ' and status =' . $status;
    }
    if ($search_where_time && $time_s && $time_e) {
      $where .= ' and ' . $search_where_time . '>= "' . $time_s . '"';
      $where .= ' and ' . $search_where_time . '<= "' . $time_e . '"';
    }
    //echo $where;
    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value, 'status' => $status, 'search_where_time' => $search_where_time, 'time_s' => $time_s, 'time_e' => $time_e, 'type' => $type
    );
    //分页开始
    $data_view['count'] = $this->gift_manage_model->count_by($where);
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //公司列表
    $data_view['gift'] = $this->gift_manage_model->get_all_by(
      $where, $data_view['offset'], $data_view['pagesize']);
    $data_view['title'] = '商品管理';
    $data_view['conf_where'] = 'index';
    $this->load->view('gift_manage/index', $data_view);
  }

  //添加商品
  public function add($type)
  {
    $data['title'] = '添加商品';
    $data['conf_where'] = 'index';
    $data['type'] = $type;
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    $img_arr = $this->input->post('p_filename2');
    $surface_img = $img_arr[0];
    //获取城市
    $data['this_user'] = $_SESSION[WEB_AUTH];
    $city_spell = $_SESSION[WEB_AUTH]['city'];
    $city_id_data = $this->city_model->get_city_by_spell($city_spell);
    $city_id = $city_id_data['id'];
    $data['this_city'] = $this->city_model->get_by_id($city_id);
    //从数据库中得到最新一条数据的ID，在此基础上加1
    $new = $this->gift_manage_model->get_one_new_by(
      $where = '', $data_view['offset'], $data_view['pagesize']);
    if (empty($new)) {
      $new_data['id'] = 1;
    } else {
      $new_data['id'] = $new[0]['id'] + 1;
    }
    if ('add' == $submit_flag) {
      if ($type == 1) {
        $paramArray = array(
          'product_name' => trim($this->input->post('product_name')),
          'score' => $this->input->post('score'),
          'product_picture' => $surface_img,//商品图片
          'product_detail' => trim($this->input->post('product_detail')),
          'attention_matter' => trim($this->input->post('attention_matter')),
          'order' => trim($this->input->post('order')),
          'status' => 1,
          'release_time' => time(),
          'down_time' => strtotime($this->input->post('down_time')),//下架时间可不填
          'stock' => trim($this->input->post('stock')),//库存必填
          //商品编号
          'product_serial_num' => $data['this_city']['spell'] . sprintf("%05d", $new_data['id'])
        );
        $data['paramArray'] = $paramArray;
        if (preg_match('/^[1-9]\d*$/', $paramArray['score'])) {
          if (preg_match('/^(0|[1-9]\d*)$/', $paramArray['stock'])) {
            if (!empty($paramArray['product_name']) && !empty($paramArray['product_detail']) && !empty($paramArray['product_picture']) && !empty($paramArray['down_time'])) {
              $addResult = $this->gift_manage_model->add_product_data($paramArray);
            } else {
              $data['mess_error'] = '带*为必填字段';
            }
          } else {
            $data['mess_error'] = '库存只能是正整数';
          }
        } else {
          $data['mess_error'] = '积分值只能是正整数';
        }
      } elseif ($type == 2) {
        $paramArray = array(
          'product_name' => trim($this->input->post('product_name')),
          'score' => '500',
          'product_picture' => $surface_img,//商品图片
          'order' => intval(trim($this->input->post('order'))),
          'status' => 1,
          'type' => $type,
          'release_time' => time(),
          'down_time' => time(),
          'stock' => trim($this->input->post('stock')),//库存必填
          'raffle_num' => trim($this->input->post('raffle_num')),//每月中奖限额
          'rate' => trim($this->input->post('rate')),
          //商品编号
          'product_serial_num' => $data['this_city']['spell'] . sprintf("%05d", $new_data['id'])
        );
        $data['paramArray'] = $paramArray;
        if (!empty($paramArray['product_name']) && !empty($paramArray['product_picture']) && !empty($paramArray['order'])) {
          if ($paramArray['order'] > 0 && $paramArray['order'] < 11 && is_int($paramArray['order'])) {
            $order_array = $this->gift_manage_model->get_raffle_order();
            if (count($order_array) < 10) {
              if (!in_array($paramArray['order'], $order_array)) {
                if ($paramArray['order'] != 3 && $paramArray['order'] != 8) {
                  $rate_total = $this->gift_manage_model->get_raffle_rate();
                  if (($paramArray['rate'] + $rate_total) <= 1) {
                    $addResult = $this->gift_manage_model->add_product_data($paramArray);
                  } else {
                    $data['mess_error'] = '现有商品中奖率总和为' . $rate_total . ',此商品不能大于' . (1 - $rate_total);
                  }
                } else {
                  //$data['mess_error'] = '此序号为“谢谢参与”专属，请另选序号添加！';
                  $paramArray['rate'] = 0;
                  $paramArray['stock'] = 1;
                  if ($paramArray['order'] == 3) {
                    $paramArray['score'] = 100;
                  } else {
                    $paramArray['score'] = 200;
                  }
                  $addResult = $this->gift_manage_model->add_product_data($paramArray);
                }
              } else {
                $data['mess_error'] = '排序序号已被占用，请修改！';
              }
            } else {
              $data['mess_error'] = '有效商品数量已达上限10个，请下架其他商品后再来添加！';
            }
          } else {
            $data['mess_error'] = '商品排序请填1~10之间的整数！';
          }
        } else {
          $data['mess_error'] = '带*为必填字段！';
        }
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('gift_manage/add', $data);
  }

  /**
   * 修改公司信息页面
   * @param int $company_id 公司编号
   */
  public function edit($id, $type)
  {
    $data = array();
    $data['title'] = "修改商品管理信息";
    $data['type'] = $type;
    $modifyResult = "";
    $list = $this->gift_manage_model->get_by_id($id);
    $data['list'] = $list;
    $data['this_city'] = $this->city_model->get_by_id($city_id);
    $submit_flag = $this->input->post('submit_flag', true);
    if ($submit_flag == "edit") {
      if ($type == 1) {
        $updatearray = array(
          'product_name' => trim($this->input->post('product_name')),
          'score' => $this->input->post('score'),
          'product_picture' => trim($this->input->post('photopic')),//商品图片
          'product_detail' => trim($this->input->post('product_detail')),
          'attention_matter' => trim($this->input->post('attention_matter')),
          'order' => trim($this->input->post('order')),
          'down_time' => strtotime($this->input->post('down_time')),
          'stock' => trim($this->input->post('stock'))
          //商品编号
          //'product_serial_num' => $data['this_city']['spell'].rand(00001,99999)
        );
        if (preg_match('/^[1-9]\d*$/', $updatearray['score'])) {
          if (preg_match('/^(0|[1-9]\d*)$/', $updatearray['stock'])) {
            if (!empty($updatearray['product_name']) && !empty($updatearray['product_detail']) && !empty($updatearray['product_picture']) && !empty($updatearray['down_time'])) {
              $modifyResult = $this->gift_manage_model->update_status_by_id($id, $updatearray);
              if ($modifyResult) {
                echo json_encode(array('result' => '1', 'msg' => '修改成功'));
                exit;
                //echo "修改成功";exit;
              } else {
                echo json_encode(array('result' => '-1', 'msg' => '修改失败'));
                exit;
                //echo '修改失败'; exit;
              }
            } else {
              echo json_encode(array('result' => '0', 'msg' => '带*为必填字段'));
              exit;
              //$data['mess_error'] = '带*为必填字段';
            }
          } else {
            echo json_encode(array('result' => '0', 'msg' => '库存只能是正整数'));
            exit;
            //$data['mess_error'] = '库存只能是正整数';
          }
        } else {
          echo json_encode(array('result' => '0', 'msg' => '积分值只能是正整数'));
          exit;
          //$data['mess_error'] = '积分值只能是正整数';
        }
      } elseif ($type == 2) {
        $updatearray = array(
          'product_name' => trim($this->input->post('product_name')),
          'product_picture' => trim($this->input->post('photopic')),//商品图片
          'order' => intval(trim($this->input->post('order'))),
          'rate' => trim($this->input->post('rate')),
          'stock' => trim($this->input->post('stock')),//库存必填
          'raffle_num' => trim($this->input->post('raffle_num')),//每月中奖限额
        );
        if (!empty($updatearray['product_name']) && !empty($updatearray['product_picture']) && !empty($updatearray['order'])) {
          if ($updatearray['order'] > 0 && $updatearray['order'] < 11 && is_int($updatearray['order'])) {
            $order_array = $this->gift_manage_model->get_raffle_order();
            if (in_array($updatearray['order'], $order_array) && $list['order'] != $updatearray['order']) {
              echo json_encode(array('result' => '0', 'msg' => '排序序号已被占用，请修改'));
              exit;
            } else {
              if ($updatearray['order'] != 3 && $updatearray['order'] != 8) {
                $rate_total = $this->gift_manage_model->get_raffle_rate();
                if (($updatearray['rate'] + $rate_total - $list['rate']) <= 1) {
                  $modifyResult = $this->gift_manage_model->update_status_by_id($id, $updatearray);
                  if ($modifyResult) {
                    echo json_encode(array('result' => '1', 'msg' => '修改成功'));
                    exit;
                  } else {
                    echo json_encode(array('result' => '-1', 'msg' => '修改失败'));
                    exit;
                  }
                } else {
                  echo json_encode(array('result' => '0', 'msg' => '现有商品中奖率总和为' . $rate_total . ',此商品不能大于' . (1 + $list['rate'] - $rate_total)));
                  exit;
                }
              } else {
                //echo json_encode(array('result'=>'0','msg'=>'此序号为“谢谢参与”专属，请另选序号修改！'));exit;
                $updatearray['rate'] = 0;
                $updatearray['stock'] = 1;
                $modifyResult = $this->gift_manage_model->update_status_by_id($id, $updatearray);
              }
            }
          } else {
            echo json_encode(array('result' => '0', 'msg' => '商品排序请填1~10之间的整数！'));
            exit;
          }
        } else {
          echo json_encode(array('result' => '0', 'msg' => '带*为必填字段'));
          exit;
        }
      }
    }

    $data['modifyResult'] = $modifyResult;
    $this->load->view('gift_manage/modify', $data);
  }

  /**
   * 删除商品
   * @param int $id 商品ID
   */
  public function delete($id)
  {
    $data_view = array();
    $data_view['deleteResult'] = '';
    $data_view['title'] = '商品管理-删除商品';
    $data_view['conf_where'] = 'index';
    $data = $this->gift_manage_model->delete_by_id($id);
    if ($data) {
      $data_view['deleteResult'] = 1;
    } else {
      $data_view['deleteResult'] = 2;
    }
    $this->load->view('gift_manage/del', $data_view);
  }

  /**
   * 修改上下架状态
   * $id 商品ID
   */
  public function status($id, $type)
  {
    $data_view = array();
    $data_view['statusResult'] = '';
    $data_view['title'] = '商品上下架状态修改';
    $data_view['conf_where'] = 'index';
    if ($type == 1) {
      $data = $this->gift_manage_model->update_status_by_id($id, $paramlist = array('status' => $type, 'release_time' => time()));
    } else {
      $data = $this->gift_manage_model->update_status_by_id($id, $paramlist = array('status' => $type, 'down_time' => time()));
    }

    if ($data && $type == 1) {
      $data_view['statusResult'] = 1;
    } elseif ($data && $type == 2) {
      $data_view['statusResult'] = 2;
    } else {
      $data_view['statusResult'] = 0;
    }
    $this->load->view('gift_manage/status', $data_view);
  }

  //上架时要重新选择下架时间
  public function modify_status($id, $type)
  {
    $data_view = array();
    $submit_flag = $this->input->post('submit_flag');
    $data_view['modifyResult'] = '';
    $data_view['type'] = $type;
    if ($submit_flag == 'modify') {
      if ($type == 1) {
        $this->load->library('form_validation');//表单验证
        $this->form_validation->set_rules('down_time', 'down_time', 'required');
        //获取参数
        $down_time = strtotime($this->input->post('down_time'));
        if ($this->form_validation->run() === true) {
          $modifyResult = $this->gift_manage_model->update_status_by_id($id, $updatearray = array('down_time' => $down_time, 'status' => '1'));
          $data_view['modifyResult'] = $modifyResult;
        } else {
          $data_view['mess_error'] = '带 * 为必填字段';
        }
      } elseif ($type == 2) {
        $gift = $this->gift_manage_model->get_by_id($id);
        $order_array = $this->gift_manage_model->get_raffle_order();
        if (count($order_array) < 10) {
          if (!in_array($gift['order'], $order_array)) {
            $rate_total = $this->gift_manage_model->get_raffle_rate();
            if (($gift['rate'] + $rate_total) <= 1) {
              $modifyResult = $this->gift_manage_model->update_status_by_id($id, $updatearray = array('status' => '1'));
              $data_view['modifyResult'] = $modifyResult;
            } else {
              $data_view['mess_error'] = '如此商品上架，整体商品中奖率将超过1，请重新编辑';
            }
          } else {
            $data_view['mess_error'] = '商品序号已被占用，请重新编辑';
          }
        } else {
          $data_view['mess_error'] = '有效商品数量已达上限8个，请下架其他商品后再来添加！';
        }
      }
    } else {
      $this->load->helper('common_load_source_helper');
      $data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
      //需要加载的JS
      $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
        . 'common/third/swf/swfupload.js,'
        . 'mls/js/v1.0/uploadpic.js,'
        . 'mls/js/v1.0/cooperate_common.js,'
        . 'common/third/jquery-ui-1.9.2.custom.min.js');
    }
    $this->load->view('gift_manage/modify_status', $data_view);
  }

  //上传图片显示页面
  public function uploadphoto($company_id)
  {
    $company = $this->agency_model->get_by_id($company_id);
    $data_view['company'] = $company;
    $this->load->view('company/uploadphoto', $data_view);

  }

  /**
   * 导出商品表数据
   * @author   wang
   */
  public function exportReport($search_where = 0, $search_value = 0, $status = 0, $search_where_time = 0, $time_s = 0, $time_e = 0, $type = 0)
  {

    ini_set('memory_limit', '-1');
    //表单提交参数组成的查询条件
    $search_where = $this->input->get('search_where', TRUE);
    $search_value = $this->input->get('search_value', TRUE);
    $status = $this->input->get('status', TRUE);
    $type = $this->input->get('type', TRUE);
    $search_where_time = $this->input->get('search_where_time', TRUE);
    //设置时间条件
    $time_s = strtotime($this->input->get('time_s', TRUE));
    $time_e = strtotime($this->input->get('time_e', TRUE));
    //$time_s = $time_s != '' ? $time_s : date('Y-m-d', strtotime('-1 day'));
    //$time_e = $time_e != '' ? $time_e : date('Y-m-d', strtotime('-1 day'));

    $where = 'id > 0 and status != 3 ';
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
    }
    if ($type) {
      $where .= ' and type =' . $type;
    }
    if ($status) {
      $where .= ' and status =' . $status;
    }
    if ($search_where_time && $time_s && $time_e) {
      $where .= ' and ' . $search_where_time . '>= "' . $time_s . '"';
      $where .= ' and ' . $search_where_time . '<= "' . $time_e . '"';
    }
    $limit = $this->gift_manage_model->count_data_by_cond($where);
    $productlist = $this->gift_manage_model->get_data_by_cond($where, 0, $limit);

    $list = array();
    if (is_full_array($productlist)) {
      foreach ($productlist as $key => $value) {
        $list[$key]['product_serial_num'] = $value['product_serial_num'];
        $list[$key]['order'] = $value['order'];
        $list[$key]['product_name'] = $value['product_name'];
        $list[$key]['score'] = $value['score'];
        $list[$key]['over_exchange_num'] = $value['over_exchange_num'];
        if ($value['status'] == 1) {
          $list[$key]['status'] = '已上架';
        } elseif ($value['status'] == 2) {
          $list[$key]['status'] = '已下架';
        }
        if ($value['type'] == 1) {
          $list[$key]['type'] = '兑换';
        } elseif ($value['type'] == 2) {
          $list[$key]['type'] = '抽奖';
        }
        if ($value['release_time']) {
          $list[$key]['release_time'] = date("Y-m-d H:i:s", $value['release_time']);
        } else {
          $list[$key]['release_time'] = date("Y-m-d H:i:s", time());
        }
        if ($value['down_time']) {
          $list[$key]['down_time'] = date("Y-m-d H:i:s", $value['down_time']);
        } else {
          $list[$key]['down_time'] = '0';
        }
      }

      $list = array_values($list);
    }

    //调用PHPExcel第三方类库
    $this->load->library('PHPExcel.php');
    $this->load->library('PHPExcel/IOFactory');
    //创建phpexcel对象
    $objPHPExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用于 2007 格式
    $objWriter->setOffice2003Compatibility(true);

    //设置phpexcel文件内容
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
      ->setLastModifiedBy("Maarten Balliauw")
      ->setTitle("Office 2007 XLSX Test Document")
      ->setSubject("Office 2007 XLSX Test Document")
      ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("Test result file");

    //设置表格导航属性
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '商品编号');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '商品排序');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '商品类型');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '商品名称');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '积分值');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '已兑换数量');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '状态');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '发布时间');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '下架时间');
    //设置表格的值
    for ($i = 2; $i <= count($list) + 1; $i++) {

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $list[$i - 2]['product_serial_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $list[$i - 2]['order']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $list[$i - 2]['type']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $list[$i - 2]['product_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $list[$i - 2]['score']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $list[$i - 2]['over_exchange_num']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $list[$i - 2]['status']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $list[$i - 2]['release_time']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $list[$i - 2]['down_time']);
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('product_nums');
    $objPHPExcel->setActiveSheetIndex(0);

    //header("Content-type: text/csv");//重要
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');   //excel 2003
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');   //excel 2007
    //header('Content-Disposition: attachment;filename="求购客源.xls"');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }

  /*
     * 上传图片
     */
  public function upload_photo1()
  {
    $filename = 'Filedata';
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    echo $fileurl;
    //echo "<script>alert('".$fileurl."')</script>";exit;

    //$div_id= $this->input->post('div_id');
    //echo "<script>window.parent.changePic('".$fileurl."','".$div_id."')</script>";
  }

  /*
     * 上传图片
     */
  public function upload_photo()
  {
    $filename = $this->input->post('action');
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    //echo "<script>alert('".$fileurl."')</script>";exit;

    $div_id = $this->input->post('div_id');
    echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
  }
}

/* End of file company.php */
/* Location: ./application/mls_admin/controllers/company.php */
