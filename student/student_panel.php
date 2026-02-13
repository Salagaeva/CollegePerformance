<?php
include "../includes/auth.php";
include "../config.php";

if ($_SESSION['role'] != 'student') {
    die("Доступ запрещен");
}

$user_id = $_SESSION['user_id'];

// Получаем оценки студента
$result = $conn->query("
    SELECT g.grade, g.date, s.name 
    FROM grades g
    JOIN subjects s ON g.subject_id = s.id
    JOIN students st ON g.student_id = st.id
    WHERE st.user_id = $user_id
    ORDER BY s.name
");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои оценки</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Мои оценки</h2>

    <?php if ($result->num_rows == 0): ?>
        <p>Оценок пока нет.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Предмет</th>
                <th>Оценка</th>
                <th>Дата</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['grade']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

    <br>
    <a href="../logout.php">Выйти</a>
</div>
</body>
</html>
