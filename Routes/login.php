<?php
session_start();

function getUsers(): array {
    if (!file_exists('users.json')) return [];
    $json = file_get_contents('users.json');
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function setCurrentUser(array $user): void {
    $_SESSION['user'] = [
        'name'  => $user['name'],
        'email' => $user['email']
    ];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = trim($_POST['pass'] ?? '');

    $users = getUsers();
    foreach ($users as $u) {
        if (strtolower($u['email']) === $email && $u['pass'] === $pass) {
            setCurrentUser($u);
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Credenciais inválidas.';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login — routes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-300 min-h-screen flex flex-col items-center justify-center">

  <!-- LOGO -->
  <a href="index.php" class="mb-6">
    <img src="logo.png" alt="routes logo" class="h-20 w-auto mx-auto hover:scale-105 transition">
  </a>

  <!-- CARD -->
  <div class="bg-white rounded-2xl shadow-lg border border-slate-200 w-full max-w-sm p-6 space-y-4">
    <h1 class="text-xl font-semibold text-slate-900 text-center mb-1">Entrar</h1>

    <?php if ($error): ?>
      <p class="text-sm text-red-600 text-center"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="space-y-3">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
        <input
          name="email"
          type="email"
          class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400"
          placeholder="voce@exemplo.com"
          required
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Palavra-passe</label>
        <input
          name="pass"
          type="password"
          class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400"
          placeholder="•••••••"
          required
        />
      </div>

      <button
        type="submit"
        class="w-full bg-emerald-500 hover:bg-emerald-600 text-emerald-950 font-semibold rounded-xl py-2 text-sm transition"
      >
        Entrar
      </button>
    </form>

    <p class="text-xs text-slate-600 text-center pt-1">
      Não tem conta?
      <a href="registar.php" class="text-emerald-700 font-semibold hover:underline">Registar</a>
    </p>
  </div>

</body>
</html>
