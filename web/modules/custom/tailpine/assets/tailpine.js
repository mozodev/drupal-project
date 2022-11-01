import feather from 'https://cdn.skypack.dev/feather-icons'
import 'https://cdn.skypack.dev/twind/shim'
import collapse from 'https://cdn.skypack.dev/@alpinejs/collapse'
import persist from 'https://cdn.skypack.dev/@alpinejs/persist'
import Alpine from 'https://cdn.skypack.dev/alpinejs'

const $ = document.querySelector.bind(document)
const $$ = document.querySelectorAll.bind(document)
Alpine.plugin(collapse)
Alpine.plugin(persist)
Alpine.store('darkMode', {
  on: 1,
  init() {
    console.log('init: ' + this.on);
    this.on = window.localStorage.getItem('Drupal.gin.darkmode')
  },
  toggle() {
    this.on ^= 1;
    console.log('toggle: ' + this.on);
    window.localStorage.setItem('Drupal.gin.darkmode', this.on ? 1 : 0)
    $('html').classList.toggle('dark')
    $('html').classList.toggle('gin--dark-mode')
  }
})
Alpine.start()

document.addEventListener('DOMContentLoaded', (event) => {
  console.log('DOM fully loaded and parsed')
});

((Drupal, once) => {
  Drupal.behaviors.initHtml = {
    attach: (context, settings) => {
      once('loadFeatherIcon', 'html', context).forEach((element) => {
        feather.replace()
      })
    }
  }
})(Drupal, once)
