<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Advanced Chat UI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link rel="stylesheet" href="https://nextgenedu-database.azurewebsites.net/style.css">
</head>
<body>

  <div class="chat-panel">
    <div class="chat-box">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="m-3"><i class="bi bi-robot me-2"></i> مساعدك الذكي </h5>
      </div>
      <div class="chat-messages" id="chatMessages"></div>
      <div class="chat-quick-buttons p-3 d-flex flex-wrap gap-2 justify-content-center d-none" id="quickButtons">
        <button class="btn btn-outline-secondary rounded-pill btn-sm" onclick="quickSend('عايز اعرف المواد اللي عندي الترم ده')"> مواد الترم</button>
        <button class="btn btn-outline-secondary rounded-pill btn-sm" onclick="quickSend('عايز الجدول الدراسي')"> الجدول</button>
        <button class="btn btn-outline-secondary rounded-pill btn-sm" onclick="quickSend('عايز اعرف عن مادة الرياضيات')">مادة</button>
        <button class="btn btn-outline-secondary rounded-pill btn-sm" onclick="quickSend('فين مكان محاضرة الكيمياء؟')">مكان محاضرة</button>
        <button class="btn btn-outline-secondary rounded-pill btn-sm" onclick="quickSend('فين قاعة B105؟')"> قاعة</button>
        <button class="btn btn-outline-secondary rounded-pill btn-sm" onclick="quickSend('فين مكان سكشن الفيزياء؟')"> مكان سكشن</button>
        <button class="btn btn-outline-secondary rounded-pill btn-sm" onclick="quickSend('فين معمل البرمجة؟')"> معمل</button>
      </div>
      <div class="card-footer">
        <form id="chatForm" class="d-flex align-items-center gap-2">
          <div class="text-center my-2">
            <button id="toggleQuickButtons" class="btn btn-outline-primary btn-sm">
  <i class="bi bi-lightning"></i>
            </button>
          </div>
          <input type="text" name="message" id="chatInput" class="form-control rounded-pill px-3" placeholder="اكتب رسالتك...">
          <input type="hidden" name="id" value="{{$user?->id}}" >
          <button type="submit" class="btn btn-primary rounded-circle"><i class="bi bi-send"></i></button>
        </form>
      </div>
    </div>
  </div>
  
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->

<script src="https://nextgenedu-database.azurewebsites.net/script.js"></script>
<script >
  function quickSend(text) {
    chatInput.value = text;
    chatInput.focus();
  }
  const userName = @json($user?->name);
  if (!userName) {
    typeMessage(`لا استطيع مساعدتك بدون التسجيل`);
    
  }else{
    typeMessage(`أهلا بيك يا بشمهندس ${userName}، أنا مساعدك الشخصي هنا علشان أساعدك لو عندك أي سؤال أو استفسار بخصوص الكلية أو المنهج الدراسي أو الجدول. قولّي ومش هتأخر عليك 🌟`);
  }

  document.getElementById('toggleQuickButtons').addEventListener('click', function() {
    const quickButtons = document.getElementById('quickButtons');
    quickButtons.classList.toggle('d-none');

    this.innerHTML = quickButtons.classList.contains('d-none') 
      ? '  <i class="bi bi-lightning"></i>'
      : '<i class="bi bi-x-lg"></i>';
  });
</script>

</body>
</html>
