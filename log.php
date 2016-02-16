<?php
// Определитель бот/человек:

// Определяем адрес сайта:
$host = preg_replace("/[^0-9A-Za-z-.]/","",trim($_SERVER['HTTP_HOST']));
// полный адрес страницы:
$uri = preg_replace("/[^0-9a-zA-Z.?&=\/-]/","",trim($_SERVER['REQUEST_URI']));
// юзер-агент посетителя:
$browser = isset($_SERVER['HTTP_USER_AGENT']) ? strip_tags(trim($_SERVER['HTTP_USER_AGENT'])) : '';
// язык посетителя в чистом виде, типа ru:
$lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr(mb_strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE']), 'UTF-8'), 0, 2, 'utf-8') : '';
// реферер
$referer = isset($_SERVER['HTTP_REFERER']) ? strip_tags(trim($_SERVER['HTTP_REFERER'])) : '';
// ip юзера:
$ip = isset($_SERVER['REMOTE_ADDR']) ? preg_replace("/[^0-9.,]/","",trim($_SERVER['REMOTE_ADDR'])) : '';
if (mb_stripos($ip, ',', 0, 'utf-8')!== false) {
$ip = explode(',', $ip);
$ip = $ip[0];
$ip = strip_tags($ip);
}

// пускаем только методом пост:
if ($_SERVER['REQUEST_METHOD'] != 'POST') die(json_encode('error1'));

// боты (user-agent), которых не учитывать по браузеру:
$badbots = array('Googlebot','YandexMetrika','PhantomJS','Java-Client','Antivirus','360Spider','AhrefsBot','ApacheBench','Aport','archive.org','Birubot','BLEXBot','bsalsa','Butterfly','CamontSpider','dataminr.com','discobot','DotBot','Exabot','Ezooms','FairShare','FeedFetcher','FlaxCrawler','FlipboardProxy','FyberSpider','Gigabot','HTTrack','ia_archiver','InternetSeer','Jakarta','JS-Kit','km.ru','kmSearchBot','larbin','libwww','Lightspeedsystems','Linguee','LinkBot','LinkExchanger','LivelapBot','lwp-trivial','MJ12bot','MetaURI','MLBot','NerdByNature','NING','NjuiceBot','Nutch','OpenHoseBot','pflab','PHP/','PostRank','ptd-crawler','Purebot','PycURL','Python','Ruby','QuerySeekerSpider','SemrushBot','SearchBot','SISTRIX','SiteBot','Sitemaps','SolomonoBot','Sogou','Soup','spbot','suggybot','Superfeedr','SurveyBot','SWeb','ttCrawler','TurnitinBot','TweetmemeBot','UnwindFetchor','Voyager','WBSearchBot','Wget','WordPress','Yeti','YottosBot','Zeus','zitebot','ZmEu');
foreach ($badbots as $badbot) {
if (mb_stripos($browser, $badbot, 0, 'utf-8')!== false) {
die(json_encode('error3'));
}
}

// ip или подсети плохих ботов:
$badips = array('192.92.196','38.99.82');
foreach ($badips as $badip) {
if (mb_stripos($ip, $badip, 0, 'utf-8')!== false) {
die(json_encode('error4'));
}
}

// получение информации о host юзера
$hostname = trim(strip_tags(gethostbyaddr($ip)));
// плохие боты по обратному хосту:
$badaddrs = array('google.com','googlebot.com','search.msn.com','amazonaws.com', 'dreamhost.com', 'h1de.net', 'kimsufi.com', 'ovh.net', 'leaseweb.com', 'search.msn.com', 'softlayer.com', 'solomono.ru', 'server4you','your-server.de','yandex.ru');
foreach ($badaddrs as $badaddr) {
if (mb_stripos($hostname, $badaddr, 0, 'utf-8')!== false) {
die(json_encode('badaddr'));
}
}

// сверяем маркеры:
$save_marker = isset($_COOKIE['marker']) ? trim(strip_tags($_COOKIE['marker'])) : '';
if (@trim($_POST['marker']) != $save_marker) {die(json_encode('marker'));}

// открытие во фрейме это плохо:
if (@trim($_POST['iframe']) != '0') {die(json_encode('iframe'));}

// метод (GET или POST)
if (@$_POST['method'] != 'GET') {die(json_encode('method'));}

// получаем страну юзера:
include('SxGeo.php'); 
$SxGeo = new SxGeo('SxGeo.dat', SXGEO_FILE);
$country = @mb_strtolower($SxGeo->getCountry($ip), 'utf-8');

// детектируем AdBlock
if (@$_POST['adb'] == '1') {$save_adblock = '1';} else {$save_adblock = '0';}

// язык браузера
$save_language = @trim(strip_tags($_POST['language']));
// поддерживается ли куки
if (@$_POST['cookietrue'] == 'true') {$save_cookietrue = '1';} else {$save_cookietrue = '0';}
// ширина монитора
if (!ctype_digit(@$_POST['width'])) {$save_width = '0';} else {$save_width = trim($_POST['width']);}
// высота монитора
if (!ctype_digit(@$_POST['height'])) {$save_height = '0';} else {$save_height = trim($_POST['height']);}
// ширина окна
if (!ctype_digit(@$_POST['widthdoc'])) {$save_widthdoc = '0';} else {$save_widthdoc = trim($_POST['widthdoc']);}
// высота окна
if (!ctype_digit(@$_POST['heightdoc'])) {$save_heightdoc = '0';} else {$save_heightdoc = trim($_POST['heightdoc']);}
// Имя браузера с версией
$save_browsername = @trim(strip_tags($_POST['browsername']));
// семейство браузеров
$save_browserfamily = @trim(preg_replace("/[^A-Za-z]/","",$save_browsername));
// название операционки
$save_osname = @trim(strip_tags($_POST['osname']));
// семейство операционок
$save_osfamily = @trim(preg_replace("/[^A-Za-z]/","",$save_osname));
// тип девайса (Mobile Desktop Tablet Spider)
$save_device = @trim(strip_tags($_POST['device']));
if ($save_device == 'Spider') {die(json_encode('spider'));}
// временная зона
$save_timezone = @trim(preg_replace("/[^0-9-]/","",$_POST['timezone']));
// заглавие страницы (ключевик):
$key = trim(strip_tags($_POST['title']));
// не пускаем ботов с моленьким окном браузера:
if ($save_widthdoc < '200' OR $save_heightdoc < '200') {die(json_encode('smallbot'));}

//file_put_contents('good.txt', "$ip $save_device $hostname $country $lang $browser \n", FILE_APPEND | LOCK_EX);
require_once 'cloaking.php';

