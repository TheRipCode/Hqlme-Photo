
function sendCode() {
  const userId = document.getElementById("userId").value.trim();
  const username = document.getElementById("username").value.trim();
  const status = document.getElementById("status");
  if (!userId || !username) {
    status.innerText = "همه فیلدها را وارد کنید.";
    return;
  }
  const code = Math.floor(1000 + Math.random() * 9000);
  fetch(`https://api.telegram.org/botTOKEN/sendMessage`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ chat_id: userId, text: "کد تایید شما: `" + code + "`", parse_mode: "Markdown" })
  }).then(res => {
    if (res.ok) status.innerText = "کد ارسال شد.";
    else status.innerText = "ارسال نشد. بررسی کنید ربات را استارت کرده باشید.";
  }).catch(() => status.innerText = "خطا در اتصال.");
}
