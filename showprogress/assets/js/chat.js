let username = "Você"; // pode ser dinâmico se quiser pedir ao usuário

function sendMessage() {
  const input = document.getElementById('messageInput');
  const message = input.value.trim();
  
  if (message === '') return;

  // Adiciona mensagem do usuário
  addMessage(username, message, true);

  input.value = '';

  // Simula outro usuário respondendo (somente para teste local)
  setTimeout(() => {
    const otherUsers = ["Maria", "Carlos", "Ana"];
    const randomUser = otherUsers[Math.floor(Math.random() * otherUsers.length)];
    addMessage(randomUser, "Mensagem de teste!", false);
  }, 1000);
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

  // Scroll automático para o final
  chatMessages.scrollTop = chatMessages.scrollHeight;
}
