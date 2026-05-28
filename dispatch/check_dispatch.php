
<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once '../config/session.php';
require_once '../config/db.php';

/** @var mysqli $conn */

header('Content-Type: application/json');

$language = $_SESSION['lang'] ?? 'en';

$adminRole = $_SESSION['admin_role'] ?? '';

if ($adminRole === 'admin') {

    $establishment =
        $_SESSION['selected_establishment']
        ?? '';

} else {

    $establishment =
        $_SESSION['establishment']
        ?? '';
}

if ($establishment === '') {

    http_response_code(400);

    echo json_encode([
        'error' => 'Establishment missing'
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Keep leading zeros
|--------------------------------------------------------------------------
*/

$inwardId = trim(
    (string) ($_GET['inward_id'] ?? '')
);

if ($inwardId === '') {

    http_response_code(400);

    echo json_encode([
        'error' => 'Invalid inward ID'
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Fetch inward record
|--------------------------------------------------------------------------
*/

$query = "
    SELECT
        register_id,
        letter_no,
        quantity
    FROM inward_letters
    WHERE
        TRIM(register_id) = TRIM(?)
        AND language = ?
        AND establishment = ?
    LIMIT 1
";

$stmt = $conn->prepare($query);

if (!$stmt instanceof mysqli_stmt) {

    http_response_code(500);

    echo json_encode([
        'error' => 'Failed to prepare inward query'
    ]);

    exit;
}

$stmt->bind_param(
    'sss',
    $inwardId,
    $language,
    $establishment
);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        'error' => 'Failed to execute inward query'
    ]);

    $stmt->close();

    exit;
}

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$stmt->close();

if (!$row) {

    http_response_code(404);

    echo json_encode([
        'error' => 'Record not found'
    ]);

    exit;
}

$total = (int) $row['quantity'];

$letterNo = (string) $row['letter_no'];

$registerId = (string) $row['register_id'];

/*
|--------------------------------------------------------------------------
| Fetch dispatched quantity
|--------------------------------------------------------------------------
*/

$dispatchQuery = "
    SELECT
        COALESCE(
            SUM(dispatch_qty),
            0
        ) AS dispatched
    FROM dispatch
    WHERE inward_id = ?
";

$stmt2 = $conn->prepare($dispatchQuery);

if (!$stmt2 instanceof mysqli_stmt) {

    http_response_code(500);

    echo json_encode([
        'error' => 'Failed to prepare dispatch query'
    ]);

    exit;
}

$stmt2->bind_param(
    's',
    $registerId
);

if (!$stmt2->execute()) {

    http_response_code(500);

    echo json_encode([
        'error' => 'Failed to execute dispatch query'
    ]);

    $stmt2->close();

    exit;
}

$dispatchResult = $stmt2->get_result();

$data = $dispatchResult->fetch_assoc();

$stmt2->close();

$alreadyDispatched =
    (int) ($data['dispatched'] ?? 0);

$pending = $total - $alreadyDispatched;

if ($pending < 0) {

    $pending = 0;
}

/*
|--------------------------------------------------------------------------
| Final response
|--------------------------------------------------------------------------
*/

echo json_encode(
    [
        'inward_id' => $registerId,
        'letter_no' => $letterNo,
        'total' => $total,
        'dispatched' => $alreadyDispatched,
        'pending' => $pending
    ],
    JSON_UNESCAPED_UNICODE
);

exit;

