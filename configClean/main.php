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
$type = GetVars('type', 'GET');
$appID = GetVars('appID', 'GET');
$modName = GetVars('modName', 'GET');
if ($act === 'del') {
  CheckIsRefererValid();
  if ($type === 'config' && !HasNameInString("cache|system", $appID) && !$zbp->CheckPlugin($appID)) {
    $zbp->DelConfig($appID);
  }
  if ($type === 'mod') {
    // $zbp->LoadModules();
    $mod = $zbp->modulesbyfilename[$modName];
    $mod->Del();
  }
  $zbp->BuildTemplate();
  $zbp->SetHint('good');
  Redirect('./main.php');
}
if (GetVars('act', 'GET') == 'view') {
  CheckIsRefererValid();
  header('Content-type: application/json');
  JsonReturn($zbp->Config($appID)->GetData());
  die();
}
InstallPlugin_configClean();
$blogtitle = '配置项扫描';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<link rel="stylesheet" href="css/main.css">
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?><small><a title="刷新" href="main.php" style="font-size: 16px;display: inline-block;margin-left: 5px;">刷新</a></small></div>
  <div class="SubMenu">
    <a href="main.php" title="首页"><span class="m-left m-now">首页</span></a>
    <a href="javascript:;" class="js-toggleAll" title="显示/隐藏启用中的配置项"><span class="m-left">显示全部</span></a>
    <?php require_once "about.php"; ?>
  </div>
  <!-- #divMin2 -->
  <div id="divMain2">
    <h3>配置项管理</h3>
    <table id="configs" width="100%" class="tableBorder">
      <tr>
        <th width="10%">项目</th>
        <th>状态</th>
        <th>查看</th>
        <th width="45%">操作</th>
      </tr>
      <?php echo configClean_list(); ?>
    </table>
    <h3>模块管理</h3>
    <p>未验证模块归属应用是否启用中，请自行判断；</p>
    <p>对于开发者，请使用<code>theme_主题ID</code>、<code>plugin_插件ID</code>设置<code>$mod->Source</code>字段；</p>
    <table width="100%" class="tableBorder">
      <tr>
        <th width="25%">项目</th>
        <th width="15%">来源</th>
        <th>操作</th>
      </tr>
      <?php echo configClean_modulesList(); ?>
    </table>
  </div>
  <!-- /#divMain2 -->
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
