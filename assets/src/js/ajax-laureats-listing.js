// console.log('test');
(function ($) {
  $(document).ready(function () {
    // Show loader on AJAX
    $(document).ajaxSend(function (event, jqxhr, settings) {
      // console.log(event, settings);
      if (settings.action === "laureats_listing") {
        // $("#overlay").fadeIn(300);
        // console.log("show loader !!!")
      }
    });

    /**
     * AJAX on SUBMIT
     */
    let on_submit = function (e) {
      e.preventDefault();

      let search_btn = document.querySelector(".search-ll-btn");
      search_btn.disabled = true;
    
      let target = $(".ajll-wrap > .ajll-content");
      target.addClass( "js-loading" );

      let serialized = $(this).serialize(); // JQuery function
      let action = "laureats_listing";

      $.ajax({
        url: ajax_laureat_listing_filters.ajax_url,
        data:
          serialized +
          "&security=" +
          ajax_laureat_listing_filters.security + // security: retrieve nonce and transmit it back to the server for verification
          "&action=" +
          action, // action hook declared in php : wp_ajax_nopriv_{$_REQUEST[‘action’]} & wp_ajax_{$_REQUEST[‘action’]}
        type: "post",
        action: action,
        success: function (result) {
          // console.log(result);
          target.html(result);
        },
        error: function (response) {
          console.warn(response);
        },
      }).done(function () {
        search_btn.disabled = false;
        target.removeClass( "js-loading" );
      });
    }
    $(document).on("submit", ".js-ll-filters-form", on_submit );

    /**
     * AJAX on PAGINATION
     */
    let on_pagination = function (e) {
      e.preventDefault();

      let target = $(".ajll-wrap > .ajll-content");
      target.addClass( "js-loading" );

      let search_btn = document.querySelector(".search-ll-btn");
      search_btn.disabled = true;

      let serialized = $(".js-ll-filters-form").serialize();
      let action = "laureats_listing";

      $.ajax({
        url: ajax_laureat_listing_filters.ajax_url,
        data:
          serialized +
          "&security=" +
          ajax_laureat_listing_filters.security + // security: retrieve nonce and transmit it back to the server for verification
          "&action=" +
          action + // action hook declared in php : wp_ajax_nopriv_{$_REQUEST[‘action’]} & wp_ajax_{$_REQUEST[‘action’]}
          "&pagenum=" +
          $(this).attr("href").replace("#", ""),
        action: action,
        type: "post",
        success: function (result) {
          // console.log(result);
          target.html(result);
          $(".ajll-wrap").get(0).scrollIntoView({ behavior: "smooth" });
        },
        error: function (response) {
          console.warn(response);
        },
      }).done(function () {
        target.removeClass( "js-loading" );
        search_btn.disabled = false;
      });
    }
    $(document).on("click", "a.page-numbers", on_pagination );

  });
})(jQuery);