<?php
namespace Fund\Http;
class Http
{
  protected $error =[
        401=>"unauthorized",
        403=>"forbiden",
        404=>"not found",
        405=>"method not allowed",
        500=>"internal serve error",
        502=>"bad gateway"
  ];
  protected $header=[];
  protected $method;
  protected $options=[
      CURLOPT_HEADER=>0,# 0,1 是否输出头部信息
      CURLOPT_RETURNTRANSFER=>1,# 0 直接输出内容 1 保存到内容到变量
     
      // CURLOPT_CUSTOMREQUEST=>$method,# GET POST PUT.....
      CURLOPT_USERAGENT=>'',# user agent  like firefox chrome  etc.
      CURLOPT_TIMEOUT=>30,# connect timeout 
  ];
  protected $ch;

  public function __construct($header = [])
  {
    
    $this->checkEnv();
    $this->ch = curl_init();
    $this->options[CURLOPT_HTTPHEADER]=$header;#http头信息
    
  }
  protected function checkEnv()
  {

  }
  protected function getHttpError($code)
  {
    return isset($this->error[$code])?$code." ".$this->error[$code]:"uncaught error";
  }
  protected function exec()
  {
    curl_setopt_array($this->ch, $this->options);
    $output = curl_exec($this->ch);
    $httpCode = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
    if($output === false){
        throw new \Exception(curl_error($this->ch)." : ".curl_errno($this->ch));
    }else if($httpCode !==200){
        throw new \Exception($this->getHttpError($httpCode),$httpCode);
    }else{
      return ['status_code'=>$httpCode,'data'=>$output];
    }
  }
  public function get($url)
  {
    $this->options[CURLOPT_URL]=$url;
    $this->options[CURLOPT_CUSTOMREQUEST]="GET";
    return $this->exec();
  }
  public function post($url,$param)
  {

    $this->options[CURLOPT_URL]=$url;
    $this->options[CURLOPT_CUSTOMREQUEST]="POST";
    $this->options[CURLOPT_POSTFIELDS]=$param;
    return $this->exec();
  }

  public function put($url)
  {

  }
  public function delete($url)
  {

  }
  public function __destruct()
  {
    curl_close($this->ch);
  }
}
