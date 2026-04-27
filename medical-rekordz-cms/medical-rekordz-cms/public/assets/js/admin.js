// ── Alert dismiss ────────────────────────────────────────────
document.querySelectorAll('.alert-close').forEach(btn => {
  btn.addEventListener('click', () => btn.parentElement.remove());
});
