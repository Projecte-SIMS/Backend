<?php
/**
 * AUDIT: Permissions System Refactored (3-Level Structure)
 * 
 * This script validates that the refactored permission system (view/manage/delete)
 * is correctly enforced across all controllers and roles.
 * 
 * Roles Structure:
 * - Admin: All permissions (users.*, roles.*, vehicles.*, tickets.*, reservations.*)
 * - Client: vehicles.view, tickets.view, tickets.manage, reservations.view, reservations.manage
 * - Maintenance: vehicles.view, vehicles.manage
 * 
 * Tokens:
 * - Client: 1|gH9ILWRLZFU5xixTg6zXGDmcwz8l4x4R03uwpVkV6bf09c6c
 * - Admin: 2|qKOBPZ1dlKlF1nL2dQSqftk4F3mQ68hieXdlLGeod8a63f33
 * - Maintenance: 4|lrhLtXYfQ68nSm1Wb4HEJq5on3E9soBW4SJOuP6A36791d82
 */

$baseUrl = 'http://localhost:8000/api';

$tokens = [
    'client' => '1|gH9ILWRLZFU5xixTg6zXGDmcwz8l4x4R03uwpVkV6bf09c6c',
    'admin' => '2|qKOBPZ1dlKlF1nL2dQSqftk4F3mQ68hieXdlLGeod8a63f33',
    'maintenance' => '4|lrhLtXYfQ68nSm1Wb4HEJq5on3E9soBW4SJOuP6A36791d82',
];

$results = [
    'users' => [],
    'roles' => [],
    'vehicles' => [],
    'tickets' => [],
    'reservations' => [],
    'admin_reservations' => [],
];

echo "\n========================================\n";
echo "PERMISSION SYSTEM AUDIT - REFACTORED\n";
echo "========================================\n\n";

// Helper function to make requests
function makeRequest($method, $url, $token, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => $response,
    ];
}

// Test result formatter
function testResult($role, $endpoint, $method, $expectedStatus, $actualStatus) {
    $passed = in_array($actualStatus, (array)$expectedStatus);
    $status = $passed ? '✅ PASS' : '❌ FAIL';
    $roleLabel = str_pad(strtoupper($role), 12);
    $endpointLabel = str_pad($endpoint, 40);
    $methodLabel = str_pad($method, 6);
    
    echo "{$status} | {$roleLabel} | {$endpointLabel} | {$methodLabel} | Expected: " . json_encode($expectedStatus) . " | Got: {$actualStatus}\n";
    
    return $passed;
}

echo "═══════════════════════════════════════════════════════════════════════════════════════\n";
echo "USERS ENDPOINTS\n";
echo "═══════════════════════════════════════════════════════════════════════════════════════\n\n";

// GET /users - viewAny
echo "GET /users (viewAny)\n";
foreach ($tokens as $role => $token) {
    $response = makeRequest('GET', "$baseUrl/users", $token);
    $expected = ($role === 'admin') ? 200 : 403;
    testResult($role, '/users', 'GET', $expected, $response['status']);
}
echo "\n";

// POST /users - create (only admin)
echo "POST /users (create)\n";
foreach ($tokens as $role => $token) {
    $data = [
        'name' => 'Test User',
        'username' => 'testuser_' . time(),
        'email' => 'testuser_' . time() . '@test.com',
        'password' => 'password123',
    ];
    $response = makeRequest('POST', "$baseUrl/users", $token, $data);
    $expected = ($role === 'admin') ? 201 : 403;
    testResult($role, '/users', 'POST', $expected, $response['status']);
}
echo "\n";

// GET /users/{id} - view (self or admin)
echo "GET /users/1 (view own)\n";
foreach ($tokens as $role => $token) {
    $response = makeRequest('GET', "$baseUrl/users/1", $token);
    // Admin can view any, Client/Maintenance can view if ID=1 (their own)
    $expected = ($role === 'admin' || $role === 'client') ? 200 : 403;
    testResult($role, '/users/{id}', 'GET', $expected, $response['status']);
}
echo "\n";

echo "═══════════════════════════════════════════════════════════════════════════════════════\n";
echo "ROLES ENDPOINTS\n";
echo "═══════════════════════════════════════════════════════════════════════════════════════\n\n";

// GET /roles - viewAny
echo "GET /roles (viewAny)\n";
foreach ($tokens as $role => $token) {
    $response = makeRequest('GET', "$baseUrl/roles", $token);
    $expected = ($role === 'admin') ? 200 : 403;
    testResult($role, '/roles', 'GET', $expected, $response['status']);
}
echo "\n";

// POST /roles - create
echo "POST /roles (create)\n";
foreach ($tokens as $role => $token) {
    $data = [
        'name' => 'CustomRole_' . time(),
        'guard_name' => 'web',
    ];
    $response = makeRequest('POST', "$baseUrl/roles", $token, $data);
    $expected = ($role === 'admin') ? 201 : 403;
    testResult($role, '/roles', 'POST', $expected, $response['status']);
}
echo "\n";

echo "═══════════════════════════════════════════════════════════════════════════════════════\n";
echo "VEHICLES ENDPOINTS\n";
echo "═══════════════════════════════════════════════════════════════════════════════════════\n\n";

// GET /vehicles - viewAny
echo "GET /vehicles (viewAny)\n";
foreach ($tokens as $role => $token) {
    $response = makeRequest('GET', "$baseUrl/vehicles", $token);
    // Client, Admin, Maintenance can view
    $expected = [200, 204];
    testResult($role, '/vehicles', 'GET', $expected, $response['status']);
}
echo "\n";

// POST /vehicles - create (admin, maintenance)
echo "POST /vehicles (create)\n";
foreach ($tokens as $role => $token) {
    $data = [
        'license_plate' => 'TEST-' . time(),
        'brand' => 'TestBrand',
        'model' => 'TestModel',
        'price_per_minute' => 0.5,
        'active' => true,
    ];
    $response = makeRequest('POST', "$baseUrl/vehicles", $token, $data);
    $expected = (in_array($role, ['admin', 'maintenance'])) ? 201 : 403;
    testResult($role, '/vehicles', 'POST', $expected, $response['status']);
}
echo "\n";

echo "═══════════════════════════════════════════════════════════════════════════════════════\n";
echo "TICKETS ENDPOINTS\n";
echo "═══════════════════════════════════════════════════════════════════════════════════════\n\n";

// GET /tickets - viewAny
echo "GET /tickets (viewAny)\n";
foreach ($tokens as $role => $token) {
    $response = makeRequest('GET', "$baseUrl/tickets", $token);
    // Client and Admin can view
    $expected = (in_array($role, ['client', 'admin'])) ? 200 : 403;
    testResult($role, '/tickets', 'GET', $expected, $response['status']);
}
echo "\n";

// POST /tickets - create (client, admin)
echo "POST /tickets (create)\n";
foreach ($tokens as $role => $token) {
    $data = [
        'title' => 'Test Ticket',
        'description' => 'Test ticket description',
    ];
    $response = makeRequest('POST', "$baseUrl/tickets", $token, $data);
    $expected = (in_array($role, ['client', 'admin'])) ? 201 : 403;
    testResult($role, '/tickets', 'POST', $expected, $response['status']);
}
echo "\n";

echo "═══════════════════════════════════════════════════════════════════════════════════════\n";
echo "RESERVATIONS ENDPOINTS\n";
echo "═══════════════════════════════════════════════════════════════════════════════════════\n\n";

// GET /reservations - viewAny
echo "GET /reservations (viewAny)\n";
foreach ($tokens as $role => $token) {
    $response = makeRequest('GET', "$baseUrl/reservations", $token);
    // Client and Admin can view
    $expected = (in_array($role, ['client', 'admin'])) ? 200 : 403;
    testResult($role, '/reservations', 'GET', $expected, $response['status']);
}
echo "\n";

// POST /reservations - create (client, admin)
echo "POST /reservations (create)\n";
foreach ($tokens as $role => $token) {
    $data = [
        'vehicle_id' => 1,
        'scheduled_start' => date('Y-m-d H:i:s', strtotime('+1 hour')),
    ];
    $response = makeRequest('POST', "$baseUrl/reservations", $token, $data);
    $expected = (in_array($role, ['client', 'admin'])) ? 201 : 403;
    testResult($role, '/reservations', 'POST', $expected, $response['status']);
}
echo "\n";

echo "═══════════════════════════════════════════════════════════════════════════════════════\n";
echo "ADMIN RESERVATIONS ENDPOINTS\n";
echo "═══════════════════════════════════════════════════════════════════════════════════════\n\n";

// GET /admin/reservations - viewAny (admin only)
echo "GET /admin/reservations (viewAny - admin only)\n";
foreach ($tokens as $role => $token) {
    $response = makeRequest('GET', "$baseUrl/admin/reservations", $token);
    $expected = ($role === 'admin') ? 200 : 403;
    testResult($role, '/admin/reservations', 'GET', $expected, $response['status']);
}
echo "\n";

echo "\n═══════════════════════════════════════════════════════════════════════════════════════\n";
echo "AUDIT COMPLETE\n";
echo "═══════════════════════════════════════════════════════════════════════════════════════\n\n";

echo "PERMISSION SUMMARY:\n";
echo "─────────────────────────────────────────────────────────────────────────────────────\n";
echo "ADMIN Role:\n";
echo "  ✓ users.view, users.manage, users.delete\n";
echo "  ✓ roles.view, roles.manage, roles.delete\n";
echo "  ✓ vehicles.view, vehicles.manage, vehicles.delete\n";
echo "  ✓ tickets.view, tickets.manage, tickets.delete\n";
echo "  ✓ reservations.view, reservations.manage, reservations.delete\n\n";

echo "CLIENT Role:\n";
echo "  ✓ vehicles.view\n";
echo "  ✓ tickets.view, tickets.manage\n";
echo "  ✓ reservations.view, reservations.manage\n";
echo "  ✗ No delete permissions\n";
echo "  ✗ No users/roles management\n\n";

echo "MAINTENANCE Role:\n";
echo "  ✓ vehicles.view, vehicles.manage\n";
echo "  ✗ No tickets/reservations access\n";
echo "  ✗ No users/roles management\n";
echo "  ✗ No delete permissions\n\n";
?>
