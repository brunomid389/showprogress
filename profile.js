function toggleEdit() {
  const fields = document.querySelectorAll('.info-value');
  const btn = document.querySelector('.edit-btn');

  if (btn.textContent === 'Editar') {
    fields.forEach(f => f.setAttribute('contenteditable', 'true'));
    btn.textContent = 'Salvar';
  } else {
    fields.forEach(f => f.setAttribute('contenteditable', 'false'));
    btn.textContent = 'Editar';
    alert('Informações salvas!');
  }
}

function editProfilePic() {
  const newUrl = prompt('Insira a URL da nova foto de perfil:');
  if (newUrl) {
    document.querySelector('.profile-pic').src = newUrl;
  }
}
