<?php declare(strict_types=1);

namespace ReactInspector\Tests\Printer\Prometheus;

use ReactInspector\Config;
use ReactInspector\Measurement;
use ReactInspector\Metric;
use ReactInspector\Printer\Prometheus\PrometheusPrinter;
use ReactInspector\Tag;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;

/**
 * @internal
 */
final class PrometheusPrinterTest extends AsyncTestCase
{
    public function testEmpty(): void
    {
        $metric = new Metric(
            new Config(
                'metric_name',
                'counter',
                'Halp'
            ),
            [],
            []
        );

        self::assertSame('', (new PrometheusPrinter())->print($metric));
    }

    public function testNotEmpty(): void
    {
        $metric = new Metric(
            new Config(
                'metric_name',
                'counter',
                'Halp'
            ),
            [
                new Tag('global', 'true'),
            ],
            [
                new Measurement(1, new Tag('t', 'a')),
                new Measurement(2, new Tag('t', 'b')),
            ]
        );

        $string = (new PrometheusPrinter())->print($metric);
        self::assertStringContainsString('# HELP metric_name Halp', $string);
        self::assertStringContainsString('# TYPE metric_name counter', $string);
        self::assertStringContainsString('metric_name{global="true",t="a"} 1 ', $string);
        self::assertStringContainsString('metric_name{global="true",t="b"} 2 ', $string);
    }
}
