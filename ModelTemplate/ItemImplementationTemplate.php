<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/27
 * Time: 下午4:48
 */

require_once __DIR__ . "/../iOSModelConstant.php";
require_once __DIR__ . "/../iOSTools.php";

class ItemImplementationTemplate
{
    public static function template(AnalysisAPI $api, $modelName, $value)
    {
        $obj = new ItemImplementationObj($api, $modelName, $value);


        $str = <<<EOD
@implementation $obj->modelName

$obj->impleURL {
    self = [super init];
    if (self) {
$obj->varSets
    }
    
    return self;
}

@end


EOD;

        return $str;
    }
}

class ItemImplementationObj {
    
    public $modelName;
    
    public $impleURL;
    
    public $varSets;
    
    public function __construct(AnalysisAPI $api, $modelName, $value) {
        $this->modelName = ucfirst($modelName);

        if (count($api->jsonExtra) > 0) {
            $this->impleURL = InstanceInitDataExtraAPI;
        } else {
            $this->impleURL = InstanceInitDataAPI;
        }

        $this->varSets = $this->varSets($api, $modelName, $value);
    }
    
    private function varSets(AnalysisAPI $api, $modelName, $value) {
        global $classMap;

        $strs = '';
        
        if ($api->jsonExtra !== null) {
            if(isset($api->jsonExtra['avatar_host'])) {
                 $strs .= <<< EOT
        NSString *avaHost = [NSString getString:extra key:@"avatar_host"];

EOT;
            } 

            if(isset($api->jsonExtra['img_host'])) {
                $strs .= <<< EOT
        NSString *imgHost = [NSString getString:extra key:@"img_host"];

EOT;
            }
        }



        $theValue = get_class($value) === 'IOSObject' ? $value->value : $value;
        foreach ($theValue as $_key => $_value) {
            if (is_object($_value) && get_class($_value) === 'IOSObject') {

                $var = ucReplaceUnderLine($_key); $initDesc = '';

                if ($api->jsonExtra !== null) {
                    $initDesc = "[[{$classMap[$_key]}Item alloc] initWithData:obj extra:extra];";
                } else {
                    $initDesc = "[[{$classMap[$_key]}Item alloc] initWithData:obj];";
                }

                //变量是数组集合
                if (strpos($_value->type, 'NSArray') !== false) {

                    $strs .= <<< EOT
                    
        NSArray *arr = [dict objectForKey:@"$_key"];
        NSMutableArray *marr = [NSMutableArray arrayWithCapacity:2];
        for (NSDictionary *obj in arr) {    
            {$classMap[$_key]}Item *objValue = $initDesc            
            [marr addObject:objValue];
        }
        self.$var = marr;

EOT;
                } else {

                    if ($api->jsonExtra !== null) {
                        $initDesc = "[[{$classMap[$_key]}Item alloc] initWithData:dict extra:extra];";
                    } else {
                        $initDesc = "[[{$classMap[$_key]}Item alloc] initWithData:dict];";
                    }

                    $strs .= <<< EOG
        self.$var = $initDesc

EOG;
                }

            } else if (strpos($_value, IOSNSString) !== false) {

                $var = ucReplaceUnderLine($_key);
                if (strpos($_value, 'NSArray') !== false) {

                    $strs .= <<< EOT
        NSArray *arr = [dict objectForKey:@"$_key"];
        NSMutableArray *marr = [NSMutableArray arrayWithCapacity:2];
        for (NSString *obj in arr) {      
            [marr addObject:obj];
        }
        self.$var = marr;

EOT;
                } else {
                    $strs .= <<< EOT
        self.$var = [NSString getString:dict key:@"$_key"];

EOT;
                    if (strpos($_key, 'avatar') !== false) {
                        $strs .= <<< EOT
        self.$var = [ResponseUtil appendImgUrlWithHost:avaHost imgUrl:self.$var];

EOT;

                    } else if (strpos($_key, 'icon') !== false || strpos($_key, 'img') !== false) {
                        $strs .= <<< EOT
        self.$var = [ResponseUtil appendImgUrlWithHost:imgHost imgUrl:self.$var];

EOT;
                    }
                }

            } else if ($_value === 'BOOL') {
                $var = ucReplaceUnderLine($_key);
                $strs .= <<< EOT
        self.$var = [[NSString getString:dict key:@"$_key"] boolValue];

EOT;
            }
        }
        
        return $strs;
    }
}
