<?php

declare(strict_types=1);

function saveLog(string $activity): void
{
    global $conn;

    /** @var mysqli $conn */

    $username =
        $_SESSION['user']
        ?? '';

    $role =
        $_SESSION['admin_role']
        ?? '';

    $establishment =
        $_SESSION['establishment']
        ?? '';

    $stmt = $conn->prepare(
        '
        INSERT INTO activity_logs
        (
            username,
            role,
            establishment,
            activity,
            log_time
        )
        VALUES
        (
            ?, ?, ?, ?, NOW()
        )
        '
    );

    if (!$stmt) {
        return;
    }

    $stmt->bind_param(
        'ssss',
        $username,
        $role,
        $establishment,
        $activity
    );

    $stmt->execute();

    $stmt->close();
}
