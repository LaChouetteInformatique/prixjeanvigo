import { elementorMobileMenuBtn } from "./components/elementorMobileMenuBtn";
import { toggleHeaderOnScroll } from "./components/toggleHeaderOnScroll";
import { scrollTopButton } from "./components/scrollTopButton";

function docReady(fn) {
  // https://stackoverflow.com/a/9899701
  // see if DOM is already available
  if (
    document.readyState === "complete" ||
    document.readyState === "interactive"
  ) {
    setTimeout(fn, 1);
  } else {
    document.addEventListener("DOMContentLoaded", fn);
  }
}

docReady(function () {
  // DOM is loaded and ready for manipulation here

  // Init Elementor mobile Btns and Elementor mobile menu popup
  // Require JQuery
  elementorMobileMenuBtn.init(429);
  
  toggleHeaderOnScroll.init();

  scrollTopButton.init();

});