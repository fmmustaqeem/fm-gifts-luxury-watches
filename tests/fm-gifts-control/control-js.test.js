'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../../wordpress/wp-content/plugins/fm-gifts-control/assets/control.js'), 'utf8');
const sampleApprovals = {
  simulation_only: true,
  persistence: 'browser-memory-only',
  items: [
    {id:'SIM-1001',label:'Pending sample',proposed_price:'12500.00',proposed_stock:8,state:'pending',stale:false},
    {id:'SIM-1002',label:'Stale sample',proposed_price:'4800.00',proposed_stock:3,state:'stale',stale:true},
    {id:'SIM-1003',label:'Approved sample',proposed_price:'9200.00',proposed_stock:12,state:'approved-not-executed',stale:false},
    {id:'SIM-1004',label:'Failed sample',proposed_price:'6500.00',proposed_stock:0,state:'failed',stale:false},
    {id:'SIM-1005',label:'Rejected sample',proposed_price:'3100.00',proposed_stock:4,state:'rejected',stale:false}
  ]
};
function element() {
  return {innerHTML:'',textContent:'',listeners:{},addEventListener(type,callback){this.listeners[type]=callback;}};
}
function harness(restUrl) {
  const ids = ['fmgc-products-content','fmgc-orders-content','fmgc-diagnostics-content','fmgc-approvals-content'];
  const elements = new Map(ids.map(id=>[id,element()]));
  const requests = [];
  const root = {};
  const document = {
    querySelector(selector) { return selector === '.fmgc-wrap' ? root : null; },
    getElementById(id) { return elements.get(id) || null; }
  };
  const window = {location:{href:'https://local.test/wp-admin/admin.php?page=fm-gifts-control',origin:'https://local.test'},FMGiftsControl:{restUrl,nonce:'test-nonce',canApprove:true}};
  const payloads = {
    products:{items:[]},
    orders:[],
    diagnostics:{php:'8.5-test',hosting_identity:'NOT VERIFIED',outbound_https:'NOT TESTED'},
    approvals:sampleApprovals
  };
  const fetch = async (url,options) => {
    requests.push({url,options});
    const endpoint = new URL(url).pathname.split('/').pop();
    return {ok:true,status:200,json:async()=>payloads[endpoint]};
  };
  return {document,window,fetch,elements,requests};
}
async function boot(restUrl) {
  const h = harness(restUrl);
  vm.runInNewContext(source,{document:h.document,window:h.window,fetch:h.fetch,URL,console});
  await new Promise(resolve=>setImmediate(resolve));
  return h;
}
(async()=>{
  const h = await boot('https://local.test/wp-json/fm-gifts-control/v1/');
  const approvals = h.elements.get('fmgc-approvals-content');
  assert.match(approvals.innerHTML,/SIM-1001/,'approval queue remains visible after async load');
  assert.match(approvals.innerHTML,/SIM-1002/,'stale sample is visible');
  assert.doesNotMatch(approvals.innerHTML,/data-id="SIM-1002"/,'stale row has no approve control');
  assert.equal(h.requests.length,4,'four dashboard reads are made');
  assert.ok(h.requests.every(r=>r.options.method==='GET'),'all dashboard requests use GET');

  const beforeClick = h.requests.length;
  const pendingButton = {dataset:{id:'SIM-1001'},closest(selector){return selector==='button[data-id]'?this:null;}};
  approvals.listeners.click({target:pendingButton});
  assert.match(approvals.innerHTML,/APPROVED — NOT EXECUTED/,'pending sample changes to inert approved-not-executed state');
  assert.equal(h.requests.length,beforeClick,'simulation click makes no network request');

  const staleButton = {dataset:{id:'SIM-1002'},closest(selector){return selector==='button[data-id]'?this:null;}};
  approvals.listeners.click({target:staleButton});
  assert.match(approvals.innerHTML,/SIM-1002[\s\S]*STALE/,'stale sample remains stale when a forged click is dispatched');
  assert.doesNotMatch(approvals.innerHTML,/data-id="SIM-1002"/,'stale sample never gains an approve control');
  assert.ok(!/localStorage|sessionStorage/.test(source),'approval state is not persisted in browser storage');
  console.log('PASS: approval queue renders after asynchronous loading');
  console.log('PASS: all dashboard requests are same-origin GET reads');
  console.log('PASS: sample approval is browser-memory-only and sends no request');
  console.log('PASS: stale sample cannot be approved');

  const external = await boot('https://outside.example/wp-json/fm-gifts-control/v1/');
  assert.equal(external.requests.length,0,'cross-origin REST base is rejected before fetch');
  assert.match(external.elements.get('fmgc-approvals-content').textContent,/WordPress origin/,'cross-origin rejection is visible');
  console.log('PASS: cross-origin REST endpoints fail closed');
})().catch(error=>{console.error(error);process.exitCode=1;});
