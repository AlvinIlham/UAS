function openProfileModal() {
  const modal = document.getElementById("profileModal");
  if (modal) {
    modal.style.display = "block";
    document.body.style.overflow = "hidden";
  }
}

function closeProfileModal() {
  const modal = document.getElementById("profileModal");
  if (modal) {
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  }
}

// Close modal when clicking outside
window.onclick = function (event) {
  const modal = document.getElementById("profileModal");
  if (event.target === modal) {
    closeProfileModal();
  }
};

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
  const mainContent = document.querySelector(".main-content");

  if (menuToggle && sidebar && mainContent) {
    menuToggle.addEventListener("click", function () {
      sidebar.classList.toggle("active");
      mainContent.classList.toggle("sidebar-active");
    });

    // Close sidebar when clicking outside
    document.addEventListener("click", function (event) {
      const isClickInsideSidebar = sidebar.contains(event.target);
      const isClickOnMenuToggle = menuToggle.contains(event.target);

      if (
        !isClickInsideSidebar &&
        !isClickOnMenuToggle &&
        window.innerWidth <= 768
      ) {
        sidebar.classList.remove("active");
        mainContent.classList.remove("sidebar-active");
      }
    });

    // Handle window resize
    window.addEventListener("resize", function () {
      if (window.innerWidth > 768) {
        sidebar.classList.remove("active");
        mainContent.classList.remove("sidebar-active");
      }
    });
  }

  // Optional: Highlight current page in menu
  const currentPage = window.location.pathname.split("/").pop().split(".")[0];
  const menuLinks = document.querySelectorAll(".menu li");

  menuLinks.forEach((link) => {
    const href = link.querySelector("a").getAttribute("href").split(".")[0];
    if (href === currentPage) {
      link.classList.add("active");
    }
  });

  // Add smooth hover effect for table rows
  const tableRows = document.querySelectorAll(".data-table tbody tr");
  tableRows.forEach((row) => {
    row.addEventListener("mouseenter", function () {
      this.style.backgroundColor = "rgba(255, 255, 255, 0.05)";
      this.style.transition = "background-color var(--transition-speed)";
    });
    row.addEventListener("mouseleave", function () {
      this.style.backgroundColor = "";
    });
  });

  // Password visibility toggle
  const toggleButtons = document.querySelectorAll(".toggle-password");

  toggleButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const input = this.previousElementSibling;

      // Toggle password visibility
      if (input.type === "password") {
        input.type = "text";
        this.textContent = "visibility";
      } else {
        input.type = "password";
        this.textContent = "visibility_off";
      }

      // Add focus effect
      input.focus();
    });
  });

  // Add animation to auth container
  const authContainer = document.querySelector(".auth-container");
  if (authContainer) {
    authContainer.style.opacity = "0";
    authContainer.style.transform = "translateY(-10px)";

    setTimeout(() => {
      authContainer.style.transition = "all 0.3s ease-out";
      authContainer.style.opacity = "1";
      authContainer.style.transform = "translateY(0)";
    }, 100);
  }

  // Profile image preview
  const profileInput = document.getElementById("profile_image");
  if (profileInput) {
    profileInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = document.querySelector(".profile-image img");
          if (img) {
            img.src = e.target.result;
          } else {
            const newImg = document.createElement("img");
            newImg.src = e.target.result;
            const defaultAvatar = document.querySelector(".default-avatar");
            if (defaultAvatar) {
              defaultAvatar.replaceWith(newImg);
            }
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Form validation
  const forms = document.querySelectorAll(".edit-form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      const inputs = form.querySelectorAll("input[required]");
      let isValid = true;

      inputs.forEach((input) => {
        if (!input.value.trim()) {
          isValid = false;
          input.classList.add("input-error");
          const group = input.closest(".input-group");
          if (group) {
            let error = group.querySelector(".error-message");
            if (!error) {
              error = document.createElement("div");
              error.className = "error-message";
              group.appendChild(error);
            }
            error.textContent = "This field is required";
          }
        } else {
          input.classList.remove("input-error");
          const error = input
            .closest(".input-group")
            ?.querySelector(".error-message");
          if (error) {
            error.remove();
          }
        }
      });

      if (!isValid) {
        e.preventDefault();
      }
    });
  });

  function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling;

    if (input.type === "password") {
      input.type = "text";
      icon.textContent = "visibility";
    } else {
      input.type = "password";
      icon.textContent = "visibility_off";
    }
  }

  // Form validation
  document.addEventListener("DOMContentLoaded", function () {
    const profileForm = document.querySelector(".profile-form");
    if (profileForm) {
      profileForm.addEventListener("submit", function (e) {
        const newPassword = document.getElementById("new_password").value;
        const confirmPassword =
          document.getElementById("confirm_password").value;

        if (newPassword && newPassword !== confirmPassword) {
          e.preventDefault();
          alert("Password baru dan konfirmasi password tidak cocok!");
          return;
        }

        if (newPassword && newPassword.length < 6) {
          e.preventDefault();
          alert("Password baru harus minimal 6 karakter!");
          return;
        }
      });
    }
  });
});
