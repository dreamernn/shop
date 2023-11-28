# 项目信息
> Simple e-commerce system for a coding challenge.

---

## 目录
1. [项目概述](#项目概述)
2. [框架介绍](#框架介绍)
3. [接口说明文档位置](#接口说明文档位置)
4. [数据库文件参考位置](#数据库文件参考位置)
5. [单元测试方式和位置](#单元测试方式和位置)
6. [Nginx配置文件位置](#nginx配置文件位置)
7. [静态文件位置](#静态文件位置)
8. [部署步骤](#部署步骤)

---

## 项目概述
> The goal of the challenge is to create a simple user interface of a shop.

---

## 框架介绍
### 框架
- **框架名称：** [Xly Framework]
- **版本：** v1.1.0
- **开发者：** [Damon.Meng]
- **联系邮箱：** [xiangchen0814@gmail.com](mailto:xiangchen0814@gmail.com)
- **补充说明：** 

> 
 - 本框架由Damon.Meng（Xiangchen.Meng）开发，内部引入一些基础类库来自于第三方。
 - 因时间紧促，目前仍在完善过程中，例如：console，redis集群，mysql集群，Grpc通信等功能，
 - 如果有任何建议或问题，请随时联系我。

### 框架目录结构
- Xly Framework
  - 📁 app                # 程序
    - 🛠️ Helpers        # 工具函数
    - 📁 Html           # 应用程序
      - 📁 Controllers		# 控制器层（入参，出参，参数校验）
         - 📁 Modules1		# 模块1
         - 📁 Modules2		# 模块2
      - 📁 Middleware		# 中间件
      - 📁 Models         # 模型层
      - 📁 Services       # 逻辑层
    - 📁 bootstrap        # 启动程序（加载环境变量，配置数据等）
    - 📁 config           # 配置文件
    - 📁 doc           	# 开发文档
    - 📁 environment      # 环境配置
    - 📁 library      		# 类库
		- 📁 Cache			# 缓存基础类库
		- 📁 Common			# 通用基础类库（Jwt, Logger等等）
		- 📁 Log				# 日志扩展类库
		- 📁 Xly				# 框架基础类库（Autoload，路由，Request，Response，数据库等）
    - 📁 logs      			# 存放日志
    - 🎨 public           # 资源文件
      - 📁 assets         # 资源分类（之后会将html，js，style移入到这里）
      - 📁 html           # html
      - 📁 images         # images
      - 📁 js             # js
      - 📁 styles         # css文件
    - 📁 routers      		# 路由配置
    - 🧪 tests            # 单元测试
    	- 📁 mock			   # 模拟数据配置
    - 📁 logs      			# 存放日志
    - 📁 vendor  			# composer
    - 📜 README.md        # 项目说明

---

## 3. 接口说明文档
接口说明文档位于 `/docs/interface_guide.pdf`，详细描述了系统的接口、请求方式、参数和返回结果。

---

## 4. 数据库文件
数据库文件位于 `/docs/Databases/shop.sql`，包含了系统所需的数据库结构和表定义。

---

## 5. 单元测试
### 单元测试方式
- 使用PhpUnit进行单元测试,可通过composer安装。

```
composer require --dev phpunit/phpunit

```
- 单元测试文件位于 `/tests` 目录下，详细测试了各个模块的功能和逻辑。

```
./vendor/bin/phpunit —testdox tests/indexTest.php
./vendor/bin/phpunit --testdox tests/productTest.php
./vendor/bin/phpunit --testdox tests/cartTest.php
./vendor/bin/phpunit --testdox tests/orderTest.php
```

- 返回示例

```
PHPUnit 8.5.34 by Sebastian Bergmann and contributors.

Product
 ✔ Customer product list
 ✔ Admin product list
 ✔ Admin edit product

Time: 239 ms, Memory: 4.00 MB

OK (3 tests, 15 assertions)
```

---

## 6. Nginx配置文件
Nginx配置文件位于 `/docs/Nginx_conf/local.shop_api.com.conf`，包含了Nginx服务器的配置信息。

---

## 7. 部署步骤
### 步骤
1. 克隆项目代码：`git clone [git@github.com:dreamernn/shop.git]`
2. 配置nginx服务器（需要reload），修改本地hosts文件
3. 导入数据库文件
4. 修改框架logs目录权限755
5. composer update
6. 按自己需要，修改框架config文件夹内的配置文件和environment下的环境变量文件
7. 浏览器访问地址（可根据nginx配置自行定义）：http://local.shop_api.com/html

---

## 8. 其他说明（可选）
[其他说明，如特殊依赖、重要注意事项等]





