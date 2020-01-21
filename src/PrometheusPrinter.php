<?php declare(strict_types=1);

namespace ReactInspector\Printer\Prometheus;

use ReactInspector\Metric;
use ReactInspector\Printer\Printer;

final class PrometheusPrinter implements Printer
{
    private const NL = "\n";

    public function print(Metric $metric): string
    {
        if (\count($metric->measurements()->get()) === 0) {
            return '';
        }

        $string = '';

        if (\strlen($metric->config()->description()) > 0) {
            $string .= '# HELP ' . $metric->config()->name() . ' ' . $metric->config()->description() . self::NL;
        }

        $string .= '# TYPE ' . $metric->config()->name() . ' ' . $metric->config()->type() . self::NL;

        foreach ($metric->measurements()->get() as $measurement) {
            $string .= $metric->config()->name();
            $tags = \array_merge($metric->tags()->get(), $measurement->tags()->get());
            $tagCount = \count($tags);
            if ($tagCount > 0) {
                $tagKeys = \array_keys($tags);
                $string .= '{';
                for ($i = 0; $i < $tagCount; $i++) {
                    $string .= $tags[$tagKeys[$i]]->key() . '="' . $tags[$tagKeys[$i]]->value() . '"';
                    if (isset($tagKeys[$i + 1])) {
                        $string .= ',';
                    }
                }
                $string .= '}';
            }
            $string .= ' ' . $measurement->value() . ' ' . \floor($metric->time() * 1000) . self::NL;
        }

        return $string . self::NL;
    }
}
