<?php

declare(strict_types=1);

/**
 * API 文档标志初始化器
 * 帮助在 README.md 中添加必要的标志
 */
class ApiDocsInitializer
{
    private string $readmePath;

    public function __construct()
    {
        $this->readmePath = __DIR__.'/../README.md';
    }

    /**
     * 初始化 API 文档标志
     */
    public function init(): void
    {
        if (! file_exists($this->readmePath)) {
            echo "错误：README.md 文件不存在\n";
            exit(1);
        }

        $content = file_get_contents($this->readmePath);

        // 检查是否已经有标志
        if (strpos($content, '<!-- API_DOCS_START -->') !== false) {
            echo "API 文档标志已存在，无需初始化\n";

            return;
        }

        // 查找 "## Available Resources" 部分
        if (preg_match('/^## Available Resources$/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $this->addMarkersAroundExistingSection($content, $matches[0][1]);
        } else {
            $this->addMarkersAtEnd($content);
        }
    }

    /**
     * 在现有的 Available Resources 部分周围添加标志
     */
    private function addMarkersAroundExistingSection(string $content, int $titlePosition): void
    {
        echo "发现现有的 'Available Resources' 部分，正在添加标志...\n";

        // 找到下一个二级标题的位置
        $afterTitle = $titlePosition + strlen('## Available Resources');
        $remainingContent = substr($content, $afterTitle);

        // 查找下一个 ## 标题
        if (preg_match('/^## /m', $remainingContent, $matches, PREG_OFFSET_CAPTURE)) {
            $nextSectionPos = $afterTitle + $matches[0][1];
        } else {
            $nextSectionPos = strlen($content);
        }

        // 构建新内容
        $before = substr($content, 0, $afterTitle);
        $apiSection = substr($content, $afterTitle, $nextSectionPos - $afterTitle);
        $after = substr($content, $nextSectionPos);

        $newContent = $before."\n\n<!-- API_DOCS_START -->".
                     rtrim($apiSection)."\n<!-- API_DOCS_END -->\n\n".$after;

        file_put_contents($this->readmePath, $newContent);
        echo "✅ 已在现有 API 文档周围添加标志\n";
    }

    /**
     * 在文件末尾添加空的 API 文档部分
     */
    private function addMarkersAtEnd(string $content): void
    {
        echo "未找到 'Available Resources' 部分，正在创建新部分...\n";

        $apiSection = "\n\n## Available Resources\n\n".
                     "<!-- API_DOCS_START -->\n".
                     "<!-- 此区域的内容会被自动生成和更新 -->\n".
                     "<!-- API_DOCS_END -->\n";

        $newContent = rtrim($content).$apiSection;

        file_put_contents($this->readmePath, $newContent);
        echo "✅ 已创建新的 API 文档部分并添加标志\n";
    }

    /**
     * 显示使用说明
     */
    public function showUsage(): void
    {
        echo "API 文档标志初始化器\n";
        echo "====================\n\n";
        echo "此脚本会在 README.md 中添加必要的标志来支持自动 API 文档更新。\n\n";
        echo "标志格式：\n";
        echo "<!-- API_DOCS_START -->\n";
        echo "... API 文档内容 ...\n";
        echo "<!-- API_DOCS_END -->\n\n";
        echo "运行此脚本后，您就可以使用 'composer docs:update-readme' 来自动更新 API 文档了。\n";
    }
}

// 运行脚本
if (php_sapi_name() === 'cli') {
    $initializer = new ApiDocsInitializer;

    if (isset($argv[1]) && $argv[1] === '--help') {
        $initializer->showUsage();
    } else {
        echo "正在初始化 API 文档标志...\n";
        $initializer->init();
        echo "\n使用 'composer docs:update-readme' 来更新 API 文档。\n";
    }
}
