<?php
    session_start();
    if(isset($_GET["action"])){
        if($_GET["action"] == "logout"){
            session_destroy();
            header("Location:index.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>情侣空间</title>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/navigator.css">
    <script language="JavaScript">
        var xmlHttp;
        function authenticate(){
            document.getElementById("authentication").style.display = "block";
            document.getElementById("authenticationFilter").style.display = "block";
        }
        function closeAuthentication(){
            document.getElementById("authentication").style.display = "none";
            document.getElementById("authenticationFilter").style.display = "none";
        }
        function showMessageBox(){
            if(document.getElementById("unreadMessageCount").innerHTML != "（0）"){
                document.getElementById("messageFrame").style.display = "block";
            }
        }
        function closeMessageBox(){
            document.getElementById("messageFrame").style.display = "none";
        }
        function GetXmlHttpObject()
        {
            var xmlHttp=null;
            try
            {
                // Firefox, Opera 8.0+, Safari
                xmlHttp=new XMLHttpRequest();
            }
            catch (e)
            {
                // Internet Explorer
                try
                {
                    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
                }
                catch (e)
                {
                    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
            }
            return xmlHttp;
        }
        function autoUpdateUnreadMessage(){
            xmlHttp=GetXmlHttpObject();
            xmlHttp.onreadystatechange=resetUnreadMessage;
            xmlHttp.open("GET","./backend/getUnreadMessage.php",true);
            xmlHttp.send(null);
        }
        function resetUnreadMessage(){
            if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
            {
                document.getElementById("unreadMessageCount").innerHTML= "（"+xmlHttp.responseText+"）";
            }
        }
        function autoUpdate(){
            autoUpdateUnreadMessage();
            setTimeout("autoUpdateUnreadMessage()", 120000);
        }
        function buttonUp(event){
            if(event.keyCode == 27){
                var authentication = document.getElementById("authentication");
                if(authentication.style.display == "block"){
                    closeAuthentication();
                }
            }
        }
    </script>
</head>
<body onkeyup="buttonUp(event)" onload="autoUpdate()">
    <!--导航栏-->
    <div class="navigator">
        <a target="mainFrame" href="./page/mainPage.php">
        <span class="navigatorItem nav">
            主页
        </span>
        </a>
        <a target="mainFrame" href="./page/dinaryPage.php">
        <span class="navigatorItem nav">
            日记
        </span>
        </a>
        <a target="mainFrame" href="./page/memorialDayPage.php">
        <span class="navigatorItem nav">
            纪念日
        </span>
        </a>
        <a target="mainFrame" href="./page/explorePage.php">
        <span class="navigatorItem nav">
            探索
        </span>
        </a>
        <?php
            if(!isset($_SESSION["userName"])){
                $infoTip = "\n<a href=\"./authentication/signup.php\" target=\"authentication\" onclick=\"authenticate()\">\n
                <span class=\"navigatorItem infoTip\">\n
                注册\n
                </span>\n
                </a>\n
                <a href=\"./authentication/signin.php\" target=\"authentication\" onclick=\"authenticate()\">\n
                <span class=\"navigatorItem infoTip\">\n
                登录\n
                </span>\n
                </a>\n
                <span class=\"navigatorItem infoTip\">\n
                您还没有登录\n
                </span>\n";
            }
            else {
                require("./backend/DBConnector.php");
                $userInfo = $connector->searchUser($_SESSION["userName"]);
                $messageNumber = mysqli_num_rows($connector->getUserMessage($userInfo["userName"], 0));

                $infoTip = "\n<a href=\"".htmlspecialchars($_SERVER["PHP_SELF"])."?action=logout\">\n
                              <span class=\"navigatorItem infoTip\">\n
                              退出登录\n
                              </span>\n
                              </a>\n";

                if(!isset($userInfo["pairState"])){
                    $infoTip = $infoTip."<a href=\"./authentication/choosePair.php\" target=\"authentication\" onclick=\"authenticate()\">\n
                                         <span class=\"navigatorItem infoTip\">\n
                                         选择情侣\n
                                         </span>\n
                                         </a>\n";
                }
                else if($userInfo["pairState"] == 0){
                    $infoTip = $infoTip."<span class=\"avigatorItem infoTip\">\n
                                         等待 ".$userInfo["pair"]." 回应\n
                                         </span>\n";
                }
                else{
                    $infoTip = $infoTip."<span class=\"avigatorItem infoTip\">\n
                                         您的情侣为 ".$userInfo["pair"]." \n
                                         </span>\n";
                }

                $infoTip = $infoTip."<span class=\"navigatorItem infoTip\">
                                        <a href=\"./page/messagePage.php\" target=\"mainFrame\">消息</a>
                                        <a href=\"javascript:void(0)\" id=\"unreadMessageCount\"
                                            onmouseover=\"showMessageBox()\"></a>
                                        <!--消息框-->
                                        <iframe name=\"messageFrame\" id=\"messageFrame\"  onmouseout=\"closeMessageBox()\"
                                            src=\"box/messageBox.php\" frameborder=\"1\"></iframe>
                                        </span>
                                     <a target=\"mainFrame\" href=\"./src/information\">\n
                                        <span class=\"navigatorItem infoTip\">".$userInfo["userName"]."</span>
                                     </a>\n";
            }
            echo $infoTip;
    ?>
    </div>

    <!--认证框-->
    <iframe name="authentication" id="authentication" src="" frameborder="0"></iframe>
    <div id="authenticationFilter"></div>

    <!--主框架-->
    <iframe name="mainFrame" id="mainFrame" src="./page/mainPage.php" frameborder="0"></iframe>
</body>
</html>