<?php
// Renvoie la liste des fichiers .mp4 si la requête AJAX est faite
if (isset($_GET['list_files'])) {
    $dir = __DIR__ . '/documents';
    $allFiles = array_diff(scandir($dir), array('.', '..'));
    $mp4Files = array_values(array_filter($allFiles, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'mp4';
    }));
    echo json_encode($mp4Files);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Document sécurisé</title>
  <style>
    body {
      margin: 0;
      background: #000;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
      color: white;
    }

    video {
      max-width: 100%;
      max-height: 90%;
      pointer-events: none;
    }

    #fileSelector {
      margin-top: 10px;
      font-size: 0.8rem;
      background: #222;
      color: white;
      border: 1px solid #555;
      padding: 4px 8px;
      border-radius: 4px;
    }
  </style>

  <script>
    // Empêche clic droit
    document.addEventListener('contextmenu', e => e.preventDefault());

    // Auto-fermeture après 3 minutes
    setTimeout(() => {
      document.body.innerHTML = "<h2 style='color:white'>Session expirée</h2>";
    }, 180000);

    // Charge les fichiers .mp4 et alimente le menu
    async function loadFileList() {
      try {
        const response = await fetch('?list_files=1');
        if (!response.ok) throw new Error('Erreur réseau');
        const files = await response.json();

        const select = document.getElementById('fileSelector');
        files.forEach(file => {
          const option = document.createElement('option');
          option.value = 'documents/' + file;
          option.textContent = file;
          select.appendChild(option);
        });

        select.addEventListener('change', e => {
          const selectedFile = e.target.value;
          if (selectedFile) {
            const video = document.getElementById('secureVideo');
            video.src = selectedFile;
            video.load();
            video.play();
          }
        });

      } catch (error) {
        console.error('Erreur chargement fichiers:', error);
      }
    }

    window.onload = loadFileList;
  </script>
</head>

<body>
  <video id="secureVideo" src="secure_decomposed.mp4" autoplay loop muted playsinline></video>
  <select id="fileSelector">
    <option value="">-- Choisir un document --</option>
  </select>
</body>
</html>