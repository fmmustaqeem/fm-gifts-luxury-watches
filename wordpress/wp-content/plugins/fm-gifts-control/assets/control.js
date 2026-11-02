(() => {
  'use strict';
  const root = document.querySelector('.fmgc-wrap');
  if (!root || !window.FMGiftsControl) return;
  const config = window.FMGiftsControl;
  const headers = {'X-WP-Nonce': config.nonce, 'Accept': 'application/json'};
  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
  const baseUrl = new URL(config.restUrl, window.location.href);
  const base = baseUrl.href.replace(/\/$/, '');
  const endpoint = (path) => {
    const url = new URL(base + '/' + path, window.location.href);
    if (url.origin !== window.location.origin) throw new Error('REST endpoint must remain on this WordPress origin.');
    return url.toString();
  };
  const safeImage = (value) => {
    if (!value) return '';
    try {
      const url = new URL(String(value), window.location.href);
      return url.origin === window.location.origin ? url.toString() : '';
    } catch (error) { return ''; }
  };
  const table = (heads, rows) => {
    if (!rows.length) return '<p>No records found.</p>';
    return '<table class="fmgc-table"><thead><tr>' + heads.map(h=>'<th>'+esc(h)+'</th>').join('') + '</tr></thead><tbody>' + rows.join('') + '</tbody></table>';
  };
  const records = (data) => Array.isArray(data) ? data : (Array.isArray(data.items) ? data.items : []);
  async function load(path, target, render) {
    const el = document.getElementById(target);
    try {
      const response = await fetch(endpoint(path), {method:'GET', credentials:'same-origin', headers});
      if (!response.ok) throw new Error('HTTP ' + response.status);
      const rendered = render(await response.json());
      if (typeof rendered === 'string') {
        el.innerHTML = rendered;
      } else if (rendered && typeof rendered.html === 'string') {
        el.innerHTML = rendered.html;
        if (typeof rendered.bind === 'function') rendered.bind(el);
      } else {
        el.textContent = 'No view was returned.';
      }
    } catch (e) { el.textContent = 'Unable to load this read-only view: ' + e.message; }
  }
  load('products','fmgc-products-content', data => table(['Image','Product','SKU','Price','Stock','Categories','Status'], records(data).map(p=>{
    const imageUrl = safeImage(p.image);
    const image = imageUrl ? '<img class="fmgc-product-thumb" src="'+esc(imageUrl)+'" alt="" loading="lazy">' : '—';
    const categories = Array.isArray(p.categories) && p.categories.length ? p.categories.join(', ') : '—';
    return '<tr><td>'+image+'</td><td>'+esc(p.name)+'</td><td>'+esc(p.sku)+'</td><td>'+esc(p.price)+'</td><td>'+esc(p.stock_status)+' / '+esc(p.stock_quantity)+'</td><td>'+esc(categories)+'</td><td>'+esc(p.status)+'</td></tr>';
  })));
  load('orders','fmgc-orders-content', data => table(['Order','Date','Status','Items','Total'], records(data).map(o=>'<tr><td>#'+esc(o.order_id)+'</td><td>'+esc(o.date)+'</td><td>'+esc(o.order_status)+'</td><td>'+esc(o.item_count)+'</td><td>'+esc(o.currency)+' '+esc(o.total)+'</td></tr>')));
  load('diagnostics','fmgc-diagnostics-content', data => table(['Check','Result'], Object.entries(data).map(([k,v])=>'<tr><td>'+esc(k)+'</td><td><code>'+esc(typeof v === 'object' ? JSON.stringify(v) : v)+'</code></td></tr>')));
  load('approvals','fmgc-approvals-content', data => {
    const items = records(data).map(item=>({...item}));
    const render = () => {
      const rows = items.map(item => {
        const stale = item.stale || item.state === 'stale';
        const state = item.state === 'approved-not-executed' ? 'APPROVED — NOT EXECUTED' : item.state.toUpperCase();
        const action = item.state === 'pending' && !stale && config.canApprove
          ? '<button class="fmgc-action" data-id="'+esc(item.id)+'">Simulate approve</button>'
          : '—';
        return '<tr><td>'+esc(item.id)+'</td><td>'+esc(item.label)+'</td><td>'+esc(item.proposed_price)+'</td><td>'+esc(item.proposed_stock)+'</td><td>'+esc(state)+(stale?' · STALE':'')+'</td><td>'+action+'</td></tr>';
      });
      return '<p><strong>SIMULATION ONLY.</strong> State exists in this browser page memory and is discarded on refresh. No price/stock action is executed.</p>' + table(['ID','Sample request','Proposed price','Proposed stock','State','Action'], rows);
    };
    return {
      html: render(),
      bind: (container) => {
        container.addEventListener('click', (event) => {
          const target = event.target;
          const button = target && typeof target.closest === 'function' ? target.closest('button[data-id]') : null;
          if (!button) return;
          const item = items.find(x=>x.id===button.dataset.id);
          if (!item || item.stale || item.state !== 'pending' || !config.canApprove) return;
          item.state = 'approved-not-executed';
          container.innerHTML = render();
        });
      }
    };
  });
})();
