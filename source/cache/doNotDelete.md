## 确保cache目录存在

请不要删除此文件 次文件为了确保`cache`目录存在


注意 需要将`cache`目录的权限设置为可写(如果服务器的`user`没有`cache`的写权限的话)，否则`minify`无法写入`cache`文件
请使用`chmod`修改`cache`的`ugoa`

```
$ sudo chmod o+w cache
```