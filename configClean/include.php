<?php
#注册插件
RegisterPlugin("configClean", "ActivePlugin_configClean");

function ActivePlugin_configClean()
{ }
function configClean_checkIsOn($name)
{
  foreach ($GLOBALS['activedapps'] as $value) {
    if (strtolower($name) === strtolower($value)) {
      return true;
    }
  }
  return false;
}
function configClean_list()
{
  global $zbp;
  $html = '';

  $configs_name = $configs_namevalue = array();
  foreach ($zbp->configs as $n => $c) {
    $configs_name[$n] = $n;
    $configs_namevalue[$n] = $c;
  }
  natcasesort($configs_name);
  $zbp->configs = array();
  foreach ($configs_name as $name) {
    $zbp->configs[$name] = $configs_namevalue[$name];
  }
  unset($configs_name, $configs_namevalue);

  foreach ($zbp->configs    as $k => $v) {
    // $html .= "<li><a id=\"$k\" href=\"javascript:;\" onclick=\"clk(this);run('open','$k');\">$k</a></li>";
    // $listOn = strtolower($zbp->option['ZC_USING_PLUGIN_LIST']);

    $isOn = $zbp->CheckPlugin($k) || configClean_checkIsOn($k) ? 1 : 0;
    $isSys = HasNameInString("cache|system|AppCentre", $k)  ? 1 : 0;
    $viewButton = "";
    $delButton = "";
    // $plugins = array();

    $app = new App();
    if ($app->LoadInfoByXml('theme', $k) == true) {
      $isTheme = 1;
      $isPlugin = 0;
      // if ($app->HasPlugin()) {
      //   array_unshift($plugins, $app);
      // }
    } else if ($app->LoadInfoByXml('plugin', $k) == true) {
      $isTheme = 0;
      $isPlugin = 1;
    } else {
      $isTheme = 0;
      $isPlugin = 0;
    }
    $viewLink = BuildSafeURL("{$zbp->host}zb_users/plugin/configClean/main.php?act=view&appID={$k}");
    $viewButton = "<a class=\"button js-view\" data-id=\"{$k}\" href=\"{$viewLink}\" title=\"查看内容\">查看</a>";
    if (!$isOn && !$isSys) {
      // $delLink = BuildSafeURL("{$zbp->host}zb_users/plugin/configClean/main.php?act=del&appID={$k}", $k);
      $delLink = BuildSafeURL("{$zbp->host}zb_users/plugin/configClean/main.php?act=del&appID={$k}");
      $delButton = "<a class=\"button\" href=\"{$delLink}\" title=\"删除该配置项\" onclick=\"return window.confirm(&quot;单击“确定”继续。单击“取消”停止。&quot;);\"><img height=\"16\" width=\"16\" src=\"{$zbp->host}zb_users/plugin/AppCentre/images/delete.png\"></a>";
    }
    $html .= "<tr class=\"item\"><td>{$k}</td><td class='isSys-{$isSys}'><span class='isOn-{$isOn}'></span><span class='isTheme-{$isTheme} isPlugin-{$isPlugin}'></span></td><td>{$viewButton}</td><td>{$delButton}</td></tr>";
    $html .= "<tr class=\"view js-{$k}\" id=\"\"><td colspan=\"4\"></td></tr>";
  }

  return $html;
}
function InstallPlugin_configClean()
{ }
function UninstallPlugin_configClean()
{ }
