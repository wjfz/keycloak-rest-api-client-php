# Keycloak REST API Client - 维护任务

.PHONY: help docs docs-check docs-preview docs-init install test

# 默认目标
help:
	@echo "可用的任务:"
	@echo "  docs-init     - 初始化 API 文档标志"
	@echo "  docs          - 更新 README.md 中的 API 文档"
	@echo "  docs-check    - 检查 API 文档是否为最新版本"
	@echo "  docs-preview  - 预览生成的 API 文档（不更新文件）"
	@echo "  install       - 安装依赖"
	@echo "  test          - 运行测试"
	@echo "  analyze       - 运行代码分析"

# 更新 API 文档
docs:
	@echo "正在更新 README.md 中的 API 文档..."
	@composer docs:update-readme

# 检查文档是否最新
docs-check:
	@echo "检查 API 文档是否为最新版本..."
	@composer docs:update-readme
	@if git diff --quiet README.md; then \
		echo "✅ API 文档是最新的"; \
	else \
		echo "❌ API 文档需要更新，请运行 'make docs'"; \
		git diff README.md; \
		exit 1; \
	fi

# 预览生成的文档
docs-preview:
	@echo "生成的 API 文档预览:"
	@echo "================================"
	@composer docs:api

# 安装依赖
install:
	@composer install

# 运行测试
test:
	@composer test

# 初始化 API 文档标志
docs-init:
	@echo "正在初始化 API 文档标志..."
	@composer docs:init

# 代码分析
analyze:
	@composer analyze
