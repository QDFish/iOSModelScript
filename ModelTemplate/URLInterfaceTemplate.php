<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/27
 * Time: 下午3:19
 */

require_once __DIR__ . "/../AnalysisAPI.php";
require_once __DIR__ . "/../iOSTools.php";

class URLInterfaceTemplate
{
    public static function template(AnalysisAPI $api) {
        $obj = new URLInterfaceObj($api);
        
        $str = <<<EOD
$obj->apiAnnotation
- (NSString *){$obj->modelName}UrlWith{$obj->apiMethod};

@end


EOD;
        return $str;
    }
}

class URLInterfaceObj {

    public $modelName;

    public $apiMethod;

    public $apiAnnotation;

    public function __construct(AnalysisAPI $api) {
        $this->modelName = lcfirst($api->modelName);
        $this->apiMethod = apiInterface($api->parameters);
        $this->apiAnnotation = <<< EOT
/*
 * $api->apiDesC
 */
EOT;
    }
}
