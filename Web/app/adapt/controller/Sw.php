<?php
namespace app\adapt\controller;
use think\Controller;
use think\Db;
use app\index\controller\Http;
use app\index\controller\Conf;

class Sw extends Controller
{

    private function checkSession($value='')
    {
        # code...
        session_start();
        if(isset($_SESSION['TOKEN'])) return $_SESSION['user'];
        else $this->error("未登录","/?status=E",3);
    }

    private function getCtx()
    {
        $ctx="";
        if($_SERVER['SERVER_NAME']=="localhost") $ctx =  "/Sw" ;
        return $ctx;
    }

    public function httpReq($params){
        $header = Conf::$header;
        array_push($header,"token:".$_SESSION['TOKEN']);
        $http = new Http();
        $info = $http->httpRequest(Conf::$url,$params,"GET",$header);
        return $info;
    }

    public function getCurrentTime(){
        $params = array(
        "method" => "getCurrentTime",
        "currDate" => date("Y-m-d",time())
        );
        return $this->httpReq($params);
    }

    public function overview()
    {
        $this->assign(['ctx' => $this->getCtx(),
                       'user' => $this->checkSession() ]);
        return $this->fetch();
    }

    public function info()
    {
        $this->assign(['ctx' => $this->getCtx(),
                       'user' => $this->checkSession() ]);
        return $this->fetch();
    }

    public function table($zc=-1)
    {
        $user = $this->checkSession();
        $s = json_decode($this->getCurrentTime(),true);
	$zc = $zc === -1 ? $s['zc'] : $zc;
	$params=array(
        "method" => "getKbcxAzc",
        "xnxqid" => $s['xnxqh'],
        "zc" => $zc ,
        "xh" => $_SESSION['account']
        );
        $info = $this->httpReq($params);
        $this->assign(['ctx' => $this->getCtx(),
                       'user' => $user,
                       'zc' => $zc,
			"info" => json_decode($info,true)
                   ]);
        return $this->fetch();
    }

    public function classroom($idleTime="")
    {
        $user = $this->checkSession();
        $info = "";
        if ($idleTime!=="") {
            $params = array(
                "method" => "getKxJscx",
                "time" => date("Y-m-d",time()),
                "idleTime" => $idleTime
            );
            $info = $this->httpReq($params);
        }
        $this->assign(['ctx' => $this->getCtx(),
                       'user' => $user,
                       'info' => $info === "" ? array() : json_decode($info,true)
                   ] );
        return $this->fetch();
    }

    public function grade($sy="")
    {
        $user = $this->checkSession();
	//$sy = $sy === "" ? json_decode($this->getCurrentTime(),true)['xnxqh'] : $sy ;
	$params = array(
            "method" => "getCjcx",
            "xh" => $_SESSION['account'],
            "xnxqid" => $sy
        );
        $info = $this->httpReq($params);
        $this->assign(['ctx' => $this->getCtx(),
                       'user' => $user,
                       'xnxqh' => $sy,
		       'info' => json_decode($info,true)
                   ]);
        return $this->fetch();
    }
    
}
