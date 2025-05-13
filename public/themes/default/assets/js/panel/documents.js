document.addEventListener("DOMContentLoaded", function() {
    "use strict";
    const list = new List('table-default', {
        sortClass: 'table-sort',
        listClass: 'table-tbody',
        valueNames: [
            'sort-name',
            'sort-group',
            'sort-remaining-words',
            'sort-remaining-images',
            'sort-country',
            'sort-status',
            'sort-quantity',
            'sort-score',
            { attr: 'data-date', name: 'sort-date' },
        ],
        page: 25,
        pagination: {
            innerWindow: 1,
            left: 0,
            right: 0,
            paginationClass: "pagination",
        },
    });
})
