function debounce(func, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}

document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("live-search-input");
    const resultsContainer = document.getElementById("live-search-results");

    if (!searchInput || !resultsContainer) return;

    searchInput.addEventListener("input", debounce(function () {
        const query = this.value.trim();

        if (query.length < 2) {
            resultsContainer.classList.remove("show");
            setTimeout(() => resultsContainer.innerHTML = '', 300);
            return;
        }

        fetch(routeUrl + "?query=" + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                let hasResults = false;

                for (const group in data) {
                    if (data[group].length > 0) {
                        hasResults = true;

                        const groupTitles = {
                            products: "Sản phẩm",
                            categories: "Danh mục",
                            orders: "Đơn hàng",
                            news: "Tin tức"
                        };
                        
                        resultsContainer.innerHTML += `<h5 class="group-title">${groupTitles[group] || "Khác"}</h5>`;
                        

                        data[group].forEach(item => {
                            let html = "";

                            if (group === "products") {
                                const name = item.product_name || "Không tên";
                                const price = item.price
                                    ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price)
                                    : "Không giá";
                                const img = item.image || "https://via.placeholder.com/60";

                                html = `
                                    <div class="live-search-item">
                                        <a href="/products/${item.id}">
                                            <img src="${img}" alt="${name}" class="live-search-thumb">
                                            <div class="live-search-info">
                                                <p class="live-search-title">${name}</p>
                                                <p class="live-search-price">${price}</p>
                                            </div>
                                        </a>
                                    </div>`;
                            }

                            else if (group === "categories") {
                                const name = item.category_name || "Không tên";
                                const img = item.image || "https://via.placeholder.com/60";
                                html = `
                                    <div class="live-search-item">
                                        <a href="/categories/${item.id}">
                                            <img src="${img}" alt="${name}" class="live-search-thumb">
                                            <div class="live-search-info">
                                                <p class="live-search-title">${name}</p>
                                            </div>
                                        </a>
                                    </div>`;
                            }

                            else if (group === "orders") {
                                const createdDate = new Date(item.created_at).toLocaleDateString('vi-VN');
                                html = `
                                    <div class="live-search-item">
                                        <a href="/orders/${item.id}">
                                            <div class="live-search-info">
                                                <p><strong>Đơn hàng #${item.id}</strong></p>
                                                <p>Ngày đặt: ${createdDate}</p>
                                                <p>Tổng: ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.tong_tien)}</p>
                                            </div>
                                        </a>
                                    </div>`;
                            }

                            else if (group === "news") {
                                const title = item.title || "Không tiêu đề";
                                const img = item.image || "https://via.placeholder.com/60";
                                html = `
                                    <div class="live-search-item">
                                        <a href="/news/${item.id}">
                                            <img src="${img}" alt="${title}" class="live-search-thumb">
                                            <div class="live-search-info">
                                                <p class="live-search-title">${title}</p>
                                            </div>
                                        </a>
                                    </div>`;
                            }

                            resultsContainer.innerHTML += html;
                        });
                    }
                }

                if (!hasResults) {
                    resultsContainer.innerHTML = `<p class="text-center p-2">Không có kết quả nào!</p>`;
                }

                resultsContainer.classList.add("show");
            })
            .catch(error => console.error('Lỗi tìm kiếm:', error));
    }, 300));

    // Ẩn khi click ngoài
    document.addEventListener("click", function (event) {
        if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.classList.remove("show");
        }
    });
});
