# Element admin

## 常用命令
1、
```bash
php artisan ide-helper:generate

php artisan ide-helper:meta

php artisan ide-helper:models "App\Models\Post"
```

为Elequent Model添加IDE注释

## FAQ
1. vscode 指定PHP版本
    新建文件 /usr/local/bin/php-8.1, 并添加可执行权限
    ```
    #!/bin/bash
    docker exec -t element-admin-php-1 php $@
    ```