/**
 * @files scrolltopbutton.php, scrollTopButton.js, _scroll-top-button.scss
 * @description hide/show the scroll-top-button depending of the visibility of 2 triggers (transparent html elements positionned via CSS) using an Intersection Observer
 *
 */

let scrollTopButton = (() => {

  return {
    init : () => {
      try {

        let scrolltopbutton = document.querySelector('#scroll-top-button > a');

        let observer = new IntersectionObserver ( (entries) => {
          
          entries.forEach( (entry) => {

            if(entry.target.classList.contains("trigger-bottom") && !entry.isIntersecting) {
              // trigger-bottom is no longer on the screen, show the scrolltopbutton
              scrolltopbutton.classList.add('js-visible');
            }

            if(entry.target.classList.contains("trigger-top") && entry.isIntersecting){
              // trigger-top is on the screen, hide the scrolltopbutton
              scrolltopbutton.classList.remove('js-visible');
            }
            
          });
        },{});

        scrolltopbutton.parentElement.querySelectorAll('div[class^="trigger"]')
          .forEach( (entry) => {
            observer.observe(entry);
          });
    
      } catch (error) {
        console.error(error);
      }
    }
  };

})();

export {scrollTopButton};

