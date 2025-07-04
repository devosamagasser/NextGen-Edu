const chatForm = document.getElementById('chatForm');
const chatInput = document.getElementById('chatInput');
const chatMessages = document.getElementById('chatMessages');
const userId = chatForm.querySelector('input[name="id"]').value; // ✅ مهم

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

  const loadingMessage = document.createElement('div');
  loadingMessage.className = 'message left';
  loadingMessage.innerHTML = `<div class="bubble d-flex align-items-center">
    <div class="spinner-border text-secondary me-2" role="status" style="width: 1.2rem; height: 1.2rem;"></div>
  </div>`;
  chatMessages.appendChild(loadingMessage);
  chatMessages.scrollTop = chatMessages.scrollHeight;

  try {
    const res = await fetch('https://nextgenedu-database.azurewebsites.net/chat/send', {
    // const res = await fetch('http://127.0.0.1:8001/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ message: text, id: userId })
    });

    const data = await res.json();
    
    console.log(data);
    console.log(data.reply);
    if (data.reply) {
      chatMessages.removeChild(loadingMessage);
      const code = parseInt(data.code ?? 0);

      switch (code) {
        case 1: renderSchedule(data.reply); break;
        case 2: renderCourseDetails(data.reply); break;
        case 7: renderCourses(data.reply); break;
        case 12:
        case 13:
          renderSummaryCard(data.reply, code);
          break;
        default:
          defalutMessage(data)
      }
    } else {
      chatMessages.removeChild(loadingMessage);
      typeMessage('لم يصل رد من الخادم.', 'left');
    }
    
  } catch (err) {
    console.log(err);
    chatMessages.removeChild(loadingMessage);
    typeMessage('حدث خطأ في الاتصال.', 'left');
  }

});


function defalutMessage(data) {
  let replyText = typeof data.reply === 'string' ? data.reply.trim() : 'لم أفهم سؤالك، الرجاء المحاولة بشكل أوضح.';
  typeMessage(replyText, 'left');
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
        <td>${(session.hall?.hall_name) ?? '-'}</td>
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

function renderCourseDetails(courseDetail) {
  const wrapper = document.createElement('div');
  wrapper.className = 'bubble p-0'; // إزالة المسافات الداخلية لأن البطاقة تحتوي كل شيء

  const card = document.createElement('div');
  card.className = 'card w-100';

  // 🟠 العنوان
  const header = document.createElement('div');
  header.className = 'card-header bg-primary text-white';
  header.innerHTML = `
    <h5 class="mb-0">${courseDetail.course.name} (${courseDetail.course.code})</h5>
    <small>${courseDetail.course.description || ''}</small>
  `;
  card.appendChild(header);

  const cardBody = document.createElement('div');
  cardBody.className = 'card-body';

  // 🟡 المدرسين
  if (courseDetail.teachers.length) {
    const section = document.createElement('div');
    section.innerHTML = `<h6><i class="bi bi-person-lines-fill me-1 text-info"></i> المدرسين:</h6>`;
    const list = document.createElement('ul');
    courseDetail.teachers.forEach(t => {
      const li = document.createElement('li');
      li.innerHTML = `<i class="bi bi-person-badge me-1 text-secondary"></i> ${t.user?.name}`;
      list.appendChild(li);
    });
    section.appendChild(list);
    cardBody.appendChild(section);
  }

  // 🟢 المحاضرات
  if (courseDetail.materials.length) {
    const section = document.createElement('div');
    section.innerHTML = `<h6 class="mt-3"><i class="bi bi-journal-text me-1 text-success"></i> المحاضرات:</h6>`;
    const list = document.createElement('ul');
    courseDetail.materials.forEach(m => {
      const li = document.createElement('li');
      li.innerHTML = `<i class="bi bi-file-earmark-pdf me-1 text-danger"></i> <a href="/storage/${m.material}" target="_blank">الأسبوع ${m.week} - ${m.title}</a>`;
      list.appendChild(li);
    });
    section.appendChild(list);
    cardBody.appendChild(section);
  }

  // 🔵 الاختبارات
  if (courseDetail.quizzes.length) {
    const section = document.createElement('div');
    section.innerHTML = `<h6 class="mt-3"><i class="bi bi-pencil-square me-1 text-primary"></i> الاختبارات:</h6>`;
    const list = document.createElement('ul');
    courseDetail.quizzes.forEach(q => {
      const li = document.createElement('li');
      li.innerHTML = `<i class="bi bi-calendar-event me-1 text-muted"></i> ${q.title} - التاريخ: ${q.date} - الساعة: ${q.start_time}`;
      list.appendChild(li);
    });
    section.appendChild(list);
    cardBody.appendChild(section);
  }

  // 🟣 التكليفات
  if (courseDetail.assignments.length) {
    const section = document.createElement('div');
    section.innerHTML = `<h6 class="mt-3"><i class="bi bi-clipboard-check me-1 text-warning"></i> التكليفات:</h6>`;
    const list = document.createElement('ul');
    courseDetail.assignments.forEach(a => {
      const li = document.createElement('li');
      li.innerHTML = `<i class="bi bi-arrow-down-circle me-1 text-secondary"></i> <a href="/storage/${a.file}" target="_blank">${a.title}</a> - التسليم قبل: ${new Date(a.deadline).toLocaleString()}`;
      list.appendChild(li);
    });
    section.appendChild(list);
    cardBody.appendChild(section);
  }

  // 🔴 الإعلانات
  if (courseDetail.announcements.length) {
    const section = document.createElement('div');
    section.innerHTML = `<h6 class="mt-3"><i class="bi bi-megaphone-fill me-1 text-danger"></i> الإعلانات:</h6>`;
    const list = document.createElement('ul');
    courseDetail.announcements.forEach(a => {
      const li = document.createElement('li');
      li.innerHTML = `<strong>${a.title}</strong>: ${a.body} <br><small class="text-muted">(${new Date(a.post_in).toLocaleString()})</small>`;
      list.appendChild(li);
    });
    section.appendChild(list);
    cardBody.appendChild(section);
  }

  card.appendChild(cardBody);
  wrapper.appendChild(card);

  const msg = document.createElement('div');
  msg.className = 'message left';
  msg.appendChild(wrapper);
  chatMessages.appendChild(msg);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Render summary (today/week) as a Bootstrap card with sections and icons
function renderSummaryCard(summaryText, code) {
  // Try to parse the summary into sections
  // Each section starts with a known Arabic header
  // For week summary, days are marked as [اليوم]
  const msg = document.createElement('div');
  msg.className = 'message left';
  const wrapper = document.createElement('div');
  wrapper.className = 'bubble p-0';

  // Card
  const card = document.createElement('div');
  card.className = 'card w-100 mb-2';

  // Card header
  const cardHeader = document.createElement('div');
  cardHeader.className = 'card-header bg-info text-white';
  cardHeader.innerHTML = code === 12 ? '<i class="bi bi-calendar-event"></i> ملخص اليوم' : '<i class="bi bi-calendar-week"></i> ملخص الأسبوع';
  card.appendChild(cardHeader);

  // Card body
  const cardBody = document.createElement('div');
  cardBody.className = 'card-body';

  // Split by day if week summary
  let daysBlocks = [];
  if (code === 13) {
    // Split by [اليوم]
    daysBlocks = summaryText.split(/\n\[(.*?)\]\n/).filter(Boolean);
    // daysBlocks will be like: [day1, block1, day2, block2, ...]
    for (let i = 0; i < daysBlocks.length; i += 2) {
      const day = daysBlocks[i];
      const block = daysBlocks[i + 1] || '';
      const daySection = document.createElement('div');
      daySection.className = 'mb-3';
      daySection.innerHTML = `<div class="fw-bold text-primary mb-1"><i class="bi bi-calendar3"></i> ${day}</div>` + parseSummarySections(block);
      cardBody.appendChild(daySection);
    }
  } else {
    // Today summary: just parse sections
    cardBody.innerHTML = parseSummarySections(summaryText);
  }

  card.appendChild(cardBody);
  wrapper.appendChild(card);
  msg.appendChild(wrapper);
  chatMessages.appendChild(msg);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Helper to parse summary sections and return HTML
function parseSummarySections(text) {
  // Section headers in Arabic
  const sections = [
    { key: 'المحاضرات/السكاشن/المعامل', icon: 'bi-easel2', color: 'text-success' },
    { key: 'الاختبارات', icon: 'bi-clipboard-check', color: 'text-primary' },
    { key: 'الواجبات', icon: 'bi-journal-check', color: 'text-warning' },
    { key: 'الإعلانات', icon: 'bi-megaphone', color: 'text-danger' },
    { key: 'المواد المضافة', icon: 'bi-folder-plus', color: 'text-info' },
  ];
  let html = '';
  sections.forEach(section => {
    // Find section block
    const regex = new RegExp(section.key + ':(.*?)(?=\n[A-Za-z\u0600-\u06FF]+:|$)', 's');
    const match = text.match(regex);
    if (match) {
      const items = match[1].trim().split('\n').filter(Boolean);
      html += `<div class="mb-2"><strong class="${section.color}"><i class="bi ${section.icon}"></i> ${section.key}:</strong>`;
      if (items.length === 0 || (items.length === 1 && items[0].startsWith('-'))) {
        html += `<div class="text-muted">${items[0] || '-'}</div>`;
      } else {
        html += '<ul class="mb-0">';
        items.forEach(item => {
          html += `<li>${item.replace(/^- /, '')}</li>`;
        });
        html += '</ul>';
      }
      html += '</div>';
    }
  });
  return html;
}
