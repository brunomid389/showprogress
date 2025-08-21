const filterButtons = document.querySelectorAll('.filter-buttons button');
const posts = document.querySelectorAll('.post');

filterButtons.forEach(button => {
  button.addEventListener('click', () => {
    filterButtons.forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    const filter = button.textContent.toLowerCase();
    posts.forEach(post => {
      const type = post.dataset.type || 'texto';
      post.style.display = (filter === 'todos' || filter === type) ? 'block' : 'none';
    });
  });
});
document.querySelector('.filter-buttons button').click();

const postBtn = document.getElementById('postBtn');
const postBox = document.getElementById('postBox');
const overlay = document.getElementById('overlay');

postBtn.addEventListener('click', () => {
  postBox.classList.remove('hidden');
  overlay.style.display = 'block';
});

document.addEventListener('click', (e) => {
  if (!postBox.classList.contains('hidden') &&
      !postBox.contains(e.target) &&
      e.target !== postBtn) {
    postBox.classList.add('hidden');
    overlay.style.display = 'none';
  }
});

// ------------------ Censura ------------------
function censurar(texto) {
  const palavrasBanidas = ['boceta','porra','sexo','puta','caralho','buceta','merda','viado','foda','piranha','boquete'];
  let censurado = texto;
  palavrasBanidas.forEach(palavra => {
    censurado = censurado.replace(new RegExp(palavra,'gi'),'****');
  });
  return censurado;
}

// ------------------ Postar com imagem ------------------
const imgIcon = document.getElementById('imgIcon');
const imageInput = document.getElementById('imageInput');

imgIcon.addEventListener('click', () => imageInput.click());

document.getElementById('submitPost').addEventListener('click', async () => {
  const titulo = document.getElementById('ti').value.trim();
  const texto = document.getElementById('te').value.trim();
  const tag = document.getElementById('ta').value.trim() || 'Nacional';

  if (!titulo && !texto && !imageInput.files[0]) {
    alert('Digite algo ou selecione uma imagem!');
    return;
  }

  const formData = new FormData();
  formData.append('titulo', censurar(titulo));
  formData.append('texto', censurar(texto));
  formData.append('tag', tag);
  if (imageInput.files[0]) formData.append('imagem', imageInput.files[0]);

  try {
    const res = await fetch('postar.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) location.reload();
    else alert('Erro ao postar!');
  } catch (err) {
    console.error(err);
    alert('Erro ao enviar postagem.');
  }
});
