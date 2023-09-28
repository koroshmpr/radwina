import $ from "jquery";

class Search {
    // 1. describe and create/initiate our object
    constructor() {
        // this.addSearchHTML();

        this.openButton = $("input[type=search]");
        this.closeButton = $(".search-overlay__close, .mobile-overlay__close , body");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $("input[type=search]:visible");
        this.resultsDiv = $(".search-overlay__results");
        this.events();

        this.isOverlayOpen = false;
        this.isSpinnerVisible = false;

        this.previousValue;
        this.typingTimer;
    }

    // 2.events
    events() {
        this.openButton.on("click", this.openOverlay.bind(this));
        this.closeButton.on("click", this.closeOverlay.bind(this));
        $(document).on("keydown", this.keyPressDispatcher.bind(this));

        this.searchField.on("keyup", this.typingLogic.bind(this));
    }


    // 3. methods (function, action...)
    typingLogic() {
        if (this.searchField.val()) {
            $(".mobile-overlay__close").removeClass('d-none');
        }
        if (this.searchField.val() != this.previousValue) {
            clearTimeout(this.typingTimer);
            if (this.searchField.val()) {
                if (!this.isSpinnerVisible) {
                    this.resultsDiv.html(`<div class="text-center mt-2"><div class="spinner-border align-baseline text-primary" role="status"></div></div>`);
                    this.isSpinnerVisible = true;

                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 750);
            } else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }
        }
        this.previousValue = this.searchField.val();
    }

    getResults() {
        $.getJSON(jsData.root_url + '/wp-json/search/v1/search?term=' + this.searchField.val(), (results) => {
            console.log(results)
            this.resultsDiv.html(`
                <div class="pt-3">
                    <div class="row g-3">
                        <!--       PRODUCT      -->
                        <div class="col-12">
                        <h5 class="mb-2">محصول</h5>
                        ${results.product.length ? '<div class="row row-cols-lg-4 row-cols-1 py-4">' : '<p class="p-2' +
                ' m-0' +
                ' border-top">هیچ محصولی یافت' +
                ' نشد</p>'}
                        ${results.product.map(item =>
                `<a class="my-2" href="${item.url}" alt="${item.title}">
                                <div class="card p-2 border-top shadow-sm my-2">
                                    <div class="row gx-2 gy-0">
                                        <div class="col-3">
                                            <div class="ratio ratio-1x1">
                                                <img src="${item.img}"
                                                     class="rounded"
                                                     alt="${item.title}">
                                            </div>
                                        </div>
    
                                        <div class="col">
                                            <div class="vstack h-100 py-2">
                                                <h6 class="text-primary mb-2">${item.title}</h6>
    
                                                <p class="text-primary mt-auto m-0">
                                                    ${item.price} <span class="ms-1">تومان</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>`
            ).join('')}
                        ${results.product.length ? '</div>' : ''}
                        </div>
                    </div>
                </div>
            `)
            this.isSpinnerVisible = false;
        });
    }

    keyPressDispatcher(e) {
        if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus')) {
            this.openOverlay();
        }
        if (e.keyCode == 27 && this.isOverlayOpen) {
            this.closeOverlay();
        }
    }

    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");

        this.searchField.val('');

        setTimeout(() => this.searchField.focus(), 301);

        this.isOverlayOpen = true;
        return false;

    }

    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.resultsDiv.html('');
        $(".mobile-overlay__close").addClass('d-none');
        this.searchField.val('');
        this.isOverlayOpen = false;
    }

}

export default Search;