async function sendMessage() {
  const input = document.getElementById('messageInput');
  const message = input.value.trim();

  if (message === '') return;

  // Envia para o PHP
  await fetch("chat.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "mensagem=" + encodeURIComponent(message)
  });

  input.value = '';

  // Atualiza o chat
  loadMessages();
}

async function loadMessages() {
  const res = await fetch("chat.php");
  const mensagens = await res.json();

  const chatMessages = document.getElementById('chatMessages');
  chatMessages.innerHTML = "";

  mensagens.forEach(m => {
    addMessage(m.nome, m.texto, true); // como só mostra o usuário logado, sempre será "true"
  });

  chatMessages.scrollTop = chatMessages.scrollHeight;
}

function addMessage(sender, text, isUser) {
  const chatMessages = document.getElementById('chatMessages');

  const msgElement = document.createElement('div');
  msgElement.classList.add('message', isUser ? 'user-message' : 'other-message');

  const nameElement = document.createElement('div');
  nameElement.classList.add('sender-name');
  nameElement.textContent = sender;

  msgElement.appendChild(nameElement);
  msgElement.appendChild(document.createTextNode(text));

  chatMessages.appendChild(msgElement);
}

// Carrega ao abrir
document.addEventListener("DOMContentLoaded", loadMessages);