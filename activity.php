<?php
// Database connection
$servername = "localhost";
$username = "root";  // default XAMPP MySQL username
$password = "";  // default XAMPP MySQL password
$dbname = "information_push_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Capture activity data from GET request
$activity_type = $_GET['activity_type'] ?? '';
$activity_data = $_GET['activity_data'] ?? '';
$user_id = 1; // Hardcoded user_id for now

if (empty($activity_type) || empty($activity_data)) {
    die(json_encode(["error" => "Missing activity_type or activity_data"]));
}

// Insert activity into user_activity table
$stmt = $conn->prepare("INSERT INTO user_activity (user_id, activity_type, activity_data) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $activity_type, $activity_data);
$stmt->execute();
$stmt->close();

$response = [];
$search_term = "%" . $activity_data . "%";

// Fetch knowledge base documents, videos, and images
$kb_query = $conn->prepare("SELECT doc_title, doc_link, video_link, image_link, category FROM knowledge_base 
                            WHERE doc_title LIKE ? OR category LIKE ?");
$kb_query->bind_param("ss", $search_term, $search_term);
$kb_query->execute();
$kb_result = $kb_query->get_result();

while ($row = $kb_result->fetch_assoc()) {
    // Add document
    if (!empty($row['doc_link'])) {
        $response[] = [
            'type' => 'document',
            'title' => $row['doc_title'],
            'link' => $row['doc_link'],
            'category' => $row['category']
        ];
    }
    // Add video
    if (!empty($row['video_link'])) {
        $response[] = [
            'type' => 'video',
            'title' => $row['doc_title'] . " (Video)",
            'link' => $row['video_link'],
            'category' => $row['category']
        ];
    }
    // Add image
    if (!empty($row['image_link'])) {
        $response[] = [
            'type' => 'image',
            'title' => $row['doc_title'] . " (Image)",
            'link' => $row['image_link'],
            'category' => $row['category']
        ];
    }
}
$kb_query->close();

// Return response as JSON
if (empty($response)) {
    echo json_encode(["message" => "No relevant information found."]);
} else {
    echo json_encode($response);
}

$conn->close();
?>
