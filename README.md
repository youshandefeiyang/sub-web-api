# sub-web极简远程配置后端
[本项目](https://github.com/youshandefeiyang/sub-web-config-backend)是为了方便自有服务器的朋友搭建远程配置后端托管服务，并且你的自定义配置可以永久保存在你自己的服务器上，小白建议搭配[youshandefeiyang/sub-web-modify](https://github.com/youshandefeiyang/sub-web-modify)使用，当然你也可以单独使用POST来获取后端返回的链接，以下是配置教程：<br/>
## 食用方法【以nginx为例】：
1.需要安装`nginx`并正确配置，以下为`nginx server块`部分配置，可以参考一下（这块建议小白使用宝塔面板等自动化运维工具）！
```shell
server
{
    listen 80;
    listen 443 ssl http2; #前端如果开启了https，后端也必须开
    server_name subapi.v1.mk; #替换你的域名
    index index.php index.html index.htm default.php default.htm default.html;
    root /www/wwwroot/subapi.v1.mk;
    add_header 'Access-Control-Allow-Origin' "*"; #解除跨域，很重要
    add_header 'Access-Control-Allow-Credentials' "true"; #解除跨域，很重要
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
2.以下为PHP部分：
```php
<?php
header('Content-Type: application/json');
$userText = urldecode($_POST['config']) ?? null;
if (empty($userText)) {
    $arr = array('msg' => "failed", 'data' => "empty value");
    echo json_encode($arr, 320);
    exit();
} else {
    function mk_dir()
    {
        $dir = 'subconverter/' . date('Y/m/md', time());
        if (is_dir('./' . $dir)) {
            return $dir;
        } else {
            mkdir('./' . $dir, 0777, true);
            return $dir;
        }
    }
    $path = '/' . mk_dir() . '/' . md5($userText) . '.' . 'ini';
    file_put_contents(".$path",$userText);
    $arr = array('code' => 0, 'msg' => "success", 'data' => "https://subapi.v1.mk$path");
    echo json_encode($arr, 320);
}
```
3.然后你需要在`/src/views/Subconverter.vue`中修改默认远程配置后端：
```diff
- const configUploadBackend = process.env.VUE_APP_CONFIG_UPLOAD_BACKEND + '/config/upload'
+ const configUploadBackend = 'https://subapi.v1.mk/sub.php' #替换你的域名
```
4.特别的，如果你使用的是[CareyWang/sub-web](https://github.com/CareyWang/sub-web)原版前端，而不是我的改版前端，你还需要在`/src/views/Subconverter.vue`中做一些修改：
```diff
confirmUploadConfig() {
      if (this.uploadConfig === "") {
        this.$message.warning("远程配置不能为空");
        return false;
      }
      this.loading = true;
      let data = new FormData();
-     data.append("password", this.uploadPassword);
      data.append("config", this.uploadConfig);
      this.$axios
        .post(configUploadBackend, data, {
          header: {
            "Content-Type": "application/form-data; charset=utf-8"
          }
        })
        .then(res => {
-         if (res.data.code === 0 && res.data.data.url !== "") {
+         if (res.data.code === 0 && res.data.data !== "") {
            this.$message.success(
              "远程配置上传成功，配置链接已复制到剪贴板，有效期三个月望知悉"
            );
            // 自动填充至『表单-远程配置』
-           this.form.remoteConfig = res.data.data.url;
+           this.form.remoteConfig = res.data.data;
            this.$copyText(this.form.remoteConfig);
            this.dialogUploadConfigVisible = false;
          } else {
            this.$message.error("远程配置上传失败: " + res.data.msg);
          }
        })
        .catch(() => {
          this.$message.error("远程配置上传失败");
        })
        .finally(() => {
          this.loading = false;
        });
    },
```
另外，如果你觉得原来自定义远程配置`5000`字符串的限制不够，现在你可以直接加个`0`了：
```diff
<el-form label-position="left">
        <el-form-item prop="uploadConfig">
          <el-input
            v-model="uploadConfig"
            type="textarea"
            :autosize="{ minRows: 15, maxRows: 15}"
-           maxlength="5000"
+           maxlength="50000"
            show-word-limit
          ></el-input>
        </el-form-item>
      </el-form>
```
