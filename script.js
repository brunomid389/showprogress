// Filtro
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

// Postar no feed
document.getElementById('submitPost').addEventListener('click', () => {
  const titulo = document.getElementById('ti').value.trim();
  const texto = document.getElementById('te').value.trim();

  if (!titulo && !texto) {
    alert('Digite algo para postar!');
    return;
  }

  const postContainer = document.getElementById('posts');
  const postDiv = document.createElement('div');
  postDiv.classList.add('post');
  postDiv.dataset.type = 'texto';

  if (titulo) {
    const h3 = document.createElement('h3');
    h3.textContent = censurar(titulo);
    postDiv.appendChild(h3);
  }

  if (texto) {
    const p = document.createElement('p');
    p.textContent = censurar(texto);
    postDiv.appendChild(p);
  }

  postContainer.prepend(postDiv);

  document.getElementById('ti').value = '';
  document.getElementById('te').value = '';
  postBox.classList.add('hidden');
  overlay.style.display = 'none';
});
