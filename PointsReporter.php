<?php

namespace stf;

class PointsReporter {

    private array $scale;

    public function __construct(array $scale) {
        $this->scale = $scale;
    }

    public function execute(int $passedMethodCount) {
        $finalPoints = 0;

        foreach ($this->scale as $threshold => $points) {
            if ($passedMethodCount >= $threshold) {
                $finalPoints = $points;
            }
        }

        printf(RESULT_PATTERN, $finalPoints, $this->getMaxPoints());
    }

    private function getMaxPoints() : int {
        $maxPoints = 0;

        foreach ($this->scale as $threshold => $points) {
            if ($points >= $maxPoints) {
                $maxPoints = $points;
            }
        }

        return $maxPoints;
    }
}

