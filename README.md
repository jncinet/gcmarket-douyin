## 发送视频到绑定抖账号《定制》

### 安装
```shell
$ composer require jncinet/gcmarket-douyin

$ php artisan migrate
```

### 使用
#### 后台添加菜单：
+ 配置：douyin/config
+ 授权账号：douyin/users
+ 发送记录：douyin/items

#### 绑定账号：
> 注意：域名必须是抖音开放平台填写的域名！！！
+ 访问URL授权：http://你的域名/douyin/code
+ 二维码授权：http://你的域名/douyin/code?url=true
> 授权完成后，可以在后台看到授权账号的open_id和access_token