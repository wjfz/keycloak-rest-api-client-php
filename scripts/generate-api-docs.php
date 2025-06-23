<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

/**
 * API 文档生成器
 * 自动扫描 Resource 类并生成 API 表格
 */
class ApiDocGenerator
{
    private string $srcPath;

    public function __construct()
    {
        $this->srcPath = __DIR__.'/../src/Resource';
    }

    /**
     * 生成所有 API 文档
     */
    public function generate(): void
    {
        $resources = $this->scanResources();
        $this->generateMarkdown($resources);
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

        // 使用正则表达式提取所有公共方法
        preg_match_all('/public function (\w+)\([^)]*\)(?::[^{]+)?\s*{((?:[^{}]|{[^{}]*})*)}/', $content, $matches, PREG_SET_ORDER);

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
        if (preg_match('/(?:new\s+(?:Query|Command)\s*\(\s*[\'"])([^\'"]+)/', $methodBody, $pathMatch)) {
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
        $returnType = $this->extractReturnType($methodBody, $className);

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
    private function extractReturnType(string $methodBody, string $className): string
    {
        // 查找 executeQuery 的返回类型
        if (preg_match('/executeQuery\([^,]+,\s*([^:,\s]+)::class/', $methodBody, $match)) {
            $typeName = $match[1];

            // 处理完整类名
            if (strpos($typeName, '\\') !== false) {
                $parts = explode('\\', $typeName);
                $shortName = end($parts);
            } else {
                $shortName = $typeName;
            }

            // 为不同类型添加链接
            if (strpos($shortName, 'Collection') !== false) {
                return "[{$shortName}](src/Collection/{$shortName}.php)";
            }

            if (in_array($shortName, ['ServerInfo', 'Client', 'Group', 'Organization', 'Realm', 'Role', 'User'])) {
                return "[{$shortName}](src/Representation/{$shortName}.php)";
            }

            if (in_array($shortName, ['StringMap', 'Map'])) {
                return "[{$shortName}](src/Type/{$shortName}.php)";
            }

            return $shortName;
        }

        // 如果是 Command，通常返回 ResponseInterface
        if (strpos($methodBody, 'executeCommand') !== false) {
            return 'ResponseInterface';
        }

        // 检查方法是否返回 void
        if (preg_match('/public function \w+\([^)]*\):\s*void/', $methodBody)) {
            return 'ResponseInterface';
        }

        return 'mixed';
    }

    /**
     * 生成 Markdown 表格
     */
    private function generateMarkdown(array $resources): void
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

        echo "## Available Resources\n\n";

        foreach ($resourceOrder as $resourceName => $title) {
            if (! isset($resources[$resourceName])) {
                continue;
            }

            $docLink = $baseUrl.($docLinks[$resourceName] ?? '');
            $apis = $resources[$resourceName];

            echo "### [{$title}]({$docLink})\n\n";
            echo "| Endpoint | Response | API |\n";
            echo "|----------|----------|-----|\n";

            foreach ($apis as $api) {
                $apiMethod = "[{$resourceName}::{$api['method']}()](src/Resource/{$resourceName}.php)";
                echo "| `{$api['endpoint']}` | {$api['response']} | {$apiMethod} |\n";
            }

            echo "\n";
        }
    }
}

// 运行脚本
if (php_sapi_name() === 'cli') {
    echo "正在生成 API 文档...\n";
    $generator = new ApiDocGenerator;
    $generator->generate();
    echo "\n生成完成！\n";
}
