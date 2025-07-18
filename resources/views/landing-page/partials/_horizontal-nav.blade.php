<!-- Horizontal Menu Start -->
<nav id="navbar_main" class="mobile-offcanvas nav navbar navbar-expand-xl hover-nav horizontal-nav py-xl-0" style="padding-left: 10px;">
    <div class="container-fluid p-lg-0">
        <div class="offcanvas-header px-0">
            <div class="navbar-brand ms-3">
                @include('landing-page.components.widgets.logo')
            </div>
            <button class="btn-close float-end px-3"></button>
        </div>

        @php
        $headerSection = App\Models\FrontendSetting::where('key', 'heder-menu-setting')->first();
        $sectionData = $headerSection ? json_decode($headerSection->value, true) : null;
        $settings = App\Models\Setting::whereIn('type', ['service-configurations','OTHER_SETTING'])
        ->whereIn('key', ['service-configurations', 'OTHER_SETTING'])
        ->get()
        ->keyBy('type');

        $serviceconfig = $settings->has('service-configurations') ? json_decode($settings['service-configurations']->value) : null;
        $othersetting = $settings->has('OTHER_SETTING') ? json_decode($settings['OTHER_SETTING']->value) : null;
        @endphp

        @if ($sectionData && isset($sectionData['header_setting']) && $sectionData['header_setting'] == 1)
        <ul class="navbar-nav iq-nav-menu list-unstyled" id="header-menu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.index') ? 'active' : '' }}" href="{{ route('frontend.index') }}">{{__('landingpage.home')}}</a>
            </li>

            {{-- Menu Categories Will be Fetched via AJAX --}}
            <li id="category-menu"></li>

            {{-- Product Menu Categories Will be Fetched via AJAX --}}
            <li id="service-menu"></li>

            {{-- Menu Categories Will be Fetched via AJAX --}}
            <li id="menu-placeholder"></li>

            {{-- Product Menu Categories Will be Fetched via AJAX --}}
            <li id="product-menu-placeholder"></li>

            {{-- Product Menu Categories Will be Fetched via AJAX --}}
            <li id="menu-options"></li>

            {{-- Product Menu Categories Will be Fetched via AJAX --}}
            <li id="product-category-menu"></li>

            {{-- Product Menu Categories Will be Fetched via AJAX --}}
            <li id="product-menu"></li>

            {{-- Product Menu Categories Will be Fetched via AJAX --}}
            <li id="product-menu-options"></li>

            {{-- @if( isset($sectionData['categories']) && $sectionData['categories'] == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('category.*') ? 'active' : '' }}" href="{{ route('category.list') }}">{{__('landingpage.categories')}}</a>
            </li>
            @endif
            @if( isset($sectionData['service']) && $sectionData['service'] == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('service.*') ? 'active' : '' }}" href="{{ route('service.list') }}">{{__('landingpage.services')}}</a>
            </li>
            @endif
            @if(optional($othersetting)->blog == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.list') }}">{{__('landingpage.blogs')}}</a>
            </li>
            @endif --}}
            @if(isset($sectionData['provider']) && $sectionData['provider'] == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.provider.*') ? 'active' : '' }}" href="{{ route('frontend.provider') }}">{{__('landingpage.providers')}}</a>
            </li>
            @endif
            @if(auth()->check() && auth()->user()->user_type == 'user' && isset($sectionData['bookings']) && $sectionData['bookings'] == 1)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('booking.*') ? 'active' : '' }}" href="{{ route('booking.list') }}">{{__('landingpage.bookings')}}</a>
            </li>
            @endif
        </ul>
        @endif
    </div>
    <!-- container-fluid.// -->
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const serviceListRouteBase = "{{ url('/service-list') }}";
        const productListRouteBase = "{{ url('/product-list') }}";
        fetchMenuCategories();           // For Category   ->   sub category   ->  service list
        fetchMenuServices();             // For Sub Category   ->   service   ->  service detail
        fetchMenuOptions();              // For Menu Options   ->   sub menu
        // fetchMenu();
        // fetchProductMenuCategories();
        fetchProductMenuCategories();    //    For Product category   ->   sub category   ->  product list
        fetchProductMenuServices();      // For Product Sub Category   ->   product   ->  product detail
        fetchProductMenuOptions();       // For Product Menu Options   ->   sub menu


        function fetchMenuCategories() {
            fetch("{{ url('/category-menu-fetch') }}")
                .then(response => response.json())
                .then(menuCategories => {
                    let menuHtml = "";
                    menuCategories.forEach(menu => {
                        let submenuHtml = "";
                        if (menu.sub_categories.length > 0) {
                            submenuHtml += `<div class="megamenu"><ul class="submenu-grid" id="submenu${menu.id}">`;
                            menu.sub_categories.forEach(submenu => {
                                submenuHtml += `
                                        <li>
                                            <form action="{{ route('category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                                @csrf
                                                <input type="hidden" name="submenu_id" value="${submenu.id}">
                                            </form>
                                            <a class="dropdown-item" href="${serviceListRouteBase}?subcategory_id=${submenu.id}" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                                ${submenu.name}
                                            </a>
                                        </li>`;
                            });
                            submenuHtml += `</ul></div>`;

                        }

                        menuHtml += `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button" style="position: relative;">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none; position: absolute; right: 0%; font-size: 20px;">+</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                    });

                    // Insert menu items before the placeholder
                    document.getElementById("category-menu").insertAdjacentHTML("beforebegin", menuHtml);
                })
                .catch(error => console.error("Error fetching menu categories:", error));
        }

        function fetchMenuServices() {
            fetch("{{ url('/service-menu-fetch') }}")
                .then(response => response.json())
                .then(menuCategories => {
                    let menuHtml = "";
                    menuCategories.forEach(menu => {
                        let submenuHtml = "";
                        if (menu.services.length > 0) {
                            submenuHtml += `<div class="megamenu"><ul class="submenu-grid" id="submenu${menu.id}">`;
                            menu.services.forEach(submenu => {
                                submenuHtml += `
                                        <li>
                                            <form action="{{ route('category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                                @csrf
                                                <input type="hidden" name="submenu_id" value="${submenu.id}">
                                            </form>
                                            <a class="dropdown-item" href="{{ url('/service-detail') }}/${submenu.id}" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                                ${submenu.name}
                                            </a>
                                        </li>`;
                            });
                            submenuHtml += `</ul></div>`;
                        }

                        menuHtml += `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button" style="position: relative;">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none; position: absolute; right: 0%; font-size: 20px;">+</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                    });

                    // Insert menu items before the placeholder
                    document.getElementById("service-menu").insertAdjacentHTML("beforebegin", menuHtml);
                })
                .catch(error => console.error("Error fetching menu categories:", error));
        }

        function fetchMenu() {
            fetch("{{ url('/category-fetch') }}")
                .then(response => response.json())
                .then(menuCategories => {
                    let menuHtml = "";
                    menuCategories.forEach(menu => {
                        let submenuHtml = "";
                        if (menu.sub_categories.length > 0) {
                            submenuHtml += `<div class="megamenu"><ul class="submenu-grid" id="submenu${menu.id}">`;
                            menu.sub_categories.forEach(submenu => {
                                submenuHtml += `
                                        <li>
                                            <form action="{{ route('category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                                @csrf
                                                <input type="hidden" name="submenu_id" value="${submenu.id}">
                                            </form>
                                            <a class="dropdown-item" href="#" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                                ${submenu.name}
                                            </a>
                                        </li>`;
                            });
                            submenuHtml += `</ul></div>`;

                        }

                        menuHtml += `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button" style="position: relative;">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none; position: absolute; right: 0%; font-size: 20px;">+</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                    });

                    // Insert menu items before the placeholder
                    document.getElementById("menu-placeholder").insertAdjacentHTML("beforebegin", menuHtml);
                })
                .catch(error => console.error("Error fetching menu categories:", error));
        }

        function fetchProductMenuCategories() {
            fetch("{{ url('/product-category-fetch') }}")
                .then(response => response.json())
                .then(menuCategories => {
                    let menuHtml = "";
                    menuCategories.forEach(menu => {
                        let submenuHtml = "";
                        if (menu.subCategories.length > 0) {
                            submenuHtml += `<div class="megamenu"><ul class="submenu-grid" id="submenu${menu.id}">`;
                            menu.subCategories.forEach(submenu => {
                                submenuHtml += `
                                        <li>
                                            <form action="{{ route('category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                                @csrf
                                                <input type="hidden" name="submenu_id" value="${submenu.id}">
                                            </form>
                                            <a class="dropdown-item" href="#" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                                ${submenu.name}
                                            </a>
                                        </li>`;
                            });
                            submenuHtml += `</ul></div>`;

                        }

                        menuHtml += `
                        <li class="nav-item dropdown" hidden>
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button" style="position: relative;">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none; position: absolute; right: 0%; font-size: 20px;">+</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                    });

                    // Insert menu items before the placeholder
                    document.getElementById("product-menu-placeholder").insertAdjacentHTML("beforebegin", menuHtml);
                })
                .catch(error => console.error("Error fetching menu categories:", error));
        }

        function fetchMenuOptions() {
            fetch("{{ url('/menu-fetch') }}")
                .then(response => response.json())
                .then(menuCategories => {
                    let menuHtml = "";
                    menuCategories.forEach(menu => {
                        let submenuHtml = "";
                        if (menu.submenus.length > 0) {
                            submenuHtml += `<div class="megamenu"><ul class="submenu-grid" id="submenu${menu.id}">`;
                            menu.submenus.forEach(submenu => {
                                submenuHtml += `
                                        <li>
                                            <form action="{{ route('category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                                @csrf
                                                <input type="hidden" name="submenu_id" value="${submenu.id}">
                                            </form>
                                            <a class="dropdown-item" href="#" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                                ${submenu.name}
                                            </a>
                                        </li>`;
                            });
                            submenuHtml += `</ul></div>`;

                        }

                        menuHtml += `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button" style="position: relative;">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none; position: absolute; right: 0%; font-size: 20px;">+</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                    });

                    // Insert menu items before the placeholder
                    document.getElementById("menu-options").insertAdjacentHTML("beforebegin", menuHtml);
                })
                .catch(error => console.error("Error fetching menu categories:", error));
        }

        function fetchProductMenuCategories() {
            fetch("{{ url('/product-category-menu-fetch') }}")
                .then(response => response.json())
                .then(menuCategories => {
                    let menuHtml = "";
                    menuCategories.forEach(menu => {
                        let submenuHtml = "";
                        if (menu.sub_categories.length > 0) {
                            submenuHtml += `<div class="megamenu"><ul class="submenu-grid" id="submenu${menu.id}">`;
                            menu.sub_categories.forEach(submenu => {
                                submenuHtml += `
                                        <li>
                                            <form action="{{ route('category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                                @csrf
                                                <input type="hidden" name="submenu_id" value="${submenu.id}">
                                            </form>
                                            <a class="dropdown-item" href="${productListRouteBase}?subcategory_id=${submenu.id}" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                                ${submenu.name}
                                            </a>
                                        </li>`;
                            });
                            submenuHtml += `</ul></div>`;

                        }

                        menuHtml += `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button" style="position: relative;">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none; position: absolute; right: 0%; font-size: 20px;">+</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                    });

                    // Insert menu items before the placeholder
                    document.getElementById("product-category-menu").insertAdjacentHTML("beforebegin", menuHtml);
                })
                .catch(error => console.error("Error fetching menu categories:", error));
        }

        function fetchProductMenuServices() {
            fetch("{{ url('/product-subcategory-fetch') }}")
                .then(response => response.json())
                .then(menuCategories => {
                    let menuHtml = "";
                    menuCategories.forEach(menu => {
                        let submenuHtml = "";
                        if (menu.services.length > 0) {
                            submenuHtml += `<div class="megamenu"><ul class="submenu-grid" id="submenu${menu.id}">`;
                            menu.services.forEach(submenu => {
                                submenuHtml += `
                                        <li>
                                            <form action="{{ route('category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                                @csrf
                                                <input type="hidden" name="submenu_id" value="${submenu.id}">
                                            </form>
                                            <a class="dropdown-item" href="{{ url('/product-detail') }}/${submenu.id}" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                                ${submenu.name}
                                            </a>
                                        </li>`;
                            });
                            submenuHtml += `</ul></div>`;
                        }

                        menuHtml += `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button" style="position: relative;">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none; position: absolute; right: 0%; font-size: 20px;">+</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                    });

                    // Insert menu items before the placeholder
                    document.getElementById("product-menu").insertAdjacentHTML("beforebegin", menuHtml);
                })
                .catch(error => console.error("Error fetching menu categories:", error));
        }

        function fetchProductMenuOptions() {
            fetch("{{ url('/product-menu-fetch') }}")
                .then(response => response.json())
                .then(menuCategories => {
                    let menuHtml = "";
                    menuCategories.forEach(menu => {
                        let submenuHtml = "";
                        if (menu.submenus.length > 0) {
                            submenuHtml += `<div class="megamenu"><ul class="submenu-grid" id="submenu${menu.id}">`;
                            menu.submenus.forEach(submenu => {
                                submenuHtml += `
                                        <li>
                                            <form action="{{ route('product.category.list.filter') }}" method="POST" style="display: none;" id="submenuForm${submenu.id}">
                                                @csrf
                                                <input type="hidden" name="submenu_id" value="${submenu.id}">
                                            </form>
                                            <a class="dropdown-item" href="#" onclick="document.getElementById('submenuForm${submenu.id}').submit();">
                                                ${submenu.name}
                                            </a>
                                        </li>`;
                            });
                            submenuHtml += `</ul></div>`;

                        }

                        menuHtml += `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="menu${menu.id}" role="button" style="position: relative;">
                                ${menu.name}
                                <span class="toggle-arrow toggle-arrow-1" style="display: none; position: absolute; right: 0%; font-size: 20px;">+</span>
                            </a>
                            ${submenuHtml}
                        </li>`;
                    });

                    // Insert menu items before the placeholder
                    document.getElementById("product-menu-options").insertAdjacentHTML("beforebegin", menuHtml);
                })
                .catch(error => console.error("Error fetching menu categories:", error));
        }

        // Toggle Dropdown Menu Functionality
        document.addEventListener("click", function(event) {
            const toggle = event.target.closest(".dropdown-toggle");
            if (toggle) {
                event.preventDefault();

                const parentLi = toggle.closest(".nav-item.dropdown");
                const submenu = parentLi.querySelector(".megamenu");
                const arrow = toggle.querySelector(".toggle-arrow");

                // Toggle current submenu
                const isOpen = submenu.classList.contains("show");

                // Optional: Close all other submenus (for accordion style)
                document.querySelectorAll(".megamenu").forEach(m => m.classList.remove("show"));
                document.querySelectorAll(".toggle-arrow").forEach(a => a.textContent = "+");

                if (!isOpen) {
                    // Make it visible temporarily to compute size
                    submenu.style.display = 'block';
                    submenu.style.visibility = 'hidden';

                    // Remove old alignments
                    submenu.classList.remove("align-left", "align-right", "align-center");

                    // Compute position
                    const rect = submenu.getBoundingClientRect();
                    const viewportWidth = window.innerWidth;

                    // Apply correct alignment
                    if (rect.left < 500) {
                        submenu.classList.add("align-left");
                    } else if (rect.right > 1000) {
                        submenu.classList.add("align-right");
                    } else {
                        submenu.classList.add("align-center");
                    }

                    // Now actually show it
                    submenu.style.display = '';
                    submenu.style.visibility = '';
                    submenu.classList.add("show");
                    if (arrow) arrow.textContent = "−";


                } else {
                    submenu.classList.remove("show");
                    if (arrow) arrow.textContent = "+";
                }

            }
        });

    });
</script>