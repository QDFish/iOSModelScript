<?php
/**
 * Created by PhpStorm.
 * User: QDFish
 * Date: 2018/4/23
 * Time: 上午11:01
 */

define("UrlMangerInterfacePath", "/Users/zgzheng/TouchiOS_new/TaQu/CommonDefine/UrlManager.h");
define("UrlMangerImplementationPath", "/Users/zgzheng/TouchiOS_new/TaQu/CommonDefine/UrlManager.m");
define("FoundationFrameworkImport", "#import <Foundation/Foundation.h>");
define("HBHttpRequestImport", "#import \"HBHTTPRequest.h\"");
define("InterfaceLabel", "@interface");
define("ImplementationLabel", "@implementation");
define("NSStringInitLabel", "@property (nonatomic, copy)");
define("BoolInitLabel", "@property (nonatomic, assign)");
define("ObjectInitLabel", "@property (nonatomic, strong)");
define("IOSNSString", "NSString *");
define("IOSNSArray", "NSSArray *");
define("IOSHttpResponse", "HBHTTPResponse");
define("IOSNSDictionary", "NSDictionary *");
define("IOSHttpRequest", "HBHTTPRequest");
define("InstanceInitDataExtraAPI", "- (instancetype)initWithData:(NSDictionary *)dict extra:(NSDictionary *)extra");
define("InstanceInitDataAPI", "- (instancetype)initWithData:(NSDictionary *)dict");
define("EndLabel", "@end\n");
define("FinishBlock", "FinishBlock");
define("RequestType", "HBHTTPRequest");
define("InitMutableArray", "NSMutableArray *marr = [NSMutableArray arrayWithCapacity:2];");


//html label
define("UpateTimeHtmlHeader", "<span style=\"color: purple\">最后更新时间：</span>");
define("TitleHtmlHeader", "<h2>");
define("TitleHtmlTail", "</h2>");
define("JsonHtmlHeader", "<pre><code class=\"language-json\">");
define("PhpHtmlHeader", "<pre><code class=\"language-php\">");
define("JsonHtmlTail", "</code></pre>");
define("ParameterHtml", "参数说明");
define("ParameterHtml1", "字段说明");
define("ReturnHtml", "返回值说明");
define("RightReturnHtml", "正确返回格式");
define("CodeHtmlHeader", "<code>");
define("CodeHtmlTail", "</code>");
define("LiHtmlHeader", "<li>");
define("LiHtmlTail", "</li>");
define("ULHtmlHeader", "<ul>\n");
define("ULHtmlTail", "\n</ul>");
define("URLReturnType", '- (NSString *)');