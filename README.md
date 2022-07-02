# sub-web聚合API
本项目作为[youshandefeiyang/sub-web-modify](https://github.com/youshandefeiyang/sub-web-modify)的扩展API，支持远程配置托管、JS排序/筛选节点，后端自动模版化，以下是配置教程：<br/>
## 使用方法【以nginx为例】：
1.需要安装`nginx`并正确配置，以下为`nginx server块`部分配置，可以参考一下（这块建议小白使用宝塔面板等自动化运维工具）！
```shell
server
{
    listen 80;
    listen 443 ssl http2; #前端如果开启了https，后端也必须开
    server_name xxx.xxx.xxx; #替换你sub-web-api的域名
    charset utf-8; #防止浏览器显示中文乱码
    index index.php index.html index.htm default.php default.htm default.html;
    root /你的subconverter后端绝对路径/subconverter/profiles;
    add_header 'Access-Control-Allow-Origin' "*"; #解除跨域，很重要
    add_header 'Access-Control-Allow-Credentials' "true"; #允许跨域使用cookies
    if ($server_port !~ 443){
        rewrite ^(/.*)$ https://$host$1 permanent;
    }
    ssl_certificate    /xxx/fullchain.pem; #证书位置
    ssl_certificate_key    /xxx/privkey.pem; #证书位置
    ssl_protocols TLSv1.1 TLSv1.2 TLSv1.3;
    ssl_ciphers EECDH+CHACHA20:EECDH+CHACHA20-draft:EECDH+AES128:RSA+AES128:EECDH+AES256:RSA+AES256:EECDH+3DES:RSA+3DES:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    add_header Strict-Transport-Security "max-age=31536000";
    error_page 497  https://$host$request_uri;
    gzip on; #开启gzip压缩
    gzip_min_length 1k; #设置对数据启用压缩的最少字节数
    gzip_buffers 4 16k;
    gzip_http_version 1.0;
    gzip_comp_level 6; #设置数据的压缩等级,等级为1-9，压缩比从小到大
    gzip_types text/plain text/css text/javascript application/json application/javascript application/x-javascript application/xml; #设置需要压缩的数据格式
    gzip_vary on;
    location ~* \.(css|js|png|jpg|jpeg|gif|gz|svg|mp4|ogg|ogv|webm|htc|xml|woff)$ {
        access_log off;
        add_header Cache-Control "public,max-age=30*24*3600";
    }
}
```
2.接口部分说明：
```
sub.php、api.php、rediredct.php、config目录均需放在/绝对路径/subconverter/profiles下，并对subconverter目录全局设置读写权限（建议直接777）
否则无法写入文件，config目录中的数据库文件手动导入或自行使用可视化工具（比如phpmyadmin或navicat等工具）导入!
```
```diff
你需要在 sub.php 中修改：
- $arr = array('code' => 0, 'msg' => "success", 'data' => "https://subapi.v1.mk$path");
+ $arr = array('code' => 0, 'msg' => "success", 'data' => "https://你的sub-web-api域名$path");
在 api.php 中修改：
- $arr = array('code' => 0, 'msg' => "success", 'data' => "https://subapi.v1.mk/redirect.php?token=$md5encode");
+ $arr = array('code' => 0, 'msg' => "success", 'data' => "https://你的sub-web-api域名/redirect.php?token=$md5encode");
在 redirect.php 中修改：
- header("Location: https://api.d1.mk/getprofile?name=profiles/subconverter/$token.ini&token=subconverter");
+ header("Location: https://你的subconverter后端域名/getprofile?name=profiles/subconverter/$token.ini&token=subconverter");
在 config/database.php 中配置你的数据库连接信息
```
3.然后你需要在前端主目录`.env`中修改远程配置后端：
```diff
- VUE_APP_CONFIG_UPLOAD_BACKEND = "https://subapi.v1.mk"
+ VUE_APP_CONFIG_UPLOAD_BACKEND = "https://xxx.xxx.xxx" #替换你sub-web-api的域名
```
4.最后你需要在后端配置文件`pref.toml`中设置
```
enable_filter = true
filter_script = "path:profiles/filter/filter.js"
sort_flag = true
sort_script = "path:profiles/script/sort.js"
```
