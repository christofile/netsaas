<?php
$txt = "hello";

// ���µ������ַ�ĳ����
$mail = '123578830@qq.com';  

// �����ʼ�
mail($mail, "My subject", $txt);

echo 'message was sent!';
?>