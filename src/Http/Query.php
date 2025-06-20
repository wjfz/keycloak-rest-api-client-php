<?php

declare(strict_types=1);

namespace Overtrue\Keycloak\Http;

class Query
{
    /**
     * @param  array<string, string>  $parameters
     * @param  \Overtrue\Keycloak\Http\Criteria|array<string,mixed>|null  $criteria
     */
    public function __construct(
        private string $path,
        private string $returnType,
        private array $parameters = [],
        private Criteria|array|null $criteria = null,
    ) {}

    public function getMethod(): Method
    {
        return Method::GET;
    }

    public function getPath(): string
    {
        $placeholders = array_map(
            static fn (string $parameter): string => '{'.$parameter.'}',
            array_keys($this->parameters),
        );

        $path = str_replace($placeholders, array_values($this->parameters), $this->path);

        return $path.$this->getQuery();
    }

    private function getQuery(): string
    {
        if (! $this->criteria) {
            return '';
        }

        if (is_array($this->criteria)) {
            $this->criteria = new Criteria($this->criteria);
        }

        return '?'.http_build_query($this->criteria->toArray());
    }

    public function getReturnType(): string
    {
        return $this->returnType;
    }
}
