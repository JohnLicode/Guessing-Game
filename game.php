<?php
session_start();

$servername = 'localhost';
$username = 'root';
$password = 'root';
$database = 'guessing_game';



$conn = new mysqli($servername, $username , $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize game variables if not set
if (!isset($_SESSION['started'])) {
    $_SESSION['started'] = false;
    $_SESSION['number'] = rand(1, 10); // Random number between 1 and 10
    $_SESSION['attempts'] = 0;
    $_SESSION['player_name'] = '';
    $_SESSION['start_time'] = 0;
}

// Handle the player's name and start button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_game'])) {
    $_SESSION['player_name'] = htmlspecialchars($_POST['player_name']);
    $_SESSION['started'] = true;
    $_SESSION['start_time'] = time(); // Record the start time as a UNIX timestamp
}

// Handle guesses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guess'])) {
    $guess = (int)$_POST['guess'];
    $_SESSION['attempts']++;

    if ($guess == $_SESSION['number']) {
        $end_time = time();
        $total_time = $end_time - $_SESSION['start_time']; // Total time in seconds

        // Save result to leaderboard
        $stmt = $conn->prepare("INSERT INTO leaderboard (player_name, attempts, start_time, end_time, total_time) VALUES (?, ?, ?, ?, ?)");
        $start_time_formatted = date("Y-m-d H:i:s", $_SESSION['start_time']);
        $end_time_formatted = date("Y-m-d H:i:s", $end_time);

        $stmt->bind_param(
            "sissi",
            $_SESSION['player_name'],
            $_SESSION['attempts'],
            $start_time_formatted,
            $end_time_formatted,
            $total_time
        );
        $stmt->execute();
        $stmt->close();

        // Display success message and reset game state
        $success = true;
        $message = "Congratulations! You guessed the correct number.";
        session_destroy(); // Clear session to allow a new game
    } else {
        $message = $guess > $_SESSION['number'] ? "Too high!" : "Too low!";
    }
}
?>
