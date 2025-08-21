function toggleEdit() {
  const fields = document.querySelectorAll('.info-value');
  const btn = document.querySelector('.edit-btn');

  if (btn.textContent === 'Editar') {
    fields.forEach(f => f.setAttribute('contenteditable', 'true'));
    btn.textContent = 'Salvar';
  } else {
    fields.forEach(f => f.setAttribute('contenteditable', 'false'));
    btn.textContent = 'Editar';

    // Atualiza inputs escondidos
    const form = document.getElementById('profileForm');
    form.querySelector('input[name="nome"]').value = fields[0].textContent.trim();
    form.querySelector('input[name="telefone"]').value = fields[1].textContent.trim();
    form.querySelector('input[name="email"]').value = fields[2].textContent.trim();
    form.querySelector('input[name="senha"]').value = fields[3].textContent.trim();
    form.querySelector('input[name="nascimento"]').value = fields[4].textContent.trim();

    form.submit(); // envia para atualizar no banco
  }
}

function editProfilePic() {
  const newUrl = prompt('Insira a URL da nova foto de perfil:');
  if (newUrl) {
    document.querySelector('.profile-pic').src = newUrl;
  }
}
