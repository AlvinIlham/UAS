function openProfileModal() {
  document.getElementById("profileModal").style.display = "flex";
}
function closeProfileModal() {
  document.getElementById("profileModal").style.display = "none";
}

// Mobile Navigation
document.addEventListener("DOMContentLoaded", function () {
  // Mobile nav toggle
  const mobileNavToggle = document.querySelector(".mobile-nav-toggle");
  const sidebar = document.querySelector(".dashboard-side");

  if (mobileNavToggle && sidebar) {
    mobileNavToggle.addEventListener("click", function () {
      sidebar.classList.toggle("active");
      this.classList.toggle("active");
    });
  }

  // Close sidebar when clicking outside
  document.addEventListener("click", function (e) {
    if (sidebar && sidebar.classList.contains("active")) {
      if (!sidebar.contains(e.target) && !mobileNavToggle.contains(e.target)) {
        sidebar.classList.remove("active");
        mobileNavToggle.classList.remove("active");
      }
    }
  });

  // Optional: Close sidebar when pressing Escape
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && sidebar && sidebar.classList.contains("active")) {
      sidebar.classList.remove("active");
      mobileNavToggle.classList.remove("active");
    }
  });

  // Optional: Image preview
  const input = document.getElementById("profileImage");
  if (input) {
    input.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
          document.getElementById(
            "profilePic"
          ).style.backgroundImage = `url('${ev.target.result}')`;
          document.getElementById("profilePic").textContent = "";
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Mobile Navigation Toggle
  const menuToggle = document.querySelector(".menu-toggle");
  if (menuToggle) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.toggle("active");
    });
  }

  // Close sidebar when clicking outside
  document.addEventListener("click", function (e) {
    if (sidebar && sidebar.classList.contains("active")) {
      if (!sidebar.contains(e.target) && e.target !== menuToggle) {
        sidebar.classList.remove("active");
      }
    }
  });

  // Close sidebar when pressing escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && sidebar && sidebar.classList.contains("active")) {
      sidebar.classList.remove("active");
    }
  });

  // Optional: Highlight current page in menu
  const currentPage = window.location.pathname.split("/").pop().split(".")[0];
  const menuLinks = document.querySelectorAll(".menu li");

  menuLinks.forEach((link) => {
    const href = link.querySelector("a").getAttribute("href").split(".")[0];
    if (href === currentPage) {
      link.classList.add("active");
    }
  });
});
