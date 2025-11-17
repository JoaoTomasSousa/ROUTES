<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registar — routes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-300 min-h-screen flex flex-col items-center justify-center">

  <!-- LOGO -->
  <a href="index.html" class="mb-6">
    <img src="logo.png" alt="routes logo" class="h-20 w-auto mx-auto hover:scale-105 transition">
  </a>

  <!-- CARD -->
  <div class="bg-white rounded-2xl shadow-lg border border-slate-200 w-full max-w-sm p-6 space-y-4">
    <h1 class="text-xl font-semibold text-slate-900 text-center mb-1">Criar Conta</h1>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Nome</label>
      <input
        id="name"
        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400"
        placeholder="O seu nome"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
      <input
        id="email"
        type="email"
        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400"
        placeholder="voce@exemplo.com"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Palavra-passe</label>
      <input
        id="pass"
        type="password"
        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400"
        placeholder="Crie uma palavra-passe"
      />
    </div>

    <button
      id="btn-reg"
      class="w-full bg-emerald-500 hover:bg-emerald-600 text-emerald-950 font-semibold rounded-xl py-2 text-sm transition"
    >
      Registar
    </button>

    <p class="text-xs text-slate-600 text-center pt-1">
      Já tem conta?
      <a href="login.html" class="text-emerald-700 font-semibold hover:underline">Login</a>
    </p>
  </div>

  <script>
    function getUsers() {
      return JSON.parse(localStorage.getItem("routes_users")) || [];
    }
    function setUsers(list) {
      localStorage.setItem("routes_users", JSON.stringify(list));
    }
    function setCurrentUser(u) {
      localStorage.setItem("routes_current_user", JSON.stringify(u));
    }

    document.getElementById("btn-reg").onclick = () => {
      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim().toLowerCase();
      const pass = document.getElementById("pass").value.trim();

      if (!name || !email || !pass)
        return alert("Preencha todos os campos.");

      const users = getUsers();
      if (users.some(u => u.email === email)) {
        return alert("Este email já está registado.");
      }

      const newUser = { name, email, pass };
      users.push(newUser);
      setUsers(users);
      setCurrentUser(newUser);

      alert("Conta criada!");
      window.location.href = "index.html";
    };
  </script>
</body>
</html>
