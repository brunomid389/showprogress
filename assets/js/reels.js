// reels.js
(() => {
  const form = document.getElementById("comment-form");
  const input = document.getElementById("comment-input");
  const list  = document.getElementById("comments-list");

  if (!form || !input || !list) {
    console.error("IDs esperados não encontrados no DOM.");
    return;
  }

  // chave única por página
  const STORAGE_KEY = "reels_comments:" + location.pathname;

  /* ---------- helpers ---------- */
  const loadComments = () => {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    } catch {
      return [];
    }
  };

  const saveComments = () => {
    const data = [...list.querySelectorAll(".comment-item")].map(el => ({
      text: el.querySelector(".comment-text")?.textContent || "",
      time: el.dataset.time || new Date().toISOString(),
    }));
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  };

  const addCommentToDOM = ({ text, time, author = "Você" }) => {
    const whenISO = time || new Date().toISOString();

    const item = document.createElement("div");
    item.className = "comment-item";
    item.dataset.time = whenISO;

    const avatar = document.createElement("img");
    avatar.className = "comment-avatar";
    avatar.alt = "avatar";
    avatar.width = 28;
    avatar.height = 28;
    // avatar placeholder distinto por horário
    avatar.src = "https://i.pravatar.cc/40?u=" + encodeURIComponent(whenISO);

    const body = document.createElement("div");
    body.className = "comment-body";

    const header = document.createElement("div");
    header.className = "comment-header";

    const name = document.createElement("span");
    name.className = "comment-author";
    name.textContent = author;

    const timeEl = document.createElement("time");
    timeEl.className = "comment-time";
    timeEl.dateTime = whenISO;
    timeEl.textContent = new Date(whenISO).toLocaleTimeString();

    header.append(name, timeEl);

    const textEl = document.createElement("p");
    textEl.className = "comment-text";
    textEl.textContent = text; // seguro contra HTML

    const del = document.createElement("button");
    del.type = "button";
    del.className = "comment-delete";
    del.title = "Excluir comentário";
    del.textContent = "×";
    del.addEventListener("click", () => {
      item.remove();
      saveComments();
    });

    body.append(header, textEl);
    item.append(avatar, body, del);
    list.appendChild(item);

    // rolar até o final
    list.scrollTop = list.scrollHeight;
  };

  /* ---------- eventos ---------- */
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;

    addCommentToDOM({ text });
    saveComments();
    input.value = "";
    input.focus();
  });

  // Enviar com Enter (Shift+Enter quebra linha)
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault();
      form.requestSubmit(); // dispara submit do form
    }
  });

  /* ---------- inicialização ---------- */
  loadComments().forEach(addCommentToDOM);
})();


