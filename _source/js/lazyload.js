jQuery(document).ready(function() {
    var lazyLoadInstance = new LazyLoad({
        elements_selector: ".lazy",
        callback_loaded: function() {
            vinhoEqualHeights();
        }
    });
});