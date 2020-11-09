<?php

declare(strict_types=1);

namespace Turing\HyperfEvo\Traits;

trait ArrayTrails
{
    /**
     * 新旧数据比较
     * @param array $source
     * @param array $now
     * @return array
     */
    public function arrDiff(array $source, array $now):array
    {
        $diff = [];
        if (!empty($source) && !empty($now) && is_array($source) && is_array($now)) {
            print_r(1);
            foreach ($now as $x => $x_value) {
                $source_value = $source[$x];
                if (!empty($source_value)) {
                    if (is_array($source_value)) {
                        $diff[$x] = $this->arrDiff($source_value, $x_value);
                    } elseif ($source_value !== $x_value) {
                        $diff[$x] = [
                            'source' => $source_value,
                            'now' => $x_value,
                        ];
                    }
                }
            }
        }

        return  $diff;
    }
}