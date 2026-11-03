(function(){'use strict';
const root=document.documentElement;
const toggle=document.querySelector('.fm-theme-toggle');
const menuButton=document.querySelector('.fm-menu-button');
const nav=document.querySelector('#fm-primary-nav');
const storageKey='fm-gifts-theme';
function setTheme(theme,save){const dark=theme==='dark';root.setAttribute('data-theme',dark?'dark':'light');if(toggle){toggle.textContent=dark?'Light mode':'Dark mode';toggle.setAttribute('aria-pressed',String(dark));toggle.setAttribute('aria-label',dark?'Switch to light mode':'Switch to dark mode');}if(save){try{localStorage.setItem(storageKey,dark?'dark':'light');}catch(error){}}}
let saved='light';try{saved=localStorage.getItem(storageKey)||'light';}catch(error){}setTheme(saved==='dark'?'dark':'light',false);
if(toggle){toggle.addEventListener('click',function(){setTheme(root.getAttribute('data-theme')==='dark'?'light':'dark',true);});}
if(menuButton&&nav){menuButton.addEventListener('click',function(){const open=nav.classList.toggle('is-open');menuButton.setAttribute('aria-expanded',String(open));});nav.addEventListener('click',function(event){if(event.target.closest('a')){nav.classList.remove('is-open');menuButton.setAttribute('aria-expanded','false');}});}
}());