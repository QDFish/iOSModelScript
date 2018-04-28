<?php
/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/23
 * Time: 上午9:14
 */

require_once "iOSModelVar.php";
require_once "iOSModelConstant.php";
require_once "AnalysisAPI.php";
require_once "WriteHandle.php";

$api = new AnalysisAPI($modelURL);
$writeHandle = new WriteHandle($api);

//不接受不创建Request且要创建Item但是jsondata为空的情况
if ($modelState === 0 && $api->jsonData['data']->value === null) {
    echo '不接受不创建Request且要创建Item但是jsonData为空的情况' . PHP_EOL;
    exit(1);
}

//写入UrlManager
$writeHandle->writeToManager();

//创建并写入相应的数据模型的文件和头部信息
$writeHandle->writeInterfaceFileHeader();
$writeHandle->writeImplementationFileHeader();

//只写数据文件
if ($modelState === 0) {
    $writeHandle->writeToModel();
} 

//请求跟数据文件一起
else {
    $writeHandle->writeToRequest();
    $writeHandle->writeToModel();
}
