<?php 
//用sdk 封装 消息交互接口 (过程版)
//定义token
define("TOKEN",'fengziyu');
if(!isset($_GET['echostr'])){
	//调用响应消息函数
	responseMsg();
}else{
	//实现网址的接入 验证消息的函数
	valid();
}

//验证消息
function valid(){
	if(checksignature()){
		//获取随机字符串
		$echostr=$_GET['echostr'];
		//返回随机字符串 网址接入成功
		echo $echostr;
		exit;
	}else{
		echo "error";
		exit;
	}
}

function checksignature(){
	//获取加密签名
	$signature=$_GET['signature'];
	//时间戳
	$timestamp=$_GET['timestamp'];//时间戳
	$nonce=$_GET['nonce'];//随机数
	//把token   timestamp nonce 存储在数组里 排序
	$stempArr=array($nonce,$timestamp,TOKEN);
	sort($stempArr,SORT_STRING);
	//转换为字符串
	$stempStr=implode($stempArr);
	//加密字符串
	$stempStr=sha1($stempStr);
	if($stempStr==$signature){
		return true;
	}else{
		return false;
	}
}

//响应消息函数
function responseMsg(){
	//1.获取用户发送的 xml消息
	// $GLOBALS 全局操作
	$postData=$GLOBALS[HTTP_RAW_POST_DATA];
	if(!$postData){
		echo "error";
		exit;
	}

	//2.解析xml
	$object=simplexml_load_string($postData,"SimpleXMLElement",LIBXML_NOCDATA);
	//获取用户发送的消息类型
	$MsgType=$object->MsgType;

	switch($MsgType){
		case 'text':
		//接收消息
		echo receiveText($object);
		break;

		case 'image':
		echo receiveImage($object);
		break;
		//地理位置
		case 'location':
		echo receiveLocation($object);
		break;
		//链接
		case 'link':
		echo receiveLink($object);
		break;
		default:
		break;
	}
}

//接收文本消息函数
function receiveText($obj){
	//获取用户发送的文本消息
	$content=$obj->Content;
	return replyText($obj,$content);
}

//接收图片消息
function receiveImage($obj){
	//图片消息id和图片链接存储在数组里
	$imageArr=array(
		"PicUrl"=>$obj->PicUrl,
		"MediaId"=>$obj->MediaId
		);

	return replyImage($obj,$imageArr);
}

//接收地理位置消息
function receiveLocation($obj){
	//经度 纬度 地理位置信息 存储在数组里
	$locationArr=array(
		"Location_Y"=>"at6[[]]
		"Location_X"=>$obj->Location_X,
		"Label"=>$obj->Label

		);

	return replyText($obj,$locationArr['Location_Y']);

}

//接收链接消息
function receiveLink($obj){
	//把Url存储在数组里
	$linkArr=array(
		'Url'=>$obj->Url
		);

	return replyText($obj,$linkArr['Url']);
}
//回复文本消息
function replyText($obj,$content){
	//封装回复的xml数据包
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
				</xml>";
	$resultstr=sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$content);
	return $resultstr;
}

//回复图片消息
function replyImage($obj,$imageArr){
	//封装xml
	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[image]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
				</xml>";
	$resultstr=sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$imageArr['MediaId']);
	return $resultstr;
}
 ?>