<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

/**
 * README API 表格更新器
 * 自动扫描 Resource 类并更新 README.md 中的 API 表格
 */
class ReadmeApiUpdater
{
    private string $srcPath;

    private string $readmePath;

    public function __construct()
    {
        $this->srcPath = __DIR__.'/../src/Resource';
        $this->readmePath = __DIR__.'/../README.md';
    }

    /**
     * 更新 README.md
     */
    public function update(): void
    {
        $resources = $this->scanResources();
        $newApiSection = $this->generateMarkdown($resources);
        $this->updateReadmeFile($newApiSection);
    }

    /**
     * 扫描所有资源类
     */
    private function scanResources(): array
    {
        $resources = [];
        $files = glob($this->srcPath.'/*.php');

        foreach ($files as $file) {
            $className = basename($file, '.php');

            // 跳过基类
            if ($className === 'Resource') {
                continue;
            }

            $apis = $this->analyzeResourceFile($file, $className);
            if (! empty($apis)) {
                $resources[$className] = $apis;
            }
        }

        return $resources;
    }

    /**
     * 分析资源文件
     */
    private function analyzeResourceFile(string $filePath, string $className): array
    {
        $content = file_get_contents($filePath);
        $apis = [];

        // 使用更精确的正则表达式提取方法
        preg_match_all('/public\s+function\s+(\w+)\s*\([^)]*\)(?::\s*[^{]+)?\s*\{((?:[^{}]++|\{(?:[^{}]++|\{[^{}]*+\})*+\})*+)\}/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $methodName = $match[1];
            $methodBody = $match[2];

            // 跳过一些非API方法
            if (in_array($methodName, ['__construct', 'clearCache'])) {
                continue;
            }

            $apiInfo = $this->extractApiFromMethod($methodBody, $methodName, $className);
            if ($apiInfo) {
                $apis[] = $apiInfo;
            }
        }

        return $apis;
    }

    /**
     * 从方法体中提取 API 信息
     */
    private function extractApiFromMethod(string $methodBody, string $methodName, string $className): ?array
    {
        // 提取 HTTP 路径
        if (preg_match('/new\s+(?:Query|Command)\s*\(\s*[\'"]([^\'"]+)[\'"]/', $methodBody, $pathMatch)) {
            $path = $pathMatch[1];
        } else {
            return null;
        }

        // 提取 HTTP 方法
        $httpMethod = 'GET'; // 默认

        if (strpos($methodBody, 'new Command') !== false) {
            if (preg_match('/Method::(GET|POST|PUT|DELETE|PATCH)/', $methodBody, $methodMatch)) {
                $httpMethod = $methodMatch[1];
            } else {
                $httpMethod = 'POST'; // Command 默认是 POST
            }
        }

        // 提取返回类型
        $returnType = $this->extractReturnType($methodBody, $className, $methodName);

        return [
            'endpoint' => $httpMethod.' '.$path,
            'response' => $returnType,
            'method' => $methodName,
            'class' => $className,
        ];
    }

    /**
     * 提取返回类型
     */
    private function extractReturnType(string $methodBody, string $className, string $methodName): string
    {
        // 先检查源码文件中的方法签名
        $sourceFile = $this->srcPath."/{$className}.php";
        $sourceContent = file_get_contents($sourceFile);

        // 匹配具体方法的返回类型声明
        if (preg_match("/public\s+function\s+{$methodName}\s*\([^)]*\):\s*([^{]+)/", $sourceContent, $returnMatch)) {
            $returnType = trim($returnMatch[1]);

            // 处理返回类型
            if ($returnType === 'void') {
                return 'ResponseInterface';
            }

            // 处理完整类名
            $shortName = $this->getShortClassName($returnType);

            return $this->formatReturnType($shortName);
        }

        // 查找 executeQuery 的返回类型
        if (preg_match('/executeQuery\([^,]+,\s*([^:,\s]+)::class/', $methodBody, $match)) {
            $typeName = $match[1];
            $shortName = $this->getShortClassName($typeName);

            return $this->formatReturnType($shortName);
        }

        // 如果是 Command，通常返回 ResponseInterface
        if (strpos($methodBody, 'executeCommand') !== false) {
            return 'ResponseInterface';
        }

        return 'mixed';
    }

    /**
     * 获取类的短名称
     */
    private function getShortClassName(string $className): string
    {
        if (strpos($className, '\\') !== false) {
            $parts = explode('\\', $className);

            return end($parts);
        }

        return $className;
    }

    /**
     * 格式化返回类型为 Markdown 链接
     */
    private function formatReturnType(string $shortName): string
    {
        // 为不同类型添加链接
        if (strpos($shortName, 'Collection') !== false) {
            return "[{$shortName}](src/Collection/{$shortName}.php)";
        }

        // 常见的 Representation 类型
        $representations = ['ServerInfo', 'Client', 'Group', 'Organization', 'Realm', 'Role', 'User'];
        foreach ($representations as $repr) {
            if (strpos($shortName, $repr) !== false) {
                return "[{$shortName}](src/Representation/{$shortName}.php)";
            }
        }

        // Type 类型
        if (in_array($shortName, ['StringMap', 'Map', 'AnyMap', 'ArrayMap', 'BooleanMap', 'IntegerMap'])) {
            return "[{$shortName}](src/Type/{$shortName}.php)";
        }

        // 其他已知类型
        if (in_array($shortName, ['ResponseInterface', 'array', 'string', 'int', 'bool', 'mixed'])) {
            return $shortName;
        }

        return $shortName;
    }

    /**
     * 生成 Markdown 表格
     */
    private function generateMarkdown(array $resources): string
    {
        $resourceOrder = [
            'AttackDetection' => 'Attack Detection',
            'Clients' => 'Clients',
            'Groups' => 'Groups',
            'Organizations' => 'Organizations',
            'Realms' => 'Realms Admin',
            'Users' => 'Users',
            'Roles' => 'Roles',
            'ServerInfo' => 'Root',
        ];

        $docLinks = [
            'AttackDetection' => '#_attack_detection',
            'Clients' => '#_clients',
            'Groups' => '#_clients',
            'Organizations' => '#_organizations',
            'Realms' => '#_realms_admin',
            'Users' => '#_users',
            'Roles' => '#_roles',
            'ServerInfo' => '#_root',
        ];

        $baseUrl = 'https://www.keycloak.org/docs-api/26.0.0/rest-api/index.html';

        $markdown = '';

        foreach ($resourceOrder as $resourceName => $title) {
            if (! isset($resources[$resourceName])) {
                continue;
            }

            $docLink = $baseUrl.($docLinks[$resourceName] ?? '');
            $apis = $resources[$resourceName];

            $markdown .= "### [{$title}]({$docLink})\n\n";
            $markdown .= "| Endpoint | Response | API |\n";
            $markdown .= "|----------|----------|-----|\n";

            foreach ($apis as $api) {
                $apiMethod = "[{$resourceName}::{$api['method']}()](src/Resource/{$resourceName}.php)";
                $markdown .= "| `{$api['endpoint']}` | {$api['response']} | {$apiMethod} |\n";
            }

            $markdown .= "\n";
        }

        return $markdown;
    }

    /**
     * 更新 README.md 文件
     */
    private function updateReadmeFile(string $newApiSection): void
    {
        $readmeContent = file_get_contents($this->readmePath);

        // 查找标志之间的内容
        $startMarker = '<!-- API_DOCS_START -->';
        $endMarker = '<!-- API_DOCS_END -->';

        $startPos = strpos($readmeContent, $startMarker);
        $endPos = strpos($readmeContent, $endMarker);

        if ($startPos !== false && $endPos !== false) {
            // 找到了标志，替换标志之间的内容
            $before = substr($readmeContent, 0, $startPos + strlen($startMarker));
            $after = substr($readmeContent, $endPos);

            // 移除 "## Available Resources" 标题，因为它在标志之前
            $apiSectionWithoutTitle = preg_replace('/^## Available Resources\n\n/', '', $newApiSection);

            $updatedContent = $before."\n".$apiSectionWithoutTitle."\n".$after;
        } else {
            // 如果没有找到标志，询问用户是否添加
            echo "警告：未找到 API 文档标志 (<!-- API_DOCS_START --> 和 <!-- API_DOCS_END -->)\n";
            echo "请在 README.md 中手动添加这些标志来标识要更新的区域。\n";

            return;
        }

        file_put_contents($this->readmePath, $updatedContent);
        echo "README.md 已更新！\n";
    }
}

// 运行脚本
if (php_sapi_name() === 'cli') {
    echo "正在更新 README.md 中的 API 表格...\n";
    $updater = new ReadmeApiUpdater;
    $updater->update();
    echo "更新完成！\n";
}
