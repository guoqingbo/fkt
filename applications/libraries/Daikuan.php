<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 贷款参数
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */
class Daikuan
{

  /**
   * Constructor
   */
  public function __construct()
  {
  }


  //贷款参数
  public function get_daikuan()
  {
    //按揭成数
    $mortgage_percentage = array(
      '2' => '2成',
      '3' => '3成',
      '4' => '4成',
      '5' => '5成',
      '6' => '6成',
      '7' => '7成',
      '8' => '8成',
      '9' => '9成',
      '10' => '其他',
    );

    //按揭年数
    for ($year = 1; $year <= 30; $year++) {
      $month = $year * 12;
      $mortgage_year[$year] = $year . '年' . '(' . $month . '期)';
    }

    //当前利率
    $current_loan = array(
      'fund' => array(
        '5年以下(含5年)' => '2.75',
        '5年以上' => '3.25',
      ),
      'business' => array(
        '1年以内(含1年)' => '4.35',
        '1年至5年(含5年)' => '4.75',
        '5年以上' => '4.9',
      ),
      'current_time' => '2015年10月24日',
    );

    //贷款利率
    $loan_option = array(
      "102" => array(
        'description' => '2015年10月24日后利率上浮20%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0435,
          '5' => 0.0475,
          '30' => 0.0490,
        ),
        'rate' => 1.2,
      ),
      "101" => array(
        'description' => '2015年10月24日后利率下浮5%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0435,
          '5' => 0.0475,
          '30' => 0.0490,
        ),
        'rate' => 0.95,
      ),
      "100" => array(
        'description' => '2015年10月24日后利率下浮10%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0435,
          '5' => 0.0475,
          '30' => 0.0490,
        ),
        'rate' => 0.9,
      ),
      "99" => array(
        'description' => '2015年10月24日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0435,
          '5' => 0.0475,
          '30' => 0.0490,
        ),
        'rate' => 0.7,
      ),
      "98" => array(
        'description' => '2015年10月24日后利率下浮20%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0435,
          '5' => 0.0475,
          '30' => 0.0490,
        ),
        'rate' => 0.8,
      ),
      "97" => array(
        'description' => '2015年10月24日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0435,
          '5' => 0.0475,
          '30' => 0.0490,
        ),
        'rate' => 0.85,
      ),
      "96" => array(
        'description' => '2015年10月24日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0435,
          '5' => 0.0475,
          '30' => 0.0490,
        ),
        'rate' => 1.1,
      ),
      "95" => array(
        'description' => '2015年10月24日后基准利率',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0435,
          '5' => 0.0475,
          '30' => 0.0490,
        ),
        'rate' => 1,
        'selected' => 1,
      ),
      "94" => array(
        'description' => '2015年08月26日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0460,
          '5' => 0.0500,
          '30' => 0.0515,
        ),
        'rate' => 0.7,
      ),
      "93" => array(
        'description' => '2015年08月26日后利率下浮20%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0460,
          '5' => 0.0500,
          '30' => 0.0515,
        ),
        'rate' => 0.8,
      ),
      "92" => array(
        'description' => '2015年08月26日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0460,
          '5' => 0.0500,
          '30' => 0.0515,
        ),
        'rate' => 0.85,
      ),
      "91" => array(
        'description' => '2015年08月26日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0460,
          '5' => 0.0500,
          '30' => 0.0515,
        ),
        'rate' => 1.1,
      ),
      "90" => array(
        'description' => '2015年08月26日后基准利率',
        'fundpercent' => array(
          '5' => 0.0275,
          '30' => 0.0325,
        ),
        'businesspercent' => array(
          '1' => 0.0460,
          '5' => 0.0500,
          '30' => 0.0515,
        ),
        'rate' => 1,
      ),
      "89" => array(
        'description' => '2015年06月28日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0300,
          '30' => 0.0350,
        ),
        'businesspercent' => array(
          '1' => 0.0485,
          '5' => 0.0525,
          '30' => 0.0540,
        ),
        'rate' => 0.7,
      ),
      "88" => array(
        'description' => '2015年06月28日后利率下浮20%',
        'fundpercent' => array(
          '5' => 0.0300,
          '30' => 0.0350,
        ),
        'businesspercent' => array(
          '1' => 0.0485,
          '5' => 0.0525,
          '30' => 0.0540,
        ),
        'rate' => 0.8,
      ),
      "87" => array(
        'description' => '2015年06月28日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0300,
          '30' => 0.0350,
        ),
        'businesspercent' => array(
          '1' => 0.0485,
          '5' => 0.0525,
          '30' => 0.0540,
        ),
        'rate' => 0.85,
      ),
      "86" => array(
        'description' => '2015年06月28日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0300,
          '30' => 0.0350,
        ),
        'businesspercent' => array(
          '1' => 0.0485,
          '5' => 0.0525,
          '30' => 0.0540,
        ),
        'rate' => 1.1,
      ),
      "85" => array(
        'description' => '2015年06月28日后基准利率',
        'fundpercent' => array(
          '5' => 0.0300,
          '30' => 0.0350,
        ),
        'businesspercent' => array(
          '1' => 0.0485,
          '5' => 0.0525,
          '30' => 0.0540,
        ),
        'rate' => 1,

      ),
      "84" => array(
        'description' => '2015年05月11日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0325,
          '30' => 0.0375,
        ),
        'businesspercent' => array(
          '1' => 0.0510,
          '5' => 0.0550,
          '30' => 0.0565,
        ),
        'rate' => 0.7,
      ),
      "83" => array(
        'description' => '2015年05月11日后利率下浮20%',
        'fundpercent' => array(
          '5' => 0.0325,
          '30' => 0.0375,
        ),
        'businesspercent' => array(
          '1' => 0.0510,
          '5' => 0.0550,
          '30' => 0.0565,
        ),
        'rate' => 0.8,
      ),
      "82" => array(
        'description' => '2015年05月11日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0325,
          '30' => 0.0375,
        ),
        'businesspercent' => array(
          '1' => 0.0510,
          '5' => 0.0550,
          '30' => 0.0565,
        ),
        'rate' => 0.85,
      ),
      "81" => array(
        'description' => '2015年05月11日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0325,
          '30' => 0.0375,
        ),
        'businesspercent' => array(
          '1' => 0.0510,
          '5' => 0.0550,
          '30' => 0.0565,
        ),
        'rate' => 1.1,
      ),
      "80" => array(
        'description' => '2015年05月11日后基准利率',
        'fundpercent' => array(
          '5' => 0.0325,
          '30' => 0.0375,
        ),
        'businesspercent' => array(
          '1' => 0.0510,
          '5' => 0.0550,
          '30' => 0.0565,
        ),
        'rate' => 1,
      ),
      "79" => array(
        'description' => '2015年03月01日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.035,
          '30' => 0.04,
        ),
        'businesspercent' => array(
          '1' => 0.0535,
          '5' => 0.0575,
          '30' => 0.059,
        ),
        'rate' => 0.7,
      ),
      "78" => array(
        'description' => '2015年03月01日后利率下浮20%',
        'fundpercent' => array(
          '5' => 0.035,
          '30' => 0.04,
        ),
        'businesspercent' => array(
          '1' => 0.0535,
          '5' => 0.0575,
          '30' => 0.059,
        ),
        'rate' => 0.8,
      ),
      "77" => array(
        'description' => '2015年03月01日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.035,
          '30' => 0.04,
        ),
        'businesspercent' => array(
          '1' => 0.0535,
          '5' => 0.0575,
          '30' => 0.059,
        ),
        'rate' => 0.85,
      ),
      "76" => array(
        'description' => '2015年03月01日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.035,
          '30' => 0.04,
        ),
        'businesspercent' => array(
          '1' => 0.0535,
          '5' => 0.0575,
          '30' => 0.059,
        ),
        'rate' => 1.1,
      ),
      "75" => array(
        'description' => '2015年03月01日后基准利率',
        'fundpercent' => array(
          '5' => 0.035,
          '30' => 0.04,
        ),
        'businesspercent' => array(
          '1' => 0.0535,
          '5' => 0.0575,
          '30' => 0.059,
        ),
        'rate' => 1,
      ),
      "74" => array(
        'description' => '2014年11月22日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0425,
        ),
        'businesspercent' => array(
          '1' => 0.056,
          '5' => 0.06,
          '30' => 0.0615,
        ),
        'rate' => 0.7,
      ),
      "73" => array(
        'description' => '2014年11月22日后利率下浮20%',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0425,
        ),
        'businesspercent' => array(
          '1' => 0.056,
          '5' => 0.06,
          '30' => 0.0615,
        ),
        'rate' => 0.8,
      ),
      "72" => array(
        'description' => '2014年11月22日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0425,
        ),
        'businesspercent' => array(
          '1' => 0.056,
          '5' => 0.06,
          '30' => 0.0615,
        ),
        'rate' => 0.85,
      ),
      "71" => array(
        'description' => '2014年11月22日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0425,
        ),
        'businesspercent' => array(
          '1' => 0.056,
          '5' => 0.06,
          '30' => 0.0615,
        ),
        'rate' => 1.1,
      ),
      "70" => array(
        'description' => '2014年11月22日后基准利率',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0425,
        ),
        'businesspercent' => array(
          '1' => 0.056,
          '5' => 0.06,
          '30' => 0.0615,
        ),
        'rate' => 1,
      ),
      "69" => array(
        'description' => '2012年7月6日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.040,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.06,
          '3' => 0.0615,
          '5' => 0.0640,
          '30' => 0.0655,
        ),
        'rate' => 0.7,
      ),
      "68" => array(
        'description' => '2012年7月6日后利率下浮20%',
        'fundpercent' => array(
          '5' => 0.040,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.06,
          '3' => 0.0615,
          '5' => 0.0640,
          '30' => 0.0655,
        ),
        'rate' => 0.8,
      ),
      "67" => array(
        'description' => '2012年7月6日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.040,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.06,
          '3' => 0.0615,
          '5' => 0.0640,
          '30' => 0.0655,
        ),
        'rate' => 0.85,
      ),
      "66" => array(
        'description' => '2012年7月6日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.040,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.06,
          '3' => 0.0615,
          '5' => 0.0640,
          '30' => 0.0655,
        ),
        'rate' => 1.1,
      ),
      "65" => array(
        'description' => '2012年7月6日后基准利率',
        'fundpercent' => array(
          '5' => 0.040,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.06,
          '3' => 0.0615,
          '5' => 0.0640,
          '30' => 0.0655,
        ),
        'rate' => 1,
      ),
      "64" => array(
        'description' => '2012年6月8日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 0.7,
      ),
      "63" => array(
        'description' => '2012年6月8日后利率下浮20%',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 0.8,
      ),
      "62" => array(
        'description' => '2012年6月8日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 0.75,
      ),
      "61" => array(
        'description' => '2012年6月8日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 1.1,
      ),
      "60" => array(
        'description' => '2012年6月8日后基准利率',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 1,
      ),
      "59" => array(
        'description' => '2011年7月7日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0445,
          '30' => 0.0539,
        ),
        'businesspercent' => array(
          '1' => 0.0656,
          '3' => 0.0665,
          '5' => 0.069,
          '30' => 0.0705,
        ),
        'rate' => 1.1,
      ),
      "58" => array(
        'description' => '2011年10月24日后基准利率',
        'fundpercent' => array(
          '5' => 0.0445,
          '30' => 0.049,
        ),
        'businesspercent' => array(
          '1' => 0.0656,
          '3' => 0.0665,
          '5' => 0.069,
          '30' => 0.0705,
        ),
        'rate' => 1,
      ),
      "56" => array(
        'description' => '2011年7月7日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0445,
          '30' => 0.049,
        ),
        'businesspercent' => array(
          '1' => 0.0656,
          '3' => 0.0665,
          '5' => 0.069,
          '30' => 0.0705,
        ),
        'rate' => 0.7,
      ),
      "57" => array(
        'description' => '2011年7月7日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0445,
          '30' => 0.049,
        ),
        'businesspercent' => array(
          '1' => 0.0656,
          '3' => 0.0665,
          '5' => 0.069,
          '30' => 0.0705,
        ),
        'rate' => 0.85,
      ),
      "55" => array(
        'description' => '2011年7月7日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0445,
          '30' => 0.049,
        ),
        'businesspercent' => array(
          '1' => 0.0656,
          '3' => 0.0665,
          '5' => 0.069,
          '30' => 0.0705,
        ),
        'rate' => 1.1,
      ),
      "54" => array(
        'description' => '2011年7月7日后基准利率',
        'fundpercent' => array(
          '5' => 0.0445,
          '30' => 0.049,
        ),
        'businesspercent' => array(
          '1' => 0.0656,
          '3' => 0.0665,
          '5' => 0.069,
          '30' => 0.0705,
        ),
        'rate' => 1,
      ),
      "53" => array(
        'description' => '2011年4月6日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 0.7,
      ),
      "52" => array(
        'description' => '2011年4月6日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 0.85,
      ),
      "51" => array(
        'description' => '2011年4月6日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 1.1,
      ),
      "50" => array(
        'description' => '2011年4月6日后基准利率',
        'fundpercent' => array(
          '5' => 0.042,
          '30' => 0.047,
        ),
        'businesspercent' => array(
          '1' => 0.0631,
          '3' => 0.064,
          '5' => 0.0665,
          '30' => 0.068,
        ),
        'rate' => 1,
      ),
      "49" => array(
        'description' => '2011年2月9日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.04,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.0606,
          '3' => 0.061,
          '5' => 0.0645,
          '30' => 0.066,
        ),
        'rate' => 0.7,
      ),
      "48" => array(
        'description' => '2011年2月9日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.04,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.0606,
          '3' => 0.061,
          '5' => 0.0645,
          '30' => 0.066,
        ),
        'rate' => 0.85,
      ),
      "47" => array(
        'description' => '2011年2月9日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.04,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.0606,
          '3' => 0.061,
          '5' => 0.0645,
          '30' => 0.066,
        ),
        'rate' => 1.1,
      ),
      "46" => array(
        'description' => '2011年2月9日后基准利率',
        'fundpercent' => array(
          '5' => 0.04,
          '30' => 0.045,
        ),
        'businesspercent' => array(
          '1' => 0.0606,
          '3' => 0.061,
          '5' => 0.0645,
          '30' => 0.066,
        ),
        'rate' => 1,
      ),
      "45" => array(
        'description' => '2010年12月26日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0430,
        ),
        'businesspercent' => array(
          '1' => 0.0581,
          '3' => 0.0585,
          '5' => 0.0622,
          '30' => 0.064,
        ),
        'rate' => 0.7,
      ),
      "44" => array(
        'description' => '2010年12月26日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0430,
        ),
        'businesspercent' => array(
          '1' => 0.0581,
          '3' => 0.0585,
          '5' => 0.0622,
          '30' => 0.064,
        ),
        'rate' => 0.85,
      ),
      "43" => array(
        'description' => '2010年12月26日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0430,
        ),
        'businesspercent' => array(
          '1' => 0.0581,
          '3' => 0.0585,
          '5' => 0.0622,
          '30' => 0.064,
        ),
        'rate' => 1.1,
      ),
      "42" => array(
        'description' => '2010年12月26日后基准利率',
        'fundpercent' => array(
          '5' => 0.0375,
          '30' => 0.0430,
        ),
        'businesspercent' => array(
          '1' => 0.0581,
          '3' => 0.0585,
          '5' => 0.0622,
          '30' => 0.064,
        ),
        'rate' => 1,
      ),
      "41" => array(
        'description' => '2010年10月20日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0350,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '1' => 0.0556,
          '3' => 0.0556,
          '5' => 0.0596,
          '30' => 0.0614,
        ),
        'rate' => 0.7,
      ),
      "39" => array(
        'description' => '2010年10月20日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0350,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '1' => 0.0556,
          '3' => 0.0556,
          '5' => 0.0596,
          '30' => 0.0614,
        ),
        'rate' => 0.85,
      ),
      "40" => array(
        'description' => '2010年10月20日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0350,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '1' => 0.0556,
          '3' => 0.0556,
          '5' => 0.0596,
          '30' => 0.0614,
        ),
        'rate' => 1.1,
      ),
      "38" => array(
        'description' => '2010年10月20日后基准利率',
        'fundpercent' => array(
          '5' => 0.0350,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '1' => 0.0556,
          '3' => 0.0556,
          '5' => 0.0596,
          '30' => 0.0614,
        ),
        'rate' => 1,
      ),
      "37" => array(
        'description' => '2008年12月23日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0333,
          '30' => 0.0387,
        ),
        'businesspercent' => array(
          '1' => 0.0531,
          '3' => 0.0540,
          '5' => 0.0576,
          '30' => 0.0594,
        ),
        'rate' => 1.1,
      ),
      "36" => array(
        'description' => '2008年12月23日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0333,
          '30' => 0.0387,
        ),
        'businesspercent' => array(
          '1' => 0.0531,
          '3' => 0.0540,
          '5' => 0.0576,
          '30' => 0.0594,
        ),
        'rate' => 0.85,
      ),
      "35" => array(
        'description' => '2008年12月23日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0333,
          '30' => 0.0387,
        ),
        'businesspercent' => array(
          '1' => 0.0531,
          '3' => 0.0540,
          '5' => 0.0576,
          '30' => 0.0594,
        ),
        'rate' => 0.7,
      ),
      "34" => array(
        'description' => '2008年12月23日后基准利率',
        'fundpercent' => array(
          '5' => 0.0350,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '1' => 0.0531,
          '3' => 0.0540,
          '5' => 0.0576,
          '30' => 0.0594,
        ),
        'rate' => 1,
      ),
      "33" => array(
        'description' => '2008年11月27日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0351,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '1' => 0.0558,
          '3' => 0.0567,
          '5' => 0.0594,
          '30' => 0.0612,
        ),
        'rate' => 1,
      ),
      "32" => array(
        'description' => '08年11月27日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0351,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '1' => 0.0558,
          '3' => 0.0567,
          '5' => 0.0594,
          '30' => 0.0612,
        ),
        'rate' => 0.7,
      ),
      "31" => array(
        'description' => '08年11月27日后基准利率',
        'fundpercent' => array(
          '5' => 0.0351,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '1' => 0.0558,
          '3' => 0.0567,
          '5' => 0.0594,
          '30' => 0.0612,
        ),
        'rate' => 1,
      ),
      "30" => array(
        'description' => '08年10月30日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0405,
          '30' => 0.0459,
        ),
        'businesspercent' => array(
          '1' => 0.0666,
          '3' => 0.0675,
          '5' => 0.0702,
          '30' => 0.072,
        ),
        'rate' => 0.85,
      ),
      "29" => array(
        'description' => '08年10月30日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0405,
          '30' => 0.0459,
        ),
        'businesspercent' => array(
          '1' => 0.0666,
          '3' => 0.0675,
          '5' => 0.0702,
          '30' => 0.072,
        ),
        'rate' => 0.7,
      ),
      "28" => array(
        'description' => '08年10月30日后基准利率',
        'fundpercent' => array(
          '5' => 0.0405,
          '30' => 0.0459,
        ),
        'businesspercent' => array(
          '1' => 0.0666,
          '3' => 0.0675,
          '5' => 0.0702,
          '30' => 0.072,
        ),
        'rate' => 1,
      ),
      "27" => array(
        'description' => '08年10月27日后利率下浮30%',
        'fundpercent' => array(
          '5' => 0.0405,
          '30' => 0.0459,
        ),
        'businesspercent' => array(
          '1' => 0.0693,
          '3' => 0.0702,
          '5' => 0.0729,
          '30' => 0.0747,
        ),
        'rate' => 0.7,
      ),
      "26" => array(
        'description' => '08年10月27日后基准利率',
        'fundpercent' => array(
          '5' => 0.0405,
          '30' => 0.0459,
        ),
        'businesspercent' => array(
          '1' => 0.0693,
          '3' => 0.0702,
          '5' => 0.0729,
          '30' => 0.0747,
        ),
        'rate' => 1,
      ),
      "25" => array(
        'description' => '08年10月9日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0432,
          '30' => 0.0486,
        ),
        'businesspercent' => array(
          '1' => 0.0693,
          '3' => 0.0702,
          '5' => 0.0729,
          '30' => 0.0747,
        ),
        'rate' => 1.1,
      ),
      "24" => array(
        'description' => '08年10月9日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0432,
          '30' => 0.0486,
        ),
        'businesspercent' => array(
          '1' => 0.0693,
          '3' => 0.0702,
          '5' => 0.0729,
          '30' => 0.0747,
        ),
        'rate' => 0.85,
      ),
      "23" => array(
        'description' => '08年10月9日后基准利率',
        'fundpercent' => array(
          '5' => 0.0432,
          '30' => 0.0486,
        ),
        'businesspercent' => array(
          '1' => 0.0693,
          '3' => 0.0702,
          '5' => 0.0729,
          '30' => 0.0747,
        ),
        'rate' => 1,
      ),
      "22" => array(
        'description' => '08年9月16日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0459,
          '30' => 0.0513,
        ),
        'businesspercent' => array(
          '1' => 0.0720,
          '3' => 0.0729,
          '5' => 0.0756,
          '30' => 0.0774,
        ),
        'rate' => 1.1,
      ),
      "21" => array(
        'description' => '08年9月16日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0459,
          '30' => 0.0513,
        ),
        'businesspercent' => array(
          '1' => 0.0720,
          '3' => 0.0729,
          '5' => 0.0756,
          '30' => 0.0774,
        ),
        'rate' => 0.85,
      ),
      "20" => array(
        'description' => '08年9月16日后基准利率',
        'fundpercent' => array(
          '5' => 0.0459,
          '30' => 0.0513,
        ),
        'businesspercent' => array(
          '1' => 0.0720,
          '3' => 0.0729,
          '5' => 0.0756,
          '30' => 0.0774,
        ),
        'rate' => 1,
      ),
      "19" => array(
        'description' => '07年12月21日后利率上浮10%',
        'fundpercent' => array(
          '5' => 0.0477,
          '30' => 0.0522,
        ),
        'businesspercent' => array(
          '1' => 0.0747,
          '3' => 0.0756,
          '5' => 0.0774,
          '30' => 0.0783,
        ),
        'rate' => 1.1,
      ),
      "18" => array(
        'description' => '07年12月21日后利率下浮15%',
        'fundpercent' => array(
          '5' => 0.0477,
          '30' => 0.0522,
        ),
        'businesspercent' => array(
          '1' => 0.0747,
          '3' => 0.0756,
          '5' => 0.0774,
          '30' => 0.0783,
        ),
        'rate' => 0.85,
      ),
      "17" => array(
        'description' => '07年12月21日后基准利率',
        'fundpercent' => array(
          '5' => 0.0477,
          '30' => 0.0522,
        ),
        'businesspercent' => array(
          '1' => 0.0747,
          '3' => 0.0756,
          '5' => 0.0774,
          '30' => 0.0783,
        ),
        'rate' => 1,
      ),
      "16" => array(
        'description' => '07年9月15日后利率下限',
        'fundpercent' => array(
          '5' => 0.0477,
          '30' => 0.0522,
        ),
        'businesspercent' => array(
          '1' => 0.0729,
          '3' => 0.0747,
          '5' => 0.0765,
          '30' => 0.0783,
        ),
        'rate' => 0.85,
      ),
      "15" => array(
        'description' => '07年9月15日后基准利率',
        'fundpercent' => array(
          '5' => 0.0477,
          '30' => 0.0522,
        ),
        'businesspercent' => array(
          '1' => 0.0729,
          '3' => 0.0747,
          '5' => 0.0765,
          '30' => 0.0783,
        ),
        'rate' => 1,
      ),
      "14" => array(
        'description' => '07年8月22日后利率下限',
        'fundpercent' => array(
          '5' => 0.0459,
          '30' => 0.0504,
        ),
        'businesspercent' => array(
          '1' => 0.0702,
          '3' => 0.0720,
          '5' => 0.0738,
          '30' => 0.0756,
        ),
        'rate' => 0.85,
      ),
      "13" => array(
        'description' => '07年8月22日后基准利率',
        'fundpercent' => array(
          '5' => 0.0459,
          '30' => 0.0504,
        ),
        'businesspercent' => array(
          '1' => 0.0702,
          '3' => 0.0720,
          '5' => 0.0738,
          '30' => 0.0756,
        ),
        'rate' => 1,
      ),
      "12" => array(
        'description' => '07年7月21日后利率下限',
        'fundpercent' => array(
          '5' => 0.0450,
          '30' => 0.0495,
        ),
        'businesspercent' => array(
          '1' => 0.0684,
          '3' => 0.0702,
          '5' => 0.072,
          '30' => 0.0738,
        ),
        'rate' => 0.85,
      ),
      "11" => array(
        'description' => '07年7月21日后基准利率',
        'fundpercent' => array(
          '5' => 0.0450,
          '30' => 0.0495,
        ),
        'businesspercent' => array(
          '1' => 0.0684,
          '3' => 0.0702,
          '5' => 0.072,
          '30' => 0.0738,
        ),
        'rate' => 1,
      ),
      "10" => array(
        'description' => '07年5月19日后基准利率',
        'fundpercent' => array(
          '5' => 0.0441,
          '30' => 0.0486,
        ),
        'businesspercent' => array(
          '5' => 0.0693,
          '30' => 0.0720,
        ),
        'rate' => 1,
      ),
      "9" => array(
        'description' => '07年5月19日后利率下限',
        'fundpercent' => array(
          '5' => 0.0441,
          '30' => 0.0486,
        ),
        'businesspercent' => array(
          '5' => 0.058905,
          '30' => 0.0612,
        ),
        'rate' => 0.85,
      ),
      "8" => array(
        'description' => '07年3月18日后基准利率',
        'fundpercent' => array(
          '5' => 0.0432,
          '30' => 0.0477,
        ),
        'businesspercent' => array(
          '5' => 0.0675,
          '30' => 0.0711,
        ),
        'rate' => 1,
      ),
      "7" => array(
        'description' => '07年3月18日后利率下限',
        'fundpercent' => array(
          '5' => 0.0432,
          '30' => 0.0477,
        ),
        'businesspercent' => array(
          '5' => 0.0675,
          '30' => 0.0711,
        ),
        'rate' => 0.85,
      ),
      "6" => array(
        'description' => '06年8月19日后利率上限',
        'fundpercent' => array(
          '5' => 0.0414,
          '30' => 0.0459,
        ),
        'businesspercent' => array(
          '5' => 0.0648,
          '30' => 0.0684,
        ),
        'rate' => 1,
      ),
      "5" => array(
        'description' => '06年4月28日后利率上限',
        'fundpercent' => array(
          '5' => 0.0414,
          '30' => 0.0459,
        ),
        'businesspercent' => array(
          '5' => 0.0612,
          '30' => 0.0639,
        ),
        'rate' => 1,
      ),
      "4" => array(
        'description' => '05年3月17日后利率上限',
        'fundpercent' => array(
          '5' => 0.0396,
          '30' => 0.0441,
        ),
        'businesspercent' => array(
          '5' => 0.0495,
          '30' => 0.0612,
        ),
        'rate' => 1,
      ),
      "3" => array(
        'description' => '05年3月17日后利率下限',
        'fundpercent' => array(
          '5' => 0.0396,
          '30' => 0.0441,
        ),
        'businesspercent' => array(
          '5' => 0.0495,
          '30' => 0.0551,
        ),
        'rate' => 1,
      ),
      "2" => array(
        'description' => '05年1月1日-3月17日利率',
        'fundpercent' => array(
          '5' => 0.0378,
          '30' => 0.0423,
        ),
        'businesspercent' => array(
          '5' => 0.0495,
          '30' => 0.0531,
        ),
        'rate' => 1,
      ),
      "1" => array(
        'description' => '05年1月1日前利率',
        'fundpercent' => array(
          '5' => 0.0360,
          '30' => 0.0405,
        ),
        'businesspercent' => array(
          '5' => 0.0477,
          '30' => 0.0504,
        ),
        'rate' => 1,
      ),
    );

    foreach ($loan_option as $key => $value) {

      foreach ($value['fundpercent'] as $k => $v) {
        $fund[$k] = $v * $value['rate'];
      }

      foreach ($value['businesspercent'] as $kk => $vv) {
        $business[$kk] = $vv * $value['rate'];
      }

      if ($value['selected']) {
        $loan[$key]['selected'] = $value['selected'];
      }
      $loan[$key]['fund'] = $fund;
      $loan[$key]['business'] = $business;
      $loan[$key]['description'] = $value['description'];
    }

    $message = array(
      'mortgage_percentage' => '按揭成数',
      'mortgage_year' => '贷款年数',
      'loan' => array(
        '描述' => '贷款利率',
        'fund' => '公积金利率（数组中key为小于等于年，value为小于等于年时的利率）',
        'business' => '商贷利率（数组中key为小于等于年，value为小于等于年时的利率）',
      ),
    );

    $result = array(
      '参数介绍' => $message,
      'mortgage_percentage' => $mortgage_percentage,
      'mortgage_year' => $mortgage_year,
      'loan' => $loan,
      'current_loan' => $current_loan,
    );
    return $result;
  }
}
