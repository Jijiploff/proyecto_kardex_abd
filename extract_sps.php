<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $sps = ['_Kardex_SaldoInicialDetallado'];
    foreach ($sps as $spName) {
        $sp = Illuminate\Support\Facades\DB::selectOne("SELECT definition FROM sys.sql_modules WHERE object_id = OBJECT_ID(?)", [$spName]);
        if ($sp) {
            file_put_contents(__DIR__ . "/{$spName}.sql", $sp->definition);
            echo "Extracted $spName.sql\n";
        } else {
            echo "SP $spName not found.\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
