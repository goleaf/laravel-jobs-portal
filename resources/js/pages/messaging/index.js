document.addEventListener('DOMContentLoaded', () => {
  const messagesContainer = document.getElementById('messagesContainer');
  if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  // Conversation switching
  const conversationItems = document.querySelectorAll('.conversation-item');
  conversationItems.forEach((item) => {
    item.addEventListener('click', function () {
      conversationItems.forEach((i) => {
        i.classList.remove('bg-indigo-50', 'border-l-4', 'border-indigo-500');
        i.classList.add('hover:bg-gray-50');
      });
      this.classList.add('bg-indigo-50', 'border-l-4', 'border-indigo-500');
      this.classList.remove('hover:bg-gray-50');
    });
  });

  const messageInput = document.getElementById('messageInput');

  function appendMessage(message) {
    if (!messagesContainer) return;
    const messageElement = document.createElement('div');
    messageElement.className = 'flex items-start space-x-3 justify-end';
    messageElement.innerHTML = `
      <div class="flex-1 flex justify-end">
        <div class="bg-indigo-600 rounded-lg p-3 max-w-xs">
          <p class="text-sm text-white">${message}</p>
        </div>
      </div>
      <img src="/images/avatar.png" alt="You" class="w-8 h-8 rounded-full">
    `;
    messagesContainer.appendChild(messageElement);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  function sendMessage() {
    const text = (messageInput?.value || '').trim();
    if (!text) return;
    // TODO: integrate with backend API
    appendMessage(text);
    if (messageInput) messageInput.value = '';
  }

  // Delegated click for send button
  document.body.addEventListener('click', (e) => {
    const target = e.target.closest('[data-action="message-send"]');
    if (!target) return;
    e.preventDefault();
    sendMessage();
  });

  // Enter to send (Shift+Enter for newline)
  if (messageInput) {
    messageInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });
  }
});


