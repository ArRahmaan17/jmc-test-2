<ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item active">
        <a href="index.html" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Barang Masuk</div>
        </a>
    </li>

    <!-- Layouts -->
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-layout"></i>
            <div data-i18n="Layouts">Master Data</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item">
                <a href="{{ route('category-management.index') }}" class="menu-link">
                    <div data-i18n="Without menu">Category</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('sub-category-management.index') }}" class="menu-link">
                    <div data-i18n="Without navbar">Sub Category</div>
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item">
        <a href="{{ route('user-management.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Management User</div>
        </a>
    </li>
</ul>
