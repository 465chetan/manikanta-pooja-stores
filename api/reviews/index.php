<?php
// ── Reviews API: Submit a review ──────────────────────────────
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = db();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $name   = trim($data['name']   ?? '');
        $rating = (int)($data['rating'] ?? 0);
        $review = trim($data['review'] ?? '');

        if (!$name || !$review || $rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Please fill all fields and choose a rating.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO reviews (name, rating, review) VALUES (:name, :rating, :review)");
        $stmt->execute([':name' => $name, ':rating' => $rating, ':review' => $review]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT id, name, rating, review, created_at FROM reviews WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 50");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'reviews' => $reviews]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
