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
	$obj = simplexml_load_string($postData,'SimpleXMLElement',LIBXML_NOCDATA);
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

		case 'event':
			echo receiveEvent($obj);
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
		'Location_Y'=>"您所在的位置的经度".$obj->Location_Y,
		'Location_X'=>"纬度".$obj->Location_X,
		'Label'=>$obj->Label
		);
    $str = implode($locationArr);
	return replyText($obj,$str);
}

function receiveLink($obj)
{
	$linkArr = array('Url'=>$obj->Url);
	return replyText($obj,$linkArr['Url']);
}

function receiveEvent($obj)
{
	$event = $obj->Event;
	switch($event){
		case 'subscribe':
		$replyMsg = "欢迎来到我的平台,回复1:可以逛淘宝,回复2:可以上京东;";
		//replyText($obj,$replyMsg);
      	$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
				</xml>";
        $resultstr=sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$replyMsg);
		echo $resultstr;
		break;
	}
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
	if($content==1)
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
		}
		elseif($content==2)
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
		}else{
			$replyXml="<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
				</xml>";
				$content = curls();
		}
	$resultstr=sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$content);
	echo $resultstr;
}


function curls()
{
	header("Content-type:text/html;charset=utf-8");
    //curl 模拟get请求1 截取部分数据 以数据流的形式显示出 而不是整个页面
    //1.初始化
    $ch=curl_init();
    //笑话接口
    $url="http://www.kuitao8.com/api/joke";
    //2.设置变量
    curl_setopt($ch,CURLOPT_URL,$url);
    //把数据以数据流的形式显示出
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //3.执行
    $output=curl_exec($ch);
    //4.关闭curl
    curl_close($ch);

    // echo $output;
    //json_decode()
    $s=json_decode($output,true);
    // var_dump($s);
    echo $s['content'];
}