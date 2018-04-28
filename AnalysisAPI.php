<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/24
 * Time: 上午9:13
 */

require_once "iOSModelVar.php";
require_once "iOSModelConstant.php";

class AnalysisAPI
{
    private $api;

    private $htmlSourceCode;
    

    //域名
    public $domain;

    //数据模型名称
    public $modelName;

    //数据模型字典
    public $jsonData;

    //数据模型额外附属字典
    public $jsonExtra;

    //资源具体位置
    public $detailURLPath;
    
    //api描述
    public $apiDesC;
    
    //参数数组集
    public $parameters = array();

    public function __construct($api)
    {
        $this->api = $api;
        $this->baseInit();
        $this->curlexec();
        $this->analysisHtml();
    }

    private function baseInit() {
        global $modelURL;
        global $domainsMap;
        global $classMap;
        $urlCompoents = explode(DIRECTORY_SEPARATOR, $modelURL);
        $this->domain = $urlCompoents[3];
        $modelName = $urlCompoents[count($urlCompoents) - 1];
        $this->modelName =  isset($domainsMap[$this->domain]['pre']) ? $domainsMap[$this->domain]['pre'] . ucfirst($modelName) : $modelName;
        $classMap['data'] = ucfirst($this->modelName);

        for ($i = 0; $i <= 3; $i++) {
            unset($urlCompoents[$i]);
        }

        $this->detailURLPath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $urlCompoents);
    }

    private function curlexec() {
        global $modelURL;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $modelURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $this->htmlSourceCode = curl_exec($ch);
        $this->htmlSourceCode = self::subHtmlStr($this->htmlSourceCode, UpateTimeHtmlHeader);
        curl_close($ch);
    }

    private function analysisHtml() {
        $this->apiDesC = self::subHtmlStr($this->htmlSourceCode, TitleHtmlHeader, TitleHtmlTail);
        $parameterResult = self::subHtmlStr($this->htmlSourceCode, ParameterHtml, RightReturnHtml);
        $parameterResult = self::subHtmlStr($parameterResult, null, ReturnHtml);
        $parameterResult = self::subHtmlStr($parameterResult, ULHtmlHeader, ULHtmlTail);
        $parameterArr = explode(LiHtmlTail, $parameterResult);
        $json = self::subHtmlStr($this->htmlSourceCode, JsonHtmlHeader, JsonHtmlTail);
        $jsonArr = json_decode($json, true);
        $this->jsonData = ['data' => isset($jsonArr['info']['data']) ? $jsonArr['info']['data'] : null] ;
        $this->jsonExtra = isset($jsonArr['info']['extra']) ? $jsonArr['info']['extra'] : null;
        $this->jsonData =  self::transformValueToType($this->jsonData);
        $this->jsonExtra = self::transformValueToType($this->jsonExtra);
        
        foreach ($parameterArr as $parameterDesc) {
            $parameter = self::subHtmlStr($parameterDesc, CodeHtmlHeader, CodeHtmlTail);
            if ($parameter !== false) {
                if ($parameter == 'page' || $parameter == 'limit') {
                    $this->parameters[] = [
                        'type' => "(int)",
                        'name' => $parameter
                    ];
                } else {
                    $this->parameters[] = [
                        'type' => "(NSString *)",
                        'name' => $parameter
                    ];
                }
            }
        }
        
        
    }

    public static function subHtmlStr($str, $header, $tail = null) {
        if ($header == null) {
            $headerPos = 0;
        } else {
            $headerPos = strpos($str, $header);
            $headerPos += strlen($header);
        }

        if ($tail === null) {
            return substr($str, $headerPos);
        }

        $tailPos = strpos($str, $tail);
        if ($tailPos === false) {
            return substr($str, $headerPos);
        }

        $strLenght = $tailPos - $headerPos;
        $result = substr($str, $headerPos, $strLenght);
        return $result;
    }

    //将字典中相关的数值转为相关的类型
    private static function transformValueToType($dict) {
        global $modelType, $classMap;
        $newDict = count($dict) > 0 ? array() : null;
        if ($newDict === null) {
            return $newDict;
        }
        foreach ($dict as $key => $value) {
            //一开始如果是数组,则只取第一部分,并把其它部分截取掉
            if ($key === 0) {
                $newDict = $value;
                return self::transformValueToType($newDict);
                break;
            }
            
            if (!is_string($value)) {
                //有类映射并且是数值是数组类型
                if (isset($classMap[$key]) && isset($value[0])) {
                    $newValue = $value[0];
                    $newKey = "NSArray<" . ucfirst($classMap[$key]) . $modelType[0] . "* > *";
                    $newDict[$key] = new IOSObject($newKey, self::transformValueToType($newValue));
                    break;
                } else if (isset($classMap[$key]) && !isset($value[0]) ) {
                    $newValue = $dict[$key];
                    $newKey = ucfirst($classMap[$key]) . $modelType[0] . ' *';
                    $newDict[$key] = new IOSObject($newKey, self::transformValueToType($newValue));
                } else if (!$classMap[$key] && isset($value[0]) && is_string($value[0])){
                    $type = "NSArray<NSString* > *";
                    $newDict[$key] = $type;
                } else {
                    self::transformValueToType(null);
                }

            } else if (strpos($key, 'is') !== false) {
                $value = 'BOOL';
                $newDict[$key] = $value;
            } else {
                $value = 'NSString *';
                $newDict[$key] = $value;
            }
        }

        return $newDict;
    }
}

class IOSObject {
    public $type;
    public $value;

    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }
}