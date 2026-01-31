// --- State Management ---
let currentUser = null;

// --- Dummy Data ---
let posts = [
  {
    id: 1,
    title: "How to Bake Cookies",
    uploader: "GrandmaJane",
    date: "2023-10-01",
    targetAge: 6,
    content:
      "A simple recipe for delicious chocolate chip cookies. Mix flour, sugar...",
  },
  {
    id: 2,
    title: "Advanced Python Coding",
    uploader: "TechWhiz_Kid",
    date: "2023-10-02",
    targetAge: 12,
    content: "Learn how to use Loops and Functions to build a calculator app.",
  },
  {
    id: 3,
    title: "Changing a Car Tire",
    uploader: "MechanicMike",
    date: "2023-10-03",
    targetAge: 16,
    content: "Safety first! Jack up the car and loosen the lug nuts...",
  },
];

// --- Initialization ---
document.addEventListener("DOMContentLoaded", () => {
  // Check if bootstrap is loaded
  if (typeof bootstrap !== "undefined") {
    const loginModal = new bootstrap.Modal(
      document.getElementById("loginModal")
    );
    loginModal.show();
  } else {
    console.error("Bootstrap JS not loaded");
  }
});

// --- Login Functionality ---
const loginForm = document.getElementById("loginForm");
if (loginForm) {
  loginForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const name = document.getElementById("loginName").value;
    const age = parseInt(document.getElementById("loginAge").value);

    if (name && age) {
      currentUser = { name, age };
      // Get the existing modal instance to hide it
      const modalElement = document.getElementById("loginModal");
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (modalInstance) {
        modalInstance.hide();
      }

      initApp();
    }
  });
}

// --- Main Logic ---
function initApp() {
  document.getElementById(
    "userDisplay"
  ).innerText = `Hi, ${currentUser.name} (${currentUser.age}yo)`;
  document.getElementById("btnLogout").classList.remove("hidden");
  document.getElementById(
    "welcomeText"
  ).innerText = `Browsing content suitable for age ${currentUser.age}+`;

  // RULE: Under age 10 can't upload
  const canUpload = currentUser.age >= 10 ? true : false;

  if (canUpload) {
    document.getElementById("btnUploadModal").classList.remove("hidden");
  } else {
    document.getElementById("btnUploadModal").classList.add("hidden");
  }

  renderPosts();
}

// --- Render Posts ---
function renderPosts() {
  const container = document.getElementById("postsContainer");
  container.innerHTML = "";

  posts.forEach((post) => {
    // RULE: Filter logic (Don't show older posts to young kids)
    if (currentUser.age < 10 && post.targetAge > 10) {
      return;
    }

    let badgeClass = post.targetAge < 18 ? "badge-kid" : "badge-adult";
    let badgeText = post.targetAge < 18 ? "Kids & Teens" : "Adult Skills";

    const html = `
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">
                        <small class="text-muted">By @${post.uploader}</small>
                        <span class="badge ${badgeClass}">${badgeText}</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">${post.title}</h5>
                        <p class="card-text">${post.content}</p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <small class="text-muted">Target Age: ${post.targetAge}+ | Date: ${post.date}</small>
                    </div>
                </div>
            </div>
        `;
    container.innerHTML += html;
  });

  if (container.innerHTML === "") {
    container.innerHTML = `<div class="col-12 text-center text-muted"><p>No posts available for your age group.</p></div>`;
  }
}

// --- Upload Functionality ---
const uploadForm = document.getElementById("uploadForm");
if (uploadForm) {
  uploadForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Security check
    if (currentUser.age < 10) {
      alert("You are too young to upload!");
      return;
    }

    const newPost = {
      id: posts.length + 1,
      title: document.getElementById("postTitle").value,
      uploader: currentUser.name,
      date: new Date().toISOString().split("T")[0],
      targetAge: parseInt(document.getElementById("postTargetAge").value),
      content: document.getElementById("postContent").value,
    };

    posts.unshift(newPost);
    renderPosts();

    const modalElement = document.getElementById("uploadModal");
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
      modalInstance.hide();
    }

    e.target.reset();
  });
}

function logout() {
  location.reload();
}
