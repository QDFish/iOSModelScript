<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/27
 * Time: 下午7:14
 */
require_once __DIR__ . "/../AnalysisAPI.php";
require_once __DIR__ . "/../iOSTools.php";
require_once __DIR__ . "/../iOSModelVar.php";

class RequestImplementationTemplate {

    public static function template(AnalysisAPI $api) {
        $obj = new RequestImplementationObj($api);

        $strs = <<< EOT
@implementation $obj->requestName {
    $obj->blockName _finish;
}

+ (void)send{$obj->requestName}With$obj->apiMethod withFinishBlock:(LiveFamilyFinishBlock)finish {
    $obj->requestName *req = [[$obj->requestName alloc] initWithURL:[[UrlManager shareManager] $obj->urlMethod] andDelegate:nil reqSysType:$obj->reqType];
    req.delegate = req;
    req->_finish = finish;
    [req addSelectFinish:@selector(requestDidFinishLoad:) andDidFailSelector:@selector(requestDidFailureLoad:)];
    [req sendRequest];
}

- (void)requestDidFinishLoad:(HBHTTPRequest *)req {
    if (req.resObj.isSuccess) {
        id data = req.resObj.responseData;
$obj->initDesc
        return;
    }
    
    self->_finish ? self->_finish(req.resObj, nil) : nil;
}

- (void)requestDidFailureLoad:(HBHTTPRequest *)req {
    self->_finish ? self->_finish(req.resObj, nil) : nil;
}

@end


EOT;
        return $strs;

    }
}

class RequestImplementationObj {

    public $requestName;

    public $urlMethod;

    public $blockName;

    public $apiMethod;

    public $reqType;

    public $initDesc;

    public function __construct(AnalysisAPI $api) {
        global $domainsMap;

        $this->requestName = ucfirst($api->modelName) . 'Request';
        $this->blockName = ucfirst($api->modelName) . FinishBlock;
        $this->apiMethod = apiInterface($api->parameters);
        $this->reqType = $domainsMap[$api->domain]['reqType'];
        $this->urlMethod = lcfirst($this->requestName) . "With" . parameterSetter($api->parameters);

        $itemName = $api->jsonData['data']->value !== null ? $api->jsonData['data']->type : null ;
        $realItemName = classMapContainType($itemName);
        if ($itemName !== null && strpos($itemName, 'NSArray') === false) {
            if ($api->jsonExtra !== null) {
                
                $this->initDesc= <<< EOT
        NSDictionary *extra = req.resObj.extraDic;
        $realItemName *item = [[$realItemName alloc] initWithData:data withExtra:extra];
        self->_finish ? self->_finish(req.resObj, item) : nil;
EOT;
            } else {
                $this->initDesc= <<< EOT
        LiveNewFamilyInfoItem *item = [[LiveNewFamilyInfoItem alloc] initWithData:data];
        self->_finish ? self->_finish(req.resObj) : nil;
EOT;
            }

        } else {
            if ($api->jsonExtra !== null) {
                $this->initDesc= <<< EOT
        NSDictionary *extra = req.resObj.extraDic;
                
        NSMutableArray *marr = [NSMutableArray arrayWithCapacity:2];
        for (NSDictionary *obj in data) {    
            $realItemName *item = [[$realItemName alloc] initWithData:data withExtra:extra];            
            [marr addObject:item];
        }                
        self->_finish ? self->_finish(req.resObj, marr) : nil;
EOT;
            } else {
                $this->initDesc = <<< EOT
        NSMutableArray *marr = [NSMutableArray arrayWithCapacity:2];
        for (NSDictionary *obj in data) {    
            $realItemName *item = [[$realItemName alloc] initWithData:data];            
            [marr addObject:item];
        }                
        self->_finish ? self->_finish(req.resObj, marr) : nil;
EOT;
            }

        }
    }
}
