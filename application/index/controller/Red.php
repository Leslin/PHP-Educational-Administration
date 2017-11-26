<?php
namespace app\index\controller;

use think\Cache;
use think\Db;
use think\Controller;
use think\Log;
use app\index\controller\Jssdk;

/**
* 微信红包
*/
class Red extends Controller
{
	private $apiclient_cert = '';
	private $apiclient_key = '';
	//pay的秘钥值
	private $apikey = "";
	//错误信息
	private $error = ''; 

	private $mchid = '';//商户号
	private $mchappid = '';//公众号

	private $openid = '';//接收者openid
	private $amount = 100;//金额
	private $partnertradeno = '';//订单号
	private $spbillcreateip = '106.14.169.121';//触发ip
	private $checkname = 'NO_CHECK';//校验要求

	private $sendname = '';
	private $wishing = '';
	private $actname = '';   //活动名称
	private $remark = '';    //备注

	private $totalnum =3;
	private $amttype ='ALL_RAND';

	//裂变红包
	private $api_group = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack";
    //普通红包
	private $api_single = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
    //企业支付
	private $api_compay = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
	//约包查询
	private $api_redbag_select = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
	//企业支付查询
	private $api_compay_select = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo";


	/**
	*公用-支付用商户号
	*@var string
	*/
	public function setMchid($mchid){
		$this->mchid = $mchid;
	}
	/**
	*公用-pay的秘钥值
	*@var string
	*/
	public function setApiKey($apikey){
		$this->apikey = $apikey;
	}


	/**
	*企业支付用微信公众号
	*@var string
	*/
	public function setMchAppid($mchappid){
		$this->mchappid = $mchappid;
	}
	/**
	*企业支付接收用户openid
	*@var string
	*/
	public function setOpenid($openid){
		$this->openid = $openid;
	}

	/**
	*企业支付金额
	*@var integer
	*/
	public function setAmount($amount){
		$this->amount = $amount;
	}
	/**
	*企业支付描述
	*@var string
	*/
	public function setDesc($desc){
		$this->remark = $desc;
	}
	
	/**
	*企业支付订单号
	*@var string
	*/
	public function setPartnerTradeNo($partnertradeno){
		$this->partnertradeno = $partnertradeno;
	}
	/**
	*企业支付触发ip
	*@var string
	*/
	public function setSpbillCreateIp($spbillcreateip){
		$this->spbillcreateip = $spbillcreateip;
	}
	/**
	*企业支付校验规则
	*@var string
	*/
	public function setCheckName($checkname){
		$this->checkname = $checkname;
	}

	/**
	*红包支付公众号
	*@var string
	*/
	public function setWxappid($wxappid){
		$this->mchappid = $wxappid;
	}
	/**
	*红包支付订单号
	*@var string
	*/
	public function setMchBillNo($mchbillno){
		$this->partnertradeno = $mchbillno;
	}
	/**
	*红包支付触发ip
	*@var string
	*/
	public function setClientIp($clientip){
		$this->spbillcreateip = $clientip;
	}
	/**
	*红包接收者/裂一变红包的种子接收者
	*@var string
	*/
	public function setReOpenid($reopenid){
		$this->openid = $reopenid;
	}
	/**
	*红包支付金额
	*@var integer
	*/
	public function setTotalAmount($totalamount){
		$this->amount = $totalamount;
	}
	/**
	*红包支付公众号
	*@var string
	*/
	public function setSendName($sendname){
		$this->sendname = $sendname;
	}
	/**
	*红包支祝福语
	*@var string
	*/
	public function setWishing($wishing){
		$this->wishing = $wishing;
	}
	/**
	*红包支付活动名称
	*@var string
	*/
	public function setActName($actname){
		$this->actname = $actname;
	}
	/**
	*红包支付备注信息
	*@var string
	*/
	public function setRemark($remark){
		$this->remark = $remark;
	}
	/**
	*红包支付个数-裂变专用
	*@var string
	*/
	public function setTotalNum($totalnum){
		$this->totalnum = $totalnum;
	}

	public function setAppId($appid){
		$this->mchappid = $appid;
	}
	/**
	*错误反馈
	*@return string
	*/
	// public function error(){
	// 	return $this->error;
	// }

	/**
	*普通红包支付
	*@return boolean
	*/
	
	public function RedBag($openid,$amount,$wishing){
		if(!$this->inited()) return;
		$obj = array();
		$obj['wxappid'] = config('red.appid');
		$obj['mch_id'] = config('red.mch_id');
		$obj['mch_billno'] = $this->GenBillNo();
		$obj['client_ip'] = $this->spbillcreateip;
		$obj['re_openid'] = $openid;
		$obj['total_amount'] = $amount;
		$obj['total_num'] = 1;
		$obj['send_name'] = config('red.sendname');;
		$obj['wishing'] = $wishing;
		$obj['act_name'] = config('red.actname');;
		$obj['remark'] = config('red.remark');;
		$url = $this->api_single;
		return json_decode($this->Pay($url,$obj),true);
	}



	/**
	*裂变红包支付
	*@return boolean
	*/
	public function RedBagGroup(){
		if(!$this->inited()) return;
		$obj = array();
		$obj['wxappid'] = $this->mchappid;
		$obj['mch_id'] = $this->mchid;
		$obj['mch_billno'] = $this->partnertradeno;
		$obj['re_openid'] = $this->openid;
		$obj['total_amount'] = $this->amount;
		$obj['total_num'] = $this->totalnum;
		$obj['amt_type'] = $this->amttype;
		$obj['send_name'] = $this->sendname;
		$obj['wishing'] = $this->wishing;
		$obj['act_name'] = $this->actname;
		$obj['remark'] = $this->remark;
		$url = $this->api_single;
		return $this->Pay($url,$obj);
	}
	/**
	*企业支付
	*@return boolean
	*/
	public function ComPay($openid,$amount,$checkname,$desc){
		if(!$this->compaycheck()){
			return false;
		}
		$obj = array();
		$obj['mch_appid'] = $this->mchappid;
		$obj['mchid'] = $this->mchid;
		$obj['openid'] = $openid;
		$obj['check_name'] = 'NO_CHECK';    //强制用户验证姓名
		$obj['re_user_name'] = $checkname;
		$obj['amount'] = $amount;
		$obj['partner_trade_no'] = $this->partnertradeno;
		$obj['spbill_create_ip'] = $this->spbillcreateip;
		$obj['desc'] = $desc;
		$url = $this->api_compay;
		return $this->Pay($url,$obj);
	}
	/**
	*红包查询
	*@return array
	*/
	public function BagSelect(){
		$this->license();
		$obj = array();
		$obj['appid'] = $this->mchappid;
		$obj['mch_id'] = $this->mchid;
		$obj['mch_billno'] = $this->partnertradeno;
		$obj['bill_type'] = 'MCHT';
		$url = $this->api_redbag_select;
		return $this->Pay($url,$obj);
	}
	/**
	*企业支付查询
	*@return array
	*/
	public function ComPaySelect(){
		$this->license();
		$obj = array();
		$obj['appid'] = $this->mchappid;
		$obj['mch_id'] = $this->mchid;
		$obj['partner_trade_no'] = $this->partnertradeno;
		$url = $this->api_compay_select;
		return $this->Pay($url,$obj);
	}

	/**
	*现金支付前准备
	*@return boolean
	*/
	private function inited(){
		$inited = true;
		$amount = $this->amount;
		if(!is_numeric($amount)){
			$this->error = "金额参数错误";
			$inited = false;
		}elseif($amount<100){
			$this->error = "金额太小";
			$inited = false;
		}elseif($amount>20000){
			$this->error = "金额太大";
			$inited = false;
		}
		if(!$this->partnertradeno){
			$this->partnertradeno = $this->GenBillNo();
		}
		if(!$this->spbillcreateip)
			$this->spbillcreateip = $_SERVER['REMOTE_ADDR'];
		return $inited;
	}
	/**
	 * 企业支付前验证
	 */

	private function compaycheck(){

		$inited = true;
		$amount = $this->amount;
		if(!is_numeric($amount)){
			$this->error = "金额参数错误";
			$inited = false;
		}elseif($amount<100){
			$this->error = "金额太小";
			$inited = false;
		}
		if(!$this->partnertradeno){
			$this->partnertradeno = $this->GenBillNo();
		}
		if(!$this->spbillcreateip)
			$this->spbillcreateip = $_SERVER['REMOTE_ADDR'];
		return $inited;

	}

	/**
	*生在订单号
	*@return boolean
	*/
	private function GenBillNo(){
		$rnd_num = array('0','1','2','3','4','5','6','7','8','9');
		$rndstr = "";
		while(strlen($rndstr)<10){
			$rndstr .= $rnd_num[array_rand($rnd_num)];    
		}

		return $this->mchid.date("Ymd").$rndstr;
	}

	/**
	*完成支付操作
	*@url string
	*@obj array
	*@return boolean
	*/
	private function Pay($url,$obj){	
		$obj['nonce_str'] = $this->create_noncestr();
		$sign = $this->getSign($obj);
		$obj['sign'] = $sign;
		$postXml = $this->arrayToXml($obj);
		$responseXml = $this->CurlPostSsl($url,$postXml);
		Log::info(json_encode($responseXml));
		return json_encode($responseXml);
	}
	/**
	*创建随机字串
	*@return string
	*/
	private function create_noncestr($length = 32){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = '';
		for ($i = 0; $i <$length; $i++){
			$str .= substr($chars,mt_rand(0,strlen($chars)-1),1);
		}
		return $str;
	}
	/**
	*创建签名
	*@return string
	*/
	private function getSign($arr){
		ksort($arr); //按照键名排序
		$sign_raw = '';
		foreach($arr as $k => $v){
			$sign_raw .= $k.'='.$v.'&';
		}
		$sign_raw .= 'key='.config('red.key');

		return strtoupper(md5($sign_raw));
	}

	/**
     * WXHongBao::genXMLParam()
     * 生成post的参数xml数据包
     * @return $xml
     */
	private function arrayToXml($arr){
		$xml ="<xml>";
		foreach ($arr as $key => $val) {
			if (is_numeric($val)) {
				$xml .= "<".$key.">".$val."</".$key.">";
			}else{
				$xml .= "<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml .= "</xml>";
		return $xml;		
	}

	/**
     * curl提交
     * @return $boolean
     */
	private function CurlPostSsl($url,$xml,$second = 30){
		
		$ch = curl_init();   	
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);    	
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		  
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT,config('red.cert_path'));    	
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM'); 
		curl_setopt($ch,CURLOPT_SSLKEY,config('red.key_path')); 
		curl_setopt($ch,CURLOPT_CAINFO,config('red.root_path'));
		
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		$data = curl_exec($ch);
		
		//return $data; 
		if($data){
			curl_close($ch);            
			$rsxml = $rsxml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
			if($rsxml->return_code == 'SUCCESS' ){
				return $rsxml;
			}else{
				
				return $rsxml->return_msg;    //
			}
		}else{ 
			curl_close($ch);
			return curl_errno($ch);
		}
	}

}