/**
 * Toogle Header visibility with scroll direction
 * 
 * @description if scroll up: header visible (slidedown animation), if scroll down and scroll position > 100vh: header hidden (slideup animation)
 * 
 * Header must have #header-wrapper ID
 *
 */

let toggleHeaderOnScroll = (() => {
  return {
    init: () => {
      let $header = document.getElementById("header-wrapper");
      // console.log($header);
      //let headerOffset = $header.getBoundingClientRect().top // Position of header in relation to the top of the page

      let previousScrollTop = 0; // Last registred scroll position
      let scrollMonitor; // Html element used to monitor scroll events
      let scrollMonitorLimit; // Html element used to constrain scrollMonitor inside documentElement
      let topMenuAnimationTrigger; // Html element used to trigger menu slide up

      let noHeaderSlide = false;

      // Create a transparent overlay html element that fill the viewport
      const createScrollMonitor = () => {
        scrollMonitor = document.createElement("div");
        scrollMonitor.id = "js-scroll-monitor";
        document.body.appendChild(scrollMonitor);
        let sms = scrollMonitor.style;
        sms.position = "absolute";
        sms.top = 0;
        sms.width = "100%";
        sms.height = "100vh";
        sms.pointerEvents = "none";
        // sms.zIndex = "10000"; // debug
        // sms.backgroundColor = "rgba(0, 0, 255, 0.4)"; // debug
      };

      // Create a transparent overlay html element positionned at the end of the document
      const createScrollMonitorLimit = () => {
        scrollMonitorLimit = document.createElement("div");
        scrollMonitorLimit.id = "js-scroll-monitor-limit";
        document.body.appendChild(scrollMonitorLimit);
        let smls = scrollMonitorLimit.style;
        smls.position = "relative";
        smls.bottom = 0;
        smls.marginTop = "-50vh";
        smls.width = "100%";
        smls.height = "50vh";
        smls.pointerEvents = "none";
        // smls.zIndex = "10000"; // debug
        // smls.backgroundColor = "rgba(0, 255, 255, 0.4)"; // debug
      };

      const watchDispatchScrollEvents = (entry) => {
        // Current scroll position
        const currentScrollTop =
          window.pageYOffset || document.documentElement.currentScrollTop;

        if (currentScrollTop > previousScrollTop) {
          // scroll down detected
          document.documentElement.dispatchEvent(new Event("scroll-down"));
        } else if (currentScrollTop < previousScrollTop) {
          // scroll up detected
          document.documentElement.dispatchEvent(new Event("scroll-up"));
        }

        previousScrollTop = currentScrollTop;
        // if the scrollMonitor is no longer on the screen, move it on the screen
        if (!entry.isIntersecting) {
          // But constrain scrollMonitor inside documentElement
          let scrollMonitorBottom =
            currentScrollTop + scrollMonitor.scrollHeight;
          let limit = scrollMonitorLimit.offsetTop;

          if (scrollMonitorBottom < limit) {
            scrollMonitor.style.top = currentScrollTop + "px";
            // console.log("updated");
          } else {
            scrollMonitor.style.top = limit;
            // console.log("prevented");
          }
        }
      };

      // Create an observer with a 10% treshold to observe an entry that fill the viewport
      const scrollObserver = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.target === scrollMonitor) {
              watchDispatchScrollEvents(entry);
            }
          });
        },
        {
          threshold: [0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1],
        }
      );

      createScrollMonitor();
      createScrollMonitorLimit();
      scrollObserver.observe(scrollMonitor);

      let isInViewport = (element) => {
        const rect = element.getBoundingClientRect();
        return (
          rect.top >= 0 &&
          rect.left >= 0 &&
          rect.bottom <=
            (window.innerHeight || document.documentElement.clientHeight) &&
          rect.right <=
            (window.innerWidth || document.documentElement.clientWidth)
        );
      };

      //Sometime scrollMonitor get lost (if the user scroll too fast)
      setInterval(function () {
        if (!isInViewport(scrollMonitor)) {
          // console.log("scrollMonitor was lost");
          scrollMonitor.style.top = window.pageYOffset + "px";
        }
      }, 2000);

      // Scroll-down event detection
      document.documentElement.addEventListener(
        "scroll-down",
        function () {
          // console.log('scroll-down : '+ document.documentElement.scrollTop);
          if (
            !$header.classList.contains("js-slide-up") &&
            document.documentElement.scrollTop > window.innerHeight*0.5 &&
            !noHeaderSlide
          ) {
            $header.classList.add("js-slide-up");
          }
        },
        false
      );

      // Scroll-up event detection
      document.documentElement.addEventListener(
        "scroll-up",
        function () {
          // console.log("scroll-up : " + document.documentElement.scrollTop);
          if ($header.classList.contains("js-slide-up") && !noHeaderSlide) {
            $header.classList.remove("js-slide-up");
          }
        },
        false
      );

      // Constrain scrollMonitor inside documentElement on resize
      const resizeObserver = new ResizeObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.target === document.documentElement) {
            let scrollMonitorHeight = scrollMonitor.scrollHeight;
            let scrollMonitorBottom =
              scrollMonitor.offsetTop + scrollMonitorHeight;
            let limit = scrollMonitorLimit.offsetTop;
            // console.log(scrollMonitorBottom, limit);
            if (scrollMonitorBottom > limit) {
              // console.log("off Limit !");
              let newTop = limit - scrollMonitorHeight;
              scrollMonitor.style.top = newTop + "px";
            }
          }
        });
      });

      resizeObserver.observe(document.documentElement);

      document.documentElement.addEventListener(
        "stop-header-slide",
        function () {
          noHeaderSlide = true;
        },
        false
      );

      document.documentElement.addEventListener(
        "resume-header-slide",
        function () {
          noHeaderSlide = false;
        },
        false
      );
    },
  };
})();

export {
  toggleHeaderOnScroll
};


