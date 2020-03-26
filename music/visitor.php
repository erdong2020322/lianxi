<?php
include 'config.php';
global $pdo;
class visitorInfo{
    //获取访客ip
    public function getIp()
    {
        $ip=false;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
    //获取网站来源
    public function getFromPage(){
        return $_SERVER['HTTP_REFERER'];
    }

}


$visitor =new visitorInfo();
//记录访客的ip地址
$address=$visitor->getIp();


function ipCount($address){
    global $pdo;
    $sql=" select times from ip_counts where ip=:address ";

//查询结果存到$result变量
    $result=$pdo->prepare($sql);
    $result->bindParam('address',$address,PDO::PARAM_STR);
    $result->execute();
    if(!$row=$result->fetch(PDO::FETCH_ASSOC)){
        $sql="insert into ip_counts (ip, times ) values('$address','1')";
        $result=$pdo->prepare($sql);
        $result->execute();
    }else{
        $times=$row['times']+1;
        $sql="update ip_counts set times='$times' where ip ='$address'";
        $result=$pdo->prepare($sql);
        $result->execute();
    }
//获取总的访问人数即数据表中所有ip的数量
    $query="select count(ip) from ip_counts";
    $items=$pdo->prepare($query);
    $items->execute();

    $data=$items->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as $v){
        $num=($v['count(ip)']);
    }
//
return $num;

}
function iptimes(){
    global $pdo;
    //查询当前访客来访的次数的sql语句
    $query2=" select sum(times) from ip_counts ";

    $item=$pdo->prepare($query2);
    $item->execute();

    $data2=$item->fetchAll(PDO::FETCH_NUM);

    foreach ($data2 as $v){
        $fwl=($v[0]);
    }
    return $fwl;

}
$ipCount=ipCount($address);
$ipTimes=iptimes();

exit(json_encode(array('count'=>$ipCount,'times'=>$ipTimes)));
