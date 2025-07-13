<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        api: __DIR__.'/routes/api.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Datastream;

echo "Checking datastream data...\n";
$count = Datastream::count();
echo "Total records: $count\n";

if ($count > 0) {
    $latest = Datastream::latest()->first();
    echo "Latest record:\n";
    echo "ID: " . $latest->id . "\n";
    echo "Temp: " . $latest->temp . "\n";
    echo "Humid: " . $latest->humid . "\n";
    echo "Light Intensity: " . $latest->light_intensity . "\n";
    echo "Timestamp: " . $latest->timestamp . "\n";
} else {
    echo "No data found in database.\n";
} 