<?php

namespace App\Services;

class FibonacciService
{
    /**
     * Calculate the Fibonacci sequence up to a given number.
     *
     * @param int $n
     * @return array
     */
    public function calculateUpTo(int $n): array
    {
        // Initialize the first two Fibonacci numbers
        $fibSequence = [0, 1];

        // Generate Fibonacci numbers until the last one is greater than $n
        while (true) {
            $next = $fibSequence[count($fibSequence) - 1] + $fibSequence[count($fibSequence) - 2];
            if ($next > $n) {
                break;
            }
            $fibSequence[] = $next;
        }

        return $fibSequence;
    }
}
