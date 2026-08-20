<div class="sigap-tabs" id="sigap-admin-master-data__tabs">
    <a href="{{ route('admin.master-data.departemen.index') }}"
       class="sigap-tabs__item {{ request()->routeIs('admin.master-data.departemen.*') ? 'sigap-tabs__item--active' : '' }}">
        Departemen
    </a>
    <a href="{{ route('admin.master-data.kategori.index') }}"
       class="sigap-tabs__item {{ request()->routeIs('admin.master-data.kategori.*') ? 'sigap-tabs__item--active' : '' }}">
        Kategori Masalah
    </a>
    <a href="{{ route('admin.master-data.sla') }}"
       class="sigap-tabs__item {{ request()->routeIs('admin.master-data.sla') ? 'sigap-tabs__item--active' : '' }}">
        SLA & Prioritas
    </a>
</div>