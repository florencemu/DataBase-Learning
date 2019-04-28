<?php
$sql1 = "update User set ScoreCount = ScoreCount +10 where ID= '123456'";
$sql2 = "update ScoreDetail  set FScore = 300 where ID= '123456'";
$sql3 = "insert into  ScoreDetail ID,Score) values ('123456',60)";
$conn = mysql_connect('localhost','root','');
mysql_select_db('DB_Lib2Test');
mysql_query('start transaction');
//mysql_query('SET autocommit=0');
mysql_query($sql1);
mysql_query($sql2);
if(mysql_errno ()/*返回上一个 MySQL 操作中的错误信息的数字编码*/){
    mysql_query('rollback');
    echo 'err';
}else{
    mysql_query('commit');
    echo 'ok';
}
// mysql_query('SET autocommit=1');
// mysql_query($sql3);

