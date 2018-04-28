<?php
/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/27
 * Time: 下午4:11
 */

require_once __DIR__ . "/iOSModelVar.php";

//大写代替下滑线
function ucReplaceUnderLine($str) {
    $strs = explode('_', $str);
    $isFirstStr = true;
    $result = '';
    foreach ($strs as $value) {
        if ($isFirstStr) {
            $value = lcfirst($value);
            $isFirstStr = false;
        }  else {
            $value = ucfirst($value);
        }
        $result = $result . $value;
    }

    return $result;
}

//URL头文件参数描述
function apiInterface($parameters) {
    $parameterDesc = '';
    foreach ($parameters as $parameterInfo) {
        $name = ucReplaceUnderLine($parameterInfo['name']);
        $type = $parameterInfo['type'];
        $parameterDesc = $parameterDesc . $name . ":" . $type . $name . ' ';
    }

    $parameterDesc = ucfirst($parameterDesc);
    return substr($parameterDesc, 0, strlen($parameterDesc) - 1);
}

//URL实现文件参数描述
function apiImplementation($parameters) {
    $parameterDesc = '';
    foreach ($parameters as $parameterInfo) {
        $str = '=%@';
        $name = $parameterInfo['name'];
        $type = $parameterInfo['type'];
        if ($type == '(int)') {
            $str = '=%d';
        }
        $parameterDesc = $parameterDesc . $name . $str . '&';
    }
    $parameterDesc = substr($parameterDesc, 0, strlen($parameterDesc) - 1);

    return $parameterDesc;
}

function parameterStr($parameters) {
    $parameterDesc = '';
    foreach ($parameters as $parameterInfo) {
        $name = ucReplaceUnderLine($parameterInfo['name']);
        $parameterDesc = $parameterDesc . $name. ', ';
    }

    return substr($parameterDesc, 0, strlen($parameterDesc) - 2);
}

function parameterSetter($parameters) {
    $parameterDesc = '';
    foreach ($parameters as $parameterInfo) {
        $name = ucReplaceUnderLine($parameterInfo['name']);
        $type = $parameterInfo['type'];
        $parameterDesc .= $name . ":" . $name . ' ';
    }

    $parameterDesc = ucfirst($parameterDesc);
    return substr($parameterDesc, 0, strlen($parameterDesc) - 1);
}

function classMapContainType($type) {
    global $classMap;
    global $modelType;
    $containType = false;
    foreach ($classMap as $key => $value) {
        if (strpos($type, $value) !== false) {
            $containType = ucfirst($value) . $modelType[0];
            break;
        }
    }

    return $containType;
}
