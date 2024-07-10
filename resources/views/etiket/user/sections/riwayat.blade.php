@extends('etiket.user.dashboard')

@section('sub-css')
@endsection


@section('sub-main')
    <script>
        const sidebarMenu = document.querySelectorAll(".dashboard-sidebar-btn");
        sidebarMenu.forEach((o, i) => {
            sidebarMenu[i].classList.remove("active");
        });
        const riwayat = document.querySelector("#dashboard-riwayat");
        riwayat.classList.add("active");
    </script>
@endsection
