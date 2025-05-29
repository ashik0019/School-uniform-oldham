<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title></title>
</head>

<body>
    @if($status=="success")
    <p>Transaction successful. Returning to App...</p>
    <script defer>
        document.addEventListener('DOMContentLoaded', function() {
            if (window && window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
                window.ReactNativeWebView.postMessage("success?q")
            } else {
                //window.location.href=window.location.origin+"/user/request";
                //window.location.href="http://localhost:3005/dashboard/list";
                window.location.href = "{{url('/')}}";
            }
        }, false)
    </script>
    @elseif($status=="fail")
    <p>Transaction Failed. Returning to App...</p>
    <script defer>
        // document.addEventListener('DOMContentLoaded', function () {
        //     if (window && window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
        //         window.ReactNativeWebView.postMessage("fail")
        //     } else {
        //         //window.location.href=window.location.origin+"/user/request";
        //         //window.location.href="http://localhost:3005/dashboard/list";
        //         window.location.href="{{url('/')}}";
        //     }
        // }, false)
        if (window && window.flutter_inappwebview && window.flutter_inappwebview.callHandler) {
            window.flutter_inappwebview.callHandler('flutterMessage', "fail");
        } else {
            window.location.href = "{{url('/')}}";
        }
    </script>
    @elseif($status=="cancel")
    <p>Transaction Canceled. Returning to App...</p>
    <script defer>
        // document.addEventListener('DOMContentLoaded', function() {
        //     if (window && window.ReactNativeWebView && window.ReactNativeWebView.postMessage) {
        //         window.ReactNativeWebView.postMessage("cancel")
        //     } else {
        //         //window.location.href=window.location.origin+"/user/request";
        //         //window.location.href="http://localhost:3005/dashboard/list";
        //         window.location.href = "{{url('/')}}";
        //     }
        // }, false)
        if (window && window.flutter_inappwebview && window.flutter_inappwebview.callHandler) {
            window.flutter_inappwebview.callHandler('flutterMessage', "cancel");
        } else {
            window.location.href = "{{url('/')}}";
        }
    </script>
    @endif
</body>

</html>