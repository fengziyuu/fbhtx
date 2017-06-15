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
	
 ?>