const swiper = new Swiper('.swiper', {
  // Optional parameters
  direction: 'horizontal',
  slidesPerView: 1,
  breakpoints: {
    // when window width is >= 320px
    320: {
      slidesPerView: 1,
      spaceBetween: 20,
    },
    // when window width is >= 480px
    // when window width is >= 640px
    768: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
  },
  // Navigation arrows
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
});

document.getElementById('redirectInput').value = window.location.pathname;

console.log(window.location.pathname);
