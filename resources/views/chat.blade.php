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
  <link rel="stylesheet" href="https://nextgenedu-database.azurewebsites/style.css">
</head>
<body>
  <div class="chat-container">
    <div class="chat-messages" id="chatMessages"></div>

    <div class="chat-footer">

    <div class="chat-quick-buttons-wrapper d-flex align-items-center justify-content-between">
      
      <div class="chat-quick-buttons flex-grow-1" id="quickButtons">
        <button class="btn text-dark btn-outline-primary btn-sm" onclick="quickSend('عايز اعرف المواد اللي عندي الترم ده')">مواد الترم</button>
        <button class="btn text-dark btn-outline-primary btn-sm" onclick="quickSend('عايز الجدول الدراسي')">الجدول</button>
        <button class="btn text-dark btn-outline-primary btn-sm" onclick="quickSend('عايز اعرف عن مادة الرياضيات')">مادة</button>
        <button class="btn text-dark btn-outline-primary btn-sm" onclick="quickSend('فين مكان محاضرة الكيمياء؟')">مكان محاضرة</button>
        <button class="btn text-dark btn-outline-primary btn-sm" onclick="quickSend('فين قاعة B105؟')">قاعة</button>
        <button class="btn text-dark btn-outline-primary btn-sm" onclick="quickSend('فين مكان سكشن الفيزياء؟')">مكان سكشن</button>
        <button class="btn text-dark btn-outline-primary btn-sm" onclick="quickSend('فين معمل البرمجة؟')">معمل</button>
      </div>

    </div>


      <form id="chatForm">
        <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
        <input type="text" class="form-control" id="chatInput" name="message" placeholder="اكتب رسالتك...">
        <button type="button" id="toggleQuickButtons" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-lightning"></i>
        </button>
        <input type="hidden" name="id" value="{{$user?->id}}">
      </form>
    </div>
  </div>

  
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->

<script src="https://nextgenedu-database.azurewebsites/script.js"></script>
<script>
    function quickSend(text) {
      const chatInput = document.getElementById('chatInput');
      chatInput.value = text;
      chatInput.focus();
    }

    const userName = @json($user?->name);
    if (!userName) {
      typeMessage(`لا استطيع مساعدتك بدون التسجيل`);
    } else {
      typeMessage(`أهلا بيك يا بشمهندس ${userName}، أنا مساعدك الشخصي هنا علشان أساعدك لو عندك أي سؤال أو استفسار بخصوص الكلية أو المنهج الدراسي أو الجدول. قولّي ومش هتأخر عليك 🌟`);
    }

    document.getElementById('toggleQuickButtons').addEventListener('click', function () {
      const quickButtons = document.getElementById('quickButtons');
      if (quickButtons.style.display === 'none' || quickButtons.style.display === '') {
        quickButtons.style.display = 'flex';
        this.innerHTML = '<i class="bi bi-x-lg"></i>';
      } else {
        quickButtons.style.display = 'none';
        this.innerHTML = '<i class="bi bi-lightning"></i>' ;
      }
    });
</script>

</body>
</html>
