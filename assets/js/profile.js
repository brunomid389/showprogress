function toggleEdit() {
  const inputs = document.querySelectorAll(".info-value");
  const saveBtn = document.getElementById("save-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const editBtn = document.querySelector(".edit-btn");
  const profilePic = document.getElementById("profile-pic");
  const fileInput = document.getElementById("profile-pic-input");

  // guarda imagem original se ainda não tiver
  if (!profilePic.dataset.original) {
    profilePic.dataset.original = profilePic.src;
  }

  // Ativa edição dos campos
  inputs.forEach(input => input.disabled = false);
  saveBtn.style.display = "inline-block";
  cancelBtn.style.display = "inline-block";
  editBtn.style.display = "none";
  fileInput.style.display = "block";

  // Preview da imagem nova
  fileInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (event) => {
        profilePic.src = event.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
}

function cancelEdit() {
  const inputs = document.querySelectorAll(".info-value");
  const saveBtn = document.getElementById("save-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const editBtn = document.querySelector(".edit-btn");
  const profilePic = document.getElementById("profile-pic");
  const fileInput = document.getElementById("profile-pic-input");

  // Volta os inputs para desativados
  inputs.forEach(input => input.disabled = true);

  // Esconde botões e input
  saveBtn.style.display = "none";
  cancelBtn.style.display = "none";
  editBtn.style.display = "inline-block";
  fileInput.style.display = "none";

  // restaura imagem original
  if (profilePic.dataset.original) {
    profilePic.src = profilePic.dataset.original;
  }
}

// Confirmação antes de salvar
document.querySelector("form").addEventListener("submit", function (e) {
  const confirmSave = confirm("Deseja salvar as alterações?");
  if (!confirmSave) {
    e.preventDefault();
    cancelEdit();
  }
});