# 自动化脚本

这个目录包含了用于自动生成和维护文档的脚本。

## API 文档生成脚本

### `init-api-docs.php`

初始化 API 文档标志的脚本，为 README.md 添加必要的标志来支持自动更新。

```bash
# 直接运行
php scripts/init-api-docs.php

# 或通过 composer 运行
composer docs:init

# 或通过 make 运行
make docs-init
```

### `generate-api-docs.php`

生成 API 文档表格的脚本，扫描所有 Resource 类并提取 API 信息，输出到控制台。

```bash
# 直接运行
php scripts/generate-api-docs.php

# 或通过 composer 运行
composer docs:api
```

### `update-readme-api.php`

更新 README.md 文件中的 API 表格部分。会自动替换标志之间的内容。

```bash
# 直接运行
php scripts/update-readme-api.php

# 或通过 composer 运行
composer docs:update-readme
```

## 工作原理

脚本会自动：

1. **扫描 Resource 类**: 遍历 `src/Resource/` 目录下的所有 PHP 文件
2. **分析方法**: 使用正则表达式解析每个公共方法
3. **提取 API 信息**: 从方法体中提取：
   - HTTP 端点路径 (从 `new Query()` 或 `new Command()` 中)
   - HTTP 方法 (GET, POST, PUT, DELETE 等)
   - 返回类型 (从方法签名或 `executeQuery` 参数中)
4. **生成 Markdown**: 格式化为标准的 Markdown 表格

## 自动化建议

### 在 CI/CD 中使用

可以在 GitHub Actions 或其他 CI/CD 系统中集成：

```yaml
- name: Update API Documentation
  run: composer docs:update-readme

- name: Check for changes
  run: git diff --exit-code README.md || (echo "API documentation is outdated" && exit 1)
```

### Git 钩子

在 git pre-commit 钩子中自动更新：

```bash
#!/bin/bash
# .git/hooks/pre-commit
composer docs:update-readme
git add README.md
```

### Make 任务

在 Makefile 中添加任务：

```makefile
docs:
	composer docs:update-readme

docs-check:
	composer docs:api > /tmp/current-api.md
	php scripts/update-readme-api.php
	git diff --exit-code README.md
```

## 标志机制

为了确保更新的精确性和安全性，脚本使用 HTML 注释作为标志来标识要更新的区域：

```markdown
## Available Resources

<!-- API_DOCS_START -->
... 这里的内容会被自动更新 ...
<!-- API_DOCS_END -->

## 其他章节
```

### 如何添加标志

如果您的 README.md 还没有这些标志，需要手动添加：

1. 在要开始更新的地方添加 `<!-- API_DOCS_START -->`
2. 在要结束更新的地方添加 `<!-- API_DOCS_END -->`
3. 运行更新脚本

## 注意事项

1. **方法检测**: 脚本只检测包含 `new Query()` 或 `new Command()` 的公共方法
2. **返回类型**: 优先从方法签名中提取，其次从 `executeQuery` 参数中提取
3. **链接生成**: 自动为 Collection、Representation 和 Type 类创建相对链接
4. **排序**: 资源按预定义顺序排列，确保文档的一致性
5. **标志要求**: 必须在 README.md 中有 `<!-- API_DOCS_START -->` 和 `<!-- API_DOCS_END -->` 标志

## 扩展

如果需要支持新的 Resource 类型或修改表格格式，可以编辑脚本中的以下部分：

- `$resourceOrder`: 资源显示顺序
- `$docLinks`: Keycloak 官方文档链接
- `formatReturnType()`: 返回类型格式化逻辑
