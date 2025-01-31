/**
 * Mobile Menu Button
 * 
 * Require JQUERY
 * 
 * @description When any .mobile-menu-btn is clicked, manage every .mobile-menu-btn active state and aria-pressed value
 *							When Elementor mobile menu popup is openned and closed, update buttons states.
 */
let elementorMobileMenuBtn = (() => {

	// Elementor Popup Events
	// ----------------------
	let elementorMobileMenuPopup = ($, elementorMobileMenuPopupID) => {

			// When popup show, add event on close button
			$(document).on("elementor/popup/show", (e, id, instance) => {
				if (id === elementorMobileMenuPopupID) {
					$(".mobile-menu-btn-close").on("click", (e) => {
						document.querySelector(".elementor-popup-modal .dialog-close-button").click()
					});
				}
			});

			// When popup is closed, dispatch event to switch mobile menu btns states
			$(document).on("elementor/popup/hide", (e, id, instance) => {
				if (id === elementorMobileMenuPopupID) {
				document.dispatchEvent(new Event('mobileMenuButtonPressed'));
				}
			});
	}


  return {
		/**
		 * 
		 * @param {int} elementorMobileMenuPopupID
		 */
    init: (elementorMobileMenuPopupID) => {

			// Add event listeners on Elementor mobile menu popup
			elementorMobileMenuPopup(jQuery, elementorMobileMenuPopupID);

      let btns = document.querySelectorAll("a.mobile-menu-btn, .mobile-menu-btn a");
			try {
				// When any .mobile-menu-btn is clicked, dispach an event
				[...btns].forEach((btn) => {
					btn.addEventListener("click", (e) => {
						document.dispatchEvent(new Event('mobileMenuButtonPressed'));
					});
				});

				// When this kind of event is detected, toggle every .mobile-menu-btn
				document.addEventListener("mobileMenuButtonPressed", (e) => {
					[...btns].forEach((btn) => {
						btn.classList.toggle("active");
						btn.setAttribute("aria-pressed", btn.getAttribute('aria-pressed') === "true" ? "false" : "true");			
					});
				});
			}
			catch(err) {
				console.log(err);
			}
			// wp_localize_script
			// console.log(php_vars);
    }
  };

})();

export {
  elementorMobileMenuBtn
};