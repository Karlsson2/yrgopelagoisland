const swiper = new Swiper('.swiper', {
  direction: 'horizontal',
  slidesPerView: 1,
  breakpoints: {
    320: {
      slidesPerView: 1,
      spaceBetween: 20,
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
});
//set the redirectpathname in the footer link to the value of the current location.
document.getElementById('redirectInput').value = window.location.pathname;
