const chatForm     = document.getElementById('chatForm');
const chatInput    = document.getElementById('chatInput');
const chatMessages = document.getElementById('chatMessages');
const userId       = chatForm.querySelector('input[name="id"]').value; // ✅ مهم

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
  bubble.innerHTML = `${text} <i class="bi bi-person-circle me-2"></i> `;
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

  // رسالة مؤقتة
  const loadingMessage = document.createElement('div');
  loadingMessage.className = 'message left';
  loadingMessage.innerHTML = `  <div class="bubble d-flex align-items-center">
    <div class="spinner-border text-secondary me-2" role="status" style="width: 1.2rem; height: 1.2rem;"></div>
  </div>`;
  chatMessages.appendChild(loadingMessage);
  chatMessages.scrollTop = chatMessages.scrollHeight;

  try {
    const res = await fetch('https://nextgenedu-database.azurewebsites.net/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        // لو عايز تبعت توكن:
        // 'Authorization': 'Bearer ' + token
      },
      body: JSON.stringify({ 
        message: text,
        id: userId // ✅ إضافة مهمة
      })
    });

    const data = await res.json();

    if (data.reply) {
      chatMessages.removeChild(loadingMessage);
      const code = data.code ?? 0;

      switch (code) {
        case 1: // الجدول الدراسي
          renderSchedule(data.reply);
          break;

        case 7: // المواد الدراسية
          renderCourses(data.reply);
          break;

        default:
          console.log(data.reply)
          let replyText = typeof data.reply === 'string' ? data.reply.trim() : 'لم أفهم سؤالك، الرجاء المحاولة بشكل أوضح.';
          typeMessage(replyText, 'left');
      }
    } else {
      chatMessages.removeChild(loadingMessage);
      typeMessage('لم يصل رد من الخادم.', 'left');
    }


  } catch (err) {
    chatMessages.removeChild(loadingMessage);
    typeMessage('حدث خطأ في الاتصال.', 'left');
    console.error(err);
  }

function renderCourses(courses) {
  if (!Array.isArray(courses) || courses.length === 0) {
    return typeMessage('لا توجد مواد مسجلة لهذا الترم.', 'left');
  }

  const wrapper = document.createElement('div');
  wrapper.className = 'bubble';

  // استخدام Bootstrap card
  const card = document.createElement('div');
  card.className = 'card w-100';

  const cardHeader = document.createElement('div');
  cardHeader.className = 'card-header text-white bg-primary';
  cardHeader.textContent = 'المواد المسجلة';

  const listGroup = document.createElement('ul');
  listGroup.className = 'list-group list-group-flush';

  courses.forEach(course => {
    const item = document.createElement('li');
    item.className = 'list-group-item';
    item.textContent = `${course.name} - ${course.code}`;
    listGroup.appendChild(item);
  });

  card.appendChild(cardHeader);
  card.appendChild(listGroup);
  wrapper.appendChild(card);

  const msg = document.createElement('div');
  msg.className = 'message left';
  msg.appendChild(wrapper);
  chatMessages.appendChild(msg);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}


function renderSchedule(scheduleData) {
  if (!Array.isArray(scheduleData) || scheduleData.length === 0) {
    return typeMessage('لا يوجد جدول دراسي متاح حالياً.', 'left');
  }

  const schedule = scheduleData[0];
  const sessions = schedule.sessions;

  const wrapper = document.createElement('div');
  wrapper.className = 'bubble';

  const responsiveWrapper = document.createElement('div');
  responsiveWrapper.className = 'table-responsive ';
  responsiveWrapper.style.overflowX = 'auto';
  responsiveWrapper.style.direction = 'rtl';

  const table = document.createElement('table');
  table.className = 'table table-bordered table-striped text-center align-middle';
  table.style.marginBottom = '0';
  table.style.width = '100%';

  const thead = document.createElement('thead');
  thead.innerHTML = `
    <tr class="table-dark">
      <th>المادة</th>
      <th>النوع</th>
      <th>اليوم</th>
      <th>الوقت</th>
      <th>القاعة</th>
      <th>المبنى</th>
    </tr>
  `;
  table.appendChild(thead);

  const tbody = document.createElement('tbody');

  for (const day in sessions) {
    sessions[day].forEach(session => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${session.course ?? '-'}</td>
        <td>${session.type ?? '-'}</td>
        <td>${day}</td>
        <td>${session.from}</td>
        <td>${session.hall?.hall_name ?? '-'}</td>
        <td>${session.hall?.building ?? '-'}</td>
      `;
      tbody.appendChild(tr);
    });
  }

  table.appendChild(tbody);
  responsiveWrapper.appendChild(table);
  wrapper.appendChild(responsiveWrapper);

  const msg = document.createElement('div');
  msg.className = 'message left';
  msg.appendChild(wrapper);
  chatMessages.appendChild(msg);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}



});
