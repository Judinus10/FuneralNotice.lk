document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("createFuneralNoticeBtn");
  if (!btn) return;

  btn.addEventListener("click", () => {
    window.location.href = "create.html";
  });
});