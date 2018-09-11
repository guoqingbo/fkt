/**
 * 通用 ajax 分页函数
 * 
 * @param  int page_now 当前页面
 * @param  int pages 总页数
 * @param  array func_callback 翻页时需要执行的函数名称
 * @param  json json_param 翻页时需要执行函数的参数
 * @return string 分页字符串
 */
function ajax_page(page_now , pages , func_callback , json_param)
{
    var ss = '';
    var show_mum = arguments[4] ? arguments[4] : 5;

    //如果页数小于1则返回空
    if (pages <= 1 || isNaN(page_now))
    {
        return '';
    }
    else
    {
        var pre = page_now == 1 ? page_now : page_now - 1; //前一页
        var end = page_now == pages ? pages : (page_now * 1) + 1; //下一页

        //首页、前一页
        if (page_now == 1)
        {
            ss += "<p class='pag'><a href='javascript:void(0)' class='lin prev linNone'>首 页</a></p>";
            ss += "<p class='pag'><a href='javascript:void(0)' class='lin prev linNone'>&lt;上一页</a></p>";
        }
        else
        {
            var first_page = 1; //第一页
            ss += "<p class='pag'><a class='lin prev' href='javascript:void(0)' onclick='" + func_callback + "(" + first_page + "," + json_param + ")'>首 页</a></p>";
            ss += "<p class='pag'><a class='lin prev' href='javascript:void(0)' onclick='" + func_callback + "(" + pre + "," + json_param + ")'>&lt;上一页</a></p>";
        }

        //前三页
        if (page_now > 3)
        {
            if (pages - page_now <= 3)
            {
                var num = page_now - (show_mum - (pages * 1 + 1 - page_now));
                num = num >= 1 ? num : 1;
                for (var i = num; i < page_now; i++)
                {
                    ss += "<p class='pag'><a href='javascript:void(0)' class='lin' onclick='" + func_callback + "(" + i + "," + json_param + ")'>" + i + "</a></p>";
                }
            }
            else
            {
                for (var i = page_now - 3; i < page_now; i++)
                {
                    ss += "<p class='pag'><a href='javascript:void(0)' class='lin' onclick='" + func_callback + "(" + i + "," + json_param + ")'>" + i + "</a></p>";
                }
            }
        }
        else
        {
            show_num = pages > page_now ? page_now : pages;
            for (var i = 1; i < show_num; i++)
            {
                ss += "<p class='pag'><a href='javascript:void(0)' class='lin' onclick='" + func_callback + "(" + i + "," + json_param + ")'>" + i + "</a></p>";
            }
        }

        //当前页面
        ss += "<p class='pag'><a  class='lin current'>" + page_now + "</a></p>";

        //后三页数
        if (pages - page_now > 3)
        {
            //当前页数小于等于3时，强制显示到第7页
            for (var i = page_now + 1; i <= page_now + 3; i++)
            {
                ss += "<p class='pag'><a href='javascript:void(0)' class='lin'  onclick='" + func_callback + "(" + i + "," + json_param + ")'>" + i + "</a></p>";
            }
        }
        else
        {
            for (var i = (page_now + 1); i <= pages; i++)
            {
                ss += "<p class='pag'><a href='javascript:void(0)' class='lin'  onclick='" + func_callback + "(" + i + "," + json_param + ")'>" + i + "</a></p>";
            }
        }

        //下一页、尾页
        if ((page_now == pages) || (pages == 0))
        {
            var last_page = pages;
            ss += "<p class='pag'><a href='javascript:void(0)'  class='lin next linNone'>下一页&gt;</a></p>";
        }
        else
        {
            var next_page = page_now + 1;
            var last_page = pages;
            ss += "<p class='pag'><a href='javascript:void(0)' class='lin next' onclick='" + func_callback + "(" + next_page + "," + json_param + ")'>下一页&gt;</a></p>";
        }
    }
    return ss;
}