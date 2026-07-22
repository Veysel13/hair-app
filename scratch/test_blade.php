<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $content = file_get_contents(__DIR__ . '/../resources/views/customer/showcase.blade.php');
    $compiled = Illuminate\Support\Facades\Blade::compileString($content);
    echo "BLADE COMPILED SUCCESSFULLY!\n";
    file_put_contents(__DIR__ . '/compiled_output.php', $compiled);
    echo "Saved compiled output to scratch/compiled_output.php\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "LINE: " . $e->getLine() . "\n";
    echo "FILE: " . $e->getFile() . "\n";
}
