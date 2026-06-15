<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["balance" => 0, "transactions" => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY date DESC, id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
$totalBalance = 0;

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    if ($row['type'] === 'income') {
        $totalBalance += $row['amount'];
    } else {
        $totalBalance -= $row['amount'];
    }
}

echo json_encode([
    "balance" => $totalBalance,
    "transactions" => $transactions
]);

$stmt->close();
$conn->close();
?>