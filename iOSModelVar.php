<?php
/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/23
 * Time: 上午9:15
 */

require_once "iOSModelConstant.php";

$modelURL = "http://10.10.50.205:1234/Forum_Api/v5/Account/getAccountHomepageInfo";
$desPath = "/Users/zgzheng/TouchiOS_new/TaQu/Controller/Live/Script";
$modelState = 1;//0为Item类型 1位Request类型
$requestMethod = 'POST';
$classMap = [
    'account_info' => 'ForumAccountInfo',
    'img_list' => 'ForumImg',
    'account_detail' => 'ForumAccountDetail',
    'gift_list' => 'ForumGiftIcon',
    'follow_circle' => 'ForumCircle',
    'circle_list' => 'ForumCirclDetail',
    'gift' => 'ForumGift'
];

//下面的变量不需要修改
$domainsMap = [
    "Live_Api" => [
        "pre" => 'live',
        "domain" => "self.liveApiDomain",
        "reqType" => 'TTREQ_LIVE_SYS'
    ],
    "Live_Trade" => [
        "pre" => 'live',
        "domain" => "self.liveTradeDomain",
        "reqType" => 'TTREQ_LIVE_SYS'
    ],
    "Forum_Api" => [
        "pre" => 'forum',
        "domain" => "self.forumDomain",
        "reqType" => 'TTREQ_BBS_SYS'
    ],
];

$fileAnnotation = '//  Created by ' . $_SERVER['USER'] . ' on ' .date('Y/m/d', $_SERVER['REQUEST_TIME']) . "\n" . '//  Copyright ©' . date('Y', $_SERVER['REQUEST_TIME']) .'年 厦门海豹信息技术. All rights reserved.' . "\n//\n\n";



$modelType = ['Item', 'Request'];;  








