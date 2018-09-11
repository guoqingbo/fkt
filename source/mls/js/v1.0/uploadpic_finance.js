function fileQueueError(file, errorCode, message) {
  try {
    if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
      alert("你选择的文件数量超过限制.\n" + (message === 0 ? "You have reached the upload limit." : "你只可以再选择" + (message > 1 ? "最多" + message + "个文件." : "1个文件.")));
      return;
    }

    //var progress = new FileProgress(file, this.customSettings.upload_target);
    //progress.setError();
    //progress.toggleCancel(false);

    switch (errorCode) {
      case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
        alert("文件" + file.name + "字节量超过上传限制.");
        return;
        //progress.setStatus("文件太大.");
        //this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
        break;
      case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
        //progress.setStatus("Cannot upload Zero Byte files.");
        //this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
        break;
      case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
        alert("文件" + file.name + "无效文件格式.");
        return;
        //progress.setStatus("无效文件格式.");
        //this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
        break;
      default:
        //if (file !== null) {
        //	progress.setStatus("Unhandled Error");
        //}
        //this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
        break;
    }
  } catch (ex) {
    this.debug(ex);
  }
}


function swfUploadLoaded() {
  var upload_nail = this.customSettings.upload_nail == '' ? 'thumbnails' : this.customSettings.upload_nail;
  if ($("#" + upload_nail + " img").size() >= this.customSettings.upload_limit) {
    this.setButtonDisabled(true);
  }
}

function fileDialogStart() {
  var upload_nail = this.customSettings.upload_nail == '' ? 'thumbnails' : this.customSettings.upload_nail;
  var count = $("#" + upload_nail + " img").size();
  var n = this.customSettings.upload_limit - count;
  this.setFileQueueLimit(n);
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
  try {
    /*
     alert(this.getStats().files_queued);
     var count=$("#thumbnails img").size();
     if( (count+numFilesSelected)>8 ){
     alert("上传图片超过" + this.customSettings.upload_limit + "张，只能再选" +(this.customSettings.upload_limit - count)+ "张图片");
     return;
     }
     */
    if (numFilesQueued > 0) {
      this.startUpload();
    }
  } catch (ex) {
    this.debug(ex);
  }
}

function uploadProgress(file, bytesLoaded) {
  this.setButtonDisabled(true);

  try {
    var percent = Math.ceil((bytesLoaded / file.size) * 100);

    var progress = new FileProgress(file, this.customSettings.upload_target);
    progress.setProgress(percent);
    if (percent === 100) {
      progress.setStatus("Creating thumbnail...");
      progress.toggleCancel(false, this);
    } else {
      progress.setStatus("Uploading...");
      progress.toggleCancel(true, this);
    }
  } catch (ex) {
    this.debug(ex);
  }
}

function uploadSuccess(file, serverData) {
  try {
    var progress = new FileProgress(file, this.customSettings.upload_target);

    var resultData = JSON.parse(serverData);

    if(resultData.success === 1) {
      addImage(resultData.result.imgUrl, this.customSettings.upload_infotype, this.customSettings.upload_nail);
    }

  } catch (ex) {
    this.debug(ex);
  }
}

//处理java上传图片接口返回的信息
function uploadSuccessNew (file, serverData) {
    try {
        var progress = new FileProgress(file, this.customSettings.upload_target);
        var resultData = JSON.parse(serverData);
        if (resultData.success == true) {
            addImage(resultData.result, this.customSettings.upload_infotype, this.customSettings.upload_nail);
        }
    } catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
  try {
    /*  I want the next upload to continue automatically so I'll call startUpload here */
    if (this.getStats().files_queued > 0) {
      this.startUpload();
    } else {
      var progress = new FileProgress(file, this.customSettings.upload_target);
      progress.setComplete();
      progress.setStatus("图片上传完毕.");
      progress.toggleCancel(false);
      var upload_nail = this.customSettings.upload_nail == '' ? 'thumbnails' : this.customSettings.upload_nail;
      if ($("#" + upload_nail + " img").size() >= this.customSettings.upload_limit) {
        this.setButtonDisabled(true);
      }
      else {
        this.setButtonDisabled(false);
      }
    }
  } catch (ex) {
    this.debug(ex);
  }
}

function uploadError(file, errorCode, message) {
  var imageName = "error.gif";
  var progress;
  try {
    switch (errorCode) {
      case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
        try {
          progress = new FileProgress(file, this.customSettings.upload_target);
          progress.setCancelled();
          progress.setStatus("Cancelled");
          progress.toggleCancel(false);
        }
        catch (ex1) {
          this.debug(ex1);
        }
        break;
      case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
        try {
          progress = new FileProgress(file, this.customSettings.upload_target);
          progress.setCancelled();
          progress.setStatus("Stopped");
          progress.toggleCancel(true);
        }
        catch (ex2) {
          this.debug(ex2);
        }
      case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
        imageName = "uploadlimit.gif";
        break;
      default:
        alert(message);
        break;
    }

    addImage("images/" + imageName, this.customSettings.upload_infotype, this.customSettings.upload_nail);

  } catch (ex3) {
    this.debug(ex3);
  }

}

//左移图片 右移图片 设为首图
function prevOrNextFun(obj, upload_nail) {
  if ($(obj).hasClass("del_left")) {
    var toImg = $(obj).parents(".add_item_pic").prev(".add_item_pic").find(".pic").find("img");
  }
  else if ($(obj).hasClass("del_right")) {
    var toImg = $(obj).parents(".add_item_pic").next(".add_item_pic").find(".pic").find("img");
  }
  else {
    var toImg = $(obj).parents(".add_item_pic").siblings(".add_item_pic0").find(".pic").find("img");
  }
  ;
  if (toImg.length > 0) {
    /*添加图片切换时，下面的描述也跟着变化*/
    var thisImg = $(obj).parents(".add_item_pic").find(".pic").find("img");
    var thisImgInput = thisImg.siblings("input.hidden_1");
    var thisImgInput2 = thisImg.siblings("input.hidden_2");


    var toImgInput = toImg.siblings("input.hidden_1");
    var toImgInput2 = toImg.siblings("input.hidden_2");
    var thisSrc = thisImg.attr("src");
    var toSrc = toImg.attr("src");
    var thisVal = thisImgInput2.val();
    var toVal = toImgInput2.val();


    thisImg.attr("src", toSrc);
    thisImgInput.val(toSrc);
    thisImgInput2.val(toVal);
    toImg.attr("src", thisSrc);
    toImgInput.val(thisSrc);
    toImgInput2.val(thisVal);
  }

};


function addImage(src, upload_infotype, upload_nail) {
  //var oSwfu = upload_infotype == 0 ? swfu0 : (upload_infotype == 1 ? swfu1 : swfu2);
  var imgarr = $("#" + upload_nail).find(".img_list");
  var len = imgarr.length + 1;
  var type_len = upload_infotype.length;
  //alert(upload_infotype);alert(type_len);
  var tag_ = upload_infotype.substr(0, 1);
  if (tag_ == 'b') {
    tag = 1;//买方
    if (type_len == 4) {//buy1-buy6
      var type = upload_infotype.substr(-1, 1);
    } else if (type_len == 5) {//buy13
      var type = upload_infotype.substring(3, 5);
    }
  } else {
    tag = 2;//卖方
    if (type_len == 5) {//sell7-sell9
      var type = upload_infotype.substr(-1, 1);
    } else if (type_len == 6) {
      var type = upload_infotype.substring(4, 6);
    }
  }//alert(type);
  var content = '<div class="img_list " len="' + len + '" id="' + tag_ + '_' + len + '"><img tag="' + tag + '" type="' + type + '" class="llll" height="75" width="100" src="' + src + '"><span class="bt_delete">&nbsp;</span><input class="hidden_1" type="hidden" value="' + src + '" name="pic_' + upload_infotype + '[]"></div>';
  $("#" + upload_nail).append(content);
  if ($("#" + upload_nail).children().length > 4) {
    $("#" + upload_nail).parents(".photo_nums").find(".go_left").show();
    $("#" + upload_nail).parents(".photo_nums").find(".go_right").show();
  }
  console.log($("#" + upload_nail).children().length);
}
//删除图片
function fun_hide_p(obj) {
  if ($(obj).parents(".add_item_pic").hasClass("add_item_pic0")) {
    $(obj).parents(".add_item_pic").next().addClass("add_item_pic0")
  }
  ;
  $(obj).parents('.add_item_pic').remove();
}
function fadeIn(element, opacity) {
  var reduceOpacityBy = 5;
  var rate = 30;	// 15 fps


  if (opacity < 100) {
    opacity += reduceOpacityBy;
    if (opacity > 100) {
      opacity = 100;
    }

    if (element.filters) {
      try {
        element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
      } catch (e) {
        // If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
        element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
      }
    } else {
      element.style.opacity = opacity / 100;
    }
  }

  if (opacity < 100) {
    setTimeout(function () {
      fadeIn(element, opacity);
    }, rate);
  }
}


/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {
  this.fileProgressID = "divFileProgress";

  this.fileProgressWrapper = document.getElementById(this.fileProgressID);
  if (!this.fileProgressWrapper) {
    this.fileProgressWrapper = document.createElement("div");
    this.fileProgressWrapper.className = "progressWrapper";
    this.fileProgressWrapper.id = this.fileProgressID;

    this.fileProgressElement = document.createElement("div");
    this.fileProgressElement.className = "progressContainer";

    var progressCancel = document.createElement("a");
    progressCancel.className = "progressCancel";
    progressCancel.href = "#";
    progressCancel.style.visibility = "hidden";
    progressCancel.appendChild(document.createTextNode(" "));

    var progressText = document.createElement("div");
    progressText.className = "progressName";
    progressText.appendChild(document.createTextNode(file.name));

    var progressBar = document.createElement("div");
    progressBar.className = "progressBarInProgress";

    var progressStatus = document.createElement("div");
    progressStatus.className = "progressBarStatus";
    progressStatus.innerHTML = "&nbsp;";

    this.fileProgressElement.appendChild(progressCancel);
    this.fileProgressElement.appendChild(progressText);
    this.fileProgressElement.appendChild(progressStatus);
    this.fileProgressElement.appendChild(progressBar);

    this.fileProgressWrapper.appendChild(this.fileProgressElement);

    document.getElementById(targetID).appendChild(this.fileProgressWrapper);
    fadeIn(this.fileProgressWrapper, 0);

  } else {
    this.fileProgressElement = this.fileProgressWrapper.firstChild;
    this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
  }

  this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
  this.fileProgressElement.className = "progressContainer green";
  this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
  this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
  this.fileProgressElement.className = "progressContainer blue";
  this.fileProgressElement.childNodes[3].className = "progressBarComplete";
  this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
  this.fileProgressElement.className = "progressContainer red";
  this.fileProgressElement.childNodes[3].className = "progressBarError";
  this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
  this.fileProgressElement.className = "progressContainer";
  this.fileProgressElement.childNodes[3].className = "progressBarError";
  this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
  this.fileProgressElement.childNodes[2].innerHTML = status;
};

FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
  this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
  if (swfuploadInstance) {
    var fileID = this.fileProgressID;
    this.fileProgressElement.childNodes[0].onclick = function () {
      swfuploadInstance.cancelUpload(fileID);
      return false;
    };
  }
};


function commonHandlers() {

  $(".selectleft").toggle(
    function () {
      $(this).parent().prev("td").find("input[type=checkbox]").attr('checked', true);
      $(this).val("清空");
      return false;
    },
    function () {
      $(this).parent().prev("td").find("input[type=checkbox]").attr('checked', false);
      $(this).val("全选");
      return false;
    }
  )


  //	$("#blockshowname").autocomplete("action.php", {onItemSelect:selectItem,formatItem:formatItem,delay:100});

  function selectItem(li) {
    if (li.extra) {
      //alert("That's '" + li.selectValue + "' you picked.");
      $("#blockid").val(li.extra[0]);
      $("#district").val(li.extra[1]);
      //$("#address").val(li.selectValue+$("#address").val());
      $("#district").change();
      //alert("That's '" + $("#district").val() + "' you picked.");
    }
  }

  function formatItem(row) {
    //return row[0] + "<br><i>" + row[2] + "</i>";
    return row[0];
  }


}


function jftsshow() {
  //document.getElementById("jfts").style.display = "block";
  $("#jfts").show();
}

function jftshide() {
  //document.getElementById("jfts").style.display = "none";
  $("#jfts").hide();
}
