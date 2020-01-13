<?php declare(strict_types=1);

namespace ReactInspector\Printer\Prometheus;

use ReactInspector\Metric;
use ReactInspector\Printer\Printer;

final class PrometheusPrinter implements Printer
{
    private const NL = "\n";

    public function print(Metric $metric): string
    {
        if (\count($metric->measurements()) === 0) {
            return '';
        }

        $string = '';

        if (\strlen($metric->config()->description()) > 0) {
            $string .= '# HELP ' . $metric->config()->name() . ' ' . $metric->config()->description() . self::NL;
        }

        $string .= '# TYPE ' . $metric->config()->name() . ' ' . $metric->config()->type() . self::NL;

        foreach ($metric->measurements() as $measurement) {
            $string .= $metric->config()->name();
            $tags = \array_merge($metric->tags(), $measurement->tags());
            $tagCount = \count($tags);
            if ($tagCount > 0) {
                $string .= '{';
                for ($i = 0; $i < $tagCount; $i++) {
                    $string .= $tags[$i]->key() . '="' . $tags[$i]->value() . '"';
                    if (isset($tags[$i + 1])) {
                        $string .= ',';
                    }
                }
                $string .= '}';
            }
            $string .= ' ' . $measurement->value() . ' ' . \floor($metric->time() * 1000) . self::NL;
        }

        return $string;
    }
}
