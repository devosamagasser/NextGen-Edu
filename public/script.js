// script.js
const chatForm     = document.getElementById('chatForm');
const chatInput    = document.getElementById('chatInput');
const chatMessages = document.getElementById('chatMessages');

function typeMessage(text, side = 'left', delay = 40) {
  const msg = document.createElement('div');
  msg.className = `message ${side}`;
  const bubble = document.createElement('div');
  bubble.className = 'bubble';
  msg.appendChild(bubble);
  chatMessages.appendChild(msg);
  chatMessages.scrollTop = chatMessages.scrollHeight;
  let i = 0;
  const timer = setInterval(() => {
    if (i < text.length) bubble.innerHTML += text.charAt(i++);
    else clearInterval(timer);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }, delay);
}

function appendMessage(text, side = 'right') {
  const msg = document.createElement('div');
  msg.className = `message ${side}`;
  const bubble = document.createElement('div');
  bubble.className = 'bubble';
  bubble.innerHTML = text;
  msg.appendChild(bubble);
  chatMessages.appendChild(msg);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

chatForm.addEventListener('submit', async e => {
  e.preventDefault();
  const text = chatInput.value.trim();
  if (!text) return;
  appendMessage(text, 'right');
  chatInput.value = '';

  // رسالة انتظار
  typeMessage('جاري المعالجة...', 'left');

  try {
    const res = await fetch('http://127.0.0.1:8001/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json'
      },
      body: JSON.stringify({ message: text })
    });
    const data = await res.json();

    console.log(data);

    // امسح “جاري المعالجة...”
    chatMessages.lastChild.remove();

    if (data.reply) {
      let replyText = data.reply.trim();
      if (replyText === '0') {
        replyText = 'لم أفهم سؤالك، الرجاء المحاولة بشكل أوضح.';
      }
      typeMessage(replyText, 'left');
    } else {
      typeMessage('لم يصل رد من الخادم.', 'left');
    }

  } catch (err) {
    chatMessages.lastChild.remove();
    typeMessage('حدث خطأ في الاتصال.', 'left');
    console.error(err);
  }
});
