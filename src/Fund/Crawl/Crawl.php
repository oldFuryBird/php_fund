<?php
namespace Fund\Crawl;;
use Fund\Http\Http;
class Crawl
{
  protected $base_url = "http://fundgz.1234567.com.cn/js/%s.js";
  public function __construct()
  {
      $this->crawler = new Http();
  }
  protected function getJson($url,$isArray = false){
      
      try{
        $response = $this->crawler->get($url);
        preg_match('/jsonpgz\((.*?)\)/',$response['data'],$matches);
        return json_decode($matches[1],$isArray);
      }catch(Exception $e){
        echo $e->getMessage();
      }
  }
  public function getOne($fundcode)
  {
      $url = sprintf($this->base_url,$fundcode);
      return $this->getJson($url);
  }

  public function getList(Array $fundcodes)
  {
      $result = [];
      foreach($fundcodes as $code){
          $url = sprintf($this->base_url,$code);
          $result[] = $this->getJson($url); 
      }
      return $result;
  }
}
// require "Http.php";
// $a = new Fund();
// var_dump($a->getSingleFundInfo(260112));