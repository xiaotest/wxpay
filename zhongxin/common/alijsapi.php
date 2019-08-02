<script type="text/javascript" src="jquery-1.9.0.min.js"></script>
<p id="result">测试 </p>

<script type="application/javascript">
    // 调试时可以通过在页面定义一个元素，打印信息，使用alert方法不够优雅
    function log(obj) {
        $("#result").append(obj).append(" ").append("<br />");
    }

    $(document).ready(function(){
        // 页面载入完成后即唤起收银台
        // 此处${tradeNO}为模板语言语法，实际调用样例类似为tradePpay("2016072621001004200000000752")
        tradePay("2019073022001418850500013689");
        // 点击payButton按钮后唤起收银台
        $("#payButton").click(function() {
            tradePay("2019073022001418850500013689");
        });

        // 通过jsapi关闭当前窗口，仅供参考，更多jsapi请访问
        // /aod/54/104510
        $("#closeButton").click(function() {
            AlipayJSBridge.call('closeWebview');
        });
    });

    // 由于js的载入是异步的，所以可以通过该方法，当AlipayJSBridgeReady事件发生后，再执行callback方法
    function ready(callback) {
        if (window.AlipayJSBridge) {
            callback && callback();
        } else {
            document.addEventListener('AlipayJSBridgeReady', callback, false);
        }
    }

    function tradePay(tradeNO) {
        ready(function(){
            // 通过传入交易号唤起快捷调用方式(注意tradeNO大小写严格)
            AlipayJSBridge.call("tradePay", {
                tradeNO: tradeNO
            }, function (data) {
                log(JSON.stringify(data));
                if ("9000" == data.resultCode) {
                    log("支付成功");
                }
            });
        });
    }
</script>