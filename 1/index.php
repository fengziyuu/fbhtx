<?php
	//TOKEN的验证操作，确认请求来自微信服务器  而不是恶意的第三方
	define('TOKEN','fenziyu');
	function signature(){
		$signature = $_GET['signature'];//微信加密签名  token timestamp nonce
		$timestamp = $_GET['timestamp'];
		$nonce = $_GET['nonce'];

		//把token timestamp nonce 存储在数组里
		$stmStr = array($nonce,$timestamp,TOKEN);
		sort($stmStr,SORT_STRING);
		$stmStr = implode($stmStr);
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
			
		case 'event':
			$Event = $obj->Event;
			switch($Event){
				case 'subscribe':
				$replyMsg = "欢迎来到我的平台,回复1:可以逛淘宝,回复2:可以上京东";
				$replyXml = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[text]]></MsgType>
								<Content><![CDATA[%s]]></Content>
							</xml>";
				break;
			}
		break;

		case "text":
		$replyMsg = $obj->Content;
		if($replyMsg==1)
		{
			$dataArray = array(
				array(
					'Title'=>'浏览淘宝1111',
					'Description'=>'this is a test',
					'PicUrl'=>'https://img10.360buyimg.com/da/jfs/t5749/237/1364970330/39708/347c201a/59256b8fNbb8043f6.gif.jpg',
					'Url'=>'https://www.taobao.com',
					),
				array(
					'Title'=>'浏览淘宝2222',
					'Description'=>'this is a test',
					'PicUrl'=>'https://img11.360buyimg.com/mobilecms/s110x110_jfs/t2581/20/346515013/295406/cc1ec305/570f39c1Nebd3b947.jpg',
					'Url'=>'https://www.jd.com',
					),
				);

			$strwx = "";
			foreach($dataArray as $k=>$v)
			{
				$strwx .="<item>
								<Title><![CDATA[".$v['Title']."]]></Title> 
								<Description><![CDATA[".$v['Description']."]]></Description>
								<PicUrl><![CDATA[".$v['PicUrl']."]]></PicUrl>
								<Url><![CDATA[".$v['Url']."]]></Url>
							</item>"; 
			}


			$replyXml = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>".count($dataArray)."</ArticleCount>
							<Articles>
							".$strwx."
							</Articles>
						</xml>";
			break;
		}
		elseif($replyMsg==2)
		{
			$dataArray = array(
				array(
					'Title'=>'浏览京东1111',
					'Description'=>'this is a test',
					'PicUrl'=>'https://img.alicdn.com/tfscom/i3/50983440/TB2ys6TrH8kpuFjy0FcXXaUhpXa_!!50983440.jpg_240x240.jpg',
					'Url'=>'https://www.taobao.com',
					),
				array(
					'Title'=>'浏览京东2222',
					'Description'=>'this is a test',
					'PicUrl'=>'https://img11.360buyimg.com/mobilecms/s110x110_jfs/t2581/20/346515013/295406/cc1ec305/570f39c1Nebd3b947.jpg',
					'Url'=>'https://www.jd.com',
					),
				);

			$strwx = "";
			foreach($dataArray as $k=>$v)
			{
				$strwx .="<item>
								<Title><![CDATA[".$v['Title']."]]></Title> 
								<Description><![CDATA[".$v['Description']."]]></Description>
								<PicUrl><![CDATA[".$v['PicUrl']."]]></PicUrl>
								<Url><![CDATA[".$v['Url']."]]></Url>
							</item>"; 
			}


			$replyXml = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>".count($dataArray)."</ArticleCount>
							<Articles>
							".$strwx."
							</Articles>
						</xml>";
			break;
		}
	}

	$result = sprintf($replyXml,$FromUserName,$ToUserName,time(),$replyMsg);
	echo $result;
