'use strict';
const searchBox = document.querySelector('#searchRows');
if (searchBox) {
  const rows = Array.from(document.querySelectorAll('#masterTable tbody tr.filterable-row'));
  const indicator = document.querySelector('#visibleCount');
  const noMatches = document.querySelector('#noMatches');
  const refresh = () => {
    const q = searchBox.value.trim().toLocaleLowerCase('id');
    let n = 0;
    rows.forEach(row => {
      const show = row.textContent.toLocaleLowerCase('id').includes(q);
      row.hidden = !show;
      if (show) n++;
    });
    if (indicator) indicator.textContent = String(n);
    if (noMatches) noMatches.hidden = n !== 0 || rows.length === 0;
  };
  searchBox.addEventListener('input', refresh);
}
const toggle = document.querySelector('#menuToggle');
const sidebar = document.querySelector('#sidebar');
if (toggle && sidebar) {
  toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
}
