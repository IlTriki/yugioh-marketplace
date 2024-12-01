import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect() {
    this.initializeSearch();
  }

  initializeSearch() {
    const form = document.getElementById("searchForm");
    const sortBySelects = document.querySelectorAll(".SortBy");
    const inStockCheckboxes = document.querySelectorAll(".FilterInStock");
    const preOrderCheckboxes = document.querySelectorAll(".FilterPreOrder");
    const outOfStockCheckboxes = document.querySelectorAll(".FilterOutOfStock");
    const priceFromInputs = document.querySelectorAll(".FilterPriceFrom");
    const priceToInputs = document.querySelectorAll(".FilterPriceTo");

    // Initialize form values from URL parameters
    const urlParams = new URLSearchParams(window.location.search);

    // Initialize checkboxes
    if (urlParams.has("inStock")) {
      syncCheckboxes(inStockCheckboxes, urlParams.get("inStock") === "1");
    }
    if (urlParams.has("preOrder")) {
      syncCheckboxes(preOrderCheckboxes, urlParams.get("preOrder") === "1");
    }
    if (urlParams.has("outOfStock")) {
      syncCheckboxes(outOfStockCheckboxes, urlParams.get("outOfStock") === "1");
    }

    // Initialize price inputs
    if (urlParams.has("priceFrom")) {
      syncInputs(priceFromInputs, urlParams.get("priceFrom"));
    }
    if (urlParams.has("priceTo")) {
      syncInputs(priceToInputs, urlParams.get("priceTo"));
    }

    // Initialize sort select
    if (urlParams.has("sortBy")) {
      syncInputs(sortBySelects, urlParams.get("sortBy"));
    }

    function syncInputs(inputs, value) {
      inputs.forEach((input) => (input.value = value));
    }

    function syncCheckboxes(checkboxes, checked) {
      checkboxes.forEach((checkbox) => (checkbox.checked = checked));
    }

    document
      .querySelector(".product-detail-filters, .lg\\:block")
      .addEventListener("change", function () {
        submitSearch();
      });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      submitSearch();
    });

    function submitSearch() {
      const params = new URLSearchParams();

      const searchInput = form.querySelector('input[name="name"]');
      if (searchInput && searchInput.value) {
        params.set("name", searchInput.value);
      }

      const visibleFilters = document.querySelector(".product-detail-filters");

      const visibleInStock = visibleFilters.querySelector(".FilterInStock");
      const visiblePreOrder = visibleFilters.querySelector(".FilterPreOrder");
      const visibleOutOfStock =
        visibleFilters.querySelector(".FilterOutOfStock");
      const visiblePriceFrom = visibleFilters.querySelector(".FilterPriceFrom");
      const visiblePriceTo = visibleFilters.querySelector(".FilterPriceTo");
      const visibleSortBy = visibleFilters.querySelector(".SortBy");

      params.set("inStock", visibleInStock.checked ? "1" : "0");
      params.set("preOrder", visiblePreOrder.checked ? "1" : "0");
      params.set("outOfStock", visibleOutOfStock.checked ? "1" : "0");

      if (visiblePriceFrom.value) {
        params.set("priceFrom", visiblePriceFrom.value);
      }
      if (visiblePriceTo.value) {
        params.set("priceTo", visiblePriceTo.value);
      }

      if (visibleSortBy.value) {
        params.set("sortBy", visibleSortBy.value);
      }

      window.location.href = `${form.action}?${params.toString()}`;
    }

    sortBySelects.forEach((select) => {
      select.addEventListener("change", function () {
        syncInputs(sortBySelects, this.value);
      });
    });

    const availabilityReset =
      document.getElementsByClassName("availability-reset");

    for (let i = 0; i < availabilityReset.length; i++) {
      availabilityReset[i].addEventListener("click", function () {
        syncCheckboxes(inStockCheckboxes, false);
        syncCheckboxes(preOrderCheckboxes, false);
        syncCheckboxes(outOfStockCheckboxes, false);
      });
    }

    inStockCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        syncCheckboxes(inStockCheckboxes, this.checked);
      });
    });

    preOrderCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        syncCheckboxes(preOrderCheckboxes, this.checked);
      });
    });

    outOfStockCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        syncCheckboxes(outOfStockCheckboxes, this.checked);
      });
    });

    priceFromInputs.forEach((input) => {
      input.addEventListener("input", function () {
        syncInputs(priceFromInputs, this.value);
      });
    });

    priceToInputs.forEach((input) => {
      input.addEventListener("input", function () {
        syncInputs(priceToInputs, this.value);
      });
    });

    const priceReset = document.getElementsByClassName("price-reset");
    for (let i = 0; i < priceReset.length; i++) {
      priceReset[i].addEventListener("click", function () {
        syncInputs(priceFromInputs, "");
        syncInputs(priceToInputs, "");
      });
    }
  }
}
