<?php

namespace Xyz\Akulov\Service\Core\Criteria;

class Criteria
{
    private $pageSize = 10;

    private $page = 1;

    private $predicates = [];

    private $sorting = [];

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPredicates(): array
    {
        return $this->predicates;
    }

    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function asc(string $field): self
    {
        $this->sorting[] = [$field, 'ASC'];
        return $this;
    }

    public function desc(string $field): self
    {
        $this->sorting[] = [$field, 'DESC'];
        return $this;
    }

    public function eq(string $field, $value): self
    {
        $this->predicates[] = ['=', $field, $value];
        return $this;
    }

    public function neq(string $field, $value): self
    {
        $this->predicates[] = ['<>', $field, $value];
        return $this;
    }

    public function lt(string $field, $value): self
    {
        $this->predicates[] = ['<', $field, $value];
        return $this;
    }

    public function lte(string $field, $value): self
    {
        $this->predicates[] = ['<=', $field, $value];
        return $this;
    }

    public function gt(string $field, $value): self
    {
        $this->predicates[] = ['>', $field, $value];
        return $this;
    }

    public function gte(string $field, $value): self
    {
        $this->predicates[] = ['>', $field, $value];
        return $this;
    }

    public function in(string $field, array $value): self
    {
        $this->predicates[] = ['IN', $field, $value];
        return $this;
    }

    public function CONTAINS(string $field, string $value): self
    {
        $this->predicates[] = ['CONTAINS', $field, $value];
        return $this;
    }
}
