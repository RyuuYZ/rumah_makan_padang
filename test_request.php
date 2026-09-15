<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/', 'GET'
    )
);
if ($response->getStatusCode() === 500) {
    if (isset($response->exception) && $response->exception) {
        echo $response->exception->getMessage();
        echo "\n";
        echo $response->exception->getFile() . ':' . $response->exception->getLine();
    } else {
        echo "500 Error but no exception object on response.\n";
    }
} else {
    echo "Status: " . $response->getStatusCode() . "\n";
}
