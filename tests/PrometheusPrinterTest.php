<?php

declare(strict_types=1);

namespace ReactInspector\Tests\Printer\Prometheus;

use ReactInspector\Config;
use ReactInspector\Measurement;
use ReactInspector\Measurements;
use ReactInspector\Metric;
use ReactInspector\Printer\Prometheus\PrometheusPrinter;
use ReactInspector\Tag;
use ReactInspector\Tags;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;

/**
 * @internal
 */
final class PrometheusPrinterTest extends AsyncTestCase
{
    public function testEmpty(): void
    {
        $metric = Metric::create(
            new Config(
                'metric_name',
                'counter',
                ''
            ),
            new Tags(),
            new Measurements()
        );

        self::assertSame('', (new PrometheusPrinter())->print($metric));
    }

    public function testNotEmpty(): void
    {
        $metric = Metric::create(
            new Config(
                'metric_name',
                'counter',
                'Halp'
            ),
            new Tags(
                new Tag('global', 'true'),
            ),
            new Measurements(
                new Measurement(1, new Tags(new Tag('t', 'a'))),
                new Measurement(2, new Tags(new Tag('t', 'b'))),
            )
        );

        $string = (new PrometheusPrinter())->print($metric);
        self::assertStringContainsString('# HELP metric_name Halp', $string);
        self::assertStringContainsString('# TYPE metric_name counter', $string);
        self::assertStringContainsString('metric_name{global="true",t="a"} 1 ', $string);
        self::assertStringContainsString('metric_name{global="true",t="b"} 2 ', $string);
    }

    public function testEmptyHelpDescription(): void
    {
        $metric = Metric::create(
            new Config(
                'metric_name',
                'counter',
                ''
            ),
            new Tags(
                new Tag('global', 'true'),
                new Tag('local', 'false'),
            ),
            new Measurements(
                new Measurement(1, new Tags(new Tag('t', 'a'))),
                new Measurement(2, new Tags(new Tag('t', 'b'))),
            )
        );

        $string = (new PrometheusPrinter())->print($metric);

        self::assertStringNotContainsString('# HELP metric_name', $string);
        self::assertStringContainsString('# TYPE metric_name counter', $string);
        self::assertStringContainsString('metric_name{global="true",local="false",t="a"} 1 ', $string);
        self::assertStringContainsString('metric_name{global="true",local="false",t="b"} 2 ', $string);
    }
}
