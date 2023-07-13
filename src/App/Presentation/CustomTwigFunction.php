<?php

declare(strict_types=1);

namespace App\Presentation;

class CustomTwigFunction
{
    /**
     * @var \Twig\TwigFunction[]
     */
    public array $functions = [];

    public function __construct()
    {
        $this->functions[] = new \Twig\TwigFunction("paginate", function (int $page, int $totalNumberOfPages): array {
            $prev = $page === 1 ? null : $page - 1;
            $next = $page === $totalNumberOfPages ? null : $page + 1;
            $items = [1];

            if ($page === 1 && $totalNumberOfPages === 1) {
                return [
                    "current" => $page,
                    "prev" => $prev,
                    "next" => $next,
                    "items" => $items,
                ];
            }
            if ($page > 4) {
                array_push($items, "…");
            }

            $r = 2;
            $r1 = $page - $r;
            $r2 = $page + $r;

            for ($i = $r1 > 2 ? $r1 : 2; $i <= min($totalNumberOfPages, $r2); $i++) {
                array_push($items, $i);
            }

            if ($r2 + 1 < $totalNumberOfPages) {
                array_push($items, "…");
            }
            if ($r2 < $totalNumberOfPages) {
                array_push($items, $totalNumberOfPages);
            }

            return $items;
        });
    }
}
