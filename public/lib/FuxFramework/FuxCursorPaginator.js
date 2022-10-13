/**
 * Create a customizable cursor pagination system
 * @param {Object} options Options of the component
 * @param {Function} options.onItemRender A function which return a DOM element to render in place of the i-th item
 * @param {Function} options.onItemSkeletonRender A function which return a DOM element to render as placeholder item while fetching new items
 * @param {Function} options.onPageRequest A function which return a Promise that resolve with the requested page
 * @param {Function} options.onEmptyPage A function which return a DOM element to render when no item to display
 * @param {Element} options.container A DOM element to use as container of the pagination system
 * @param {Element} options.controlsContainer A DOM element to use as container of control buttons
 * @param {String} options.itemsContainerClassName
 * */
function FuxCursorPaginator(options) {

    /**
     * @param {Object[]} page.data The items of the page
     * @param {Number} page.max_items
     * @param {Number} page.total The number of total items of the pagination
     * @param {String} page.prev The prev cursor
     * @param {String} page.next The next cursor
     * */
    const render = page => {
        itemsContainer.className = options.itemsContainerClassName || '';
        itemsContainer.innerHTML = '';
        paginationPage.data.map(i => itemsContainer.appendChild(options.onItemRender(i)))
        if (!paginationPage.data || !paginationPage.data.length) {
            itemsContainer.appendChild(options.onEmptyPage());
        }

        if (!page.prev && !page.next) {
            controlsContainer.style.display = 'none';
        } else {
            controlsContainer.style.display = null;
        }

        prevBtn.disabled = !page.prev;
        nextBtn.disabled = !page.next;

        paginationLabel.innerHTML = `Pagina ${currentPage} di ${Math.ceil(page.total / page.max_items)}`
    }

    const fetch = cursor => {
        //Using placeholders
        if (options.onItemSkeletonRender) {
            const placeholderNum = paginationPage.max_items || 5;
            itemsContainer.innerHTML = '';
            for (let i = 0; i < placeholderNum; i++) itemsContainer.appendChild(options.onItemSkeletonRender());
        }

        options.onPageRequest(cursor)
            .then(page => {
                //The field "total" has the correct info only when cursor is null, otherwise it contains wrong informations
                // caused by conditions injected in the query based on the cursor settings.
                paginationPage = {...page, total: cursor ? paginationPage.total : page.total};
                render(paginationPage);
            });
    }

    const handleGoPrev = _ => {
        currentPage -= 1;
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        paginationPage.prev && fetch(paginationPage.prev);
    }
    const handleGoNext = _ => {
        currentPage += 1;
        prevBtn.disabled = true;
        nextBtn.disabled = true;
        paginationPage.next && fetch(paginationPage.next);
    }


    let paginationPage = {
        data: [], //The items of the page
        max_items: 0,
        total: 0, //The number of total items of the pagination
        prev: '', //The prev cursor
        next: '' //The next cursor
    };
    let currentPage = 1;

    const itemsContainer = options.controlsContainer ? options.container : document.createElement('div');
    const controlsContainer = document.createElement('div');
    controlsContainer.innerHTML = `
        <div class="d-flex justify-content-center" style="display: none;">
            <nav class="text-center">
                <ul class="pagination">
                    <li class="page-item"><button class="page-link" data-role="prev">Precedente</button></li>
                    <li class="page-item"><button class="page-link" data-role="next">Successivo</button></li>
                </ul>
                <span data-role="label">Pagina 1 di 1</span>
            </nav>
        </div>
        `;
    const paginationLabel = controlsContainer.querySelector('[data-role="label"]');
    const prevBtn = controlsContainer.querySelector('[data-role="prev"]');
    prevBtn.addEventListener('click', handleGoPrev);
    const nextBtn = controlsContainer.querySelector('[data-role="next"]');
    nextBtn.addEventListener('click', handleGoNext);

    if (options.controlsContainer) {
        options.controlsContainer.appendChild(controlsContainer);
    } else {
        options.container.appendChild(itemsContainer);
        options.container.appendChild(controlsContainer);
    }

    fetch(null);

    return {
        reset: function () {
            paginationPage = {
                data: [], //The items of the page
                max_items: 0,
                total: 0, //The number of total items of the pagination
                prev: '', //The prev cursor
                next: '' //The next cursor
            };
            currentPage = 1;
            fetch(null);
        }
    }
}


(function () {
    var head = document.head || document.getElementsByTagName('head')[0];
    var style = document.createElement('style');
    head.appendChild(style);
    //language=CSS
    style.appendChild(document.createTextNode(`
        @keyframes shineAnimation {
            0% {
                background-position: 0% 0%;
            }
            100% {
                background-position: -135% 0%;
            }
        }
        
        .skeleton-placeholder{
            transition: 0.3s;
            background: linear-gradient(-90deg, #dedede 0%, #fcfcfc 50%, #dedede 100%);
            background-size: 400% 400%;
            animation: shineAnimation 1s infinite;
            opacity:1;
        }
    `));
})();

FuxCursorPaginator.utility = {
    basicSkeletonRender: (w, h) => _ => {
        const el = document.createElement('div');
        el.className = 'card border-0 p-2 shadow-sm mb-1 skeleton-placeholder';
        if (w) el.style.width = w + "px";
        if (h) el.style.height = h + "px";
        return el;
    },
    tableRowSkeletonRender: (w, h) => _ => {
        const row = document.createElement('tr');
        const skeleton = document.createElement('td');
        skeleton.className = 'skeleton-placeholder'
        skeleton.colSpan = 999;
        if (w) skeleton.style.width = w + "px";
        if (h) skeleton.style.height = h + "px";
        row.appendChild(skeleton)
        return row;
    }
}