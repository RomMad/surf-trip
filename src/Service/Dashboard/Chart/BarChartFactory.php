<?php

declare(strict_types=1);

namespace App\Service\Dashboard\Chart;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final readonly class BarChartFactory
{
    public const string CHART1_COLOR_TOKEN = '--chart-1';
    public const string CHART2_COLOR_TOKEN = '--chart-2';
    public const int BAR_BORDER_RADIUS = 12;
    public const int BAR_THICKNESS = 18;
    public const int BAR_MAX_THICKNESS = 24;

    public function __construct(
        private ChartBuilderInterface $chartBuilder,
    ) {}

    /**
     * @param list<string>               $labels
     * @param list<int>                  $data
     * @param list<array<string, mixed>> $datasets
     * @param array<string, mixed>       $optionsOverride
     */
    public function create(
        array $labels,
        array $data,
        bool $horizontal = false,
        array $datasets = [],
        array $optionsOverride = [],
    ): Chart {
        if ([] === $datasets) {
            $datasets = [[]];
        }

        $datasets = array_map(
            static fn (array $dataset): array => array_replace_recursive([
                'data' => $data,
                'backgroundColor' => self::CHART1_COLOR_TOKEN,
                'borderColor' => self::CHART1_COLOR_TOKEN,
                'borderRadius' => self::BAR_BORDER_RADIUS,
                'barThickness' => self::BAR_THICKNESS,
                'maxBarThickness' => self::BAR_MAX_THICKNESS,
            ], $dataset),
            $datasets,
        );

        return $this->chartBuilder->createChart(Chart::TYPE_BAR)
            ->setData([
                'labels' => $labels,
                'datasets' => $datasets,
            ])
            ->setOptions(array_replace_recursive(
                $this->createOptions($horizontal),
                $optionsOverride,
            ))
        ;
    }

    /**
     * @return array<string, mixed>
     */
    private function createOptions(bool $horizontal): array
    {
        $options = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'datalabels' => [
                    'anchor' => 'center',
                    'align' => 'center',
                    'color' => '#ffffff',
                    'font' => [
                        'weight' => '600',
                    ],
                ],
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'x' => $horizontal
                    ? [
                        'beginAtZero' => true,
                        'ticks' => [
                            'precision' => 0,
                            'stepSize' => 1,
                        ],
                    ]
                    : [
                        'grid' => [
                            'display' => false,
                        ],
                    ],
                'y' => $horizontal
                    ? [
                        'grid' => [
                            'display' => false,
                        ],
                    ]
                    : [
                        'beginAtZero' => true,
                        'ticks' => [
                            'precision' => 0,
                            'stepSize' => 1,
                        ],
                    ],
            ],
        ];

        if ($horizontal) {
            $options['indexAxis'] = 'y';
        }

        return $options;
    }
}
