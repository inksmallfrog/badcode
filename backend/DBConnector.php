<?php

/**
 * Created by IntelliJ IDEA.
 * User: inksmallfrog
 * Date: 11/18/15
 * Time: 7:03 AM
 */
class DBConnector
{
    private static $_instance;
    var $conn;

    private function __construct(){
        $this->createConnection();
    }

    private function createConnection(){
        $this->conn = mysql_connect("127.0.0.1:3306", "root", "123456");
        if(!conn){
            echo "连接数据库失败".mysql_error();
        }
    }

    public static function getInstance(){
        if(!isset(self::$_instance)){
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }

    public function searchUser($userName){
        mysql_select_db("LoverSpaceInformation");
        $result = mysql_query("select * from user WHERE userName=\"".$userName."\";");
        return mysql_fetch_array($result);
    }

    public function getUserNewMessage($userName){
        mysql_select_db("LoverSpaceInformation");
        mysql_query("create table if not EXISTS message(sender varchar(40), receiver varchar(40),
                     message varchar(1000), message_type tinyint(1), state tinyint(1));");
        $result = mysql_query("select message from message WHERE receiver=\"".$userName."\" and state=0;");
        return $result;
    }

    public function authenticateUser($userName, $password){
        mysql_query("create database if not exists LoverSpaceInformation");
        mysql_select_db("LoverSpaceInformation");
        mysql_query("create table if not EXISTS user(userName varchar(40), password varchar(40),
                     gender tinyint(1), pair VARCHAR(40), pairState tinyint(1));");
        $result = mysql_query("select password from user WHERE  userName=\"".$userName."\";");
        $user = mysql_fetch_array($result);
        if(!$user){
            return "用户名不存在";
        }
        else if($user["password"] != $password){
            return "密码错误";
        }
        else{
            return "欢迎回家来～";
        }
    }

    public function signupNewUser($userName, $password, $gender){
        mysql_query("create database if not exists LoverSpaceInformation");
        mysql_select_db("LoverSpaceInformation");
        mysql_query("create table if not EXISTS user(userName varchar(40), password varchar(40),
                     gender tinyint(1), pair VARCHAR(40), pairState tinyint(1));");
        $result = mysql_query("select * from user WHERE userName=\"".$userName."\";");
        if(mysql_fetch_array($result)){
            return "用户已存在";
        }
        $result = mysql_query("insert into user(userName, password, gender)
                               VALUES(\"".$userName."\", \"".$password."\", \"".$gender."\");");
        return "注册成功";
    }

    public function sendPairMessage($sender, $receiver, $message){
        if($sender == $receiver){
            return "祝孤生orz";
        }
        mysql_select_db("LoverSpaceInformation");
        $result = mysql_query("select password from user WHERE  userName=\"".$receiver."\";");
        $user = mysql_fetch_array($result);
        if(!$user){
            return "用户名不存在";
        }
        mysql_query("create table if not EXISTS message(sender varchar(40), receiver varchar(40),
                     message varchar(1000), message_type tinyint(1), state tinyint(1));");
        mysql_query("insert into message(sender, receiver, message, message_type, state)
                     VALUES(\"".$sender."\", \"".$receiver."\", \"".$message."\", 0, 0)");
        mysql_query("update user set pair=\"".$receiver."\", pairState=0 where userName=\"".$sender."\";");
        return "申请成功";
    }
}

$connector = DBConnector::getInstance();
?>