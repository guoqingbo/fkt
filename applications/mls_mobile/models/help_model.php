<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * zsb
 *
 * 业务类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Help_model
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          Lion
 */
class Help_model extends MY_Model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获取税费计算器结果
   * parms  int  $housekind  房源类型 1住宅 2非住宅 3房改房
   * parms  int  $averprice   均价
   * parms  int  $buildarea   建筑面积
   * parms  int  $transferyear  几年内转让 1 ：2年内 2 ：2年后
   * parms  int  $firstbuy  是否首次购房 1 首次购房 2 非首次购房
   *
   **/
  function get_tax_calculate($housekind, $averprice, $buildarea,
                             $transferyear, $firstbuy, $lab4_rate = 1)
  {

    $housetype = $housekind == 2 ? "非住宅" : "普通住房";

    if ($housetype == "普通住房") {
      if ($buildarea >= 144) {
        $housetype = "非普通住房";
      }
    }

    if ($housetype == '普通住房') {
      $ghfee_rate = 3;
      //契税
      if ($firstbuy == 1) {
        if ($buildarea <= 90) {
          $qfee_rate = 0.01;
        } else {
          $qfee_rate = 0.015;
        }
      } else {
        if ($buildarea <= 90) {
          $qfee_rate = 0.01;
        } else {
          $qfee_rate = 0.02;
        }
      }
      $qf = $qfee_rate * 100;
      $djfee_rate = $housekind == 3 ? 40 : 80;
      $gbfee_rate = $housekind == 3 ? 18 : 20;
      $lab_s1 = $housekind == 3 ? "(房改房为100元/套)" : "(3元/平方米)";
      $lab_s4 = "(全额" . $lab4_rate . "%)";
      $lab_b1 = $housekind == 3 ? "(房改房为100元/套)" : "(3元/平方米)";

      $lab_b2 = "(成交价" . $qf . "%)";
      $lab_b3 = "(" . $djfee_rate . "元/人)";
      $lab_b4 = "(" . $gbfee_rate . "元)";

      switch ($transferyear) {
        case "1":
          $yyfee_rate = 0.0555;
          $zzfee_rate = 0.05;
          $lab_s2 = "(全额5.55%)";
          $lab_s3 = "(全额5%)";
          $lab_s4 = "(全额" . $lab4_rate . "%)";
          break;
        case "2":
          $yyfee_rate = 0;
          $zzfee_rate = 0;
          $lab_s2 = "(暂不征收)";
          $lab_s3 = "(暂不征收)";
          $lab_s4 = "(全额" . $lab4_rate . "%)";
          break;
      }
    }

    if ($housetype == '非普通住房') {
      $ghfee_rate = 3;
      if ($firstbuy == 1) {
        $qfee_rate = 0.015;
        $lab_b2 = "(成交价1.5%)";
      } else {
        $qfee_rate = 0.02;
        $lab_b2 = "(成交价2%)";
      }
      $djfee_rate = $housekind == 3 ? 40 : 80;
      $gbfee_rate = $housekind == 3 ? 18 : 20;
      $lab_s1 = $housekind == 3 ? "(房改房为100元/套)" : "(3元/平方米)";
      $lab_s4 = "(全额" . $lab4_rate . "%)";
      $lab_b1 = $housekind == 3 ? "(房改房为100元/套)" : "(3元/平方米)";
      $lab_b3 = "(" . $djfee_rate . "元/人)";
      $lab_b4 = "(" . $gbfee_rate . "元)";

      switch ($transferyear) {
        case "1":
          $yyfee_rate = 0.0555;
          $zzfee_rate = 0.05;
          $lab_s2 = "(全额5.55%)";
          $lab_s3 = "(全额5%)";
          break;

        case "2":
          $yyfee_rate = 0.0555;
          $zzfee_rate = 0;
          $lab_s2 = "(差额5.55%)";
          $lab_s3 = "(暂不征收)";
          $lab_s4 = "(全额" . $lab4_rate . "%)";
          break;

      }
    }

    if ($housetype == '非住宅') {
      $ghfee_rate = 5;
      $qfee_rate = 0.03;
      $djfee_rate = 30;
      $gbfee_rate = 90;
      $lab_s1 = "(5元/平方米)";
      $lab_s4 = "(差额20%)";
      $lab_b1 = "(5元/平方米)";
      $lab_b2 = "(成交价3%)";
      $lab_b3 = "(20元+10元/套/人)";
      $lab_b4 = "(" . $gbfee_rate . "元)";

      switch ($transferyear) {
        case "1":
          $yyfee_rate = 0.0555;
          $zzfee_rate = 0.05;
          $lab_s2 = "(全额5.55%)";
          $lab_s3 = "(全额5%)";
          break;

        case "2":
          $yyfee_rate = 0.0555;
          $zzfee_rate = 0;
          $lab_s2 = "(全额5.55%)";
          break;

      }
    }

    $house_type = $housetype; //房屋性质
    $house_price = round(($averprice * $buildarea) / 10000, 4); //税前总价

    /*******************卖方税费**********************/
    $hosue_ghfee = $housekind == 3 ? 100 : round(($ghfee_rate * $buildarea), 2);//过户费

    $hosue_ghfee_desc = $lab_s1;//过户费标注说明

    //营业税赋值（营改增，营业税全部为0）
    $house_yyfee = 0.00;
    $house_yyfee_desc = "(暂不征收)";//营业税标注说明

    $house_tdfee = round(($averprice * $buildarea) * $zzfee_rate, 2);//土地增值税

    $house_tdfee_desc = $lab_s3;//土地增值税标注说明

    //个人所得税
    if ($housetype == '普通住房') {
      $house_sdfee = $transferyear == 1 ? round(($averprice * $buildarea) * $lab4_rate / 100, 2) : round(($averprice * $buildarea) * $lab4_rate / 100, 2);
    } elseif ($housetype == '非普通住房') {
      $house_sdfee = round(($averprice * $buildarea) * $lab4_rate / 100, 2);
    } elseif ($housetype == '非住宅') {
      $house_sdfee = round(($averprice * $buildarea) * $lab4_rate / 100, 2);
    }

    $house_sdfee_desc = $lab_s4; //个人所得税标注说明

    $house_sell_count = $hosue_ghfee + $house_yyfee + $house_tdfee + $house_sdfee;//卖方税费合计

    /*********************买方税费***************************/
    $buy_ghfee = $housekind == 3 ? 100 : round($ghfee_rate * $buildarea, 2);//过户费

    $buy_ghfee_desc = $lab_b1; //过户费标注说明

    $buy_qfee = round(($averprice * $buildarea) * $qfee_rate, 2);//契税

    $buy_qfee_desc = $lab_b2; //契税标注说明

    $buy_djfee = round($djfee_rate, 2);//房屋产权登记费

    $buy_djfee_desc = $lab_b3;//房屋产权登记费标注说明

    $buy_gbfee = round($gbfee_rate, 2);//土地证工本费

    $buy_gbfee_desc = $lab_b4;//土地证工本费标注说明

    $buy_total = $buy_ghfee + $buy_qfee + 100;//合计

    //整理结果
    $res = array();
    $res['查看结果'][]['房屋性质'] = $house_type;
    $res['查看结果'][]['税前总价'] = $house_price . ' 万元';

    $res['卖方税费'][]['过户费'] = $hosue_ghfee . ' 元' . $buy_ghfee_desc;
    $res['卖方税费'][]['营业税'] = $house_yyfee . ' 元' . $house_yyfee_desc;
    $res['卖方税费'][]['土地增值税'] = $house_tdfee . ' 元' . $house_tdfee_desc;
    $res['卖方税费'][]['个人所得税'] = $house_sdfee . ' 元' . $house_sdfee_desc;
    $res['卖方税费'][]['合计'] = $house_sell_count . ' 元';

    $res['买方税费'][]['过户费'] = $buy_ghfee . ' 元' . $buy_ghfee_desc;
    $res['买方税费'][]['契税'] = $buy_qfee . ' 元' . $buy_qfee_desc;
    $res['买方税费'][]['房屋产权登记费'] = $buy_djfee . ' 元' . $buy_djfee_desc;
    $res['买方税费'][]['土地证工本费'] = $buy_gbfee . ' 元' . $buy_gbfee_desc;
    $res['买方税费'][]['合计'] = $buy_total . ' 元';
    //print_r($res);die;
    return $res;
  }
}

?>
