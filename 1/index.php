<?php
//用sdk封装消息交互接口
define('TOKEN','fengziyu');
if(!isset($_GET['echostr']))
	responseMsg();
else
	valid();

//验证消息
function valid()
{
	if(checksignature())
	{
		$echostr = $_GET['echostr'];
		echo $echostr;
		exit;
	}	
	else
	{
		echo 'error';
		exit;
	}
}

function checksignature()
{
	$signature = $_GET['signature'];
	$timestamp = $_GET['timestamp'];
	$nonce = $_GET['nonce'];
	$stampArr = array($nonce,$timestamp,TOKEN);
	sort($stampArr,SORT_STRING);
	$stampStr = implode($stampArr);
	$stampStr = sha1($stampStr);
	if($stampStr == $signature)
		return true;
	else
		return false;
}

function responseMsg()
{
	$postData = $GLOBALS[HTTP_RAW_POST_DATA];
	if(!$postData)
	{
		echo 'error';
		exit;
	}
	$obj = simplexml_load_string($postData,'SimpleXMLElement',LIMXML_NOCDATA);
	$FromUserName = $obj->FromUserName;
	$ToUserName = $obj->ToUserName;
	$MsgType = $obj->MsgType;
	switch($MsgType)
	{
		case 'text':
			echo receiveText($obj);
		break;

		case 'image':
			echo receiveImage($obj);
		break;

		case 'location':
			echo receiveLocation($obj);
		break;

		case 'link':
			echo receiveLink($obj);
		break;

		default:
		break;
	}
}

function receiveText($obj)
{
	$content = $obj->Content;
	return replyText($obj,$content);
}

function receiveImage($obj)
{
	$imageArr = array(
		'PicUrl'=>$obj->PicUrl,
		'MediaId'=>$obj->MediaId
		);
	return replyImage($obj,$imageArr);
}

function receiveLocation($obj)
{
	$locationArr = array(
		'Location_Y'=>at6[[]],
		'Location_X'=>$obj->Location_X,
		'Label'=>$obj->Label
		);
	return replyText($obj,$locationArr['Location_Y']);
}

function receiveLink($obj)
{
	$linkArr = array('Url'=>$obj->Url);
	return replyText($obj,$linkArr['Url']);
}

function replyImage($obj,$imageArr)
{
	$replyXml = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[image]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
				</xml>";
	$resultstr = sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$imageArr['MediaId']);
	echo $resultstr;
}

function replyText($obj,$content)
{
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