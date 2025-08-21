// ------------------ Filtros ------------------
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

// ------------------ Abrir/fechar caixa de post ------------------
const postBtn = document.getElementById('postBtn');
const postBox = document.getElementById('postBox');
const overlay = document.getElementById('overlay');

postBtn.addEventListener('click', () => {
  postBox.classList.remove('hidden');
  overlay.style.display = 'block';
});

// Fecha se clicar fora da caixa de post
document.addEventListener('click', (e) => {
  if (
    !postBox.classList.contains('hidden') && // só se estiver aberta
    !postBox.contains(e.target) && // clique não está dentro da caixa
    e.target !== postBtn // clique não foi no botão de abrir
  ) {
    postBox.classList.add('hidden');
    overlay.style.display = 'none';
  }
});


// ------------------ Censura ------------------
function censurar(texto) {
  const palavrasBanidas = [
    'boceta', 'porra', 'sexo', 'puta', 'caralho', 
    'buceta', 'merda', 'viado', 'foda', 'piranha', 'boquete'
  ];
  let censurado = texto;
  palavrasBanidas.forEach(palavra => {
    const regex = new RegExp(palavra, 'gi');
    censurado = censurado.replace(regex, '****');
  });
  return censurado;
}

// ------------------ Função de postar imagem ------------------
const imgIcon = document.getElementById('imgIcon');
const imageInput = document.getElementById('imageInput');
let selectedImage = null;

imgIcon.addEventListener('click', () => {
  imageInput.click();
});

imageInput.addEventListener('change', (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedImage = URL.createObjectURL(file);
  }
});

// ------------------ Postar no feed ------------------
document.getElementById('submitPost').addEventListener('click', () => {
  const titulo = document.getElementById('ti').value.trim();
  const texto = document.getElementById('te').value.trim();

  if (!titulo && !texto && !selectedImage) {
    alert('Digite algo ou selecione uma imagem para postar!');
    return;
  }

  const postContainer = document.getElementById('posts');
  const postDiv = document.createElement('div');
  postDiv.classList.add('post');
  postDiv.dataset.type = selectedImage ? 'imagem' : 'texto';

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

  if (selectedImage) {
    const img = document.createElement('img');
    img.src = selectedImage;
    img.alt = "Imagem postada";
    img.style.maxWidth = "100%";
    img.style.borderRadius = "10px";
    postDiv.appendChild(img);

    selectedImage = null;
    imageInput.value = "";
  }

  postContainer.prepend(postDiv);

  document.getElementById('ti').value = '';
  document.getElementById('te').value = '';
  document.getElementById('ta').value = '';
  postBox.classList.add('hidden');
  overlay.style.display = 'none';
});