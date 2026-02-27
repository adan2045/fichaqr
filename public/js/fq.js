document.addEventListener('DOMContentLoaded', () => {
  // Auto-focus and select for scanner/inputs
  const auto = document.querySelector('[data-autofocus]');
  if (auto) {
    auto.focus();
    if (auto.select) auto.select();
  }

  // Optional: close alerts if they have a close button
  document.querySelectorAll('[data-alert-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      const wrap = btn.closest('.fq-alert');
      if (wrap) wrap.remove();
    });
  });
});
