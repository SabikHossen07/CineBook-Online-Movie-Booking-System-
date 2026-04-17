const typingText = document.getElementById("typing-text");

if (typingText) {
  const texts = [
    "Experience Cinema Like Never Before",
    "Book Your Favorite Movie Tickets",
    "Your Perfect Movie Night Starts Here"
  ];

  let textIndex = 0;
  let charIndex = 0;
  let isDeleting = false;

  function typeEffect() {
    const currentText = texts[textIndex];

    if (isDeleting) {
      typingText.textContent = currentText.substring(0, charIndex - 1);
      charIndex--;
    } else {
      typingText.textContent = currentText.substring(0, charIndex + 1);
      charIndex++;
    }

    let speed = isDeleting ? 40 : 80;

    if (!isDeleting && charIndex === currentText.length) {
      speed = 1500;
      isDeleting = true;
    } else if (isDeleting && charIndex === 0) {
      isDeleting = false;
      textIndex = (textIndex + 1) % texts.length;
      speed = 400;
    }

    setTimeout(typeEffect, speed);
  }

  window.addEventListener("load", typeEffect);
}

function filterMovies(category, clickedButton) {
  const cards = document.querySelectorAll(".movie-card");
  const searchInput = document.getElementById("movieSearch");
  const searchText = searchInput ? searchInput.value.toLowerCase() : "";
  const noResults = document.getElementById("noResults");

  document.querySelectorAll(".tab-item").forEach(tab => {
    tab.classList.remove("active-tab");
  });

  if (clickedButton) {
    clickedButton.classList.add("active-tab");
  }

  let visibleCount = 0;

  cards.forEach(card => {
    const title = card.querySelector("h3").textContent.toLowerCase();
    const cardCategory = card.getAttribute("data-category");

    const categoryMatch = category === "all" || cardCategory === category;
    const searchMatch = title.includes(searchText);

    if (categoryMatch && searchMatch) {
      card.style.display = "block";
      visibleCount++;
    } else {
      card.style.display = "none";
    }
  });

  if (noResults) {
    noResults.style.display = visibleCount === 0 ? "block" : "none";
  }
}

function searchMovies() {
  const activeTab = document.querySelector(".tab-item.active-tab");
  let currentCategory = "all";

  if (activeTab) {
    const tabText = activeTab.textContent.trim();

    if (tabText === "Coming Soon") {
      currentCategory = "coming";
    } else if (tabText === "Top Rated") {
      currentCategory = "top";
    } else {
      currentCategory = "all";
    }
  }

  filterMovies(currentCategory, activeTab);
}