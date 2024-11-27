<?php include 'game.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Number Guessing Game (1-10)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .container {
            width: 300px;
            margin: auto;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .timer {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
    </style>
    <script>
        let startTime = <?= isset($_SESSION['start_time']) ? $_SESSION['start_time'] : 'null' ?>;
        let isGameOver = <?= isset($success) && $success ? 'true' : 'false' ?>;

        function updateTimer() {
            if (!startTime || isGameOver) return;

            const now = Math.floor(Date.now() / 1000);
            const elapsed = now - startTime;

            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;

            document.getElementById("timer").textContent = 
                `Time Elapsed: ${minutes}m ${seconds}s`;

            if (!isGameOver) {
                requestAnimationFrame(updateTimer);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            updateTimer();
        });
    </script>
</head>
<body>
    <h1>Number Guessing Game (1-10)</h1>
    <?php if (!$_SESSION['started']): ?>
        <div class="container">
            <form method="POST">
                <label for="player_name">Enter Your Name:</label>
                <input type="text" name="player_name" id="player_name" required>
                <button type="submit" name="start_game">Start Game</button>
            </form>
        </div>
    <?php else: ?>
        <div class="timer" id="timer">Time Elapsed: 0m 0s</div>
        <div class="container">
            <?php if (isset($success) && $success): ?>
                <p>Congratulations, <?= htmlspecialchars($_SESSION['player_name']) ?>! You guessed the number in <?= $_SESSION['attempts'] ?> attempts.</p>
                <p>Time taken: <?= $total_time ?> seconds.</p>
                <a href="index.php">Play Again</a>
            <?php else: ?>
                <form method="POST">
                    <p>Player: <?= htmlspecialchars($_SESSION['player_name']) ?></p>
                    <label for="guess">Enter your guess (1-10):</label>
                    <input type="number" name="guess" id="guess" min="1" max="10" required>
                    <button type="submit">Submit Guess</button>
                </form>
                <?php if (isset($message)): ?>
                    <p><?= $message ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <h2>Leaderboard</h2>
    <table border="1" style="margin: auto;">
        <tr>
            <th>Rank</th>
            <th>Player Name</th>
            <th>Attempts</th>
            <th>Total Time (s)</th>
        </tr>
        <?php
        $result = $conn->query("SELECT player_name, attempts, total_time FROM leaderboard ORDER BY total_time ASC LIMIT 10");
        $rank = 1;
        while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><?= htmlspecialchars($row['player_name']) ?></td>
                <td><?= $row['attempts'] ?></td>
                <td><?= $row['total_time'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
