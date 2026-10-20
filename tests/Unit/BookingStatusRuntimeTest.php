<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class BookingStatusRuntimeTest extends TestCase
{
    public function test_status_models_work_without_phpunit_and_preserve_explicit_zero(): void
    {
        $process = new Process([PHP_BINARY, dirname(__DIR__).'/Fixtures/booking-status-without-phpunit.php']);
        $process->run();
        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput().$process->getOutput());
        $this->assertSame(['without_phpunit' => true, 'checks' => 6], json_decode($process->getOutput(), true));
    }
}
