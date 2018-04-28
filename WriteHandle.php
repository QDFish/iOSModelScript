<?php

/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/24
 * Time: 下午6:04
 */
require_once "AnalysisAPI.php";
require_once "iOSModelConstant.php";
require_once "iOSTools.php";
require_once "ModelTemplate/ItemInterfaceTemplate.php";
require_once "ModelTemplate/ItemImplementationTemplate.php";
require_once "ModelTemplate/URLImplementationTemplate.php";
require_once "ModelTemplate/URLInterfaceTemplate.php";
require_once "ModelTemplate/RequestInterfaceTemplate.php";
require_once "ModelTemplate/RequestImplementationTemplate.php";

class WriteHandle
{
    private $api;

    //数据头文件名称
    private $interfaceModelFilename;

    //数据实现文件名称
    private $implementationFilename;

    public function __construct(AnalysisAPI $analysis)
    {
        $this->api = $analysis;
        $this->initModelFile();
    }


    private function initModelFile() {
        global $modelState; global $modelType;
        $this->interfaceModelFilename = ucfirst($this->api->modelName) . $modelType[$modelState] . '.h';
        $this->implementationFilename = ucfirst($this->api->modelName) . $modelType[$modelState] . '.m';
    }
        
    //写入数据模型头文件的头部信息
    public function writeInterfaceFileHeader() {
        global $desPath, $fileAnnotation, $classMap, $modelState;

        $filePath = $desPath . DIRECTORY_SEPARATOR . $this->interfaceModelFilename;
        $writeHandle = fopen($filePath, 'w+');
        fwrite($writeHandle, $fileAnnotation);
        fwrite($writeHandle,  ($modelState === 0 ? FoundationFrameworkImport : HBHttpRequestImport) . "\n\n");
        $this->writeForwardClass($writeHandle, $this->api->jsonData);
        fwrite($writeHandle, PHP_EOL);
        fclose($writeHandle);

        echo "创建{$filePath}成功" . PHP_EOL;
    }

    //写入数据模型实现文件的头部信息
    public function writeImplementationFileHeader() {
        global $desPath, $fileAnnotation;

        $filePath = $desPath . DIRECTORY_SEPARATOR . $this->implementationFilename;
        $writeHandle = fopen($desPath . DIRECTORY_SEPARATOR . $this->implementationFilename, 'w+');
        fwrite($writeHandle, $fileAnnotation);
        fwrite($writeHandle, '#import "' . $this->interfaceModelFilename . "\"\n\n");
        fclose($writeHandle);
        echo "创建{$filePath}成功" . PHP_EOL;
    }

    public function writeToManager() {
        $this->writeInterfaceFile();
        $this->writeImplementation();
    }

    public function writeToModel() {
        if ($this->api->jsonData['data']->value === null) {
            return;
        }

        global $desPath;

        $writeHandle = fopen($desPath . DIRECTORY_SEPARATOR . $this->interfaceModelFilename, 'a+');
        $this->writeModelsToInteraface($this->api->jsonData, $writeHandle);
        fclose($writeHandle);
        echo "写入{$this->api->modelName}Item.h成功" . PHP_EOL;

        $writeHandle = fopen($desPath . DIRECTORY_SEPARATOR . $this->implementationFilename, 'a+');
        $this->writeModelsToImplementation($this->api->jsonData, $writeHandle);
        fclose($writeHandle);
        echo "写入{$this->api->modelName}Item.m成功" . PHP_EOL;
    }
    
    public function writeToRequest() {
        global $desPath;

        $filePath = $desPath . DIRECTORY_SEPARATOR . $this->interfaceModelFilename;
        $writeHandle = fopen($filePath, 'a+');
        fwrite($writeHandle, RequestInterfaceTemplate::template($this->api));
        fclose($writeHandle);
        echo "写入{$this->interfaceModelFilename}成功" . PHP_EOL;

        $filePath = $desPath . DIRECTORY_SEPARATOR . $this->implementationFilename;
        $writeHandle = fopen($filePath, 'a+');
        fwrite($writeHandle, RequestImplementationTemplate::template($this->api));
        fclose($writeHandle);
        echo "写入{$this->interfaceModelFilename}成功" . PHP_EOL;
    }

    //写入.m文件循环递归
    private function writeModelsToImplementation($jsonData, $handle) {
        foreach ($jsonData as $key => $value) {
            if (is_object($value) && get_class($value) === 'IOSObject') {

                $_type = $value->type;
                $_value = $value->value;
                if (($iOSType = classMapContainType($_type)) !== false) {
                    fwrite($handle, ItemImplementationTemplate::template($this->api, $iOSType, $value));
                    $this->writeModelsToImplementation($_value, $handle);
                }
            }
        }
    }

    //写入model头文件循环递归
    private function writeModelsToInteraface($jsonData, $handle) {
        foreach ($jsonData as $key => $value) {
            if (is_object($value) && get_class($value) === 'IOSObject') {

                $_type = $value->type;
                $_value = $value->value;
                if (($iOSType = classMapContainType($_type)) !== false) {
                    fwrite($handle, ItemInterfaceTemplate::template($this->api, $iOSType, $value));
                    $this->writeModelsToInteraface($_value, $handle);
                }
            }
        }
    }

    //写入Url头文件
    private function writeInterfaceFile() {
        $writeHandle = fopen(UrlMangerInterfacePath, 'c+');
        if ($writeHandle === false) {
            echo 'writeURLInterface failure' . PHP_EOL;
            exit(1);
        }


        while (!feof($writeHandle)) {
            $buffer = fgets($writeHandle);
            if ($buffer == "@end\n") {
                fseek($writeHandle, -5, SEEK_CUR);
                fwrite($writeHandle, URLInterfaceTemplate::template($this->api));
            }
        }
        
        fclose($writeHandle);
        echo "写入" . UrlMangerInterfacePath . "成功" . PHP_EOL;
    }

    //写入URL实现文件
    private function writeImplementation() {
        $writeHandle = fopen(UrlMangerImplementationPath, 'c+');
        if ($writeHandle === false) {
            echo 'writeURLImplementation failure' . PHP_EOL;
            exit(1);
        }

        while (!feof($writeHandle)) {
            $buffer = fgets($writeHandle);
            if ($buffer == EndLabel) {
                fseek($writeHandle, -5, SEEK_CUR);
                fwrite($writeHandle, URLImplementationTemplate::template($this->api));
            }
        }
        fclose($writeHandle);

        echo "写入" . UrlMangerImplementationPath . "成功" . PHP_EOL;
    }

    private function writeForwardClass($handle, $jsonData) {
        global $classMap;
        foreach ($jsonData as $key => $value) {
            if (is_object($value) && get_class($value) === 'IOSObject' && $value->value !== null) {
                if (($forwardType = classMapContainType($value->type)) !== false) {
                    fwrite($handle, '@class ' . $forwardType . ';'. PHP_EOL);
                    $this->writeForwardClass($handle, $value->value);
                }
            }
        }
    }

}