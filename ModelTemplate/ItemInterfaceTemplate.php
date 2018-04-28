<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/27
 * Time: 下午3:25
 */
require_once __DIR__ . "/../AnalysisAPI.php";
require_once __DIR__ . "/../iOSModelConstant.php";
require_once __DIR__ . "/../iOSTools.php";

class ItemInterfaceTemplate
{
    public static function template(AnalysisAPI $api, $modelName, $value) {
        $obj = new ItemInterfaceObj($api, $modelName, $value);
        
        
        $str = <<<EOD
@interface $obj->modelName : NSObject

$obj->varSets

$obj->instanceInit;

@end


EOD;

        return $str;
    }
    
}

class ItemInterfaceObj {
    //数据名称
    public $modelName;
    
    //变量声明集合
    public $varSets;
    
    //数据初始化语法
    public $instanceInit;
    
    public function __construct(AnalysisAPI $api, $modelName, $value) {
        $this->modelName = ucfirst($modelName);
        $this->varSets = $this->varSets($modelName, $value);
        if (count($api->jsonExtra) > 0) {
            $this->instanceInit = InstanceInitDataExtraAPI;
        } else {
            $this->instanceInit = InstanceInitDataAPI;
        }
    }
    
    private function varSets($modelName, $value) {
        $theValue = get_class($value) === 'IOSObject' ? $value->value : $value;

        $strs = '';
        foreach ($theValue as $_key => $_value) {
            $prefix = ''; $type = ''; $paramamerName = '';
            if (is_object($_value) && get_class($_value) === 'IOSObject') {
                $prefix = ObjectInitLabel;
                $type = $_value->type;
                $paramamerName = ucReplaceUnderLine($_key);

            } else if ($_value === 'NSString *') {
                $prefix =  NSStringInitLabel;
                $type = $_value;
                $paramamerName = ucReplaceUnderLine($_key);
            } else if ($_value === 'BOOL') {
                $prefix =  BoolInitLabel;
                $type = "$_value ";
                $paramamerName = ucReplaceUnderLine($_key);
            }
            
            //@property (nonatomic, assign) BOOL isCreate;
            $strs .=  <<<EOT
$prefix $type{$paramamerName};

EOT;
        }
        
        
        return $strs;
    }
    
}