<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS系统公用函数库-资源加载
 *
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Helpers
 * @author      HOUSE365 ESF Dev Team
 * @link        http://nj.sell.house365.com/
 */

//--------------------------------------------------------

/**
 * 加截javascript 文件
 *
 * @param type $js_url  脚本路径名称，可以一维数组
 */
function load_js($js_url)
{
    $js = '';
    $js_url = explode(',', $js_url);
    if (is_array($js_url))
    {
        $strJs = '';
        foreach($js_url as $v)
        {
            if ($js != '')
            {
                $js .= "\n";
            }
            $strJs .= ',' . $v;
        }

        //$js .= '<script type="text/javascript" src="'. MLS_SOURCE_URL . '/min/?f=' . ltrim($strJs, ',') .'&debug=1"></script>';
        $js .= '<script type="text/javascript" src="'. MLS_SOURCE_URL . '/min/?f=' . ltrim($strJs, ',') .'"></script>';
    }
    else
    {
        //$js .= '<script type="text/javascript" src="'. MLS_SOURCE_URL . '/min/?f=' . ltrim($js_url) .'&debug=1"></script>';
        $js .= '<script type="text/javascript" src="'. MLS_SOURCE_URL . '/min/?f=' . ltrim($js_url) .'"></script>';
    }
    return $js;
}


/**
 * 加截css文件
 *
 * @param type $css_url  样式路径名称，可以一维数组
 */
function load_css($css_url)
{
    $css = '';
    $css_url = explode(',', $css_url);
    if (is_array($css_url))
    {
        $strCss = '';
        foreach($css_url as $v)
        {
            if ($css != '')
            {
                $css .= "\n";
            }
            $strCss .= ',' . $v;
        }
        $css .= '<link href="' .MLS_SOURCE_URL . '/min/?f=' . ltrim($strCss, ',')
              .'" rel="stylesheet" type="text/css">';
    }
    else
    {
        $css .= '<link href="' .MLS_SOURCE_URL . '/min/?f=' . ltrim($css_url)
                    .'" rel="stylesheet" type="text/css">';
    }
    return $css;
}
