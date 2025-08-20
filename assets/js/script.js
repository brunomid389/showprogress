// Filtro
const filterButtons = document.querySelectorAll('.filter-buttons button');

filterButtons.forEach(button => {
  button.addEventListener('click', () => {
    filterButtons.forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');

    const filter = button.textContent.toLowerCase();

    document.querySelectorAll('.post').forEach(post => {
      const type = post.dataset.type || 'texto';
      post.style.display = (filter === 'todos' || filter === type) ? 'block' : 'none';
    });
  });
});

document.querySelector('.filter-buttons button').click();

// Abrir/fechar caixa de post
const postBtn = document.getElementById('postBtn');
const postBox = document.getElementById('postBox');
const overlay = document.getElementById('overlay');

postBtn.addEventListener('click', () => {
  postBox.classList.remove('hidden');
  overlay.style.display = 'block';
});

overlay.addEventListener('click', () => {
  postBox.classList.add('hidden');
  overlay.style.display = 'none';
});

// Função de censura
function censurar(texto) {
  const palavrasBanidas = ['boceta', 'porra', 'sexo', 'puta', 'caralho', 'buceta', 'merda', 'viado', 'foda', 'piranha', 'boquete'];
  let censurado = texto;
  palavrasBanidas.forEach(palavra => {
    const regex = new RegExp(palavra, 'gi');
    censurado = censurado.replace(regex, '****');
  });
  return censurado;
}

// Postar no feed (texto, imagem, vídeo)
document.getElementById('submitPost').addEventListener('click', async () => {
  const titulo = document.getElementById('ti').value.trim();
  const texto = document.getElementById('te').value.trim();
  const tag = document.getElementById('ta').value.trim() || 'Nacional';

  const imgInput = document.getElementById('imgPost'); // input file de imagem
  const videoInput = document.getElementById('videoPost'); // input file de vídeo
  const imgUrl = document.getElementById('imgUrl').value.trim(); // URL opcional
  const videoUrl = document.getElementById('videoUrl').value.trim(); // URL opcional

  if (!titulo && !texto && !imgInput.files.length && !videoInput.files.length && !imgUrl && !videoUrl) {
    alert('Digite algo ou selecione um arquivo para postar!');
    return;
  }

  const formData = new FormData();
  formData.append('titulo', censurar(titulo));
  formData.append('texto', censurar(texto));
  formData.append('tag', tag);

  // Imagem
  if (imgInput.files.length > 0) {
    formData.append('imagem', imgInput.files[0]);
  } else if (imgUrl) {
    formData.append('img_url', imgUrl);
  }

  // Vídeo
  if (videoInput.files.length > 0) {
    formData.append('video', videoInput.files[0]);
  } else if (videoUrl) {
    formData.append('video_url', videoUrl);
  }

  try {
    const res = await fetch('postar.php', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (data.success) {
      location.reload();
    } else {
      alert('Erro ao postar');
    }
  } catch (err) {
    console.error(err);
    alert('Erro ao enviar a postagem.');
  }
});


// Postar no banco
document.getElementById('submitPost').addEventListener('click', async () => {
  const titulo = document.getElementById('ti').value.trim();
  const texto = document.getElementById('te').value.trim();
  const tag = document.getElementById('ta').value.trim() || 'Nacional';

  if (!titulo && !texto) {
    alert('Digite algo para postar!');
    return;
  }

  const formData = new FormData();
  formData.append('titulo', censurar(titulo));
  formData.append('texto', censurar(texto));
  formData.append('tag', tag);

  const res = await fetch('postar.php', {
    method: 'POST',
    body: formData
  });

  const data = await res.json();

  if (data.success) {
    location.reload();
  } else {
    alert('Erro ao postar');
  }
});
