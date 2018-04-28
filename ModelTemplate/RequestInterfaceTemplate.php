<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/27
 * Time: 下午6:47
 */
require_once __DIR__ . "/../AnalysisAPI.php";
require_once __DIR__ . "/../iOSTools.php";

class RequestInterfaceTemplate
{
    public static function template(AnalysisAPI $api) {

        $obj = new RequestInterfaceObj($api);

        $strs = <<< EOT
typedef void (^$obj->blockName)($obj->blockParameters);

@interface $obj->requestName : HBHTTPRequest

+ (void)send{$obj->requestName}With$obj->apiMethod withFinishBlock:($obj->blockName)finish;

@end


EOT;
        return $strs;
    }

}

class RequestInterfaceObj {

    public $requestName;

    public $blockParameters;

    public $blockName;

    public $apiMethod;

    public function __construct(AnalysisAPI $api) {
        $this->requestName = ucfirst($api->modelName) . 'Request';
        $this->blockName = ucfirst($api->modelName) . FinishBlock;
        $this->apiMethod = apiInterface($api->parameters);

        $itemType = '';
        $itemName = $api->jsonData['data']->value !== null ? $api->jsonData['data']->type : null ;
        if ($itemName !== null && strpos($itemName, 'NSArray') === false) {
            $itemType = 'item';
        } else {
            $itemType = 'items';
        }
        
        if ($itemName === null) {
            $this->blockParameters = <<< EOT
HBHTTPResponse *resp
EOT;
        } else {
            $this->blockParameters = <<< EOT
HBHTTPResponse *resp, $itemName$itemType
EOT;
        }
    }
}