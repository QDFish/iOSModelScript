<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/27
 * Time: 下午6:21
 */

require_once __DIR__ . "/../AnalysisAPI.php";
require_once __DIR__ . "/../iOSTools.php";
require_once __DIR__ . "/../iOSModelVar.php";

class URLImplementationTemplate
{
    public static function template(AnalysisAPI $api)
    {
        global $requestMethod;
        
        $obj = new URLImplementationObj($api);

        if ($requestMethod === 'GET') {
            $str = <<<EOD
$obj->apiAnnotation
- (NSString *){$obj->modelName}UrlWith{$obj->apiMethod} {
     return [NSString stringWithFormat:@"%@$obj->detailPath?$obj->impleMethod", $obj->domain, $obj->parameterDesc];
}

@end


EOD;
        } else {
            $str = <<<EOD
$obj->apiAnnotation
- (NSString *){$obj->modelName}Url {
     return [NSString stringWithFormat:@"%@$obj->detailPath", $obj->domain];
}

@end


EOD;
        }
        
    
        return $str;

    }
}

class URLImplementationObj {

    public $modelName;

    public $domain;

    public $apiMethod;

    public $impleMethod;

    public $apiAnnotation;

    public $parameterDesc;
    
    public $detailPath;

    public function __construct(AnalysisAPI $api) {
        global $domainsMap;
        
        $this->modelName = lcfirst($api->modelName);
        $this->apiMethod = apiInterface($api->parameters);
        $this->detailPath = $api->detailURLPath;
        $this->impleMethod = apiImplementation($api->parameters);
        $this->domain = $domainsMap[$api->domain]['domain'];
        $this->parameterDesc = parameterStr($api->parameters);
        $this->apiAnnotation = <<< EOT
/*
 * $api->apiDesC
 */
EOT;
    }
}