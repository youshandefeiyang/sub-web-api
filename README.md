# sub-web聚合API
[本项目](https://github.com/youshandefeiyang/sub-web-config-backend)是为了方便自有服务器的朋友搭建自定义上传托管服务，并且上传后可以永久保存在自己的服务器上，小白建议搭配[youshandefeiyang/sub-web-modify](https://github.com/youshandefeiyang/sub-web-modify)使用，以下是配置教程：<br/>
## 使用方法【以nginx为例】：
1.需要安装`nginx`并正确配置，以下为`nginx server块`部分配置，可以参考一下（这块建议小白使用宝塔面板等自动化运维工具）！
```shell
server
{
    listen 80;
    listen 443 ssl http2; #前端如果开启了https，后端也必须开
    server_name xxx.xxx.xxx; #替换你的域名
    charset utf-8; #防止浏览器显示中文乱码
    index index.php index.html index.htm default.php default.htm default.html;
    root /绝对路径/profiles;
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
sub.php、api.php

```
3.然后你需要在主目录`.env`中修改默认远程配置后端：
```diff
- VUE_APP_CONFIG_UPLOAD_BACKEND = "https://subapi.v1.mk/sub.php"
+ VUE_APP_CONFIG_UPLOAD_BACKEND = "https://xxx.xxx.xxx/sub.php" #替换你的域名
+ VUE_APP_SCRIPT_CONFIG = "https://github.com/tindy2013/subconverter/blob/master/README-cn.md#%E9%85%8D%E7%BD%AE%E6%96%87%E4%BB%B6"
+ VUE_APP_SCRIPT_BACKEND = "https://xxx.xxx.xxx/api.php" #替换你的域名
```
4.特别的，如果你使用的是[CareyWang/sub-web](https://github.com/CareyWang/sub-web)原版前端，而不是我的改版前端，你还需要在`/src/views/Subconverter.vue`中做一些修改：
```diff
<el-button
       style="width: 120px"
       type="danger"
       @click="makeUrl"
-      :disabled="form.sourceSubUrl.length === 0"
+      :disabled="form.sourceSubUrl.length === 0 || btnBoolean"
       >生成订阅链接</el-button>
```
```diff
- <el-dialog
-      :visible.sync="dialogUploadConfigVisible"
-      :show-close="false"
-      :close-on-click-modal="false"
-      :close-on-press-escape="false"
-      width="700px"
-    >
-      <div slot="title">
-        Remote config upload
-        <el-popover trigger="hover" placement="right" style="margin-left: 10px">
-          <el-link type="primary" :href="sampleConfig" target="_blank" icon="el-icon-info">参考配置</el-link>
-          <i class="el-icon-question" slot="reference"></i>
-        </el-popover>
-      </div>
-      <el-form label-position="left">
-        <el-form-item prop="uploadConfig">
-          <el-input
-            v-model="uploadConfig"
-            type="textarea"
-            :autosize="{ minRows: 15, maxRows: 15}"
-            maxlength="5000"
-            show-word-limit
-          ></el-input>
-        </el-form-item>
-      </el-form>
-      <div slot="footer" class="dialog-footer">
-        <el-button @click="uploadConfig = ''; dialogUploadConfigVisible = false">取 消</el-button>
-        <el-button
-          type="primary"
-          @click="confirmUploadConfig"
-          :disabled="uploadConfig.length === 0"
-       >确 定</el-button>
-      </div>
-    </el-dialog>
+ <el-dialog
+        :visible.sync="dialogUploadConfigVisible"
+        :show-close="false"
+        :close-on-click-modal="false"
+        :close-on-press-escape="false"
+        width="80%"
+    >
+      <el-tabs v-model="activeName" type="card">
+        <el-tab-pane label="远程配置上传" name="first">
+          <el-link type="danger" :href="sampleConfig" style="margin-bottom: 15px" target="_blank" icon="el-icon-info">
+            参考配置
+          </el-link>
+          <el-form label-position="left">
+            <el-form-item prop="uploadConfig">
+              <el-input
+                  v-model="uploadConfig"
+                  type="textarea"
+                  :autosize="{ minRows: 15, maxRows: 15}"
+                  maxlength="50000"
+                  show-word-limit
+              ></el-input>
+            </el-form-item>
+          </el-form>
+          <div style="float: right">
+            <el-button type="primary" @click="uploadConfig = ''; dialogUploadConfigVisible = false">取 消</el-button>
+            <el-button
+                type="primary"
+                @click="confirmUploadConfig"
+                :disabled="uploadConfig.length === 0"
+            >确 定
+            </el-button>
+          </div>
+        </el-tab-pane>
+        <el-tab-pane label="JS排序节点" name="second">
+          <el-link type="danger" :href="scriptConfig" style="margin-bottom: 15px" target="_blank" icon="el-icon-info">
+            使用方法
+          </el-link>
+          <el-form label-position="left">
+            <el-form-item prop="uploadScript">
+              <el-input
+                  v-model="uploadScript"
+                 placeholder="使用JavaScript对节点进行自定义排序，本功能后端接口自动模版化，JS无需以挤在一行加换行符的形式输入，注意：如果你还需要自定义上传远程配置，此操作务必 +                 在其之后进行！"
+                  type="textarea"
+                  :autosize="{ minRows: 15, maxRows: 15}"
+                  maxlength="50000"
+                  show-word-limit
+              ></el-input>
+            </el-form-item>
+          </el-form>
+          <div style="float: right">
+            <el-button type="primary" @click="uploadScript = ''; dialogUploadConfigVisible = false">取 消</el-button>
+            <el-button
+                type="primary"
+                @click="confirmUploadScript"
+                :disabled="uploadScript.length === 0"
+            >确 定
+            </el-button>
+          </div>
+        </el-tab-pane>
+      </el-tabs>
+    </el-dialog>
```
```diff
- const configUploadBackend = process.env.VUE_APP_CONFIG_UPLOAD_BACKEND + '/config/upload'
+ const configUploadBackend = process.env.VUE_APP_CONFIG_UPLOAD_BACKEND
+ const configScriptBackend = process.env.VUE_APP_SCRIPT_BACKEND
+ const scriptConfigSample = process.env.VUE_APP_SCRIPT_CONFIG
```
```diff
export default {
  data() {
    return {
      backendVersion: "",
+     activeName: 'first',
      // 是否为 PC 端
      isPC: true,
+     btnBoolean: false,
      options: {
```
```diff
confirmUploadConfig() {
      if (this.uploadConfig === "") {
        this.$message.warning("远程配置不能为空");
        return false;
      }
      this.loading = true;
      let data = new FormData();
-     data.append("password", this.uploadPassword);
-     data.append("config", this.uploadConfig);
+     data.append("config", encodeURIComponent(this.uploadConfig));
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
+   renderPost() {
+      let data = new FormData();
+      data.append("target",encodeURIComponent(this.form.clientType));
+      data.append("url",encodeURIComponent(this.form.sourceSubUrl));
+      data.append("config",encodeURIComponent(this.form.remoteConfig));
+      data.append("exclude",encodeURIComponent(this.form.excludeRemarks));
+      data.append("include",encodeURIComponent(this.form.includeRemarks));
+      data.append("filename",encodeURIComponent(this.form.filename));
+      data.append("rename",encodeURIComponent(this.form.rename));
+      data.append("append_type",encodeURIComponent(this.form.appendType.toString()));
+      data.append("emoji",encodeURIComponent(this.form.emoji.toString()));
+      data.append("list",encodeURIComponent(this.form.nodeList.toString()));
+      data.append("udp",encodeURIComponent(this.form.udp.toString()));
+      data.append("tfo",encodeURIComponent(this.form.tfo.toString()));
+      data.append("expand",encodeURIComponent(this.form.expand.toString()));
+      data.append("scv",encodeURIComponent(this.form.scv.toString()));
+      data.append("fdn",encodeURIComponent(this.form.fdn.toString()));
+      data.append("sort",encodeURIComponent(this.form.sort.toString()));
+      data.append("sdoh",encodeURIComponent(this.form.tpl.surge.doh.toString()));
+      data.append("cdoh",encodeURIComponent(this.form.tpl.clash.doh.toString()));
+      data.append("newname",encodeURIComponent(this.form.new_name.toString()));
+      return data;
+    },
+    confirmUploadScript() {
+      if (this.uploadScript === "") {
+        this.$message.warning("自定义JS不能为空");
+        return false;
+      }
+      this.loading = true;
+      let data = this.renderPost();
+      data.append("sortscript",encodeURIComponent(this.uploadScript));
+      this.$axios
+          .post(configScriptBackend,data,{
+            header: {
+              "Content-Type": "application/form-data; charset=utf-8"
+            }
+          })
+          .then(res => {
+            if (res.data.code === 0 && res.data.data !== "") {
+              this.$message.success(
+                  "自定义JS上传成功，订阅链接已复制到剪贴板"
+              );
+              this.customSubUrl = res.data.data;
+              this.dialogUploadConfigVisible = false;
+              this.btnBoolean=true;
+              this.$copyText(res.data.data);
+            } else {
+              this.$message.error("自定义JS上传失败: " + res.data.msg);
+            }
+          })
+          .catch(() => {
+            this.$message.error("自定义JS上传失败");
+          })
+          .finally(() => {
+            this.loading = false;
+          })
+    },
```
5.最后你需要在后端配置文件`pref.toml`中设置
```
sort_flag = true
sort_script = "path:/绝对路径/profiles/xxx.js"
```
