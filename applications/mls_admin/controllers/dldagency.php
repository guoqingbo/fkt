<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dldagency extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 导表---把线上的门店公司表导入mls.agency中
   * @access public
   * @return void
   * date 2015-05-27
   * author angel_in_us
   */
  public function index()
  {
    ini_set("max_execution_time", "60000000000000");
    //数据库连接
    $con_nj = mysql_connect("172.17.1.50", "root", "idontcare");#南京二手房-172.17.1.50 数据库 - house;   数据表 - agency
    $con_hf = mysql_connect("172.17.1.51", "root", "idontcare");#分站二手房-172.17.1.51 数据库 - hf_house;数据表 - agency
    $con_ms = mysql_connect("172.17.1.50", "root", "idontcare");#南京/合肥二手房-172.17.1.50 数据库 - mls_nj/mls_hf;数据表 - agency
    //判断连接成功与否
    if (!$con_nj) {
      die('Could not connect: ' . mysql_error());
    }
    if (!$con_hf) {
      die('Could not connect: ' . mysql_error());
    }
    /**
     * 代码说明：
     * 1.结合 mls 的 agency 表来建临时表 agency_bak
     * 2.特别注意 agency_bak 新增三个字段：agentcode、agenttype、parentcode(对应相应线上 agency 表中的三个同名字段)
     * 3.先把 agency_bak 表中的 dist_id 字段置为varchar(60)，等结合 district 表再对应把 dist_id update后，再置为smallint(6)
     * 4.把南京/合肥...等的数据库中 agency 表把所需数据对应导入到该临时表
     * 5.根据 agenttype (1:分公司;2:总公司;3:独立公司;)以及 parentcode(子公司编码[agenttype=1]) 和 agentcode(总公司编码[agenttype=2]|分公司编码[agenttype=1]|独立公司编码[agenttype=3])
     */


    /**
     * 1.把南京/合肥...等的数据库中 agency 表把所需数据对应导入到 agency_bak 表 (不用该段代码时，请注释掉，以免重复导数据)
     * 2.启用该代码时请注意连接对应的数据库，防止误操作，造成数据错乱
     * 3.祝您启用愉快，蟹蟹
     */
    /***********************************导数据代码开始***********************
     * mysql_select_db("hf_house", $con_hf);#选择要导出的数据库
     * //查询所需字段
     * $result = mysql_query("SELECT dist,streetid,agentshortname,director,agenttel,agentaddr,agentfax,agentmail,agenttype,agentcode,parentcode  FROM agency where valid_flag = '1' limit 0,2000",$con_hf);
     * mysql_select_db("mls_hf", $con_ms);#选择要导入的数据库 mls_hf/mls_nj...
     * //开始导数据
     * while($row = mysql_fetch_assoc($result))
     * {
     * //组装 sql
     * $query  = 'insert into agency_bak (dist_id,street_id,name,agentcode,parentcode,agenttype,linkman,telno,address,fax,email,status)'
     * . ' values ("'.$row['dist'].'","'.$row['streetid'].'","'.$row['agentshortname'].'","'.$row['agentcode'].'","'.$row['parentcode'].'",'
     * . '"'.$row['agenttype'].'","'.$row['director'].'","'.$row['agenttel'].'","'.$row['agentaddr'].'",'
     * . '"'.$row['agentfax'].'","'.$row['agentmail'].'","1")';
     * $res    = mysql_query($query,$con_ms);
     * if(!$res){
     * exit('数据插入有误，请稍后重试。');
     * }
     * }
     * die('over');
     **********************************导数据代码结束***************************/

    /**
     * 1.根据 agenttype (1:分公司;2:总公司;3:独立公司;)以及 parentcode(子公司编码[agenttype=1]) 和
     *  agentcode(总公司编码[agenttype=2]|分公司编码[agenttype=1]|独立公司编码[agenttype=3]) 字段来
     * 更新 agency_bak 表中的 company_id
     * 2.总公司 company_id 为 0； 子公司 company_id 为 对应上级公司的在 agency_bak 里面的 id 值；独立公司 请插入两次
     * 3.注意分公司、总公司的从属关系 以及 独立公司的特殊性
     */
    /***********************************更新company_id开始***********************/
    //当为子公司的时候(启用时请把注释去掉，反之注释上)
    /*
    mysql_select_db("mls_hf", $con_ms);#选择要操作的数据库 mls_hf/mls_nj...
    $sql = 'select * from agency_bak where agenttype = 1 limit 0,1000';
    $result = mysql_query($sql,$con_ms);

    //开始更新 agency_bak 表中的 company_id 字段
    while($row = mysql_fetch_assoc($result))
    {
        //echo '<pre>';print_r($row);die;
        //组装 sql 查询该子公司 parentcode 所对应上级公司的 id 值
        $ssql       =   'select id from agency_bak where agentcode ="'.$row['parentcode'].'"';
        $res        =   mysql_fetch_assoc(mysql_query($ssql,$con_ms));
        $cid        = intval($res['id']);

        //组装 sql 更新 agency_bak 表中的 company_id 字段
        $usql       =   'update agency_bak set company_id = '.$cid.' where agentcode ="'.$row['agentcode'].'"';
        $uresult    =   mysql_query($usql,$con_ms);
        if(!$uresult){
            exit('数据更新失败，请稍后重试。');
        }
    }
    die('更新 company_id 结束~！');
    */
    //当为独立公司的时候(启用时请把注释去掉，反之注释上)
    /*
    mysql_select_db("mls_hf", $con_ms);#选择要操作的数据库 mls_hf/mls_nj...
    $sql = 'select * from agency_bak where agenttype = 3 limit 0,1000';
    $result = mysql_query($sql,$con_ms);

    //当为独立公司的时候，默认为公司，需重新作为门店插入 agency 一次，company_id 即为独立公司的 id
    while($row = mysql_fetch_assoc($result))
    {
        //echo '<pre>';print_r($row);die;
        //组装 sql 把独立公司作为门店插入 agency_bak 中
        $ssql       =   'insert into agency_bak (dist_id,street_id,name,agentcode,parentcode,agenttype,linkman,telno,address,fax,email,company_id,status)'
                . ' values("'.$row['dist_id'].'","'.$row['street_id'].'","'.$row['name'].'","'.$row['agentcode'].'","'.$row['parentcode'].'","'.$row['agenttype'].'",'
                . '"'.$row['linkman'].'","'.$row['telno'].'","'.$row['address'].'","'.$row['fax'].'","'.$row['email'].'","'.$row['id'].'",1)';
        $res        =   mysql_query($ssql,$con_ms);
        if(!$res){
            exit('数据插入失败，请稍后重试。');
        }
    }
    die('已按需把独立公司作为门店插入 agency_bak 中！');
    */

    /***********************************更新company_id结束***********************/


  }

  /**
   * 采集安居客的公司和门店
   */
  public function collect_anjuke_agency()
  {
    die();
    $city = 'lanzhou';
    /** '苏州'**/
    //获取当前采集的区属及下属分页的数据
    $collect_dist_num = intval($this->input->get('collect_dist_num'));
    $page = intval($this->input->get('page'));
    $url = $this->input->get('url');
    $this->load->library('Curl');
    $this->load->model('dldagency_model');
    $host = $city . '.anjuke.com';
    //采集城市的区属地址
    if ($collect_dist_num == 0 && $page == 0) {
      $base_district_url = 'http://' . $city . '.anjuke.com/tycoon/p1-st1';
      $data = Curl::browers($host, $base_district_url);
    } else {
      $url = substr($url, 0, strripos($url, '/'));
      $url = $url . '/' . 'p' . $page . '-st1';
      $data = Curl::browers($host, $url);
    }

    //防止跳转
    $data = str_replace('script', '', $data);
    preg_match('/<div class="box">
            <dl class="zone1">(.*)<\/dd>/siU', $data, $prj);
    preg_match_all('<a href="(.*)">', $prj[1], $hrefs);

    //匹配到的区属
    $district = $hrefs[1];
    $total_distrcit = count($district);
    //判断是否完全采集结束
    if ($collect_dist_num >= $total_distrcit) {
      echo '采集完成';
      die();
    }
    if ($url == '') {
      $url = $district[$collect_dist_num];
    }
    $is_next_page = preg_match('/<span class="nextpage"><ins>下一页<\\/ins>/', $data);
    if (!$is_next_page && $collect_dist_num == 0) {
      echo '采集完成';
      die();
    }
    if ($is_next_page && $collect_dist_num == 0) {
      $collect_dist_num++;
    } else {
      $this->load->model('agency_model');
      preg_match_all('/<!--这个地方加判断，有评价显示评价icon-->(.*)<\/dd>/siU', $data, $prj);
      $all_agency_arr = $prj[1];
      if (is_full_array($all_agency_arr)) {
        foreach ($all_agency_arr as $v) {
          $agency_arr = explode(' ', trim(strip_tags($v)));
          $company_name = $agency_arr[0];
          $agency_name = $agency_arr[1];
          if ($company_name == '' || $agency_name == '' || $company_name == '其它') {
            continue;
          }
          $company = $this->agency_model->get_one_by("company_id = 0 and name = '{$company_name}'");
          if (is_full_array($company)) {
            $company_id = $company['id'];
          } else {
            //插入公司数据
            $company_id = $this->agency_model->add_company(0, 0, $company_name, '', '', '', '', '', '', '');
            //角色权限
            $this->agency_model->init_company_permission($company_id);
          }
          $agency = $this->agency_model->get_one_by("company_id = $company_id and name = '{$agency_name}'");
          if (!is_full_array($agency)) {
            $this->agency_model->add_agency(0, 0, $agency_name, '', '', $company_id);
          }
        }
      }

    }
    //当采到最后一页时
    if (!$is_next_page) {
      $page = 1;
      $collect_dist_num++;
      $url = $district[$collect_dist_num - 1];
    } else {
      $page++;
    }
    //echo $data;
    sleep(2);//die();
    $redrict_url = MLS_ADMIN_URL . "/dldagency/collect_anjuke_agency?collect_dist_num="
      . $collect_dist_num . '&page=' . $page . '&url=' . $url;
    $this->dldagency_model->show_msg('正在采集中，请稍后……', $redrict_url);

    //
    /**
     * 1、采集的站点名称   安居客
     * 2、采集的城市
     * 3、采集区域数组
     * 3、判断经纪人所属的公司是否存在，有则查询公司编号，反之插入即返回相应的值
     * 4、判断经纪人所属的门店是否存在，有则公司编号，反之插入即返回相应的值
     *
     */
  }
}

/* End of file dldagency.php */
/* Location: ./application/mls_admin/controllers/dldagency.php */
