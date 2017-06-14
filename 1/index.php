<?php
	//TOKEN的验证操作，确认请求来自微信服务器  而不是恶意的第三方
	define('TOKEN','fenziyu');
	function signature(){
		$signature = $_GET['signature'];//微信加密签名  token timestamp nonce
		$timestamp = $_GET['timestamp'];
		$nonce = $_GET['nonce'];

		//把token timestamp nonce 存储在数组里
		$stmArr = array($nonce,$timestamp,TOKEN);
		sort($stmArr,SORT_STRING);
		$stmStr = implode($stmArr);
		$stmStr = sha1($stmStr);
		if($stmStr == $signature)
			return true;
		else
			return false;
	}

	if(signature())
	{
		$echostr = $_GET['echostr'];
		if($echostr)
		{
			echo $echostr;
			exit;
		}
	}


	$postData = $HTTP_RAW_POST_DATA;
	if(!$postData){
		echo 'error';
		exit;
	}

	//把接收到的xml数据包转换为对象  SimpleXMLElement 是新对象的类  LIBXML_NOCDATA  这是文本节点 CDATA标签里 XML文本不进行解析
	$obj = simplexml_load_string($postData,'SimpleXMLElement',LIBXML_NOCDATA);
	$FormUserName = $obj->FormUserName;//获取发送方
	$ToUserName = $obj->ToUserName;//获取接受方
	$MsgType = $obj->MsgType;//获取用户发送的消息类型

	switch($MsgType)
	{
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
			
		
	}

	$result = sprintf($replyXml,$FromUserName,$ToUserName,time(),$replyMsg);
	echo $result;
