<?php
//在开发模式下自定义菜单
// (1) 获取access_token值
	$appid="wx1ec677af86f59d7d"; 
	//定义凭证秘钥
	$appsecret="4c9a7d6f3f0413aa77a98d9ccbc08018";
	//接口地址 
	$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$output=curl_exec($ch);
	curl_close($ch);
	// echo $output;
	$access_token=json_decode($output,true);
	// var_dump($access_token);
	$access_token=$access_token['access_token'];
	// (2)请求接口
	
	 
	//设置请求的地址
	// $url="http://localhost/c/opt.php";
	$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
	//携带的参数
	
	$post='{
     "button":[
     {	
          "type":"click",
          "name":"今日歌曲",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"菜单",
           "sub_button":[
           {	
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
            {
               "type":"view",
               "name":"视频",
               "url":"http://v.qq.com/"
            },
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
       }]
 	}';
	//1.初始化
	$cn=curl_init();
	//2.设置变量
	curl_setopt($cn,CURLOPT_URL,$url);
	//模拟post请求
	curl_setopt($cn,CURLOPT_POST,1);
	//post 发送内容
	curl_setopt($cn,CURLOPT_POSTFIELDS,$post);
	//3.执行
	$output=curl_exec($cn);
	//4.关闭
	curl_close($cn);
	echo $output;
	<?php 

//微信公众平台基础接口PHP SDK(面向对象)

define("TOKEN",'fengziyu');

$wechat = new Wechat_base_api();

if(!isset($_GET['echostr']))
	$wechat->responseMsg();//调用响应消息函数
else
	$wechat->valid();//实现网址接入.调用验证消息函数

class Wechat_base_api
{
	//验证消息
	public function valid()
	{
		if($this->checkSignature())
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

	//检查签名
	public function checkSignature()
	{
		//获取微信服务器GET请求的4个参数
		$signature = $_GET['signature'];
		$timestamp = $_GET['timestamp'];
		$nonce = $_GET['nonce'];

		//定义一个数组 存储其中三个参数  分别是timestamp nonce token
		$tempArr = array($nonce,$timestamp,TOKEN);

		//进行排序
		sort($tempArr,SORT_STRING);

		//将数组转换成字符串
		$tmpStr = implode($tempArr);

		//进行sha1加密算法
		$tmpStr = sha1($tmpStr);

		//判断请求是否来自微信服务器,对比$tmpStr 和 $signature
		if($tmpStr == $signature)
			return true;
		else
			return false;
	}

	//响应消息
	public function responseMsg()
	{
		//根据用户传过来的消息类型进行不同的响应

		//1.接受微信服务器POST过来的数据 XML数据包
		$postData = $GLOBALS[HTTP_RAW_POST_DATA];
		if(!$postData)
		{
			echo 'error';
			exit;
		}

		//2.解析XML数据包
		$obj = simplexml_load_string($postData,'SimpleXMLElement',LIBXML_NOCDATA);

		//3.获取消息类型
		$MsgType = $obj->MsgType;
		switch($Msgtype)
		{
			case 'text':
	 				//接收文本消息
	 				echo $this->receiveText($obj);
	 			break;

	 		case 'image':
 		        	//接收图片消息
 		        	echo $this->receiveImage($obj);	
	 			break;

	 		case 'voice':
 		        	//接收语音消息
 		        	echo $this->receiveVoice($obj);	
	 			break;
	 			
	 		
	 		case  'link':
	 				//接收链接消息
	 				echo $this->receiveLink($obj);
	 				break;

	 		case 'location':
 					//接收地理位置消息
 					$data=$this->receiveLocation($obj);
 					$ch=curl_init();
					$url="http://api.map.baidu.com/telematics/v3/weather?location=".$data['Location_Y'].",".$data['Location_X']."&output=json&ak=6b219a615eb77699a10eb54054959a2e";
 					//2.设置变量
					curl_setopt($ch,CURLOPT_URL,$url);
					//把数据以数据流的形式显示出
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
					$output=curl_exec($ch);
					curl_close($ch);
					$s=json_decode($output,true);
					$data=$s['results'][0]['weather_data'];
					$dataarr=array();
					//遍历
					foreach($data as $key=>$value){
						$dataarr[]=array(
							'Title'=>$value['date'],
							'Description'=>$value['weather'].$value['wind'].$value['temperature'],
							'PicUrl'=>$value['dayPictureUrl'],
							'Url'=>''
							);
					}

					//回复图文消息
					echo $this->replyNews($object,$dataarr);		
	 				break; 

	 		default:
	 			break;
		}

	}

	//接收地理位置信息
	private function receiveLocation($obj){
		//把获取到的地理位置信息存储在数组
		$locationArr=array(
			"Location_X"=>$obj->Location_X,
			"Location_Y"=>$obj->Location_Y,
			"Label"=>$obj->Label
			);

		return $locationArr;
	}

	//接受文本消息
	private function receiveText($obj)
	{
		//获取文本消息的内容
		$content = $obj->Content;
		//发送文本消息
		return $this->replyText($obj,$content);
	}

	//接受图片消息
	private function receiveImage($obj)
	{
		//获取图片消息的内容
		$imageArr = array(
				'PicUrl'=>$obj->PicUrl,
				'MediaId'=>$obj->MediaId,
			);
		//发送图片消息
		return $this->replyImage($obj,$imageArr);
	}

	//接受语音消息
	private function receiveVoive($obj)
	{
		//获取语音消息内容
		$voiceArr = array(
				'MediaId'=>$obj->MediaId,
				'Format'=>$obj->Format,
			);
		//发送语音消息
		return $this->replyVoice($obj,$voiceArr);
	}

	//接收链接消息
	private function receiveLink($obj)
	{
		//接收链接消息的内容
		$linkArr = array(
				"Title"=>$obj->Title,
				"Description"=>$obj->Description,
				"Url"=>$obj->Url
			);
		//回复文本消息
		return $this->replyText($obj,"你发过来的链接地址是{$linkArr['Url']}");
	}

	//发送文本消息
	private function replyText($obj,$content){
		$replyXml = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
	        //返回一个进行xml数据包

		$resultStr = sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$content);
	        return $resultStr;		
	}

	//发送图片消息
	private function replyImage($obj,$imageArr){
		$replyXml = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[image]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
					</xml>";
	        //返回一个进行xml数据包

		$resultStr = sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$imageArr['MediaId']);
	        return $resultStr;			
	}

	//回复语音消息
	private function replyVoice($obj,$voiceArr)
	{
		$replyXml = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[voice]]></MsgType>
					<Voice>
					<MediaId><![CDATA[%s]]></MediaId>
					</Voice>
					</xml>";
	        //返回一个进行xml数据包

		$resultStr = sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),$voiceArr['MediaId']);
	        return $resultStr;		
	}

	//回复图文消息
	private function replyNews($obj,$newsArr){
		$itemStr = "";
		if(is_array($newsArr))
		{
			foreach($newsArr as $item)
			{
				$itemXml ="<item>
					<Title><![CDATA[%s]]></Title> 
					<Description><![CDATA[%s]]></Description>
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
					</item>";
				$itemStr .= sprintf($itemXml,$item['Title'],$item['Description'],$item['PicUrl'],$item['Url']);
			}

		}

		$replyXml = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>%s</ArticleCount>
					<Articles>
						{$itemStr}
					</Articles>
					</xml> ";
	        //返回一个进行xml数据包

		$resultStr = sprintf($replyXml,$obj->FromUserName,$obj->ToUserName,time(),count($newsArr));
	        return $resultStr;			
	}	

		
}
 ?>
 ?>