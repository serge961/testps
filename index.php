<?php
require('phpQuery/phpQuery.php');
session_start();

// БЛОК ПОЛУЧЕНИЯ ИНФОРМАЦИИ С САЙТА
$url ='https://otzovik.com/reviews/myasoet_shop-internet_magazin_myasa/';
$referer = 'https://google.com';

function get_content_curl($url, $referer){

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

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); //куда идем
curl_setopt($ch, CURLOPT_REFERER, $referer); //откуда пришли
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
curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
$html = curl_exec($ch);
curl_close($ch);

return $html;
}


$html = get_content_curl($url, $referer);

$pq = phpQuery::newDocument($html);
$links = $pq->find('script');
$link = $links->text();

//ПРОВЕРКА НА КАПЧУ
if (strpos($link, 'capt4a') != false){

    list($a, $b, $c) = explode("'",$link);
    $link = 'otzovik.com'.$b;
    $html = get_content_curl($link, $url);
    $pq = phpQuery::newDocument($html);
    $links = $pq->find('input');
    $form_link = $links->attr('value');
    $links = $pq->find('img');
    $link = $links->attr('src');

echo'
<form action="send_capcha.php" method="post">
<!--<form action="index.php" method="post">-->

    <!--<input type="hidden" name="captcha_url" value="/in_town/shops/?&capt4a=3431660990111734">-->
	<input type="hidden" name="captcha_url" value="'.$form_link.'">
	<p>Код на картинке:</p><input type="text" name="llllllllllllllll" style="width: 120px; font-size:20px;">
	<!--<img src="/scripts/captcha/index.php?rand=5033040">-->
	<img src="https://otzovik.com'.$link.'">
	<input id="btn" name=action_capcha_ban type=submit value="Отправить">
	</form>';
}else{
	$pq = phpQuery::newDocument($html);
};

//ОБРАБОТКА ДАННЫХ ПАРСИНГА
$links = $pq->find('.status4');
$n=1;
$data=[];
foreach ($links as $link)
{
	$pqLink = pq($link);	$info = $pqLink->find('.item-left .user-info .login-line .user-login span');
	$data[$n]['name'] = trim($info->text());

//	$info = $pqLink->find('.item-right h3');
//	$data[$n]['title'] = trim($info->text());

	$info = $pqLink->find('.item-right .review-postdate');
	$data[$n]['date'] = trim($info->text());

	$info = $pqLink->find('.item-right .product-rating');
	$data[$n]['score'] = trim($info->attr('title'));

//	$info = $pqLink->find('.item-right .review-plus');
//	$data[$n]['plus'] = trim($info->text());

//	$info = $pqLink->find('.item-right .review-minus');
//	$data[$n]['minus'] = trim($info->text());

	$info = $pqLink->find('.item-right .review-bar a');
	$link_otzuv = 'https://otzovik.com'.$info->attr('href');

    $str2 = get_content_curl($link_otzuv, $url);
	$pq1 = phpQuery::newDocument($str2);

    $links1 = $pq1->find('.review-body');
	$data[$n]['otzuv'] = trim($links1->text());

	$n++;

usleep(5000000);	
};

if (!empty($data)){

    print_r($data);

};


?>
