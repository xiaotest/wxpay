<?php
require_once dirname(__FILE__).'/common.php';
require_once dirname(__FILE__).'/qrcode.php';
require_once dirname(__FILE__).'/log.php';
require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/mysql.php';
$logHandler = new CLogFileHandler("../logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);

/**
 * 获取私有key字符串 重新格式化  为保证任何key都可以识别
 * @param $private_key
 * @return string
 */
 function get_private_key($private_key){
    $search = [
        "-----BEGIN RSA PRIVATE KEY-----",
        "-----END RSA PRIVATE KEY-----",
        "\n",
        "\r",
        "\r\n"
    ];

    $private_key=str_replace($search,"",$private_key);
    return $search[0] . PHP_EOL . wordwrap($private_key, 64, "\n", true) . PHP_EOL . $search[1];
}

/**
 * 获取公共key字符串  重新格式化 为保证任何key都可以识别
 * @param $public_key
 * @return string
 */
 function get_public_key($public_key){
    $search = [
        "-----BEGIN PUBLIC KEY-----",
        "-----END PUBLIC KEY-----",
        "\n",
        "\r",
        "\r\n"
    ];
    $public_key=str_replace($search,"",$public_key);
    return $search[0] . PHP_EOL . wordwrap($public_key, 64, "\n", true) . PHP_EOL . $search[1];
}

//从数据库取出keypem
function keypem(){
    $companyid=10000;
    $dbname = WxPayConfig::dbname . "_" . $companyid;
    $db = new mysql(WxPayConfig::sqlHOST, WxPayConfig::mysqlname, WxPayConfig::mysqlpaw, $dbname);
    //读取密钥文件
    //数据库key 可以存储 -----BEGIN RSA PRIVATE KEY-----  MIICeAqhkiG  -----END RSA PRIVATE KEY-----这种格式，
    //也可以直接存储    MIICeAqhkiG  值 这种格式
    $sqlstr = "select  * from test where id=1";
    $db->query($sqlstr);
    $key = $db->fetchArray(MYSQL_ASSOC);
   return  $key[0];
}

//加签
function sign($data){
     $pem = file_get_contents(dirname(__FILE__).'/keypem.pem');//文件
//     $keypem=keypem();//数据库读取  keypem
//     $pem=$keypem['private']; //私钥
     $pem=get_private_key($pem);//格式转化
    //获取私钥
    $pkeyid = openssl_pkey_get_private($pem);
    //MD5WithRSA私钥加密
    openssl_sign($data,$sign,$pkeyid,OPENSSL_ALGO_MD5);
    //返回base64加密之后的数据
    openssl_free_key($pkeyid);
//  $t=base64_encode($sign);
    $md5sign=base64_encode(base64_encode($sign));
    //解密-1:error验证错误 1:correct验证成功 0:incorrect验证失败
//     $pubkey = openssl_pkey_get_public($pem);
//     $ok = openssl_verify($data,base64_decode(base64_decode($t)),$pubkey,OPENSSL_ALGO_MD5);
//    // var_dump($ok);
//    Log::DEBUG('ok'.$ok);
//    Log::DEBUG('t'.$t);
    return $md5sign;
}

//报文头  数据  交易码(接口名称)tranCode    版本号 version
function head($inspOrder,$version){
    $head=array(
        "tranCode"=>$inspOrder,//Y getQrCode	交易码
        "version"=>$version,// Y version	版本号
        "cstId"=>"070010",// Y cstId	客户端系统编号
        "cstSeqNo"=>'L'.date("YndHis").'10000',//Y cstSeqNo	客户端系统流水号
        "cstTxnDt"=>date('Ymd'),//Y  cstTxnDt	客户端系统交易日期
        "cstTxnTm"=>date('His'),//Y cstTxnDt	客户端系统交易时间
        "termId"=>"",//N  termId	客户端系统终端号
        "origCstSeqNo"=>'L'.date("YndHis").'10001', //origCstSeqNo	源客户端系统流水号	String(32)	Y
        "origCstTxnDt"=>date('Ymd'),//origCstTxnDt	源客户端系统交易日期	String(8)	Y
        "origCstTxnTm"=>date('His'), //origCstTxnTm	源客户端系统交易时间	String(6)	Y
        "macNode"=>"", //macNode	Mac校验的节点号	String(50)	N
        "macValue"=>"", //macValue	Mac值	String(32)	N
        "timeStamp"=>time(),//timeStamp	时间戳	String	Y
        "nonceStr"=>createNonceStr(),//nonceStr	随机字符串	String(32)	Y
    );
    return $head;
}

//下单支付接口
function  preOrder(){
    $head=head('preOrder','2.0.0');
    $head=array_filter($head);//去掉空数组元素
    ksort($head);
    $str = '';
    foreach($head as $k => $v) {
        $str .= $k.'='.$v.'&';
    }
    //主体
    $body=array(
        "outTradeNo"=>'L'.'10000'.date("YndHis").rand(1,9999),//外部商户接入平台的订单号
        "payWay"=>"WX_PUB", //支付方式 WX_PUB WX_BCTP  WX_ORD
        'mercId'=>'8000440305000603',//商户ID
        'payOrderAmount'=>0.01,
        'mercProName'=>'商品名称',//商品名称
        'body'=>'商品名称',//商品描述
        'payType'=>'1',
        'mercChaName'=>'口袋零钱平台测试',//商户中文名称
        'payOrdRcvAmt'=>0.01,//订单实付金额
        'termialIp'=>ip(),//终端IP
        // 'subAppId'=>''
//        'openid'=>'oyw6cjgNr5jZ3pnhdnLZWY4yTULo',
  // 'qrCodeInfo'=>'134525278614386832',//被扫模式二维码值
        'subAppId'=>'wx360cee6438ac79e2',//wx360cee6438ac79e2    appid   我们 wx51bac079c96a958d
        //'subMchId'=>'1243620602',
        'subOpenId'=>'oWLMP0Vu5xGXgj2qtC7AucYr8fLU'
    );

    $body=array_filter($body);
    ksort($body);
    $str1 = '';
    foreach($body as $k => $v) {
        $str1 .= $k.'='.$v.'&';
    }
//$str1=rtrim($str1, "&") ;
    $sign=$str.'|'.$str1;
    Log::DEBUG('sign'.$sign);
    //签名加密流程
    $signature=sign($sign);
    Log::DEBUG('sign22'.$signature);

    $data=array(
        //报文请求信息
        'service'=>array(
            //报文头
            'head'=>$head,
            //主体
            'body'=>$body,
            //签名
            'sign'=>array(
                //签名类型
                "signType"=>"MD5WithRSA",
                //签名
                'signature'=>$signature,
            )
        )
    );
// }

   $json=json_encode_UN($data);
    $url="http://alipay.sunnywx.pw/merchantFront/preOrder";
    $res_json=http_post_json($url,$json);
    $res=json_decode($res_json,true);
    $rspCode=$res['service']['head']['rspCode'];
    //rspCode  返回code码  200 成功
     if($rspCode==200){
         /*
          *
          * 下单成功  处理
          */
         echo '下单成功';
     }
     return $res;
}

//8.6. 订单查询  建议优先使用平台支付订单号查询
 function  inspOrder($payOrdNo='',$outTradeNo=''){
      //报文头 tranCode（交易码）：inspOrder   version（接口版本）：2.0.0
     $head=head('inspOrder',"2.0.0");
     $head=array_filter($head);//去掉空数组元素
     ksort($head);
     $str = '';
     foreach($head as $k => $v) {
         $str .= $k.'='.$v.'&';
     }
      //主体
     $body=array(
        'payOrdNo'=>$payOrdNo,//平台支付订单号查询
//         'mercId'=>'', //商户号
       "outTradeNo"=>$outTradeNo,//外部商户接入平台的订单号
     );
     $body=array_filter($body);
     ksort($body);
     $str1 = '';
     foreach($body as $k => $v) {
         $str1 .= $k.'='.$v.'&';
     }
     $sign=$str.'|'.$str1;
     Log::DEBUG('sign'.$sign);
//签名加密流程
     $signature=sign($sign);
     $data=array(
         //报文请求信息
         'service'=>array(
             //报文头
             'head'=>$head,
             //主体
             'body'=>$body,
             //签名
             'sign'=>array(
                 //签名类型
                 "signType"=>"MD5WithRSA",
                 //签名
                 'signature'=>$signature,
             )
         )
     );
     $json=json_encode_UN($data);
     $url="http://alipay.sunnywx.pw/merchantFront/inspOrder";
     $res_json=http_post_json($url,$json);
     $res=json_decode($res_json,true);
     $rspCode=$res['service']['head']['rspCode'];
     //rspCode  返回code码  200 成功
     if($rspCode==200){
         /*
        *
        * 订单查询  处理
        */
         echo '查询成功';
     }
     return $res;
 }

 //8.5. 退款交易  •	tranCode（交易码）：refund //•	version（接口版本）：2.0.0
 function  refund($origPayOrdNo,$outTradeNo,$mercId,$refundAmt,$refundCause){
     //报文头 tranCode（交易码）：inspOrder   version（接口版本）：2.0.0
     $head=head('refund',"2.0.0");
     $head=array_filter($head);//去掉空数组元素
     ksort($head);
     $str = '';
     foreach($head as $k => $v) {
         $str .= $k.'='.$v.'&';
     }
     //主体
     $body=array(
         'origPayOrdNo'=>$origPayOrdNo,//原平台支付订单号
          'mercId'=>$mercId, //商户号
          "outTradeNo"=>$outTradeNo,//外部商户接入平台的订单号
          'refundAmt'=>$refundAmt, //退款金额
         'refundCause'=>$refundCause //退款原因
     );
     $body=array_filter($body);
     ksort($body);
     $str1 = '';
     foreach($body as $k => $v) {
         $str1 .= $k.'='.$v.'&';
     }
     $sign=$str.'|'.$str1;
   Log::DEBUG('sign'.$sign);
//签名加密流程
     $signature=sign($sign);
     $data=array(
         //报文请求信息
         'service'=>array(
             //报文头
             'head'=>$head,
             //主体
             'body'=>$body,
             //签名
             'sign'=>array(
                 //签名类型
                 "signType"=>"MD5WithRSA",
                 //签名
                 'signature'=>$signature,
             )
         )
     );
     $json=json_encode_UN($data);
     $url="http://alipay.sunnywx.pw/merchantFront/refund";
     $res_json=http_post_json($url,$json);
     $res=json_decode($res_json,true);
     $rspCode=$res['service']['head']['rspCode'];
     //rspCode  返回code码  200 成功
     if($rspCode==200){
         /*
        *
        * 退款成功  处理
        */
         echo '退款成功';
     }
     return $res;
 }


//8.7. 退款查询
 function  qryRefund($refundOrdNo='',$outRefundNo='',$mercId){
     //报文头 tranCode（交易码）：inspOrder   version（接口版本）：2.0.0
     $head=head('qryRefund',"2.0.0");
     $head=array_filter($head);//去掉空数组元素
     ksort($head);
     $str = '';
     foreach($head as $k => $v) {
         $str .= $k.'='.$v.'&';
     }
     //主体
     $body=array(
         'refundOrdNo'=>$refundOrdNo,//原平台支付订单号
         "outRefundNo"=>$outRefundNo,//外部商户接入平台的订单号
         'mercId'=>$mercId
     );
     $body=array_filter($body);
     ksort($body);
     $str1 = '';
     foreach($body as $k => $v) {
         $str1 .= $k.'='.$v.'&';
     }
     $sign=$str.'|'.$str1;
   Log::DEBUG('sign'.$sign);
//签名加密流程
     $signature=sign($sign);
     $data=array(
         //报文请求信息
         'service'=>array(
             //报文头
             'head'=>$head,
             //主体
             'body'=>$body,
             //签名
             'sign'=>array(
                 //签名类型
                 "signType"=>"MD5WithRSA",
                 //签名
                 'signature'=>$signature,
             )
         )
     );
     $json=json_encode_UN($data);
     $url="http://alipay.sunnywx.pw/merchantFront/qryRefund";
     $res_json=http_post_json($url,$json);
     $res=json_decode($res_json,true);
     $rspCode=$res['service']['head']['rspCode'];
     //rspCode  返回code码  200 成功
     if($rspCode==200){
         /*
        *
        * 退款查询成功  处理
        */
         echo '商户退款查询成功';
     }
     return $res;
 }

//8.3. 生成二维码
function  getQrCode($mercId){
    //报文头 tranCode（交易码）：inspOrder   version（接口版本）：2.0.0
    $head=head('getQrCode',"2.0.0");
    $head=array_filter($head);//去掉空数组元素
    ksort($head);
    $str = '';
    foreach($head as $k => $v) {
        $str .= $k.'='.$v.'&';
    }
    //主体
    $body=array(
        'outTradeNo'=>'L'.'10000'.date("YndHis").rand(1,9999),//原平台支付订单号
        'mercId'=>$mercId,
        "qrType"=>'01',//qrType	String	2	二维码类型  只能填01
        'createType'=>'2',//createType	String	2	二维码生成方式
        'qrSource'=>'1',  //qrSource	String	2	二维码来源
        'payOrderAmount'=>'0.01'
    );
    $body=array_filter($body);
    ksort($body);
    $str1 = '';
    foreach($body as $k => $v) {
        $str1 .= $k.'='.$v.'&';
    }
    $sign=$str.'|'.$str1;
    Log::DEBUG('sign'.$sign);
//签名加密流程
    $signature=sign($sign);
    $data=array(
        //报文请求信息
        'service'=>array(
            //报文头
            'head'=>$head,
            //主体
            'body'=>$body,
            //签名
            'sign'=>array(
                //签名类型
                "signType"=>"MD5WithRSA",
                //签名
                'signature'=>$signature,
            )
        )
    );
    $json=json_encode_UN($data);
    $url="http://alipay.sunnywx.pw/merchantFront/getQrCode";
    $res_json=http_post_json($url,$json);
    $res=json_decode($res_json,true);
    $rspCode=$res['service']['head']['rspCode'];
    //rspCode  返回code码  200 成功
    if($rspCode==200){
        /*
       *
       * 创建二维码成功  处理
       */
        echo "创建二维码成功:地址"."<br/>".$url=$res['service']['body']['qrCode'];
//        $url=$res['service']['body']['qrCode'];
//        $qrcode = new QRcode();
//       @ob_end_clean();
     //  return $qrcode::png($url, false, '', '5', 3);
    }
    return $res;
}

//查询数据接口  payOrderNo  2019073118051610000001  outTradeNo M20197311805158127
//$data=inspOrder('2019073118051610000001','');

////下单支付接口
$data=preOrder();

//退款  refund  origPayOrdNo 平台支付订单号 查询所用参数  商户号必填 8000440305000603
//$data=refund('2019073118051610000001','M20197311805158127','8000440305000603','0.01','测试接口');

//退款查询
//$data=qryRefund('2019080113374476500001','M20197311805158127','8000440305000603');

 //二维码
//$data=getQrCode('8000440305000603');

dump($data);
//预下单 'http://vip.bilalipay.com/zhongxin/common/noticy.php'
function unifiedorder($gid,$gname,$price,$notifyUrl){
    if($_SERVER['SERVER_ADDR'] == '127.0.0.1'){
        return  array(
            'code'      =>200,
            'msg'       => '预支付成功。',
            'pay_info'  => 'SUCCESS',
            'prepay_id' => 'SGDFG4563FFFFFFFSDFQ43435FGFG',
        );
    }

    $data = array(
        'appid'            =>'wx507a0dc679ec6413',//服务商微信号appid
        'mch_id'		   =>'1229746202',//服务商商户号
        'sub_appid'        =>'wx51bac079c96a958d',	//子商户appid
        'sub_mch_id'       =>'1243620602',//子商户商户号
        'device_info'	   =>'1229746202',
        'nonce_str'        =>createNonceStr(),
        'body'             => $gname,
        'out_trade_no'     => $gid,//商户订单号
        'total_fee'        => intval($price*100),//订单金额
        'spbill_create_ip' => $_SERVER['SERVER_ADDR'],//ip
        'trade_type'       => 'JSAPI',
        'notify_url'       =>$notifyUrl,//通知地址
        'sub_openid'       =>'oyw6cjgNr5jZ3pnhdnLZWY4yTULo',//用户openid
    );
    //  weixin::write_log($data);
    ksort($data);
    $str = '';
    foreach($data as $k => $v) { $str .= $k.'='.$v.'&'; }
    $str.= 'key='.'wugangisaboy1wincomemustcome2ok8';
    $data['sign'] = strtoupper(md5($str));
    $xml = '<xml>';
    foreach($data as $k => $v) { $xml .= '<'.$k.'>'.$v.'</'.$k.'>'; }
    $xml.= '</xml>';
    $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
     $ret=request_post($url,$xml);
    if(!$ret){
        $res = array('code' => -2004, 'msg' => '预支付失败。', 'pay_info' => 'NO_RETURN');
    }else{
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $ret = (array)simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA);
        if(!isset($ret['prepay_id']) || !$ret['prepay_id']){
            $res = array(
                'code'     => -2005,
                'msg'      => '预支付失败。',
                'pay_info' => json_encode($ret),
                'debug'    => json_encode($data),
            );

        }else{
            $res = array(
                'code'      =>200,
                'msg'       => '预支付成功。',
                'pay_info'  => $ret['return_code'].'|'.$ret['return_msg'],
                'prepay_id' => $ret['prepay_id'],
            );
        }
    }
//  Log::DEBUG('res'.json_encode($res));
    return $res;
}

//创建支付包
 function buildPayPackage($prepay_id){
//    $weixin = CWe::app()->weixin->getCurInfo();
    $data = array(
        'appId'     =>'wx51bac079c96a958d',
        'timeStamp' => time().'',
        'nonceStr'  =>createNonceStr(),
        'package'   => 'prepay_id='.$prepay_id,
        'signType'  => 'MD5',
    );
    ksort($data);
    $str = '';
    foreach($data as $k => $v) { $str .= $k.'='.$v.'&'; }
    $str.= 'key='.'wugangisaboy1wincomemustcome2ok8';
    $data['paySign'] = strtoupper(md5($str));
    return $data;
}


//测试  获取jsapi支付参数
function pay(){
     $gid="WXVIPCARDON1000".time();
     $res=unifiedorder($gid,'测试','0.01','http://vip.bilalipay.com/zhongxin/common/noticy.php');
    if($res['code']==200){
        $res1=buildPayPackage($res['prepay_id']);
        return $res1;
    }
    return $res;
}


//$gid="WXVIPCARDON1000".time();
// //$res=unifiedorder($gid,'测试','0.01','http://vip.bilalipay.com/zhongxin/common/noticy.php');
//$res1=buildPayPackage('wx02134135197949df1418161b1013088700');
// dump(pay());
