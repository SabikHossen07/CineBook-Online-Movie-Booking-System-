const typingText = document.getElementById("typing-text");

if (typingText) {
  const text = "Your Perfect Movie Night Starts Here";
  let index = 0;

  function typeEffect() {
    if (index < text.length) {
      typingText.textContent += text.charAt(index);
      index++;
      setTimeout(typeEffect, 80);
    }
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