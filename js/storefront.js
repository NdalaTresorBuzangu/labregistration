document.addEventListener('DOMContentLoaded', () => {
    const pageType = document.body.dataset.page || '';

    const state = {
        filters: {
            category_id: '',
            brand_id: '',
            price_min: '',
            price_max: '',
        },
        query: '',
        page: 1,
        limit: 12,
        meta: {
            categories: [],
            brands: [],
        },
    };

    const apiBase = document.body.dataset.apiBase || 'actions/product_actions.php';
    const allProductsRoot = document.getElementById('product-grid');
    const paginationRoot = document.getElementById('product-pagination');
    const stateMessage = document.getElementById('product-state-message');
    const searchInputs = document.querySelectorAll('[data-role="product-search-input"]');
    const categorySelectors = document.querySelectorAll('[data-role="filter-category"]');
    const brandSelectors = document.querySelectorAll('[data-role="filter-brand"]');
    const priceMinInputs = document.querySelectorAll('[data-role="filter-price-min"]');
    const priceMaxInputs = document.querySelectorAll('[data-role="filter-price-max"]');
    const filterForms = document.querySelectorAll('[data-role="inline-search-form"]');

    init();

    function init() {
        if (!allProductsRoot && pageType !== 'landing') {
            return;
        }

        // Pull initial query from dataset or URL
        if (document.body.dataset.query) {
            state.query = document.body.dataset.query;
        } else {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('q')) {
                state.query = urlParams.get('q');
            }
            if (urlParams.has('category_id')) {
                state.filters.category_id = urlParams.get('category_id');
            }
            if (urlParams.has('brand_id')) {
                state.filters.brand_id = urlParams.get('brand_id');
            }
            if (urlParams.has('price_min')) {
                state.filters.price_min = urlParams.get('price_min');
            }
            if (urlParams.has('price_max')) {
                state.filters.price_max = urlParams.get('price_max');
            }
        }

        fetchMeta().then(() => {
            populateFilterControls();
            attachEventListeners();

            if (pageType === 'landing') {
                return;
            }

            if (pageType === 'single-product') {
                return;
            }

            loadProducts();
        });
    }

    function fetchMeta() {
        return fetch(`${apiBase}?action=meta`)
            .then((response) => response.json())
            .then((payload) => {
                if (payload.success && payload.data) {
                    state.meta.categories = payload.data.categories || [];
                    state.meta.brands = payload.data.brands || [];
                }
            })
            .catch(() => {
                // Meta load failure shouldn't block core functionality
            });
    }

    function populateFilterControls() {
        const categoryOptions = ['<option value="">All categories</option>'];
        state.meta.categories.forEach((category) => {
            categoryOptions.push(`<option value="${category.cat_id}">${category.cat_name}</option>`);
        });
        categorySelectors.forEach((select) => {
            select.innerHTML = categoryOptions.join('');
            if (state.filters.category_id) {
                select.value = state.filters.category_id;
            }
        });

        const brandOptions = ['<option value="">All brands</option>'];
        state.meta.brands.forEach((brand) => {
            brandOptions.push(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
        });
        brandSelectors.forEach((select) => {
            select.innerHTML = brandOptions.join('');
            if (state.filters.brand_id) {
                select.value = state.filters.brand_id;
            }
        });

        priceMinInputs.forEach((input) => {
            if (state.filters.price_min) {
                input.value = state.filters.price_min;
            }
        });

        priceMaxInputs.forEach((input) => {
            if (state.filters.price_max) {
                input.value = state.filters.price_max;
            }
        });

        searchInputs.forEach((input) => {
            if (state.query) {
                input.value = state.query;
            }
        });
    }

    function attachEventListeners() {
        categorySelectors.forEach((select) => {
            select.addEventListener('change', (event) => {
                state.filters.category_id = event.target.value;
                refreshAfterFilterChange();
            });
        });

        brandSelectors.forEach((select) => {
            select.addEventListener('change', (event) => {
                state.filters.brand_id = event.target.value;
                refreshAfterFilterChange();
            });
        });

        priceMinInputs.forEach((input) => {
            input.addEventListener('change', () => {
                state.filters.price_min = input.value;
                refreshAfterFilterChange();
            });
        });

        priceMaxInputs.forEach((input) => {
            input.addEventListener('change', () => {
                state.filters.price_max = input.value;
                refreshAfterFilterChange();
            });
        });

        filterForms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                const searchField = form.querySelector('[data-role="product-search-input"]');
                const minField = form.querySelector('[data-role="filter-price-min"]');
                const maxField = form.querySelector('[data-role="filter-price-max"]');

                if (searchField) {
                    state.query = searchField.value.trim();
                }
                if (minField) {
                    state.filters.price_min = minField.value;
                }
                if (maxField) {
                    state.filters.price_max = maxField.value;
                }

                refreshAfterFilterChange();
            });
        });

        searchInputs.forEach((input) => {
            const form = input.closest('form');
            if (form) {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    state.query = input.value.trim();
                    refreshAfterFilterChange();
                });
            } else {
                input.addEventListener('keyup', (event) => {
                    if (event.key === 'Enter') {
                        state.query = input.value.trim();
                        refreshAfterFilterChange();
                    }
                });
            }
        });
    }

    function loadProducts() {
        if (!allProductsRoot) {
            return;
        }

        setLoadingState(true);

        const params = new URLSearchParams({ action: state.query ? 'search' : 'list' });
        params.append('limit', state.limit.toString());
        params.append('page', state.page.toString());

        if (state.filters.category_id) {
            params.append('category_id', state.filters.category_id);
        }
        if (state.filters.brand_id) {
            params.append('brand_id', state.filters.brand_id);
        }
        if (state.filters.price_min) {
            params.append('price_min', state.filters.price_min);
        }
        if (state.filters.price_max) {
            params.append('price_max', state.filters.price_max);
        }
        if (state.query) {
            params.append('q', state.query);
        }

        fetch(`${apiBase}?${params.toString()}`)
            .then((response) => response.json())
            .then((payload) => {
                if (!payload.success) {
                    throw new Error(payload.message || 'Unable to load products');
                }

                renderProducts(payload.data || []);
                renderPagination(payload.meta || {});

                if (payload.meta && payload.meta.resources) {
                    state.meta.categories = payload.meta.resources.categories || state.meta.categories;
                    state.meta.brands = payload.meta.resources.brands || state.meta.brands;
                }
            })
            .catch((error) => {
                renderErrorState(error.message);
            })
            .finally(() => {
                setLoadingState(false);
            });
    }

    function renderProducts(products) {
        if (!allProductsRoot) {
            return;
        }

        if (!Array.isArray(products) || products.length === 0) {
            allProductsRoot.innerHTML = '';
            if (stateMessage) {
                stateMessage.innerHTML = '<div class="app-empty-state"><h5 class="fw-semibold mb-2">No products found</h5><p class="mb-0">Try adjusting your filters or search terms.</p></div>';
            }
            return;
        }

        if (stateMessage) {
            stateMessage.innerHTML = '';
        }

        const cards = products.map((product) => {
            const rawImage = product.product_image && product.product_image !== '' ? product.product_image : null;
            const imageUrl = rawImage ? rawImage : 'https://via.placeholder.com/400x320?text=No+Image';
            const safeImage = escapeHtml(imageUrl);
            const category = escapeHtml(product.cat_name || 'Uncategorised');
            const brand = escapeHtml(product.brand_name || 'Unknown brand');
            const title = escapeHtml(product.product_title || 'Untitled product');
            const price = Number(product.product_price || 0).toLocaleString(undefined, { style: 'currency', currency: 'USD' });
            const link = `single_product.php?id=${product.product_id}`;

            return `
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="app-card h-100">
                        <a href="${link}" class="d-block mb-3">
                            <img src="${safeImage}" alt="${title}" class="img-fluid rounded" style="width:100%;height:220px;object-fit:cover;">
                        </a>
                        <span class="badge badge-gradient mb-2">${category}</span>
                        <h5 class="fw-semibold mb-1"><a href="${link}">${title}</a></h5>
                        <p class="text-muted mb-1">${brand}</p>
                        <p class="fw-semibold mb-3">${price}</p>
                        <div class="d-flex gap-2">
                            <a href="${link}" class="btn btn-outline-secondary btn-sm">View details</a>
                            <button class="btn app-button-primary btn-sm add-to-cart-btn" type="button" data-product-id="${product.product_id}" data-quantity="1">
                                <i class="fa fa-shopping-cart"></i> Add to cart
                            </button>
                        </div>
                    </div>
                </div>`;
        }).join('');

        allProductsRoot.innerHTML = `<div class="row g-4">${cards}</div>`;
    }

    function renderPagination(meta) {
        if (!paginationRoot) {
            return;
        }

        const totalPages = meta.pages || 1;
        const currentPage = meta.page || 1;

        if (totalPages <= 1) {
            paginationRoot.innerHTML = '';
            return;
        }

        let buttons = '';
        const createButton = (label, page, disabled = false, active = false) => {
            const classes = ['btn', 'btn-sm', active ? 'app-button-primary' : 'btn-outline-secondary'];
            const disabledAttr = disabled ? 'disabled' : '';
            return `<button type="button" class="${classes.join(' ')}" data-page="${page}" ${disabledAttr}>${label}</button>`;
        };

        buttons += createButton('Prev', currentPage - 1, currentPage === 1);

        const pageRange = buildPageRange(currentPage, totalPages);
        pageRange.forEach((page) => {
            if (page === '…') {
                buttons += '<span class="mx-2 text-muted">…</span>';
                return;
            }
            buttons += createButton(page, page, false, page === currentPage);
        });

        buttons += createButton('Next', currentPage + 1, currentPage >= totalPages);

        paginationRoot.innerHTML = buttons;
        paginationRoot.querySelectorAll('button[data-page]').forEach((button) => {
            button.addEventListener('click', () => {
                const targetPage = Number(button.dataset.page);
                if (!Number.isNaN(targetPage) && targetPage !== state.page && targetPage >= 1 && targetPage <= totalPages) {
                    state.page = targetPage;
                    loadProducts();
                }
            });
        });
    }

    function buildPageRange(current, total) {
        const delta = 2;
        const range = [];
        const start = Math.max(1, current - delta);
        const end = Math.min(total, current + delta);

        for (let page = start; page <= end; page += 1) {
            range.push(page);
        }

        if (start > 1) {
            if (start > 2) {
                range.unshift('…');
            }
            range.unshift(1);
        }

        if (end < total) {
            if (end < total - 1) {
                range.push('…');
            }
            range.push(total);
        }

        return range;
    }

    function renderErrorState(message) {
        if (allProductsRoot) {
            allProductsRoot.innerHTML = '';
        }
        if (stateMessage) {
            stateMessage.innerHTML = `<div class="app-empty-state"><h5 class="fw-semibold mb-2">An error occurred</h5><p class="mb-0">${escapeHtml(message || 'Please try again later.')}</p></div>`;
        }
    }

    function setLoadingState(isLoading) {
        if (!stateMessage) {
            return;
        }

        if (isLoading) {
            stateMessage.innerHTML = '<div class="text-center text-muted py-4">Loading products…</div>';
        } else if (!stateMessage.innerHTML.includes('error') && !stateMessage.innerHTML.includes('No products')) {
            stateMessage.innerHTML = '';
        }
    }

    function redirectToSearch() {
        const params = new URLSearchParams();
        if (state.query) {
            params.append('q', state.query);
        }
        if (state.filters.category_id) {
            params.append('category_id', state.filters.category_id);
        }
        if (state.filters.brand_id) {
            params.append('brand_id', state.filters.brand_id);
        }
        if (state.filters.price_min) {
            params.append('price_min', state.filters.price_min);
        }
        if (state.filters.price_max) {
            params.append('price_max', state.filters.price_max);
        }
        window.location.href = `product_search_result.php?${params.toString()}`;
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value).replace(/[&<>'"]/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#039;',
            '"': '&quot;',
        }[char]));
    }

    function refreshAfterFilterChange() {
        state.page = 1;
        if (pageType === 'landing') {
            redirectToSearch();
        } else {
            loadProducts();
        }
    }
});

