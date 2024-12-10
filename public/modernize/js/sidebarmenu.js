document.addEventListener("DOMContentLoaded", function () {
  "use strict";

  // Mendapatkan URL saat ini
  var url = window.location.href;

  // Mendapatkan path dari URL dengan menghapus protokol dan host
  var path = url.replace(window.location.protocol + "//" + window.location.host + "/", "");

  // Memilih semua elemen link pada sidebar
  var sidebarLinks = document.querySelectorAll("ul#sidebarnav a");

  // Mencari elemen sidebar yang sesuai dengan URL saat ini atau path
  var element = Array.from(sidebarLinks).find(function (link) {
    return link.href === url || link.getAttribute("href") === path;
  });

  // Jika elemen ditemukan, aktifkan status 'active' dan 'selected' pada elemen-elemen terkait
  if (element) {
    // Iterasi ke atas hingga mencapai '.sidebar-nav' atau 'ul#sidebarnav'
    var parent = element.closest("ul#sidebarnav");
    while (element) {
      // Jika elemen adalah 'li' dengan anak 'a', tambahkan class 'active'
      if (element.tagName === "LI" && element.querySelector("a")) {
        element.querySelector("a").classList.add("active");
        if (!element.closest("ul#sidebarnav")) {
          element.classList.add("active");
        } else {
          element.classList.add("selected");
        }
      }
      // Jika elemen bukan 'ul' dan tidak memiliki anak 'a', tambahkan class 'selected'
      else if (element.tagName !== "UL" && !element.querySelector("a")) {
        element.classList.add("selected");
      }
      // Jika elemen adalah 'ul', tambahkan class 'in'
      else if (element.tagName === "UL") {
        element.classList.add("in");
      }
      element = element.parentElement; // Lanjutkan iterasi ke elemen induk
    }
  }

  // Mengatur elemen yang diklik sebagai 'active' dan memperlihatkan menu yang terkait
  document.querySelectorAll("#sidebarnav a").forEach(function (link) {
    link.addEventListener("click", function (e) {
      // Jika elemen belum 'active'
      if (!this.classList.contains("active")) {
        // Tutup menu yang terbuka dan hapus kelas 'active' pada semua elemen
        this.closest("ul").querySelectorAll("ul").forEach(function (ul) {
          ul.classList.remove("in");
        });
        this.closest("ul").querySelectorAll("a").forEach(function (a) {
          a.classList.remove("active");
        });

        // Buka menu baru dan tambahkan kelas 'active'
        var nextUl = this.nextElementSibling;
        if (nextUl && nextUl.tagName === "UL") {
          nextUl.classList.add("in");
        }
        this.classList.add("active");
      } else {
        // Jika elemen sudah 'active', hapus kelas 'active' dan tutup submenu
        this.classList.remove("active");
        this.closest("ul").classList.remove("active");
        var nextUl = this.nextElementSibling;
        if (nextUl && nextUl.tagName === "UL") {
          nextUl.classList.remove("in");
        }
      }
    });
  });

  // Mencegah default behavior pada elemen dengan class 'has-arrow'
  document.querySelectorAll("#sidebarnav > li > a.has-arrow").forEach(function (arrowLink) {
    arrowLink.addEventListener("click", function (e) {
      e.preventDefault();
    });
  });
});
