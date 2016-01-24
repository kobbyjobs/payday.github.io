<?php
// запрещаем отображаться ошибкам:
error_reporting(0);
ini_set('display_errors', 'off');
// разрешаем отображаться ошибкам:
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');
header('Content-Type: text/html; charset=UTF-8');
$start_time = microtime(true);

// просто чтоб было:
$breadcrumbs = '';
$description = '';
$keywords = '';
$pass = '';
$id = '';
$keypars = '';
$content = '';
$description = '';
$fotos = '';
$videos = '';
$p = '';
$foto = '';
$video = '';
$www = '1';
// и доп поля таблицы задефолтим:
$column1 = '';
$column2 = '';
$column3 = '';
$column4 = '';
$column5 = '';
$column6 = '';
$column7 = '';
$column8 = '';
$column9 = '';
$column10 = '';
$column11 = '';
$column12 = '';
$column13 = '';
$column14 = '';
$column15 = '';

// текущий домен
$host = preg_replace("/[^0-9A-Za-z-.]/","",trim($_SERVER['HTTP_HOST']));
$host = mb_strtolower($host, 'utf-8');
// текущий урл:
$uri = trim(strip_tags($_SERVER['REQUEST_URI']));
// адрес текущего скрипта:
$script = trim(strip_tags($_SERVER['SCRIPT_NAME']));
// текущая дата:
$time = time();

// ip юзера:
$ip = isset($_SERVER['REMOTE_ADDR']) ? preg_replace("/[^0-9.,]/","",trim($_SERVER['REMOTE_ADDR'])) : '';
if (mb_stripos($ip, ',', 0, 'utf-8')!== false) {
$ip = explode(',', $ip);
$ip = $ip[0];
$ip = strip_tags($ip);
}
// маркер слежения:
$testmarker = sha1($time.$ip);
if(!isset($_COOKIE['marker'])) {setcookie('marker', $testmarker, time()+5184000, '/');} 
else {$testmarker = trim(strip_tags($_COOKIE['marker']));}

// подгружаем конфиги:
if (file_exists('conf/'.$host.'.php')) {require_once 'conf/'.$host.'.php';} else {require_once 'conf.php';}

// убираем www
if ($www == '1') {
if (mb_substr($host, 0, 4, 'utf-8') == 'www.') {$host2 = mb_substr($host, 4, 125, 'utf-8');} else {$host2 = $host;}
if ($host != $host2) {
header('HTTP/1.1 301 Moved Permanently');
header('Location: http://'.$host2.$uri);
die();
}
}

// мусор из ЧПУ:
function UserFriendlyURLs($text) {
$badchar2 = array ('«','»','{','}','#','"','—','\'',' ','-','*','.',',','!','?',':','/','(',')','+','=','<','>','\\','%2F','@','№',';','|','`','~','&');
$text = mb_strtolower(trim(strip_tags($text)), 'utf-8');
$text = str_ireplace($badchar2, '_', $text);
$text = preg_replace('!_{2,}!u', ' ', $text);
$text = str_ireplace('_', ' ', $text);
$text = trim($text);
$text = str_ireplace(' ', '_', $text);
return $text;
}

// канонический урл по умолчанию:
if ($ufurl == '1') {
$canonical = 'http://'.$host.'/';
} else {
$canonical = 'http://'.$host.$script;
}

// пригодится на будущее:
$token = @$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
if(!isset($_COOKIE['id'])) {setcookie('id', md5($token), time()+5184000, '/');} 

// проверяем есть ли база:
if (!file_exists('db/'.$host.'.db')) {

// загружаем первоначальные ключевики:
$keys = @file('keys/'.$host.'.txt');
$key = @trim($keys[0]);
if ($key == '') {
header('HTTP/1.1 404 Not Found');
header('Status: 404 Not Found');
die('Файл ключевиков keys/'.$host.'.txt пуст или не существует. Пропишите туда хотя бы один ключевик.');
}
// создаем базу:	
$db = new SQLite3('db/'.$host.'.db');
$db->busyTimeout(5000);
$db->exec('PRAGMA journal_mode=WAL;');
if (!$db) die('Невозможно создать базу!');

$query = $db->exec('CREATE TABLE IF NOT EXISTS pages (id INTEGER PRIMARY KEY, url TEXT UNIQUE, key TEXT UNIQUE, keys INTEGER, content TEXT, description TEXT, fotos TEXT, videos TEXT, ufu TEXT UNIQUE, column1 TEXT, column2 TEXT, column3 TEXT, column4 TEXT, column5 TEXT, column6 TEXT, column7 TEXT, column8 TEXT, column9 TEXT, column10 TEXT, column11 TEXT, column12 TEXT, column13 TEXT, column14 TEXT, column15 TEXT, search TEXT);'); 
if (!$query) die('Невозможно создать таблицу!');
// добавляем начальные ключевики:
$db->exec('BEGIN IMMEDIATE;');
foreach ($keys as $lines) {
$lines = trim($lines);
$line = explode('|', $lines);
$key = $db->escapeString(trim($line[0]));
$column1 = @$db->escapeString(trim($line[1]));
$column2 = @$db->escapeString(trim($line[2]));
$column3 = @$db->escapeString(trim($line[3]));
$column4 = @$db->escapeString(trim($line[4]));
$column5 = @$db->escapeString(trim($line[5]));
$column6 = @$db->escapeString(trim($line[6]));
$column7 = @$db->escapeString(trim($line[7]));
$column8 = @$db->escapeString(trim($line[8]));
$column9 = @$db->escapeString(trim($line[9]));
$column10 = @$db->escapeString(trim($line[10]));
$column11 = @$db->escapeString(trim($line[11]));
$column12 = @$db->escapeString(trim($line[12]));
$column13 = @$db->escapeString(trim($line[13]));
$column14 = @$db->escapeString(trim($line[14]));
$column15 = @$db->escapeString(trim($line[15]));

$ufu = UserFriendlyURLs($line[0]);
$ufu = @$db->escapeString(trim($ufu));

$search = strip_tags($key);
$search = mb_strtolower($search, 'utf-8');
$search = explode(' ', $search);
$search = array_unique($search);
$search = implode(' ', $search);
$search = @$db->escapeString($search);

$add = @$db->exec("INSERT INTO pages (key, ufu, column1, column2, column3, column4, column5, column6, column7, column8, column9, column10, column11, column12, column13, column14, column15, search) VALUES ('".$key."', '".$ufu."', '".$column1."', '".$column2."', '".$column3."', '".$column4."', '".$column5."', '".$column6."', '".$column7."', '".$column8."', '".$column9."', '".$column10."', '".$column11."', '".$column12."', '".$column13."', '".$column14."', '".$column15."', '".$search."');");
}
$db->exec('COMMIT;');
$db->close();

}

// открываем базу:
$db = new SQLite3('db/'.$host.'.db');
$db->busyTimeout(5000);
$db->exec('PRAGMA journal_mode=WAL;');
if (!$db) die('Невозможно открыть базу!');

// в будущих версиях удалить:
$add_column = @$db->exec('ALTER TABLE pages ADD search TEXT;');

if (isset($_GET[$get_page])) {
// СТРАНИЦА:
$p = @trim($_GET[$get_page]);

if ($ufurl == '1') {
$p =  UserFriendlyURLs($p);

if ($p != @$_GET[$get_page]) {
header('HTTP/1.1 301 Moved Permanently');
header('Location: /page/'.urlencode($p).'/');
die();
}
$canonical = 'http://'.$host.'/page/'.urlencode(UserFriendlyURLs($p)).'/';
$p = @$db->escapeString(trim($p));
} else {
if (!ctype_digit($p)) {
header('HTTP/1.1 301 Moved Permanently');
header('Location: '.$script.'?'.$get_page.'=1');
die();
}
$canonical = 'http://'.$host.$script.'?'.$get_page.'='.$p;
}

if ($ufurl == '1') {
$page = $db->querySingle('SELECT * FROM pages WHERE ufu="'.$p.'"', true);
} else {
$page = $db->querySingle('SELECT * FROM pages WHERE id='.$p, true);
}
$pageid = $page['id'];
// полученное из базы:
$key = @$page['key'];
// если ключевика нету:
if ($key == '') {
header('HTTP/1.1 301 Moved Permanently');
header('Location: '.$script.'?'.$get_page.'=1');
die();
}
$id = @$page['id']; // id ключевика в базе
$keypars = @$page['keys']; // уже парсили (1) или нет доп ключи
$content = @$page['content']; // сериализованный массив предложений
$description = @$page['description']; // одно предложение
$fotos = @$page['fotos']; // сериализованный массив ссылок на картинки
$videos = @$page['videos']; // сериализованный массив id видео с ютуба
// доп поля:
$column1 = @$page['column1'];
$column2 = @$page['column2'];
$column3 = @$page['column3'];
$column4 = @$page['column4'];
$column5 = @$page['column5'];
$column6 = @$page['column6'];
$column7 = @$page['column7'];
$column8 = @$page['column8'];
$column9 = @$page['column9'];
$column10 = @$page['column10'];
$column11 = @$page['column11'];
$column12 = @$page['column12'];
$column13 = @$page['column13'];
$column14 = @$page['column14'];
$column15 = @$page['column15'];

// парсинг ключевиков:
$keys = array();
if ($keysource == '1' AND $keypars != '1' AND !file_exists($host.'.log')) {

if ($lang == 'ru') {
// ключевики sputnik.ru
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://sgs.sputnik.ru/?format=xml&type=web&query='.urlencode($key));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);
preg_match_all('!header="(.*?)"/\>!siu', $outch, $lines);
foreach ($lines[1] as $line) {
$line = trim($line);
$keys[md5($line)] = $line;
}

// ключевики mail.ru
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://suggests.go.mail.ru/ie8?q='.urlencode($key));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);
preg_match_all('!\<Text\>(.*?)\</Text\>!siu', $outch, $lines);
foreach ($lines[1] as $line) {
$line = trim($line);
$keys[md5($line)] = $line;
}

// ключевики yandex.ru
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://suggest.yandex.ru/suggest-ff.cgi?part='.urlencode($key).'&uil=ru&sn=50');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);
$lines = @(array)json_decode($outch);
foreach ($lines[1] as $line) {
$line = trim($line);
$keys[md5($line)] = $line;
}

// ключевики nigma.ru
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://autocomplete.nigma.ru/complete/query_help.php?suggest=true&q='.urlencode($key));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);
$lines = @(array)json_decode($outch);
foreach ($lines[1] as $line) {
$line = trim($line);
$keys[md5($line)] = $line;
}
} else {
// нерусские ключевики
// ключевики yahoo.com
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://search.yahoo.com/sugg/gossip/gossip-us-fp/?nresults=20&command='.urlencode($key));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);
$outch = str_ireplace('fxsearch(', '', $outch);
$outch = str_ireplace('],[],[]])', ']]', $outch);
$lines = @(array)json_decode($outch);
foreach ($lines[1] as $line) {
$line = trim($line);
$keys[md5($line)] = $line;
}
// ключевики google.com
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://clients1.google.com/s?hl='.$lang.'&client=opera&q='.urlencode($key));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);
$lines = @(array)json_decode($outch);
foreach ($lines[1] as $line) {
$line = trim($line);
$keys[md5($line)] = $line;
}
// ключевики ask.com
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://ss.ask.com/query?li=ff&sstype=prefix&num=20&q='.urlencode($key));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);
$lines = @(array)json_decode($outch);
foreach ($lines[1] as $line) {
$line = trim($line);
$keys[md5($line)] = $line;
}
}
// добавляем дополнительные ключевики:
$keystr = mb_strtolower($key, 'utf-8'); // переводим исходный ключевик в нижний регистр
shuffle($keys); // перемешиваем
$blackkeys = file('black_key.txt');
$db->exec('BEGIN IMMEDIATE;');
foreach ($keys as $line) {
$line = trim($line);
// проверяем на стоп слова
$titlestr = mb_strtolower($line, 'utf-8'); // переводим спаршенный ключевик в нижний регистр

foreach ($blackkeys as $blackkey) {
$blackkey = trim($blackkey);
$keyfind = strripos($titlestr, $blackkey);
if ($keyfind !== false){$badkey = '1'; break;} else {$badkey = '0';}
}

// проверяем точное вхождение:
if ($keyexactmatch == '1') {
$key2find = strripos($titlestr, $keystr);
if ($key2find !== false){} else {$badkey = '1';}
}
// конец проверки по стоп словам.
if ($badkey != '1') {
$ufu = UserFriendlyURLs($line);
$ufu = @$db->escapeString(trim($ufu));
$line = $db->escapeString($line);
$column1 = @$db->escapeString(trim($column1));
$column2 = @$db->escapeString(trim($column2));
$column3 = @$db->escapeString(trim($column3));
$column4 = @$db->escapeString(trim($column4));
$column5 = @$db->escapeString(trim($column5));
$column6 = @$db->escapeString(trim($column6));
$column7 = @$db->escapeString(trim($column7));
$column8 = @$db->escapeString(trim($column8));
$column9 = @$db->escapeString(trim($column9));
$column10 = @$db->escapeString(trim($column10));
$column11 = @$db->escapeString(trim($column11));
$column12 = @$db->escapeString(trim($column12));
$column13 = @$db->escapeString(trim($column13));
$column14 = @$db->escapeString(trim($column14));
$column15 = @$db->escapeString(trim($column15));

$search = strip_tags($line);
$search = mb_strtolower($search, 'utf-8');
$search = explode(' ', $search);
$search = array_unique($search);
$search = implode(' ', $search);
$search = @$db->escapeString($search);

$add = @$db->exec("INSERT INTO pages (key, ufu, column1, column2, column3, column4, column5, column6, column7, column8, column9, column10, column11, column12, column13, column14, column15, search) VALUES ('".$line."', '".$ufu."', '".$column1."', '".$column2."', '".$column3."', '".$column4."', '".$column5."', '".$column6."', '".$column7."', '".$column8."', '".$column9."', '".$column10."', '".$column11."', '".$column12."', '".$column13."', '".$column14."', '".$column15."', '".$search."');");
}
}
$db->exec('COMMIT;');
// номер последней вставленной страницы:
$lastnum = (int) $db->lastInsertRowID();
// проверяем достижение лимита количества страниц:
if ($lastnum > $maxpage) {file_put_contents('log/'.$host.'.log', $maxpage);}
// обновляем маркер текущей страницы
if ($ufurl == '1') {
$update = $db->exec("UPDATE pages SET keys='1' WHERE ufu='".$p."';");
} else {
$update = $db->exec("UPDATE pages SET keys='1' WHERE id='".$p."';");
}
}
// конец парсинга ключевиков

// парсинг контента
if ($content == '' OR $content == 'a:0:{}') {
$badchar = array ("\n","\r","\t",'&nbsp;','&laquo;','&raquo;','&quot;','&#8592;','&#8594;','&#39;','&#8211;','&#32;','&#160;','&#8212;','&#8230;','&#039;','&rarr;','&mdash;','&gt;','&lt;','{','}','#','"','—', '\'');
$outch = '';
$bings =  array(1, 16, 31, 46);
foreach ($bings as $bing) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.bing.com/search?format=rss&first='.$bing.'&q='.urlencode($key));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
$outch.= curl_exec($ch);
curl_close($ch);
}

$outch = str_ireplace($badchar, ' ', $outch);
$outch = str_ireplace('...', '.', $outch);
$outch = str_ireplace(' .', '.', $outch);
$outch = str_ireplace('..', '.', $outch);
$outch = str_ireplace(',.', '.', $outch);
$outch = str_ireplace(':.', '.', $outch);
$outch = preg_replace('#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#iS', '', $outch);
$outch = preg_replace('(\d{1,2}\/\d{1,2}\/\d{4})', '', $outch);
$outch = preg_replace('/(http:\/\/)(\S+)/i', '', $outch);
$outch = preg_replace('/(https:\/\/)(\S+)/i', '', $outch);

preg_match_all('!\<description\>(.*?)\</description\>!siu', $outch, $lines);
$content = array_unique($lines[1]);
unset($content[0]);
shuffle($content);
$content = implode(' ', $content);
// начало чисток переспама:
$content = str_ireplace('.', '.#', $content);
$arr = explode(' ', $content);
$arr = array_unique($arr);
$content = implode(' ', $arr);
$arr = explode('#', $content);
$n = floor(count($arr) /2);
$end = $n;
$content = array();
$i = 0;
while($i <= $end) {
$content[$i] = @str_ireplace('.', '', $arr[$i]). ' '.@trim(mb_strtolower($arr[$n], 'utf-8'));
$i++; $n++;
}
// конец чисткам переспама
$description = @$content[0];
$description = str_ireplace('"', '', $description);
$description = str_ireplace('\'', '', $description);
$line = $db->escapeString(serialize($content));
$description = $db->escapeString($description);

$search_content = implode(' ', $content);
$search = strip_tags($key.' '.$search_content);
$search = mb_strtolower($search, 'utf-8');
$search = explode(' ', $search);
$search = array_unique($search);
$search = implode(' ', $search);
$search = @$db->escapeString($search);

if ($ufurl == '1') {
$update = $db->exec("UPDATE pages SET content='".$line."', description='".$description."', search='".$search."' WHERE ufu='".$p."';");
} else {
$update = $db->exec("UPDATE pages SET content='".$line."', description='".$description."', search='".$search."' WHERE id='".$p."';");
}
$content = $line;
}
// конец парсинга контента
// вывод контента:
$content = @unserialize($content);
$cnt = $content;
$content = @array_slice($content, 0, $contentsize);
$content = '<p>'.@implode(' ', $content).'</p>';
$content = str_ireplace('. .', '.', $content);

// парсинг картинок
if ($fotos == '' AND $maxfoto != '0') {
// если ключевик слишком длинный:
if (mb_strlen($key, 'utf-8') > 30) {$keyp = preg_replace('!^(.{0,30})\s.*!su', '$1', $key);} else {$keyp = $key;}
// картинки из твиттера
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://twitter.com/search?q='.urlencode($keyp).'&src=typd&mode=photos');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
$outch = curl_exec($ch);
curl_close($ch);
preg_match_all('!data-resolved-url-small="(.*?)"!siu', $outch, $lines1);
// картинки из бинг
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.bing.com/images/search?q='.urlencode($keyp.' '.$imgsource));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);
preg_match_all('!imgurl:&quot;(.*?)&quot;,ow:&quot;!siu', $outch, $lines2);

$fotos = array_merge($lines1[1], $lines2[1]);
$fotos = array_unique($fotos);
shuffle($fotos);
$line = $db->escapeString(serialize($fotos));
if ($ufurl == '1') {
$update = $db->exec("UPDATE pages SET fotos='".$line."' WHERE ufu='".$p."';");
} else {
$update = $db->exec("UPDATE pages SET fotos='".$line."' WHERE id='".$p."';");
}
$fotos = $line;
}
// контент парсинга картинок
// вывод картинок:
$fotos = @unserialize($fotos);
$fotos = @array_slice($fotos, 0, $maxfoto);
$foto = '<img src="'.@implode('" style="float:left; margin:5px; vertical-align:top; width:'.$widthfoto.'; height:'.$heightfoto.';" title="'.$key.'" alt="'.$key.'"  class="img-responsive" /><img src="', $fotos).'" style="float:left; margin:5px; vertical-align:top; width:'.$widthfoto.'; height:'.$heightfoto.';" title="'.$key.'" alt="'.$key.'"  class="img-responsive" />';

// парсинг видео
if ($videos == '' AND $maxvideo != '0') {
// если ключевик слишком длинный:
if (mb_strlen($key, 'utf-8') > 30) {$keyp = preg_replace('!^(.{0,30})\s.*!su', '$1', $key);} else {$keyp = $key;}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.bing.com/videos/search?&q='.urlencode($keyp).'&qft=+filterui:msite-youtube.com&FORM=R5VR15');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie/'.$host.'.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
$outch = curl_exec($ch);
curl_close($ch);

preg_match_all('!youtube.com/watch\?v=(.*?)&!siu', $outch, $lines2);
$videos = array_unique($lines2[1]);
shuffle($videos);
$line = $db->escapeString(serialize($videos));
if ($ufurl == '1') {
$update = $db->exec("UPDATE pages SET videos='".$line."' WHERE ufu='".$p."';");
} else {
$update = $db->exec("UPDATE pages SET videos='".$line."' WHERE id='".$p."';");
}
$videos = $line;
}
// конец парсинга видео
// вывод видео:
$videos2 = @unserialize($videos);
$videos = @unserialize($videos);
$videos = @array_slice($videos, 0, $maxvideo);
$video = '<p class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://youtube.com/embed/'.@implode('?rel=0" allowfullscreen></iframe></p><p class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://youtube.com/embed/', $videos).'?rel=0" allowfullscreen></iframe></p>';

// перелинковка категорий:
if ($p == '1') {
// получаем примерное количество страниц: 
$page = $db->querySingle('SELECT id FROM pages ORDER BY id DESC;', true);
$d = 1; $i = '1';
$content.= '<center>';
while($i <= 10) {
$d = $d + $catpage;
if ($ufurl == '1') {
$content.= ' <a href="/category/'.$d.'/" class="btn btn-default">'.$d.'</a> ';
} else {
$content.= ' <a href="'.$script.'?'.$get_category.'='.$d.'" class="btn btn-default">'.$d.'</a> ';
}
$i++;
if ($d - $catpage > $page['id']) break;
}
$content.= '</center>';
}
echo '<!--'.$pageid.'-->';
// СТРАНИЦА (конец)
} elseif (isset($_GET[$get_category])) {
// КАТЕГОРИЯ
$p = @trim($_GET[$get_category]);
if (!ctype_digit($p)) {
header('HTTP/1.1 301 Moved Permanently');
header('Location: '.$script.'?'.$get_category.'=1');
die();
}

if ($ufurl == '1') {
$canonical = 'http://'.$host.'/category/'.$p.'/';
} else {
$canonical = 'http://'.$host.$script.'?'.$get_category.'='.$p;
}
$page = $db->querySingle('SELECT * FROM pages ORDER BY id DESC LIMIT '.($p-1).', 20;', true);
// полученное из базы:
$key = @$page['key'];
$foto = '';
$video = '';

$content = '<ul>';
$list = $db->query('SELECT id, key, ufu, description FROM pages ORDER BY id DESC LIMIT '.($p-1).', '.$catpage.';');
$mcount = 0;
while ($echo = $list->fetchArray()) 
{
if ($ufurl == '1') {
$content.= '<li><a href="/page/'.urlencode($echo['ufu']).'/">'.$echo['key'].'</a></li>';
} else {
$content.= '<li><a href="'.$script.'?'.$get_page.'='.$echo['id'].'">'.$echo['key'].'</a></li>';
}
if ($echo['description'] != '') {$content.= '<span>'.$echo['description'].'</span>';}
$mcount = $mcount + 1;
}
if ($mcount == 0) {
header('HTTP/1.1 404 Not Found');
header('Status: 404 Not Found');
}
// перелинковка категории:
$content.= '</ul><center>';

// получаем примерное количество страниц: 
$page = $db->querySingle('SELECT id FROM pages ORDER BY id DESC;', true);

if ($p > $catpage) {
if ($ufurl == '1') {
$content.= '<a href="/category/'.($p-$catpage).'/" class="btn btn-default">«««</a>';
} else {
$content.= '<a href="'.$script.'?'.$get_category.'='.($p-$catpage).'" class="btn btn-default">«««</a>';
}
}
$d = $p;
$i = '1';
while($i <= 10) {
$d = $d + $catpage;
if ($ufurl == '1') {
$content.= ' <a href="/category/'.$d.'/" class="btn btn-default">'.$d.'</a> ';
} else {
$content.= ' <a href="'.$script.'?'.$get_category.'='.$d.'" class="btn btn-default">'.$d.'</a> ';
}
$i++;
if ($d - $catpage > $page['id']) break;
}

if ($mcount == $catpage) {
if ($ufurl == '1') {
$content.= '<a href="/category/'.($p+$catpage).'/" class="btn btn-default">»»»</a>';
} else {
$content.= '<a href="'.$script.'?'.$get_category.'='.($p+$catpage).'" class="btn btn-default">»»»</a>';
}
}

// 
$content.= '</center>';
// конец перелинковки категорий
// пингуем rss и xml карту каждые 3 часа:
$pingdate = @file_get_contents('ping/'.$host.'.ping');
if ($time - $pingdate > 10800) {
file_put_contents('ping/'.$host.'.ping', $time);
// пингуем RSS:
if ($ufurl == '1') {
$rssurl = 'http://'.$host.'/rss/';
} else {
$rssurl = 'http://'.$host.$script.'?'.$get_feed.'=rss';
}
$xmlping = '<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>weblogUpdates.ping</methodName>
    <params>
        <param>
            <value>'.$host.'</value>
        </param>
        <param>
            <value>'.$rssurl.'</value>
        </param>
    </params>
</methodCall>';

// яндекс блоги:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://ping.blogs.yandex.ru/RPC2');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . mb_strlen($xmlping), 'Content-type: text/xml')); 
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlping);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
$outch = curl_exec($ch);
curl_close($ch);

// google блоги:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://blogsearch.google.com/ping/RPC2');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . mb_strlen($xmlping), 'Content-type: text/xml')); 
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlping);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
$outch = curl_exec($ch);
curl_close($ch);

if ($ufurl == '1') {$sitemapurl = 'http://'.$host.'/sitemap/';} else {$sitemapurl = 'http://'.$host.$script.'?'.$get_feed.'=sitemap';}
// xml карту в google
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.google.com/webmasters/sitemaps/ping?sitemap='.$sitemapurl);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
$outch = curl_exec($ch);
curl_close($ch);

// xml карту в bing
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.bing.com/webmaster/ping.aspx?sitemap='.$sitemapurl);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
$outch = curl_exec($ch);
curl_close($ch);

// КАТЕГОРИЯ (конец)
}
} elseif (isset($_GET[$get_feed])) {
// xml карта сайта
if ($_GET[$get_feed] == 'sitemap') {
header('Content-Type: text/xml');
$list = $db->query('SELECT id, ufu FROM pages;'); 
echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
while ($echo = $list->fetchArray()) { 
if ($ufurl == '1') {
echo '<url>
   <loc>http://'.$host.'/page/'.urlencode($echo['ufu']).'/</loc>
   <changefreq>weekly</changefreq>
   <priority>1.0</priority>
</url>
';
} else {
echo '<url>
   <loc>http://'.$host.$script.'?'.$get_page.'='.$echo['id'].'</loc>
   <changefreq>weekly</changefreq>
   <priority>1.0</priority>
</url>
';
}
} 
echo '</urlset>';
die();
}
//рсс лента
if ($_GET[$get_feed] == 'rss' OR $uri == '/rss/') {
header('Content-Type: text/xml');
if ($ufurl == '1') {
$rssurl = 'http://'.$host.'/rss/';
$rssindexurl = 'http://'.$host.'/';
} else {
$rssurl = 'http://'.$host.$script.'?'.$get_feed.'=rss';
$rssindexurl = 'http://'.$host.$script;
}
$list = $db->query('SELECT id, key, ufu, description FROM pages ORDER BY id DESC LIMIT 30;'); 
echo '<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
<channel>
<title>'.$host.'</title>
<link>'.$rssindexurl.'</link>
<atom:link href="'.$rssurl.'" rel="self" type="application/rss+xml" />
<language>'.$lang.'</language>
';
while ($echo = $list->fetchArray()) 
{ 
echo '
<item>
<title><![CDATA['.$echo['key'].']]></title>';
if ($ufurl == '1') {
echo '<link>http://'.$host.'/page/'.urlencode($echo['ufu']).'/</link>
<guid>http://'.$host.'/page/'.urlencode($echo['ufu']).'/</guid>';
} else {
echo '<link>http://'.$host.$script.'?'.$get_page.'='.$echo['id'].'</link>
<guid>http://'.$host.$script.'?'.$get_page.'='.$echo['id'].'</guid>';
}
echo '<description><![CDATA['.$echo['description'].']]></description>
</item>
';
}
echo '
</channel>
</rss>';
die();
}
// удаление страницы:
if ($_GET[$get_feed] == 'del') {
$delpagenum = @trim(strip_tags($_GET['pg']));
if (!ctype_digit($delpagenum)) {die('укажите номер страницы');}
$delpagenum = @$db->escapeString(trim($delpagenum));
if ($pass != '' AND @$_GET['pass'] == $pass AND $delpagenum != '1') {
$del = @$db->exec('DELETE FROM pages WHERE id='.$delpagenum.';');
//echo '<meta http-equiv="refresh" content="1; url='.$script.'?'.$get_page.'='.$p.'">';
die('удалено');
}
die('ошибка');
}
// список ссылок в формате wmsn кросспостинга:
if ($_GET[$get_feed] == 'wmsn') {

$list = $db->query('SELECT id, key, ufu FROM pages;'); 

while ($echo = $list->fetchArray()) { 
if ($ufurl == '1') {
echo $echo['key'].'|http://'.$host.'/page/'.urlencode($echo['ufu']).'/<br>';
} else {
echo $echo['key'].'|http://'.$host.$script.'?'.$get_page.'='.$echo['id'].'<br>';
}
} 
die();
}
// список ссылок в bbcode формате:
if ($_GET[$get_feed] == 'bb') {

$list = $db->query('SELECT id, key, ufu FROM pages;'); 

while ($echo = $list->fetchArray()) { 
if ($ufurl == '1') {
echo '[url=http://'.$host.'/page/'.urlencode($echo['ufu']).'/]'.$echo['key'].'[/url]<br>';
} else {
echo '[url=http://'.$host.$script.'?'.$get_page.'='.$echo['id'].']'.$echo['key'].'[/url]<br>';
} 
}
die();
}
// список ссылок в HTML формате:
if ($_GET[$get_feed] == 'html') {

$list = $db->query('SELECT id, key, ufu FROM pages;'); 

while ($echo = $list->fetchArray()) { 
if ($ufurl == '1') {
echo '&lt;a href="http://'.$host.'/page/'.urlencode($echo['ufu']).'/"&gt;'.$echo['key'].'&lt;/a&gt;<br>';
} else {
echo '&lt;a href="http://'.$host.$script.'?'.$get_page.'='.$echo['id'].'"&gt;'.$echo['key'].'&lt;/a&gt;<br>';
} 
}
die();
}
// список ссылок в TXT формате:
if ($_GET[$get_feed] == 'txt') {

$list = $db->query('SELECT id, ufu FROM pages;'); 

while ($echo = $list->fetchArray()) { 
if ($ufurl == '1') {
echo 'http://'.$host.'/page/'.urlencode($echo['ufu']).'/<br>';
} else {
echo 'http://'.$host.$script.'?'.$get_page.'='.$echo['id'].'<br>';
}
} 
die();
}

// robots.txt:
if ($_GET[$get_feed] == 'robots') {
header('Content-Type: text/plain');
if ($ufurl == '1') {
echo 'User-agent: *
Disallow: '.$script.'
Sitemap: http://'.$host.'/sitemap/

User-Agent: Yandex
Disallow: '.$script.'
Sitemap: http://'.$host.'/sitemap/
Host: '.$host.'
';
} else {
echo 'User-agent: *
Disallow: /*/
Sitemap: http://'.$host.$script.'?'.$_GET[$get_feed].'=sitemap

User-Agent: Yandex
Disallow: /*/
Sitemap: http://'.$host.$script.'?'.$_GET[$get_feed].'=sitemap
Host: '.$host.'
';
}
die();
}

// Добавление ключей через форму:
if ($_GET[$get_feed] == 'add') {
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (@trim($_POST['pass']) != $pass OR $pass == '') die('Укажите правильный пароль');
$list = $_POST['list'];
$list = explode("\n", $list);
$db->exec('BEGIN IMMEDIATE;');
foreach ($list as $lines) {
$lines = trim($lines);
$line = explode('|', $lines);
$key = $db->escapeString($line[0]);
$column1 = @$db->escapeString(trim($line[1]));
$column2 = @$db->escapeString(trim($line[2]));
$column3 = @$db->escapeString(trim($line[3]));
$column4 = @$db->escapeString(trim($line[4]));
$column5 = @$db->escapeString(trim($line[5]));
$column6 = @$db->escapeString(trim($line[6]));
$column7 = @$db->escapeString(trim($line[7]));
$column8 = @$db->escapeString(trim($line[8]));
$column9 = @$db->escapeString(trim($line[9]));
$column10 = @$db->escapeString(trim($line[10]));
$column11 = @$db->escapeString(trim($line[11]));
$column12 = @$db->escapeString(trim($line[12]));
$column13 = @$db->escapeString(trim($line[13]));
$column14 = @$db->escapeString(trim($line[14]));
$column15 = @$db->escapeString(trim($line[15]));
$ufu = UserFriendlyURLs($line[0]);
$ufu = @$db->escapeString(trim($ufu));

$search = strip_tags($key);
$search = mb_strtolower($search, 'utf-8');
$search = explode(' ', $search);
$search = array_unique($search);
$search = implode(' ', $search);
$search = @$db->escapeString($search);

$add = @$db->exec("INSERT INTO pages (key, ufu, column1, column2, column3, column4, column5, column6, column7, column8, column9, column10, column11, column12, column13, column14, column15, search) VALUES ('".$key."', '".$ufu."', '".$column1."', '".$column2."', '".$column3."', '".$column4."', '".$column5."', '".$column6."', '".$column7."', '".$column8."', '".$column9."', '".$column10."', '".$column11."', '".$column12."', '".$column13."', '".$column14."', '".$column15."', '".$search."');");
$line = $lines;
if (!$add) {
echo '- '.$line.'<br>';
} else {
echo '+ '.$line.'<br>';
}
}
$db->exec('COMMIT;');
$db->close();

} else {
if (!file_exists('db/'.$host.'.db')) {
// создаем базу:	
$db = new SQLite3('db/'.$host.'.db');
$db->busyTimeout(5000);
$db->exec('PRAGMA journal_mode=WAL;');
if (!$db) die('Невозможно создать базу!');

$query = $db->exec('CREATE TABLE IF NOT EXISTS pages (id INTEGER PRIMARY KEY, url TEXT UNIQUE, key TEXT UNIQUE, keys INTEGER, content TEXT, description TEXT, fotos TEXT, videos TEXT, ufu TEXT UNIQUE, column1 TEXT, column2 TEXT, column3 TEXT, column4 TEXT, column5 TEXT, column6 TEXT, column7 TEXT, column8 TEXT, column9 TEXT, column10 TEXT, column11 TEXT, column12 TEXT, column13 TEXT, column14 TEXT, column15 TEXT, search TEXT);'); 
if (!$query) die('Невозможно создать таблицу!');
}
echo '<!DOCTYPE html>
<html>
  <head>
<title>Добавить ключевики</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<link rel="stylesheet" href="https://wmsn.biz/files/bootstrap/css/bootstrap.min.css">
<script src="https://wmsn.biz/files/jquery.min.js"></script>
<script src="https://wmsn.biz/files/bootstrap/js/bootstrap.min.js"></script>
<style type="text/css">
body {
padding-top: 5px;
padding-bottom: 10px;
padding-left: 10px;
padding-right: 10px;
overflow-y: scroll;
}
form {
padding-left: 1px;
}
</style>
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
<div class="container">
	<div class="col-md-2"></div>
<div class="col-md-8 well">
<div class="text-center alert alert-info">	
Autodor.SQLite.Wmsn
</div>
<form class="form-horizontal" role="form" action="" method="post">
  <div class="form-group">
    <label for="pass" class="col-sm-4 control-label">Пароль</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" id="pass" name="pass" required="required" value="">
    </div>
  </div>
  <div class="form-group">
    <label for="pass" class="col-sm-4 control-label">Ключевики:</label>
    <div class="col-sm-8">
      <textarea class="form-control" name="list"></textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
<button type="submit" name="submit" class="btn btn-primary col-md-4">Добавить</button> 
    </div>
  </div>
</form>
<p>Формат строк с ключевиками такой же, как и для первоначального txt файла ключевиков, т.е. можно доп поля с разделенными вертикальной чертой | данными (до 15 доп полей).</p>
<hr />
<iframe src="https://wmsn.biz/mframe.php?userid=2&limit=5&char=150" 
width="100%" frameborder="0" height="375"></iframe>
</div>

  <div class="col-md-2"></div> 
</div>
<div class="text-center"><small>Powered by <a href="http://dor.tdsse.com/" target="_blank">Autodor.SQLite.Wmsn</a></small></div> 
</body>
</html>
';
}
die();
}

// генерация кеширования:
if ($_GET[$get_feed] == 'gen') {
$p = @trim($_GET['p']);
if (!ctype_digit($p)) {
header('HTTP/1.1 301 Moved Permanently');
header('Location: '.$script.'?'.$get_feed.'=gen&p=1');
die();
}

$list = $db->query('SELECT id, ufu FROM pages ORDER BY id ASC LIMIT '.($p-1).', 20;');
$mcount = 0;
while ($echo = $list->fetchArray()) 
{
if ($ufurl == '1') {
echo '<iframe src="/page/'.urlencode($echo['ufu']).'/" width="70" height="25" frameborder="0" scrolling="no"></iframe>';
} else {
echo '<iframe src="'.$script.'?'.$get_page.'='.$echo['id'].'&tpl=no" width="35" height="25" frameborder="0" scrolling="no"></iframe>';
}
$mcount = $mcount + 1;
}
if ($mcount > 1) {
echo '<meta http-equiv="refresh" content="1; url='.$script.'?'.$get_feed.'=gen&p='.($p+20).'">';
} else echo '<h1>Генерация кэша завершена.</h1>';
die();
}
// конец генерации кеширования

// хрень непонятная:
header('HTTP/1.1 301 Moved Permanently');
header('Location: '.$script.'?'.$get_page.'=1');
die();
} elseif (isset($_GET[$get_search])) {
// поиск по дорвею:
$key = trim(urldecode(strip_tags($_GET[$get_search])));
if ($sindex == '1') {
if ($ufurl == '1') {
$canonical = 'http://'.$host.'/tag/'.urlencode($key).'/';
} else {
$canonical = 'http://'.$host.$script.'?'.$get_search.'='.urlencode($key);
}
} else {
$canonical = 'http://'.$host.$script;
}
$line = str_ireplace(' ', '%', $key);
$line = $db->escapeString($line);

$pages = $db->query('SELECT * FROM pages WHERE search LIKE \'%'.$line.'%\' LIMIT '.$slimit.';');
while ($echo = $pages->fetchArray()) {
if ($ufurl == '1') {
$content.= '<li><a href="/page/'.urlencode($echo['ufu']).'/">'.$echo['key'].'</a></li>';
} else {
$content.= '<li><a href="'.$script.'?'.$get_page.'='.$echo['id'].'">'.$echo['key'].'</a></li>';
}
if ($echo['description'] != '') {$content.= '<span>'.$echo['description'].'</span>';}

$description = @$echo['description'];
$fotos = @$echo['fotos']; // сериализованный массив ссылок на картинки
$videos = @$echo['videos']; // сериализованный массив id видео с ютуба
$p = @$echo['id'];
}
// вывод картинок:
$fotos = @unserialize($fotos);
$fotos = @array_slice($fotos, 0, $maxfoto);
$foto = '<img src="'.@implode('" style="float:left; margin:5px; vertical-align:top; width:'.$widthfoto.'; height:'.$heightfoto.';" title="'.$key.'" alt="'.$key.'"  class="img-responsive" /><img src="', $fotos).'" style="float:left; margin:5px; vertical-align:top; width:'.$widthfoto.'; height:'.$heightfoto.';" title="'.$key.'" alt="'.$key.'"  class="img-responsive" />';
// вывод видео:
$videos = @unserialize($videos);
$videos = @array_slice($videos, 0, $maxvideo);
$video = '<p class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://youtube.com/embed/'.@implode('?rel=0" allowfullscreen></iframe></p><p class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://youtube.com/embed/', $videos).'?rel=0" allowfullscreen></iframe></p>';

} else {
// морда или какая-то неучтенна фигня:
$canonical = 'http://'.$host.'/';
// полученное из базы данных морды:
$page = $db->querySingle('SELECT * FROM pages WHERE id=1', true);
$key = @$page['key'];
$description = @$page['description'];
$fotos = @$page['fotos']; // сериализованный массив ссылок на картинки
$videos = @$page['videos']; // сериализованный массив id видео с ютуба
$content = '<ul>';
$list = $db->query('SELECT id, key, ufu, description FROM pages ORDER BY random() LIMIT '.$catpage.';');
while ($echo = $list->fetchArray()) {
if ($ufurl == '1') {
$content.= '<li><a href="/page/'.urlencode($echo['ufu']).'/">'.$echo['key'].'</a></li>';
} else {
$content.= '<li><a href="'.$script.'?'.$get_page.'='.$echo['id'].'">'.$echo['key'].'</a></li>';
}
if ($echo['description'] != '') {$content.= '<span>'.$echo['description'].'</span>';}
}
if ($ufurl == '1') {
$content.= '<li><a href="/page/'.urlencode(UserFriendlyURLs($key)).'/">'.$key.'</a></li>';
} else {
$content.= '<li><a href="'.$script.'?'.$get_page.'=1">'.$key.'</a></li>';
}
$content.= '</ul>'; 
// вывод картинок:
$fotos = @unserialize($fotos);
$fotos = @array_slice($fotos, 0, $maxfoto);
$foto = '<img src="'.@implode('" style="float:left; margin:5px; vertical-align:top; width:'.$widthfoto.'; height:'.$heightfoto.';" title="'.$key.'" alt="'.$key.'"  class="img-responsive" /><img src="', $fotos).'" style="float:left; margin:5px; vertical-align:top; width:'.$widthfoto.'; height:'.$heightfoto.';" title="'.$key.'" alt="'.$key.'"  class="img-responsive" />';
// вывод видео:
$videos = @unserialize($videos);
$videos = @array_slice($videos, 0, $maxvideo);
$video = '<p class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://youtube.com/embed/'.@implode('?rel=0" allowfullscreen></iframe></p><p class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" src="https://youtube.com/embed/', $videos).'?rel=0" allowfullscreen></iframe></p>';

// получаем примерное количество страниц: 
$page = $db->querySingle('SELECT id FROM pages ORDER BY id DESC;', true);
$d = 1; $i = '1';
$content.= '<center>';
while($i <= 10) {
$d = $d + $catpage;
if ($ufurl == '1') {
$content.= ' <a href="/category/'.$d.'/" class="btn btn-default">'.$d.'</a> ';
} else {
$content.= ' <a href="'.$script.'?'.$get_category.'='.$d.'" class="btn btn-default">'.$d.'</a> ';
}
$i++;
if ($d - $catpage > $page['id']) break;
}
$content.= '</center>';
}
// конец морды

// ссылки перелинковки
$link = '<ul>';
$list = $db->query('SELECT id, key, ufu FROM pages ORDER BY id LIMIT '.($pageid+1).', '.$keycount.';');
while ($echo = $list->fetchArray()) 
{
if ($ufurl == '1') {
$link.= '<li><a href="/page/'.urlencode($echo['ufu']).'/">'.$echo['key'].'</a></li>';
} else {
$link.= '<li><a href="'.$script.'?'.$get_page.'='.$echo['id'].'">'.$echo['key'].'</a></li>';
}
}
$link.= '</ul>';

// подгружаем шаблон:
if (@$_GET['tpl'] == 'no') {
echo 'OK';
} else {
require_once $tpl;
}
$exec_time = microtime(true) - $start_time; // вычисляем сколько времени выполнялся скрипт
$exec_time = round($exec_time, 3); // такая точная цифра нам не нужна, округлим
$memory = memory_get_usage();
$memory = round($memory / 1024, 3);
echo '<!-- Time: '.$exec_time.' Sec. | Memory: '.$memory.' Kb. -->'; // выводим время выполнения крипта
