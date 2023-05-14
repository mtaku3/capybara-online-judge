<?php

declare(strict_types=1);

namespace App\Infrastructure\CycleORM\TypecastHandler;

use App\Application\Session\ValueObject\RefreshToken;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\Parser\UncastableInterface;

class RefreshTokenTypecast implements CastableInterface, UncastableInterface
{
    /**
     * @var array
     */
    private array $rules = [];

    /**
     * @param DatabaseInterface $database
     * @return void
     */
    public function __construct(private DatabaseInterface $database)
    {
    }

    /**
     * @param array<string, mixed> $rules
     * @return array<string, mixed>
     */
    public function setRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            if ($rule === "RefreshToken") {
                unset($rules[$key]);
                $this->rules[$key] = $rule;
            }
        }

        return $rules;
    }

    /**
     * @param array $values
     * @return array
     */
    public function cast(array $values): array
    {
        foreach ($this->rules as $column => $rule) {
            if (!isset($values[$column])) {
                continue;
            }

            $values[$column] = new RefreshToken($values[$column]);
        }

        return $values;
    }

    public function uncast(array $values): array
    {
        foreach ($this->rules as $column => $rule) {
            if (!isset($values[$column])) {
                continue;
            }

            $values[$column] = (string)$values[$column];
        }

        return $values;
    }
}
