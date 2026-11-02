(() => {
  'use strict';
  const root = document.querySelector('.fmgc-wrap');
  if (!root || !window.FMGiftsControl) return;
  const headers = {'X-WP-Nonce': FMGiftsControl.nonce};
  const base = FMGiftsControl.restUrl.replace(/\/approvals\/?$/, '');
  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
  const table = (heads, rows) => {
    if (!rows.length) return '<p>No records found.</p>';
    return '<table class="fmgc-table"><thead><tr>' + heads.map(h=>'<th>'+esc(h)+'</th>').join('') + '</tr></thead><tbody>' + rows.join('') + '</tbody></table>';
  };
  async function load(path, target, render) {
    const el = document.getElementById(target);
    try {
      const response = await fetch(base + '/' + path, {method:'GET', credentials:'same-origin', headers});
      if (!response.ok) throw new Error('HTTP ' + response.status);
      el.innerHTML = render(await response.json());
    } catch (e) { el.textContent = 'Unable to load this read-only view: ' + e.message; }
  }
  load('products','fmgc-products-content', data => table(['Image','Product','SKU','Price','Stock','Categories','Status'], (data.items||[]).map(p=>{
    const image = p.image ? '<img class="fmgc-product-thumb" src="'+esc(p.image)+'" alt="" loading="lazy">' : '—';
    const categories = Array.isArray(p.categories) && p.categories.length ? p.categories.join(', ') : '—';
    return '<tr><td>'+image+'</td><td>'+esc(p.name)+'</td><td>'+esc(p.sku)+'</td><td>'+esc(p.price)+'</td><td>'+esc(p.stock_status)+' / '+esc(p.stock_quantity)+'</td><td>'+esc(categories)+'</td><td>'+esc(p.status)+'</td></tr>';
  })));
  load('orders','fmgc-orders-content', data => table(['Order','Date','Status','Items','Total'], (data.items||[]).map(o=>'<tr><td>#'+esc(o.order_id)+'</td><td>'+esc(o.date)+'</td><td>'+esc(o.order_status)+'</td><td>'+esc(o.item_count)+'</td><td>'+esc(o.currency)+' '+esc(o.total)+'</td></tr>')));
  load('diagnostics','fmgc-diagnostics-content', data => table(['Check','Result'], Object.entries(data).map(([k,v])=>'<tr><td>'+esc(k)+'</td><td><code>'+esc(typeof v === 'object' ? JSON.stringify(v) : v)+'</code></td></tr>')));
  load('approvals','fmgc-approvals-content', data => {
    const items = (data.items||[]).map(item=>({...item}));
    const render = () => {
      const rows = items.map(item => '<tr><td>'+esc(item.id)+'</td><td>'+esc(item.label)+'</td><td>'+esc(item.state)+'</td><td>'+(item.state==='proposed'?'<button class="fmgc-action" data-id="'+esc(item.id)+'" data-state="approved-not-executed">Simulate approve</button> <button class="fmgc-action" data-id="'+esc(item.id)+'" data-state="rejected">Reject</button>': '—')+'</td></tr>');
      document.getElementById('fmgc-approvals-content').innerHTML = '<p><strong>SIMULATION ONLY.</strong> State exists in this browser page memory and is discarded on refresh. No price/stock action is executed.</p>' + table(['ID','Sample request','State','Action'], rows);
      document.querySelectorAll('#fmgc-approvals-content button').forEach(button => button.addEventListener('click', () => {
        const item = items.find(x=>x.id===button.dataset.id);
        if (!item || item.state !== 'proposed') return;
        item.state = button.dataset.state;
        render();
      }));
    };
    render();
    return '';
  });
})();
