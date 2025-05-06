@extends('layouts.app')
@section('title', 'Halaman Kasir | TrackBooth')
@section('page', 'Halaman Kasir')
@section('content')
<div class="app-content">
    <div class="container-fluid">
        @include('components.alert')

        <!-- Tombol Keranjang Sticky -->
        <!-- Tombol Nama Meja -->
        @if ($meja)
        <button class="btn btn-secondary w-100 mb-2" disabled>
            <i class="bi bi-info-circle"></i> Meja: {{ $meja->nama_meja }}
        </button>
        @endif

        <button id="cartToggle" class="btn btn-sm btn-primary w-100 sticky-cart">
            <i class="bi bi-cart-fill"></i> Keranjang
        </button>

        <!-- Kontainer Keranjang Sticky -->
        <div id="cartContainer" class="cart-container d-none">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-center">Keranjang</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table">
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cartTable">

                            </tbody>
                        </table>
                    </div>
                    <h5 class="text-end mt-2">Total: <span id="cartTotal">Rp 0</span></h5>
                    <button class="btn btn-success w-100 mt-2" id="checkoutButton">Bayar</button>
                </div>
            </div>
        </div>
        {{-- MODAL KONFIRMASI --}}
        <!-- Modal Konfirmasi Pembayaran -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <p>Silakan pilih metode pembayaran:</p>
                <select id="paymentMethod" class="form-select">
                    <option value="cash">CASH</option>
                    <option value="qris">QRIS</option>
                </select>
                <p class="mt-3">Apakah Anda yakin ingin melanjutkan transaksi?</p>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button id="confirmPayment" class="btn btn-success">Konfirmasi</button>
                </div>
            </div>
            </div>
        </div>
        {{-- end MOdal konfirmasi --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        {{-- <input type="text" id="searchProduct" class="form-control mb-2" placeholder="Cari Produk..."> --}}
                        <div class="position-relative">
                            <input type="text" id="searchProduct" class="form-control mb-2 pe-5" placeholder="Cari Produk...">
                            <button type="button" id="clearSearch" class="btn btn-transparent position-absolute end-0 top-50 translate-middle-y me-3 border-0" 
                                style="display: none;">
                                <i class="bi bi-x-circle text-muted"></i>
                            </button>
                        </div>                        
                        <div class="row g-2" id="productList">
                            @php
                                use Illuminate\Support\Facades\Storage;
                            @endphp
                            @foreach($products as $product)
                                <div class="col-6 col-md-3 col-lg-3 product-item" data-name="{{ strtolower($product->nama_produk) }}">
                                    <div class="card product-card text-center p-2" data-id="{{ $product->id }}">
                                        @php
                                            $gambarPath = public_path($product->gambar);
                                            $gambarURL = file_exists($gambarPath) && !empty($product->gambar) ? asset($product->gambar) : null;
                                        @endphp
                                        @if($gambarURL)
                                            <img src="{{ $gambarURL }}" class="card-img-top" alt="{{ $product->nama_produk }}" style="height: 150px;" loading="lazy">
                                        @else
                                            <div class="d-flex justify-content-center align-items-center" style="height: 150px; background: #f8f9fa;">
                                                <svg width="50" height="50" fill="gray" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.002 1a1 1 0 0 1 .99.858L14 2v10a1 1 0 0 1-.883.993L13 13H3a1 1 0 0 1-.993-.883L2 12V2a1 1 0 0 1 .883-.993L3 1h10zM3 2v10h10V2H3zm4.828 3.172a3 3 0 1 1 4.244 4.244 3 3 0 0 1-4.244-4.244z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    
                                        {{-- <img src="{{ asset($product->gambar) }}"  class="card-img-top" alt="{{ $product->nama_produk }}" style="height: 150px; " loading="lazy"> --}}
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <h6 class="card-title fw-bold">{{ $product->nama_produk }}</h6>
                                            <p class="text-primary fs-6 mb-1">Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</p>
                                            <p class="text-muted small">Stok: <strong>{{ $product->stok_produk }}</strong></p>
                                            <button class="btn btn-sm btn-success w-100 add-to-cart" 
                                                data-id="{{ $product->id }}" 
                                                data-name="{{ $product->nama_produk }}" 
                                                data-price="{{ $product->harga_produk }}" 
                                                data-stock="{{ $product->stok_produk }}" 
                                                {{ $product->stok_produk == 0 ? 'disabled' : '' }}>
                                                {{ $product->stok_produk == 0 ? 'Habis' : 'Tambah' }}
                                            </button>
                                        </div>                                        
                                    </div>
                                </div>                            
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /* Sticky Cart Button */
    .sticky-cart {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: #007bff;
        color: white;
        padding: 10px;
        font-size: 18px;
        text-align: center;
    }

    /* Sticky Cart Container */
    .cart-container {
        position: sticky;
        top: 50px;
        z-index: 999;
        /* background: white; */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        /* border-radius: 5px; */
        width: 100%;
    }

    .table-responsive {
        max-height: 200px; /* Ubah sesuai kebutuhan */
        overflow-y: auto;
    }


    /* Animasi Cart */
    .cart-container.d-none {
        display: none !important;
    }
    .sticky-cart {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: #007bff;
        color: white;
        padding: 10px;
        font-size: 18px;
        text-align: center;
    }
    .cart-container {
        transition: all 0.3s ease-in-out;
    }
    .cart-container.d-none {
        display: none !important;
    }
</style>
@endsection
@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let searchInput = document.getElementById("searchProduct");
        let clearButton = document.getElementById("clearSearch");
        let productItems = document.querySelectorAll(".product-item");

        // Fungsi filter produk
        function filterProducts() {
            let searchValue = searchInput.value.toLowerCase();
            
            productItems.forEach(function (item) {
                let productName = item.getAttribute("data-name");

                if (productName.includes(searchValue)) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });

            // Tampilkan tombol clear jika input tidak kosong
            clearButton.style.display = searchValue ? "block" : "none";
        }

        // Event listener untuk input pencarian
        searchInput.addEventListener("keyup", filterProducts);

        // Event listener untuk tombol clear
        clearButton.addEventListener("click", function () {
            searchInput.value = "";
            filterProducts(); // Reset filter
        });
    });
</script>

<script>
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    document.getElementById('cartToggle').addEventListener('click', function() {
        document.getElementById('cartContainer').classList.toggle('d-none');
    });
    function renderCart() {
        let cartTable = document.getElementById('cartTable');
        let cartTotal = document.getElementById('cartTotal');
        cartTable.innerHTML = '';
        let total = 0;

        cart.forEach((item, index) => {
            let subtotal = item.price * item.qty;
            total += subtotal;
            cartTable.innerHTML += `
                <tr>
                    <td>${item.name}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="updateQty(${index}, -1)">-</button>
                        ${item.qty}
                        <button class="btn btn-sm btn-success" onclick="updateQty(${index}, 1)">+</button>
                    </td>
                    <td>Rp ${subtotal.toLocaleString()}</td>
                    <td><button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">Hapus</button></td>
                </tr>`;
        });
        cartTotal.innerText = `Rp ${total.toLocaleString()}`;
    }
    function updateQty(index, change) {
        if (cart[index].qty + change > 0 && cart[index].qty + change <= cart[index].stock) {
            cart[index].qty += change;
        } else {
            alert('Stok tidak mencukupi atau tidak boleh kurang dari 1!');
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
    }

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            let id = this.dataset.id;
            let name = this.dataset.name;
            let price = parseInt(this.dataset.price);
            let stock = parseInt(this.dataset.stock);
            let existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                if (existingItem.qty < stock) {
                    existingItem.qty++;
                } else {
                    alert('Stok tidak mencukupi!');
                    return;
                }
            } else {
                cart.push({ id, name, price, qty: 1, stock });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
            document.getElementById('cartContainer').classList.remove('d-none');
        });
    });

    function removeFromCart(index) {
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
    }

    document.getElementById('checkoutButton').addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Keranjang kosong!');
            return;
        }

        let paymentModalEl = document.getElementById('paymentModal');
        if (!paymentModalEl) {
            console.error("Error: Elemen modal tidak ditemukan.");
            return;
        }

        var paymentModal = bootstrap.Modal.getOrCreateInstance(paymentModalEl);
        paymentModal.show();
    });


    // Konfirmasi Pembayaran
    document.getElementById('confirmPayment').addEventListener('click', function() {
        let paymentMethodEl = document.getElementById('paymentMethod');
        if (!paymentMethodEl) {
            console.error("Error: Elemen metode pembayaran tidak ditemukan.");
            alert("Metode pembayaran tidak valid!");
            return;
        }

        let paymentMethod = paymentMethodEl.value;

        fetch("/kasir/sales", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ cart, paymentMethod })
        })
        .then(response => response.json())
        .then(data => {
            // console.log("Response dari server:", data);
            localStorage.removeItem('cart');
            let paymentModalEl = document.getElementById('paymentModal');
            if (paymentModalEl) {
                var paymentModal = bootstrap.Modal.getOrCreateInstance(paymentModalEl);
                paymentModal.hide();
            }
            cart = [];
            location.reload();
            // alert(data.message);
            // renderCart();

            // Panggil fungsi untuk refresh stok produk
            // location.reload(); // Refresh halaman

            // updateProductStock();
        })
        .catch(error => console.error('Error:', error));
    });
    // Fungsi untuk mengambil stok terbaru dan memperbarui tampilan produk
    function updateProductStock() {
        fetch('/api/products') // Pastikan endpoint ini mengembalikan daftar produk terbaru
            .then(response => response.json())
            .then(products => {
                let productList = document.getElementById('productList');
                productList.innerHTML = ''; // Kosongkan list

                products.forEach(product => {
                    // Format harga dengan pemisah ribuan
                    const formattedPrice = new Intl.NumberFormat('id-ID').format(product.harga_produk);

                    // Handle gambar (gunakan placeholder jika tidak ada gambar)
                    const imageSrc = product.gambar 
                        ? `/storage/${product.gambar}` // Gunakan path dari database
                        : '/img/no-image.jpg'; // Placeholder jika gambar tidak ada

                    // Cek stok untuk tombol
                    const stockStatus = product.stok_produk > 0 ? 'Tambah' : 'Habis';
                    const disabledAttr = product.stok_produk > 0 ? '' : 'disabled';

                    let productHTML = `
                        <div class="col-6 col-md-3 col-lg-3 product-item" data-name="${product.nama_produk.toLowerCase()}">
                            <div class="card product-card text-center p-2" data-id="${product.id}">
                                <img src="${imageSrc}" 
                                    class="card-img-top" 
                                    alt="${product.nama_produk}"
                                    style="height: 150px; object-fit: cover;">
                                    
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h6 class="card-title fw-bold">${product.nama_produk}</h6>
                                    <p class="text-primary fs-6 mb-1">Rp ${formattedPrice}</p>
                                    <p class="text-muted small">Stok: <strong>${product.stok_produk}</strong></p>
                                    
                                    <button class="btn btn-sm btn-success w-100 add-to-cart" 
                                        data-id="${product.id}" 
                                        data-name="${product.nama_produk}" 
                                        data-price="${product.harga_produk}" 
                                        data-stock="${product.stok_produk}"
                                        ${disabledAttr}>
                                        ${stockStatus}
                                    </button>
                                </div>                                        
                            </div>
                        </div>
                    `;
                    
                    productList.innerHTML += productHTML;
                });

                // Re-bind event listeners untuk tombol "Tambah"
                bindAddToCart();
            })
            .catch(error => console.error('Gagal memperbarui stok produk:', error));
    }

    // Fungsi untuk mengikat event listener ke tombol "Tambah"
    function bindAddToCart() {
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                let id = this.dataset.id;
                let name = this.dataset.name;
                let price = parseInt(this.dataset.price);
                let stock = parseInt(this.dataset.stock);
                let existingItem = cart.find(item => item.id === id);

                if (existingItem) {
                    if (existingItem.qty < stock) {
                        existingItem.qty++;
                    } else {
                        alert('Stok tidak mencukupi!');
                        return;
                    }
                } else {
                    cart.push({ id, name, price, qty: 1, stock });
                }

                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
                document.getElementById('cartContainer').classList.remove('d-none');
            });
        });
    }
    // function bindAddToCart() {
    //     document.querySelectorAll('.add-to-cart').forEach(button => {
    //         button.addEventListener('click', function() {
    //             let id = this.dataset.id;
    //             let name = this.dataset.name;
    //             let price = parseInt(this.dataset.price);
    //             let stock = parseInt(this.dataset.stock);
    //             let existingItem = cart.find(item => item.id === id);

    //             if (existingItem) {
    //                 if (existingItem.qty < stock) {
    //                     existingItem.qty++;
    //                 } else {
    //                     alert('Stok tidak mencukupi!');
    //                     return;
    //                 }
    //             } else {
    //                 cart.push({ id, name, price, qty: 1, stock });
    //             }

    //             localStorage.setItem('cart', JSON.stringify(cart));
    //             renderCart();
    //             document.getElementById('cartContainer').classList.remove('d-none');
    //         });
    //     });
    // }


    renderCart();
</script>   
@endsection
