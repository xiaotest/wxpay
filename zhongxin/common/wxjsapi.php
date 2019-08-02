

<script type="application/javascript">
function onBridgeReady(){
WeixinJSBridge.invoke(
'getBrandWCPayRequest', {
"appId":"wx360cee6438ac79e2",     //公众号名称，由商户传入
"timeStamp":"1564730910",         //时间戳，自1970年以来的秒数
"nonceStr":"a50429410547407f961b526cf29bf07d", //随机串
"package":"prepay_id=wx021528300199334b94ad52ed1964537100",
"signType":"RSA",         //微信签名方式：
"paySign":"MCuY6AbKz1NjPCyphFACZJyaTM1Oa663dtyRHmLbHhAo95kTu8eTG9zYrj8eUnl4UwqMUlzNtXgFDQyGdnP3HrEO3okMMu/whHZXduyQOQXTjysQ1SGF/D5KYoDsIC8nDAARiu3KN/2g3Q5f3bQ/eKvJ+g8Y9MFH/MDXIUaM/ToZHGxZcsMt7tRti1S6UVbhuKTx97TjIOP1mbtuIwDq/3x11AFxXPgQL6M9FGnVefzEG0ecHIky84zsR4g8Exz3rOHogC0hocu/qD/4KGoo1Gh9PlTRwQXgZBXDnghbkFJHsTBcUYvUolyAwCWi4RMmnDO1sLIjJok9DDRI0bBc+g==" //微信签名
},
function(res){
if(res.err_msg == "get_brand_wcpay_request:ok" ){
// 使用以上方式判断前端返回,微信团队郑重提示：
//res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
}
});
}
if (typeof WeixinJSBridge == "undefined"){
if( document.addEventListener ){
document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
}else if (document.attachEvent){
document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
}
}else{
onBridgeReady();
}
</script>