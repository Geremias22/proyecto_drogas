let timer = null;

async function fetchProducts() {
  const q = document.getElementById('productSearch')?.value || '';
  const cat = document.getElementById('categoryId')?.value || '0';

  const params = new URLSearchParams();
  params.set('q', q);

  if (cat && cat !== '0') params.set('category', cat);

  const res = await fetch(`index.php?c=product_list&a=ajax&${params.toString()}`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  });

  const html = await res.text();
  const grid = document.getElementById('productGrid');
  if (grid) grid.innerHTML = html;
}


document.addEventListener('DOMContentLoaded', () => {
  const search = document.getElementById('productSearch');
  if (!search) return;

  search.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(fetchProducts, 100); // debounce
  });

  // Si luego añades categorías:
  const catSelect = document.getElementById('categorySelect');
  if (catSelect) {
    catSelect.addEventListener('change', fetchProducts);
  }
});
