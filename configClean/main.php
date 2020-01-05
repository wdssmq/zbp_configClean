<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
  $zbp->ShowError(6);
  die();
}
if (!$zbp->CheckPlugin('configClean')) {
  $zbp->ShowError(48);
  die();
}

$act = GetVars('act', 'GET');
$suc = GetVars('suc', 'GET');
$appID = GetVars('appID', 'GET');
if (GetVars('act', 'GET') == 'del') {
  CheckIsRefererValid($appID);
  if (!HasNameInString("cache|system", $appID) && !$zbp->CheckPlugin($appID)) {
    $zbp->DelConfig($appID);
  }
  $zbp->BuildTemplate();
  $zbp->SetHint('good');
  Redirect('./main.php' . ($suc == null ? '' : '?act=$suc'));
}
if (GetVars('act', 'GET') == 'view') {
  CheckIsRefererValid($appID);
  header('Content-type: application/json');
  JsonReturn($zbp->Config($appID)->GetData());
  die();
}

$blogtitle = '配置项扫描';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<style>
  .view {
    display: none;
  }

  .view td {
    padding: 1rem;
  }

  .isOn-1::before {
    content: "启用";
  }

  .isTheme-1::before {
    content: "（主题）";
  }

  .isPlugin-1::before {
    content: "（插件）";
  }

  .isOn-0::before {
    content: "未启用";
  }

  .isTheme-0.isPlugin-0::before {
    content: "（不存在）";
  }

  .isSys-1::before {
    content: "（系统）";
  }

  .isSys-1>span {
    display: none;
  }

  .tr-gray {
    background-color: #F4F4F4;
  }
</style>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?><small><a title="刷新" href="main.php" style="font-size: 16px;display: inline-block;margin-left: 5px;">刷新</a></small></div>
  <div class="SubMenu">
    <a href="main.php" title="首页"><span class="m-left m-now">首页</span></a>
    <a href="javascript:;" class="js-toggleAll" title="显示/隐藏启用中的配置项"><span class="m-left">显示全部</span></a>
    <?php require_once "about.php"; ?>
  </div>
  <div id="divMain2">
    <!-- <form action="<?php echo BuildSafeURL("main.php?act=save"); ?>" method="post"> -->
    <table width="100%" class="tableBorder">
      <tr>
        <th width="10%">项目</th>
        <th>状态</th>
        <th>查看</th>
        <th width="45%">操作</th>
      </tr>
      <?php echo configClean_list(); ?>
    </table>
    <!-- </form> -->
  </div>
</div>
<script>
  $(function() {
    function fnStripe() {
      $("tr.item:visible").each(function(i) {
        if (i % 2 !== 0) {
          $(this).addClass("tr-gray");
        } else {
          $(this).removeClass("tr-gray");
        }
      });
    }
    $(".js-toggleAll").click(function() {
      $(".isOn-1,.isSys-1").each(function() {
        $(this).parents("tr").toggle();
        // $(this).parents("tr").toggleClass("item");
      });
      fnStripe();
    });
    $(".isOn-1,.isSys-1").each(function() {
      $(this).parents("tr").hide();
    });
    fnStripe();
    $(".js-view").click(function() {
      let id = $(this).data("id");
      if (!$(`.js-${id}`).is(":hidden")) {
        $(`.js-${id}`).hide();
        return false;
      }
      // $("tr.view").slideUp(800);
      $.get(this.href, function(data) {
        if (data.err.code === 0) {
          let rlt = "",
            object = data.data;
          for (const key in object) {
            if (object.hasOwnProperty(key)) {
              const element = object[key];
              rlt += `${key}：${element}<br>`;
            }
          }
          $(`.js-${id} td`).html(rlt);
          $(`.js-${id}`).show(300).siblings(".view").hide();
        }
      });
      return false;
    })
  });
</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
