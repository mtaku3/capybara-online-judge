<?php

declare(strict_types=1);

namespace App\Application\GetProblems;

class GetProblemsRequest
{
    /**
     * @var int
     */
    public readonly int $Page;
    /**
     * @var int
     */
    public readonly int $Limit;

    /**
     * @param int $page
     * @param int $limit
     * @return void
     */
    public function __construct(int $page, int $limit)
    {
        $this->Page = $page;
        $this->Limit = $limit;
    }
}
