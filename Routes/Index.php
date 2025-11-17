<?php
session_start();

// Logout simples via query string ?logout=1
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>routes</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Leaflet CSS -->
  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  />

  <style>
    #map { height: 70vh; }
  </style>
</head>
<body class="bg-gray-300 min-h-screen flex flex-col">

  <!-- HEADER -->
  <header class="bg-slate-900 text-white">
    <nav class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-2">
        <img src="logo.png" alt="routes logo" class="h-14 w-auto">
      </a>

      <div class="flex items-center gap-3">
        <?php if ($user): ?>
          <span class="text-sm text-slate-100">
            Olá, <strong><?= htmlspecialchars($user['name']) ?></strong>
          </span>
          <a
            href="index.php?logout=1"
            class="px-3 py-1.5 rounded-lg border border-white/30 text-xs font-medium hover:bg-white/10 transition"
          >
            Terminar sessão
          </a>
        <?php else: ?>
          <a
            href="login.php"
            class="px-3 py-1.5 rounded-lg border border-white/30 text-xs font-medium hover:bg-white/10 transition"
          >
            Login
          </a>
          <a
            href="registar.php"
            class="px-3 py-1.5 rounded-lg bg-emerald-500 text-emerald-950 text-xs font-semibold hover:bg-emerald-600 transition"
          >
            Registar
          </a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <!-- MAIN -->
  <main class="flex-1 flex justify-center px-4 py-6">
    <section class="bg-white rounded-2xl shadow-lg border border-slate-200 w-full max-w-3xl p-5 space-y-4">
      <!-- Form -->
      <div>
        <label for="local" class="block text-sm font-semibold text-slate-800 mb-1">
          Introduza o seu local ou cidade
        </label>
        <input
          id="local"
          class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
          placeholder="Ex.: Lisboa, Porto, Faro..."
        />
        <button
          id="btn-procurar"
          class="mt-3 w-full rounded-xl bg-emerald-500 hover:bg-emerald-600 text-emerald-950 font-semibold py-2 text-sm transition"
        >
          Mostrar pontos turísticos
        </button>
        <p id="info" class="mt-2 text-sm text-slate-600"></p>
      </div>

      <!-- Mapa -->
      <div id="map" class="w-full rounded-2xl border border-slate-200 overflow-hidden"></div>

      <!-- Cidades -->
      <div class="pt-2 border-t border-slate-200 space-y-2">
        <h3 class="font-semibold text-slate-800">Pontos turísticos em cidades principais</h3>
        <p class="text-xs text-slate-500">
          Sugestões rápidas que pode explorar com o mapa:
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-slate-700">
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
            <h4 class="font-semibold mb-1">Lisboa</h4>
            <ul class="list-disc list-inside space-y-0.5">
              <li>Torre de Belém</li>
              <li>Mosteiro dos Jerónimos</li>
              <li>Castelo de São Jorge</li>
              <li>Praça do Comércio</li>
            </ul>
          </div>
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
            <h4 class="font-semibold mb-1">Porto</h4>
            <ul class="list-disc list-inside space-y-0.5">
              <li>Ribeira do Porto</li>
              <li>Ponte Dom Luís I</li>
              <li>Livraria Lello</li>
              <li>Torre dos Clérigos</li>
            </ul>
          </div>
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
            <h4 class="font-semibold mb-1">Faro</h4>
            <ul class="list-disc list-inside space-y-0.5">
              <li>Cidade Velha de Faro</li>
              <li>Igreja do Carmo</li>
              <li>Marina de Faro</li>
              <li>Ilha de Faro</li>
            </ul>
          </div>
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
            <h4 class="font-semibold mb-1">Coimbra</h4>
            <ul class="list-disc list-inside space-y-0.5">
              <li>Universidade de Coimbra</li>
              <li>Biblioteca Joanina</li>
              <li>Santa Clara-a-Velha</li>
              <li>Quinta das Lágrimas</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
    /* ---------- MAPA + POIs ---------- */
    const map = L.map('map').setView([38.7223, -9.1393], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contribuidores'
    }).addTo(map);

    const markersLayer = L.layerGroup().addTo(map);
    const infoEl = document.getElementById("info");
    const btnProcurar = document.getElementById("btn-procurar");

    async function geocode(q) {
      const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1`;
      const res = await fetch(url);
      const data = await res.json();
      if (!data.length) throw new Error("Local não encontrado");
      return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
    }

    async function fetchPOIs(lat, lon) {
      const apiKey = "5ae2e3f221c38a28845f05b6";
      const url = `https://api.opentripmap.com/0.1/pt/places/radius?radius=2000&lon=${lon}&lat=${lat}&kinds=interesting_places,museums,monuments,architecture,castles,historic&limit=20&apikey=${apiKey}`;
      const res = await fetch(url);
      return res.json();
    }

    btnProcurar.addEventListener("click", async () => {
      const q = document.getElementById("local").value.trim();
      if (!q) {
        alert("Introduza o local ou cidade.");
        return;
      }

      infoEl.textContent = "A procurar pontos de interesse...";
      btnProcurar.disabled = true;

      try {
        const [lat, lon] = await geocode(q);
        map.setView([lat, lon], 14);
        markersLayer.clearLayers();

        const pois = await fetchPOIs(lat, lon);
        if (!Array.isArray(pois) || !pois.length) {
          infoEl.textContent = "Nenhum ponto turístico encontrado por perto.";
          return;
        }

        pois.forEach(p => {
          if (!p || !p.point) return;
          const { lat: latP, lon: lonP } = p.point;

          const marker = L.marker([latP, lonP]).addTo(markersLayer);
          marker.bindPopup(
            `<strong>${p.name || "Ponto turístico"}</strong><br><small>${p.kinds || ""}</small>`
          );

          marker.on("click", () => {
            map.flyTo([latP, lonP], 18, { duration: 1.2 });
          });
        });

        infoEl.textContent = `${pois.length} pontos turísticos encontrados!`;
      } catch (err) {
        console.error(err);
        infoEl.textContent = "Erro: " + err.message;
      } finally {
        btnProcurar.disabled = false;
      }
    });
  </script>
</body>
</html>
