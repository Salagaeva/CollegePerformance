<?php
include "../includes/auth.php";
include "../config.php";

if ($_SESSION['role'] != 'teacher') {
    die("Доступ запрещен");
}

// Получаем ID учителя
$teacher_data = $conn->query("SELECT id FROM teachers WHERE user_id = " . $_SESSION['user_id']);
if ($teacher_data->num_rows == 0) {
    die("Учитель не найден в системе.");
}
$teacher_id = $teacher_data->fetch_assoc()['id'];

// Получаем список студентов с ФИО и группой
$students = $conn->query("
    SELECT st.id, st.full_name, g.name AS group_name
    FROM students st
    LEFT JOIN groups_college g ON st.group_id = g.id
    ORDER BY st.full_name
");

// Получаем предметы, к которым привязан преподаватель
$teacher_subjects = $conn->query("
    SELECT ts.subject_id, s.name AS subject_name
    FROM teacher_subjects ts
    JOIN subjects s ON ts.subject_id = s.id
    WHERE ts.teacher_id = $teacher_id
");
$allowed_subjects = [];
while ($row = $teacher_subjects->fetch_assoc()) {
    $allowed_subjects[$row['subject_id']] = $row['subject_name'];
}

// Удаление оценки
if (isset($_GET['delete_grade'])) {
    $grade_id = intval($_GET['delete_grade']);
    // Удаляем только свои оценки
    $conn->query("DELETE FROM grades WHERE id = $grade_id AND teacher_id = $teacher_id");
    header("Location: teacher_panel.php");
    exit();
}

// Получаем оценки только по своим предметам
$grades = $conn->query("
    SELECT g.id AS grade_id, st.full_name, g.grade, g.date, s.name AS subject_name, g.student_id
    FROM grades g
    JOIN students st ON g.student_id = st.id
    JOIN subjects s ON g.subject_id = s.id
    WHERE g.teacher_id = $teacher_id
    ORDER BY st.full_name, g.date DESC
");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель преподавателя</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Панель преподавателя</h2>

    <p>Привет, <?= htmlspecialchars($_SESSION['login'] ?? 'Преподаватель') ?>!</p>

    <a href="add_grade.php" class="btn">Выставить оценку</a>
    <a href="../logout.php" class="btn">Выйти</a>

    <h3>Список студентов</h3>
    <?php if ($students->num_rows > 0): ?>
        <table>
            <tr>
                <th>ФИО</th>
                <th>Группа</th>
            </tr>
            <?php while($student = $students->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($student['full_name']) ?></td>
                <td><?= htmlspecialchars($student['group_name'] ?? '-') ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Студенты пока не добавлены.</p>
    <?php endif; ?>

    <h3>Мои предметы</h3>
    <?php if ($allowed_subjects): ?>
        <ul>
            <?php foreach($allowed_subjects as $subj): ?>
                <li><?= htmlspecialchars($subj) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Предметы пока не назначены.</p>
    <?php endif; ?>

    <h3>Мои оценки</h3>
    <?php if ($grades->num_rows > 0): ?>
        <table>
            <tr>
                <th>Студент</th>
                <th>Предмет</th>
                <th>Оценка</th>
                <th>Дата</th>
                <th>Действие</th>
            </tr>
            <?php while($g = $grades->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($g['full_name']) ?></td>
                <td><?= htmlspecialchars($g['subject_name']) ?></td>
                <td><?= $g['grade'] ?></td>
                <td><?= $g['date'] ?></td>
                <td>
                    <a href="?delete_grade=<?= $g['grade_id'] ?>" onclick="return confirm('Удалить оценку?')">Удалить</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Оценки пока не выставлены.</p>
    <?php endif; ?>

</div>
</body>
</html>
