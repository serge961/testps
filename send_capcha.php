<?php
require('index.php');
session_destroy();

$captcha = $_POST['llllllllllllllll'];
$captcha_url = $_POST['captcha_url'];

	$headers = array(
		'cache-control: max-age=0',
		'upgrade-insecure-requests: 1',
		'user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
		'sec-fetch-user: ?1',
		'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
		'x-compress: null',
		'sec-fetch-site: none',
		'sec-fetch-mode: navigate',
		'accept-encoding: deflate, br',
		'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
	);


$post =[
	'captcha_url'=> $captcha_url,
	'llllllllllllllll' => $captcha,
];
$send_captcha_url = 'https://otzovik.com'.$captcha_url;
$ch = curl_init($send_captcha_url);
curl_setopt($ch, CURLOPT_REFERER, $send_captcha_url);
curl_setopt($ch, CURLOPT_COOKIESESSION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookie.txt'); // сохраняем куки
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookie.txt'); //передаем куки
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //отправить загловки
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // возврат результата в качестве строки, а не в браузер
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //проверять ssl сертификат CA
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//проверить ssl сертификат HOST
curl_setopt($ch, CURLOPT_HEADER, true); //вывод шапки сайта в текст
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // переходит по редиректам
curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt ($ch, CURLOPT_POST, true); //FIXED
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$response = curl_exec($ch);
curl_close($ch);
/*
var_dump($response);
echo '-----------------------<br>';
echo $send_captcha_url;
echo '<br>-----------------------<br>';
echo get_content_curl($send_captcha_url, $send_captcha_url);
*/
header('Location: index.php');
?>