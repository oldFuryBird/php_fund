<?php
namespace Fund\UI;
use Fund\Crawl\Crawl;
class UI
{

  public static function colorUiInConsole($data)
  { 
        $header = ["基金编号","基金名称","昨日收盘","当前价格","涨幅%","更新时间"];
         $header = array_map(function($item){
          return "\033[36m".$item."\033[0m";
        },$header);
        $headerFormat = "%-24s%-42s%-24s%-24s%-21s%-21s\n";
         $result =  vsprintf($headerFormat, $header);
          $up = "\033[01;31m%s\033[0m";#red
          $down = "\033[01;32m%s\033[0m";#green   \033[01;40;37m str \033[0m]  01 亮度  40背景 37m字体颜色
         if($data instanceof \stdClass){
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
               $dataformat  = "%-11s%-{$namepad}s%-11s%-11s%-22s%-12s\n";
               // $dataformat  = "%-11s%-42s%-11s%-11s%-22s%-12s\n";
              $formtdata =  [$item->fundcode,$item->name,$item->dwjz,$item->gsz,$item->gszzl,$item->gztime];
              $result .= vsprintf($dataformat, $formtdata);
              }
         }
       echo  $result;
  }
  public static function rawDataConsole($result)
  { 
    echo json_encode($result);
  }
  public static function help()
  {
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
  public static function initCommand()
  {
    global $argv;
    $cmd = isset($argv[1])?$argv[1]:'-o';
    switch($cmd){
       case '-d':
     case '--daemon':
      echo "no daemon action yet";
      //action
      break;
     case '-o':
     case '--output':
      if(isset($argv[2])){
          $fundRepo = new Crawl();
          if(strpos($argv[2], ',')){
              $fundCodelist = explode(',', $argv[2]);
             
              $result = $fundRepo->getList($fundCodelist);
          }else{
              $result = $fundRepo->getOne($argv[2]);
          }
           $pretty = isset($argv[3])&&($argv[3]==='--pretty');
           if($pretty&&$result)return self::colorUiInConsole($result);
           self::rawDataConsole($result);
      }else{
        self::help();
      }
      break;
      default:
       self::help();
    }
  }

}

