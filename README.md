# kapi
 kapi后端
* 后端运行需要king框架支持,可以clone https://github.com/xilin693中的system项目
    * kapi项目与system项目放在平级运行
    * 更多king框架的资料请查看king框架文档
    * 文档地址http://example.fjlssy.cn/king
* 使用需修改.env文件,按实际的数据库及redis修改
    * env文件可以支持多环境,需要在nginx中配置ENV_FILE环境变量支持
    * env中的数据库有database和rest两个,rest为生成接口使用,需要给予更高的权限,以便访问不同的数据库及表来生成接口
    如果不需要生成则不用配置该项
* kapi插件在tools目录下,需要解压后,在chrome开发者工具中加载该插件
* kapi数据库在tools目录下,使用需要导入到数据库中运行

