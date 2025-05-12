<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Advanced Chat UI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

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
      <div class="chat-quick-buttons p-3 d-flex flex-wrap gap-2 justify-content-center">
        <button class="btn btn-outline-primary rounded-pill btn-sm" onclick="quickSend('عايز الجدول الدراسي')"> الجدول</button>
        <button class="btn btn-outline-success rounded-pill btn-sm" onclick="quickSend('عايز اعرف عن مادة الرياضيات')">مادة</button>
        <button class="btn btn-outline-warning rounded-pill btn-sm" onclick="quickSend('فين مكان محاضرة الكيمياء؟')">مكان محاضرة</button>
        <button class="btn btn-outline-info rounded-pill btn-sm" onclick="quickSend('فين مكان سكشن الفيزياء؟')"> مكان سكشن</button>
        <button class="btn btn-outline-secondary rounded-pill btn-sm" onclick="quickSend('فين معمل البرمجة؟')"> معمل</button>
        <button class="btn btn-outline-dark rounded-pill btn-sm" onclick="quickSend('فين قاعة B105؟')"> قاعة</button>
      </div>
      <div class="card-footer">
        <form id="chatForm" class="d-flex align-items-center gap-2">
          <input type="text" name="message" id="chatInput" class="form-control rounded-pill px-3" placeholder="اكتب رسالتك...">
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
</script>
</body>
</html>
