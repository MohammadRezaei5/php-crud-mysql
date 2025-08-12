<?php
require 'db_config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST': // ایجاد کاربر جدید (Create)
        $data = json_decode(file_get_contents("php://input"));
        $sql = "INSERT INTO users (name, email) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$data->name, $data->email]);
        if ($result) {
            echo json_encode(["message" => "User created."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error."]);
        }
        break;

    case 'GET': // خواندن کاربران (Read)
        if (isset($_GET['id'])) {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_GET['id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "User not found."]);
            }
        } else {
            $sql = "SELECT * FROM users";
            $stmt = $pdo->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        }
        break;

    case 'PUT': // به‌روزرسانی کاربر (Update)
        parse_str(file_get_contents("php://input"), $_PUT);
        $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$_PUT['name'], $_PUT['email'], $_PUT['id']]);
        if ($result) {
            echo json_encode(["message" => "User updated."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error."]);
        }
        break;

    case 'DELETE': // حذف کاربر (Delete)
        parse_str(file_get_contents("php://input"), $_DELETE);
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$_DELETE['id']]);
        if ($result) {
            echo json_encode(["message" => "User deleted."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed."]);
        break;
}
