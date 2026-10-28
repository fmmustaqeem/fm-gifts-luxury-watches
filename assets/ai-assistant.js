(() => {
  const panel = document.getElementById("fmAiPanel");
  const toggle = document.getElementById("fmAiToggle");
  const close = document.getElementById("fmAiClose");
  const form = document.getElementById("fmAiForm");
  const input = document.getElementById("fmAiInput");
  const log = document.getElementById("fmAiMessages");
  const status = document.getElementById("fmAiStatus");
  if (!panel || !toggle || !form || !input || !log || !status) return;

  const messages = [];
  let sending = false;
  const welcome = "Assalam-o-alaikum! I can help you explore watches, gifts, fragrances and accessories. Catalogue listings may be demos; our team confirms stock and final prices.";
  function addMessage(role, text) {
    const item = document.createElement("div");
    item.className = "fm-ai-message " + (role === "user" ? "is-user" : "is-assistant");
    item.textContent = text;
    log.append(item);
    log.scrollTop = log.scrollHeight;
  }
  function setOpen(open) {
    panel.hidden = !open;
    toggle.setAttribute("aria-expanded", String(open));
    if (open) input.focus();
  }
  toggle.addEventListener("click", () => setOpen(panel.hidden));
  if (close) close.addEventListener("click", () => { setOpen(false); toggle.focus(); });
  document.addEventListener("keydown", event => {
    if (event.key === "Escape" && !panel.hidden) { setOpen(false); toggle.focus(); }
  });
  addMessage("assistant", welcome);

  form.addEventListener("submit", async event => {
    event.preventDefault();
    const text = input.value.trim();
    if (!text || sending) return;
    input.value = "";
    messages.push({role: "user", content: text});
    addMessage("user", text);
    sending = true;
    status.textContent = "Thinking…";
    const submit = form.querySelector('button[type="submit"]');
    if (submit) submit.disabled = true;
    try {
      const response = await fetch("api/ai-chat.php", {
        method: "POST",
        headers: {"Content-Type": "application/json", "Accept": "application/json"},
        body: JSON.stringify({messages: messages.slice(-8)})
      });
      const data = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(data.error || "The assistant is temporarily unavailable.");
      const reply = String(data.reply || "").slice(0, 5000);
      messages.push({role: "assistant", content: reply});
      addMessage("assistant", reply);
      status.textContent = "AI can help; store team confirms stock and orders.";
    } catch (error) {
      addMessage("assistant", (error && error.message) || "Sorry, please try again or contact us on WhatsApp.");
      status.textContent = "Assistant unavailable";
    } finally {
      sending = false;
      if (submit) submit.disabled = false;
      input.focus();
    }
  });
})();