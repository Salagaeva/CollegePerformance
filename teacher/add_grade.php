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

// Получаем предметы, к которым привязан преподаватель
$teacher_subjects = $conn->query("
    SELECT ts.subject_id, s.name AS subject_name
    FROM teacher_subjects ts
    JOIN subjects s ON ts.subject_id = s.id
    WHERE ts.teacher_id = $teacher_id
    ORDER BY s.name
");

// Формируем массив доступных предметов
$allowed_subjects = [];
while ($row = $teacher_subjects->fetch_assoc()) {
    $allowed_subjects[$row['subject_id']] = $row['subject_name'];
}

// Получаем всех студентов с ФИО и группой
$students = $conn->query("
    SELECT st.id, st.full_name, g.name AS group_name
    FROM students st
    LEFT JOIN groups_college g ON st.group_id = g.id
    ORDER BY st.full_name
");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];
    $date = date("Y-m-d");

    // Проверка, что выбранный предмет действительно у преподавателя
    if (!array_key_exists($subject_id, $allowed_subjects)) {
        $message = "Ошибка: вы не можете ставить оценки по этому предмету!";
    } else {
        $stmt = $conn->prepare("INSERT INTO grades (student_id, subject_id, teacher_id, grade, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $student_id, $subject_id, $teacher_id, $grade, $date);

        if ($stmt->execute()) {
            $message = "Оценка добавлена!";
        } else {
            $message = "Ошибка при добавлении оценки: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выставить оценку</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Добавить оценку</h2>

    <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>

    <form method="POST">
        <label>Студент:</label>
        <select name="student_id" required>
            <option value="">Выберите студента</option>
            <?php while($student = $students->fetch_assoc()): ?>
                <option value="<?= $student['id'] ?>">
                    <?= htmlspecialchars($student['full_name'] . " (" . ($student['group_name'] ?? '-') . ")") ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Предмет:</label>
        <select name="subject_id" required>
            <option value="">Выберите предмет</option>
            <?php foreach ($allowed_subjects as $id => $name): ?>
                <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Оценка (2-5):</label>
        <input type="number" name="grade" min="2" max="5" required>

        <button type="submit">Сохранить</button>
    </form>

    <br>
    <a href="teacher_panel.php">Назад</a>
</div>
</body>
</html>

