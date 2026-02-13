<?php
include "../includes/auth.php";
include "../config.php";

if ($_SESSION['role'] != 'admin') {
    die("Доступ запрещен");
}

$success = "";
$error = "";

// Получаем группы и предметы для формы
$groups = $conn->query("SELECT * FROM groups_college ORDER BY name");
$subjects = $conn->query("SELECT * FROM subjects ORDER BY name");

// Добавление нового пользователя
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $login = $_POST['login'];
    $full_name = $_POST['full_name'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : NULL;      // для студента/опц. для преподавателя
    $subject_id = !empty($_POST['subject_id']) ? $_POST['subject_id'] : NULL; // для преподавателя

    // Проверка уникальности логина
    $check = $conn->prepare("SELECT id FROM users WHERE login = ?");
    $check->bind_param("s", $login);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $error = "Пользователь с таким логином уже существует!";
    } else {
        // Добавляем в users
        $stmt = $conn->prepare("INSERT INTO users (login, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $login, $password, $role);
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;

            // Добавляем студента
            if ($role == 'student') {
                $stmt2 = $conn->prepare("INSERT INTO students (user_id, full_name, group_id) VALUES (?, ?, ?)");
                $stmt2->bind_param("isi", $user_id, $full_name, $group_id);
                $stmt2->execute();
            }
            // Добавляем преподавателя и связываем с предметом и группой
            elseif ($role == 'teacher') {
                $stmt2 = $conn->prepare("INSERT INTO teachers (user_id, full_name) VALUES (?, ?)");
                $stmt2->bind_param("is", $user_id, $full_name);
                $stmt2->execute();
                $teacher_id = $conn->insert_id;

                if ($subject_id) {
                    $stmt3 = $conn->prepare("INSERT INTO teacher_subjects (teacher_id, subject_id, group_id) VALUES (?, ?, ?)");
                    $stmt3->bind_param("iii", $teacher_id, $subject_id, $group_id);
                    $stmt3->execute();
                }
            }

            $success = "Пользователь добавлен!";
        } else {
            $error = "Ошибка при добавлении пользователя: " . $conn->error;
        }
    }
    $check->close();
}

// Удаление пользователя
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: admin_panel.php");
    exit();
}

// Получение списка всех пользователей
$result = $conn->query("SELECT * FROM users ORDER BY role, id");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Панель администратора</h2>

    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <h3>Добавить нового пользователя</h3>
    <form method="POST">
        <input type="text" name="login" placeholder="Логин" required>
        <input type="text" name="full_name" placeholder="ФИО" required>
        <input type="password" name="password" placeholder="Пароль" required>

        <select name="role" id="role_select" required>
            <option value="">Выберите роль</option>
            <option value="student">Студент</option>
            <option value="teacher">Преподаватель</option>
            <option value="admin">Администратор</option>
        </select>

        <!-- Для студента: выбор группы -->
        <div id="student_fields" style="display:none; margin-top:10px;">
            <label>Группа:</label>
            <select name="group_id">
                <option value="">Выберите группу</option>
                <?php
                $groups->data_seek(0);
                while($group = $groups->fetch_assoc()): ?>
                    <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Для преподавателя: выбор предмета и опциональной группы -->
        <div id="teacher_fields" style="display:none; margin-top:10px;">
            <label>Предмет:</label>
            <select name="subject_id">
                <option value="">Выберите предмет</option>
                <?php
                $subjects->data_seek(0);
                while($subject = $subjects->fetch_assoc()): ?>
                    <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Группа (опционально):</label>
            <select name="group_id">
                <option value="">Выберите группу</option>
                <?php
                $groups->data_seek(0);
                while($group = $groups->fetch_assoc()): ?>
                    <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" name="add_user">Добавить</button>
    </form>

    <h3>Список пользователей</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>ФИО / Логин</th>
            <th>Роль</th>
            <th>Действие</th>
        </tr>
        <?php while($user = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['login']) ?></td>
            <td><?= $user['role'] ?></td>
            <td>
                <?php if($user['id'] != $_SESSION['user_id']): ?>
                    <a href="?delete_user=<?= $user['id'] ?>" onclick="return confirm('Удалить пользователя?')">Удалить</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="../logout.php" class="btn">Выйти</a>
</div>

<script>
// Показать/скрыть поля в зависимости от роли
document.getElementById('role_select').addEventListener('change', function() {
    let role = this.value;
    document.getElementById('student_fields').style.display = role === 'student' ? 'block' : 'none';
    document.getElementById('teacher_fields').style.display = role === 'teacher' ? 'block' : 'none';
});
</script>

</body>
</html>


