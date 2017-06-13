<?php
//$str 微信公众平台服务器接收到的xml数据包 
$str="<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[fromUser]]></FromUserName>
            <CreateTime>1351776360</CreateTime>
            <MsgType><![CDATA[location]]></MsgType>
            <Location_X>23.134521</Location_X>
            <Location_Y>113.358803</Location_Y>
            <Scale>20</Scale>
            <Label><![CDATA[位置信息]]></Label>
            <MsgId>1234567890123456</MsgId>
         </xml>";


// 	//把xml数据包转换为对象
$obj=simplexml_load_string($str,"SimpleXMLElement",LIBXML_NOCDATA);

var_dump($obj);
// 	echo $obj->ToUserName;

//$format="there are %d monkeys in the %s";
//$num=3;
//$l='tree';
//把%符号替换为一个作为参数传递变量
echo sprintf($format,$num,$l);
 ?>