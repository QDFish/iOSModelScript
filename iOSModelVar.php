<?php
/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/23
 * Time: 上午9:15
 */

require_once "iOSModelConstant.php";

$modelURL = "http://10.10.50.205:1234/Live_Api/v1/Family/familyLog";
$desPath = "/Users/zgzheng/Desktop";
$modelState = 1;//0为Item类型 1位Request类型
$classMap = [
//    'info' => 'LiveNewFamilyInfo',
//    'list' => 'LiveNewList',
];

$domainsMap = [
    "Live_Api" => [
        "pre" => LivePre,
        "domain" => "self.liveApiDomain",
        "reqType" => 'TTREQ_LIVE_SYS'
    ]
];

$fileAnnotation = '//  Created by ' . $_SERVER['USER'] . ' on ' .date('Y/m/d', $_SERVER['REQUEST_TIME']) . "\n" . '//  Copyright ©' . date('Y', $_SERVER['REQUEST_TIME']) .'年 厦门海豹信息技术. All rights reserved.' . "\n//\n\n";



$modelType = ['Item', 'Request'];;  








