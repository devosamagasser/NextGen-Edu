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
  <link rel="stylesheet" href="http://127.0.0.1:8001/style.css">
</head>
<body>

  <div class="chat-panel">
    <div class="chat-box">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="m-3"><i class="bi bi-robot me-2"></i> مساعدك الذكي </h5>
      </div>
      <div class="chat-messages" id="chatMessages"></div>
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
<script src="http://127.0.0.1:8001/script.js"></script>
</body>
</html>
