/**
 * panggonan-nav-fix.js
 * Fix: Builder sets inline width on .w-dropdown-list which overrides CSS.
 * This script overrides it back to auto so long labels like "Tanya Jawab" fit.
 */
(function () {
  function fixDropdownWidth() {
    var dropdowns = document.querySelectorAll('.nav-menu .w-dropdown-list, .nav-menu .dropdown-list');
    dropdowns.forEach(function (el) {
      // Override inline style that Builder injects ONLY for the navbar
      el.style.setProperty('width', 'auto', 'important');
      el.style.setProperty('min-width', '160px', 'important');
      el.style.setProperty('overflow', 'visible', 'important');
    });
  }

  // Run on every dropdown toggle click
  document.addEventListener('click', function (e) {
    var toggle = e.target.closest('.w-dropdown-toggle, .navbar-dropdown');
    if (toggle) {
      setTimeout(fixDropdownWidth, 50);
      setTimeout(fixDropdownWidth, 200);
    }
  });

  // Also run on hover
  document.addEventListener('mouseover', function (e) {
    var toggle = e.target.closest('.w-dropdown-toggle, .navbar-dropdown');
    if (toggle) {
      setTimeout(fixDropdownWidth, 50);
    }
  });

  // Run once on load as well (in case dropdown is pre-opened)
  document.addEventListener('DOMContentLoaded', fixDropdownWidth);
  window.addEventListener('load', fixDropdownWidth);
})();

