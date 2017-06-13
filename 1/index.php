<?php
//token的验证操作 确认请求的确来自于微信服务器 而不是恶意的第三方
define('TOKEN','fengziyu');
function signature(){
	//接收微信服务器的参数
	$signature=$_GET['signature'];//微信加密签名  token  timestamp  nonce
	$timestamp=$_GET['timestamp'];//时间戳
	$nonce=$_GET['nonce'];//随机数
	//把token   timestamp  nonce 存储在数组里
	$stmArr=array($nonce,$timestamp,TOKEN);
	//排序 sort SORT_STRING 快速的排序
	sort($stmArr,SORT_STRING);
	//转换为字符串
	$stmStr=implode($stmArr);
	//对字符串做加密(sha1)
	$stmStr=sha1($stmStr);
	//把加密以后的字符串和微信加密签名 signature 做对比 标识该请求的确来自于微信服务器
	if($stmStr==$signature){
		return true;
	}else{
		return false;
	}

}
if(signature()){
	//原样返回echostr 随机字符串 接口接入成功  成为开发者
	$echostr=$_GET['echostr'];
	if($echostr){
		echo $echostr;
		exit;
	}
}


//微信公众平台接收普通用户发送的消息（XML） (post)  1    1
$postData=$HTTP_RAW_POST_DATA;
//判断
if(!$postData){
	echo "error";
	exit();
}
// <xml>
//  <ToUserName><![CDATA[toUser]]></ToUserName> 接收方 微信公众账号服务器 
//  <FromUserName><![CDATA[fromUser]]></FromUserName> 发送方 普通微信用户
//  <CreateTime>1348831860</CreateTime> 发送时间
//  <MsgType><![CDATA[text]]></MsgType> 发送消息类型
//  <Content><![CDATA[]]>1</Content>  发送的内容
//  <MsgId>1234567890123456</MsgId>
//  </xml>



//把接收到的xml数据包 转换为对象
//SimpleXMLElement  是新对象的类 
//LIBXML_NOCDATA 设置文本节点  CDATA标签里 xml文本不进行解析
$obj=simplexml_load_string($postData,"SimpleXMLElement",LIBXML_NOCDATA);


// 微信公众账号服务器 被动回复消息


// <xml>
//  <ToUserName><![CDATA[toUser]]></ToUserName>   普通微信用户
//  <FromUserName><![CDATA[fromUser]]></FromUserName>  微信公众账号服务器
//  <CreateTime>1348831860</CreateTime>
//  <MsgType><![CDATA[text]]></MsgType>
//  <Content><![CDATA[]]>1</Content>
//  <MsgId>1234567890123456</MsgId>
//  </xml>

//获取发送方 (微信用户 )-》当做被动回复的接收方
$FromUserName=$obj->FromUserName;
//获取接收方 (微信公众账号服务器 )-》当做被动回复的发送方
$ToUserName=$obj->ToUserName;

//获取用户发送的消息类型
$MsgType=$obj->MsgType;
switch($MsgType){
	case 'text':
	//获取用户发送过来的文本消息(当做回复消息)
	$replyMsg=$obj->Content;

	//把回复的信息封装在xml数据包里
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
				</xml>";

	break;

	//接收图片消息被动回复图片消息
	case 'image':
	//获取用户发送过来的图片消息(当做回复消息)
	$replyMsg=$obj->MediaId;
	//把回复的消息封装在xml数据包里
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[image]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
				</xml>";

	break;

	//接收语音消息和被动回复语音
	case "voice":
	//获取用户发送的语音(当做回复消息)
	$replyMsg=$obj->MediaId;
	//封装回复的xml
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[voice]]></MsgType>
					<Voice>
					<MediaId><![CDATA[%s]]></MediaId>
					</Voice>
				</xml>";

	break;

	//接收地理位置消息和被动回复地理位置
	case "location":
	//获取用户发送的地理位置(当做回复消息)
	$replyMsg=$obj->MediaId;
        var_dump($obj);
	//封装回复的xml
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[location]]></MsgType>
					<location>
					<MediaId><![CDATA[%s]]></MediaId>
					</location>
				</xml>";

	break;

	//接收链接消息和被动回复链接
	case "link":
	//获取用户发送的链接(当做回复消息)
	$replyMsg=$obj->MediaId;
	//封装回复的xml
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[link]]></MsgType>
					<Voice>
					<MediaId><![CDATA[%s]]></MediaId>
					</Voice>
				</xml>";

	break;


	//接收小视频消息和被动回复小视频
	case "shortvideo":
	//获取用户发送的视频(当做回复消息)
	$replyMsg=$obj->MediaId;
	//封装回复的xml
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[shortvideo]]></MsgType>
					<video>
					<MediaId><![CDATA[%s]]></MediaId>
					</video>
				</xml>";

	break;

	//接收视频消息和被动回复视频
	case "video":
	//获取用户发送的视屏(当做回复消息)
	$replyMsg=$obj->MediaId;
	//封装回复的xml
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[video]]></MsgType>
					<Voice>
					<MediaId><![CDATA[%s]]></MediaId>
					</Voice>
				</xml>";

	break;





	//事件推送 关注 
	case 'event':
	$Event=$obj->Event;
	switch($Event){
		//关注
		case "subscribe":
		$replyMsg="欢迎来到我的平台！谢谢您的关注";
		//封装xml数据包
		$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
				</xml>";

		break;
	}
	break;
}

//把%符号替换为一个作为参数传递变量
$result=sprintf($replyXml,$FromUserName,$ToUserName,time(),$replyMsg);
echo $result;
 ?>