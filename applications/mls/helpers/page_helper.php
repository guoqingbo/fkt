<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type: text/html; charset=utf-8");
/**
 * 系统基础函数文件
 *
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Helpers
 */

/**
* 伪静态的uri链接地址
*
* @author cxf updatetime 2013-12-05
* @param int
* @param int
* @return mixed
*/
if(!function_exists('page_uri'))
{
    function page_uri($page,$pages,$url)
    {
        $ss = '';
        if($pages <= 1 || !is_numeric($page))
        {
            return '';
        }
        else
        {
            $pre = $page == 1 ? $page : $page - 1;
            $end = $page == $pages ? $pages : page + 1;

            if($page == 1 )
            {
                $ss .='<li id="dataTables-example_previous" tabindex="0" aria-controls="dataTables-example" class="paginate_button previous disabled"><a href="#">首页</a></li><li id="dataTables-example_previous" tabindex="0" aria-controls="dataTables-example" class="paginate_button previous  disabled"><a href="#">上一页</a></li>';
            }
            else
            {
                $ss .='<li id="dataTables-example_previous" tabindex="0" aria-controls="dataTables-example" class="paginate_button previous"><a href="javascript:void(0);" onclick="search_form.pg.value=1;search_form.submit();return false;">首页</a></li><li id="dataTables-example_previous" tabindex="0" aria-controls="dataTables-example" class="paginate_button previous"><a href="javascript:void(0);" onclick="search_form.pg.value='.$pre.';search_form.submit();return false;">上一页</a></li>';
            }

            $a = ($pages-$page) > 6 ? ($page-1) : ($pages-5);

            for($i=$a;$i<=$page-1;$i++)
            {
                if($i<1) continue;
                $ss .='<li tabindex="0" aria-controls="dataTables-example" class="paginate_button "><a href="javascript:void(0);" onclick="search_form.pg.value='.$i.';search_form.submit();return false;">'.$i.'</a></li>';
            }

            $ss .= '<li tabindex="0" aria-controls="dataTables-example" class="paginate_button active"><a href="#">'.$page.'</a></li>';

            if($page < $pages)
            {
                $flag = 0;
                for($i=$page+1;$i<=$pages;$i++)
                {
                    $ss .= '<li tabindex="0" aria-controls="dataTables-example" class="paginate_button "><a href="javascript:void(0);" onclick="search_form.pg.value='.$i.';search_form.submit();return false;">'.$i.'</a></li>';
                    $flag++;
                    if($page>1)
                    {
                        if($flag==4) break;
                    }
                    else
                    {
                        if($page==1 & $flag+$page==6) break;
                    }
                }
            }

            if( ($page == $pages) || ($pages == 0) )
            {
                $ss .= '<li id="dataTables-example_previous" tabindex="0" aria-controls="dataTables-example" class="paginate_button previous disabled"><a >下一页</a></li><li id="dataTables-example_previous" tabindex="0" aria-controls="dataTables-example" class="paginate_button previous disabled"><a href="#">尾页</a></li>';
            }
            else
            {
                $ss .= '<li id="dataTables-example_previous" tabindex="0" aria-controls="dataTables-example" class="paginate_button previous"><a href="javascript:void(0);" onclick="search_form.pg.value='.$end.';search_form.submit();return false;">下一页</a></li><li id="dataTables-example_previous" tabindex="0" aria-controls="dataTables-example" class="paginate_button previous"><a href="javascript:void(0);" onclick="search_form.pg.value='.$pages.';search_form.submit();return false;">尾页</a></li>';
            }
        }

        $ss .= '';

        return $ss;
    }
}

/* End of file user_helper.php */
/* Location: ./applications/helpers/user_helper.php */
