<?php
/**
 * 	配置账号信息
 */



/**
 * 	配置账号信息
 */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('date.timezone', 'Asia/Shanghai');
header("Content-type:text/html;charset=utf-8");
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:GET,POST");
class WxPayConfig
{
    //=======【基本信息设置】=====================================
    //
    /**
     * TODO: 修改这里配置为您自己申请的商户信息
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     * @var string
     */
    //token 过期时间 30分钟
    const token='30';
    const APPID = 'wx507a0dc679ec6413';
    const MCHID = '1229746202';
    const KEY = 'wugangisaboy1wincomemustcome2ok8';
    const APPSECRET = '5f9a7e1fa7da0e3b83a56310e38add63';
    //const APPID = 'wxcc5fb9781e6c8c9c';
    //const MCHID = '1480439762';
    //const KEY = 'wolixiangdenvhaishixiongda201705';
    //const APPSECRET = '4c21bace1f9bc8841e4f73ed64ab9563';
    const SUBAPPID = 'wxcc5fb9781e6c8c9c';
    const SUBAPPSECRET = '5748117f9847e44c3f271f45ed7d1276';
    const SUBMCHID = '1384256502';
    const DESKEY = 'WAD2015=lyfBenny';
    const DESKEY2 = 'benny2016lybWAD';
//    const sqlHOST ='rm-wz94006ut86t1l0a1.mysql.rds.aliyuncs.com';
    const sqlHOST ='rm-wz94006ut86t1l0a1no.mysql.rds.aliyuncs.com';

    const mysqlpaw ='WAD@123456a';
    const mysqlname ='root';
    const dbname = 'wadragon';
    const md5key ='huazhiguang123456';

//    const sqlHOST ='47.92.91.226';
//    const mysqlname ='root';
//    const mysqlpaw ='root';
//    const dbname = 'wadragon';

    //const dbname = 'dishes';
    const vipHOST ='http://wx.bilalipay.com/';
    const sjid =1001;
    //=======【证书路径设置】=====================================
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * @var path
     */
//	const SSLCERT_PATH = 'D:\phpStudy\WWW\Wxpay\cert/apiclient_cert.pem';
//	const SSLKEY_PATH = 'D:\phpStudy\WWW\Wxpay\cert/apiclient_key.pem';
//	const SSLCERT_PATH = '/data/svn_work/alpoke_proj/tags/server/aicloud_www_v0.0.2/Wxpay/cert/apiclient_cert.pem';
//	const SSLKEY_PATH = '/data/svn_work/alpoke_proj/tags/server/aicloud_www_v0.0.2/Wxpay/cert/apiclient_key.pem';
    const SSLCERT_PATH = '/alidata/www/default/Wxpay/cert/apiclient_cert.pem';
    const SSLKEY_PATH = '/alidata/www/default/Wxpay/cert/apiclient_key.pem';

    //=======【异步通知url设置】===================================
    //异步通知url，商户根据实际开发过程设定
    const NOTIFY_URL = 'http://dc.bilalipay.com/HzgDishe/wxpay/notify.php';
    const DISHE_URL = 'http://dc.bilalipay.com/HzgDishe/dishes/';
    //=======【curl超时设置】===================================
    //本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
    const CURL_TIMEOUT = 60;
    //=======【curl代理设置】===================================
    /**
     * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @var unknown_type
     */
    const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
    const CURL_PROXY_PORT = 0;//8080;

    //=======【JSAPI路径设置】===================================
    //获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
    const JS_API_CALL_URL = 'http://dc.bilalipay.com/HzgDishe/wxpay/js_api_call.php';

    //=======【上报信息配置】===================================
    /**
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    const REPORT_LEVENL = 1;
    const Pushint = 1;

    //富友常用参数
    const version ='1.0'; //版本号
    const ins_cd = '08A9999999';  //机构号,接入机构在富友的唯一代码
    const mchnt_cd = '0002900F0370542';  //商户号, 富友分配给二级商户的商户号
    const term_id = '88888888';  //终端号

    const furl ="https://fundwx.fuiou.com/";  //富友测试url
    const url ='http://vip.bilalipay.com/fuyou/common/noticy.php';  //测试回调地址
}