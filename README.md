个人脚本(类)合集
===================================

Ban/ban.class.php
-----------------------------------
关键词过滤(2014-03-12) 
<pre>
public  __construct 
private sortByLen   自定义排序
public  has_ban     比较是否存在过滤关键词
public  filter      过滤
public  info        获取类信息
</pre>
过滤词库文件放入文件夹下，以单个回车算一个关键词

tCatch/tcatch.class.php
-----------------------------------
源码抓取(2014-03-14) 
<pre>
public   __construct 
private  fetch        抓取
private  getCharset   获取编码
private  uni_decode   编码转换
</pre>
