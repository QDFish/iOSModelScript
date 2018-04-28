<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/27
 * Time: 下午3:19
 */

require_once __DIR__ . "/../AnalysisAPI.php";
require_once __DIR__ . "/../iOSTools.php";
require_once __DIR__ . "/../iOSModelVar.php";

class URLInterfaceTemplate
{
    public static function template(AnalysisAPI $api) {
        global $requestMethod;
        
        $obj = new URLInterfaceObj($api);
        
        if ($requestMethod === 'GET') {
            $str = <<<EOD
$obj->apiAnnotation
- (NSString *){$obj->modelName}UrlWith{$obj->apiMethod};

@end


EOD;
        } else {
            $str = <<<EOD
$obj->apiAnnotation
- (NSString *){$obj->modelName}Url;

@end


EOD;
        }
        
        return $str;
    }
}

class URLInterfaceObj {

    public $modelName;

    public $apiMethod;

    public $apiAnnotation;
    

    public function __construct(AnalysisAPI $api) {
        
        global $requestMothod;
        
        $this->modelName = lcfirst($api->modelName);
        $this->apiMethod = apiInterface($api->parameters);
        $this->apiAnnotation = <<< EOT
/*
 * $api->apiDesC
 */
EOT;
    }
}
