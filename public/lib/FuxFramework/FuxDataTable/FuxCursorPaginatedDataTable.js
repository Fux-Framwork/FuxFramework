/**
 * Create
 * @param {Object} options Options of the component
 * @param {Function} options.fetchUrl
 * @param {Function} options.fetchParams
 * @param {Function} options.emptyText
 * @param {Function} options.columnNameOverride A function which returns a string (also HTML string) to use in place of
 * @param {Function} options.filters An object where each key represent a possible column name and each
 * value is a function which transform a specific value. The function has the following arguments (cellValue, rowData)
 * @param {Function} options.skipColumns An array of columns names which will not be shown
 * @param {Object} options.cellValueOverrideSettings An object where each key represent a possible column name and each
 * value is a function which transform a specific value. The function has the following arguments (cellValue, rowData)
 * @param {Element} options.container A DOM element to use as container of the table
 * */
function FuxCursorPaginatedDataTable(options) {

    const table = document.createElement('table');
    table.className = "table w-100";

    /** @MARK Header */
    const tableHeader = document.createElement('thead');
    tableHeader.className = "bg-primary text-white";
    table.appendChild(tableHeader);

    function updateTableHeader(cols) {
        const row = document.createElement('tr');
        cols.map(colName => {
            if (!options.skipColumns || !options.skipColumns.find(c => c == colName)) {
                row.innerHTML += `<th><b>${options.columnNameOverride ? options.columnNameOverride(colName) : colName}</b></div>`;
            }
        });
        tableHeader.innerHTML = '';
        tableHeader.appendChild(row);
    }

    /** @MARK Filters */
    let filtersValue = {};
    const filtersRow = document.createElement('tr');
    filtersRow.className = "bg-light text-white d-none";
    table.appendChild(filtersRow);

    let __filter_rendered = false;

    function renderFiltersRow(cols) {
        if (!options.filters || __filter_rendered) return;
        __filter_rendered = true;
        filtersRow.classList.remove('d-none');
        cols.map(field => {
            if (options.skipColumns && options.skipColumns.find(c => c == field)) return;
            const filter = options.filters[field] || null;
            const filterCol = document.createElement('td');
            if (filter) {
                filterCol.appendChild(filter ? filter.getElement(filter.data.fieldAlias || field, handleFilterChange) : document.createElement('div'));
            } else {
                const emptyEl = document.createElement('div');
                emptyEl.innerHTML = '&nbsp;'
                filterCol.appendChild(emptyEl);
            }

            filtersRow.appendChild(filterCol);
        });
    }

    let __filter_change_timeout = null;

    function handleFilterChange(name, value, filterCondition, fetchImmediately) {
        if (filtersValue[name] && filtersValue[name].value == value) return;
        if (__filter_change_timeout) clearTimeout(__filter_change_timeout);
        filtersValue[name] = {value: value, condition: filterCondition};
        if (value === '' && filtersValue[name] != undefined) delete filtersValue[name];
        __filter_change_timeout = setTimeout(paginator.reset, fetchImmediately ? 1 : 300);
    }


    /** @MARK Body */
    const tableBody = document.createElement('tbody');
    table.appendChild(tableBody);


    /** @MARK Controls */
    const controlsContainer = document.createElement('div');

    options.container.appendChild(table);
    options.container.appendChild(controlsContainer);


    const paginator = FuxCursorPaginator({
        container: tableBody,
        controlsContainer: controlsContainer,
        onItemRender: function (row) {
            const rowEl = document.createElement('tr');
            rowEl.className = "bg-white";
            const cols = Object.keys(row);
            updateTableHeader(cols);
            renderFiltersRow(cols);
            cols.map(k => {
                if (!options.skipColumns || !options.skipColumns.find(c => c == k)) {
                    const v = options.cellValueOverrideSettings && options.cellValueOverrideSettings[k] ?
                        options.cellValueOverrideSettings[k](row[k], row) :
                        row[k];
                    rowEl.innerHTML += `<td>${v}</td>`
                }
            });
            return rowEl;
        },
        onItemSkeletonRender: FuxCursorPaginator.utility.tableRowSkeletonRender(null, 58),
        onPageRequest: function (cursor) {
            return new Promise((resolve, reject) => {
                const url = options.fetchUrl;
                const params = {...(options.fetchParams || {}), cursor: cursor};
                //Aggiungo i filtri ai parametri
                const filters = {};
                Object.keys(filtersValue).map(f => {
                    if (filtersValue[f]) {
                        filters[f] = filtersValue[f];
                    }
                });
                if (Object.keys(filters).length) params.filters = btoa(JSON.stringify(filters));
                FuxHTTP.get(url, params, FuxHTTP.RESOLVE_DATA, FuxHTTP.REJECT_MESSAGE)
                    .then(paginationPage => resolve(paginationPage))
                    .catch(FuxSwalUtiltiy.error);
            });
        },
        onEmptyPage: function () {
            const el = document.createElement('div');
            el.innerHTML = `
                    <h3 class="text-center">Non c'è nulla da visualizzare qui</h3>
                `;
            return el;
        }
    });
}

FuxCursorPaginatedDataTable.utility = {
    lodashColumnNameOverride: col => col.toLowerCase().replace(/_/g, " ").replace(/\b[a-z]/g, l => l.toUpperCase()),
};

