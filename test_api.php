<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = App\Models\Usuario::first();
if (!$user) {
    echo "No users found!\n";
    exit;
}

$token = $user->createToken('test_token')->plainTextToken;
echo "TOKEN: " . $token . "\n";

$request2 = Illuminate\Http\Request::create('/api/cursos', 'GET');
$request2->headers->set('Authorization', 'Bearer ' . $token);
$request2->headers->set('Accept', 'application/json');
$response2 = $kernel->handle($request2);

echo "CURSOS RESPONSE STATUS: " . $response2->status() . "\n";
echo "CURSOS RESPONSE: " . $response2->getContent() . "\n";
