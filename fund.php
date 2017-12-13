#! /usr/bin/env php 
<?php
/**
http://fundgz.1234567.com.cn/js/260112.js?rt=1513048761263
指定爬取 260112 基金信息
可以在每天开盘和结束的时候将信息发给邮箱

功能需求：
1，可以输出到屏幕，显示涨幅颜色；
2,可以运行在后台，或执行定时任务，工作日下午3点半将信息发送给邮箱
*/
/**
@params String $url
@return object stdClass
*/
function getJson($url,$isArray = false){
    $response = crawl($url);
    $pattern = '/jsonpgz\((.*?)\)/';
    if($response['status_code']!==200) return false;
    preg_match($pattern,$response['data'],$matches);# $matches $0 $1 分别对应数组下标
    return  json_decode($matches[1],$isArray);
}
function getHttpError($code){
        $https = [
        401=>"unauthorized",
        403=>"forbiden",
        404=>"not found",
        405=>"method not allowed",
        500=>"internal serve error",
        502=>"bad gateway"
        ];
        return isset($https[$code])?$https[$code]:"uncaught error";
}
function crawl($url,$method="GET",$header = []){
    $ch = curl_init();
    $options = [
      CURLOPT_URL =>$url,
      CURLOPT_HEADER=>0,# 0,1 是否输出头部信息
      CURLOPT_RETURNTRANSFER=>1,# 0 直接输出内容 1 保存到内容到变量
      CURLOPT_HTTPHEADER=>$header,#http头信息
      CURLOPT_CUSTOMREQUEST=>$method,# GET POST PUT.....
      CURLOPT_USERAGENT=>'',# user agent  like firefox chrome  etc.
      CURLOPT_TIMEOUT=>30,# connect timeout seconds  30s   毫秒CURLOPT_TIMEOUT_MS
      //curl_setopt ( $ch,  CURLOPT_NOSIGNAL,true);//支持毫秒级别超时设置
    ];
    curl_setopt_array($ch, $options);
    $output = curl_exec($ch);
    $httpCode =curl_getinfo($ch,CURLINFO_HTTP_CODE);
    if($output === false ){
      echo "Error : ".curl_error($ch)." ,errrono: ".curl_errno($ch);
      curl_close($ch);
    }elseif($httpCode!==200){
      return  ['status_code'=>$httpCode,'data'=>getHttpError($httpCode)];
    }else{
      curl_close($ch);
      return  ['status_code'=>$httpCode,'data'=>$output];
    }
}
// $url = "http://fundgz.1234567.com.cn/js/260112.js?rt=1513048761263";
// var_dump(getJson($url));
//$argv 参数内容 数组格式  print_r($argv);  print_r($argc);
//$argc 参数个数 int
/**
  funcode,name,jzrq,dwjz,gsz,gszzl,gztime
  vsprintf(format ,array)
  utf 8 ch_zn  3
*/
function prettyResult($data){
        $header = ["基金编号","基金名称","昨日收盘","当前价格","涨幅%","更新时间"];
         $header = array_map(function($item){
          return "\033[36m".$item."\033[0m";
        },$header);
        $headerFormat = "%-24s%-42s%-24s%-24s%-21s%-21s\n";
       
         $result =  vsprintf($headerFormat, $header);
          $up = "\033[01;31m%s\033[0m";#red
          $down = "\033[01;32m%s\033[0m";#green   \033[01;40;37m str \033[0m]  01 亮度  40背景 37m字体颜色
         if($data instanceof stdClass){
              $data->gszzl = $data->gszzl > 0?sprintf($up,$data->gszzl):sprintf($down,$data->gszzl);
                $data->name = mb_strlen($data->name)>10?mb_substr($data->name, 0,10).'...':$data->name;               
               $namelength = mb_strlen($data->name)>10?10:mb_strlen($data->name);
               $namepad = 29+$namelength;
               $dataformat  = "%-11s%-{$namepad}s%-11s%-11s%-22s%-12s\n";
              $formtdata =  [$data->fundcode,$data->name,$data->dwjz,$data->gsz,$data->gszzl,$data->gztime];
            
              $result .= vsprintf($dataformat, $formtdata);
         }elseif(is_array($data)){
              foreach($data as $item){
               $item->gszzl = $item->gszzl > 0?sprintf($up,$item->gszzl):sprintf($down,$item->gszzl);
               $item->name =  mb_strlen($item->name)>10?mb_substr($item->name, 0,10).'...':$item->name;                 
               $namelength = mb_strlen($item->name)>10?10:floor(strlen($item->name)/3 );
            
                $namepad = 29+$namelength;
               // $namepad =29+$namelength;
               // echo $namepad."\n";
            
               // echo $namelength;
               $dataformat  = "%-11s%-{$namepad}s%-11s%-11s%-22s%-12s\n";
               // $dataformat  = "%-11s%-42s%-11s%-11s%-22s%-12s\n";
              $formtdata =  [$item->fundcode,$item->name,$item->dwjz,$item->gsz,$item->gszzl,$item->gztime];
              $result .= vsprintf($dataformat, $formtdata);
              }
         }
       echo  $result;
}
function initCommand(){
  global $argv;
   $cmd = isset($argv[1])?$argv[1]:'-o';
  switch( $cmd ) {
     case '-d':
     case '--daemon':
      //action
      break;
     case '-o':
     case '--output':
      if(isset($argv[2])){
          if(strpos($argv[2], ',')){
              $fundCodelist = explode(',', $argv[2]);
              $result = [];
              foreach($fundCodelist as $code){
                     $url = "http://fundgz.1234567.com.cn/js/".$code.".js";
                      if($successdata = getJson($url)){
                        $result[]=$successdata;
                      }
              }
          }else{
               $url = "http://fundgz.1234567.com.cn/js/".$argv[2].".js";
               $result = getJson($url);
          }
           $pretty = isset($argv[3])&&($argv[3]==='--pretty');
           if($pretty&&$result)return prettyResult($result);
           echo json_encode($result);
      }else{
        help();
      }
      break;
      case NULL:
      echo 11111;break;
      default:
       help();
  }
}
# heredoc raw out put the string 
function help(){
  $helpinfo = <<<HELP
Usage: 
        fund [options]  <fund_code(s)>   show the price of the fund
options:
        -d,--daemon         run as a daemon
        -o,--output         show the current data
             --pretty           pretty table
        -h,--help           show this help ifno       
HELP;
echo $helpinfo."\n";
}
initCommand();
