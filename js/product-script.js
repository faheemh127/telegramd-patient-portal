class HLDProduct {
  constructor() {
    this.products = document.querySelectorAll("[data-product]");
    this.init();
  }

  init() {
    this.products.forEach((product) => {
      const buttons = product.querySelectorAll(".tab-button");
      const contents = product.querySelectorAll(".tab-content");

      buttons.forEach((button) => {
        button.addEventListener("click", () => {
          const target = button.dataset.tab;

          buttons.forEach((btn) => btn.classList.remove("active"));
          contents.forEach((content) => content.classList.remove("active"));

          button.classList.add("active");
          product
            .querySelector(`[data-content="${target}"]`)
            .classList.add("active");
        });
      });
    });

    this.initFAQ();
    this.initReviewsSlider();
    this.initFloatingCTA();
    this.hldInitGLPSlider();
    this.hldInitLabModal();
  } // init function ends

  hldInitLabModal() {
    const openBtn = document.querySelector("[data-hld-open-modal]");
    const modal = document.querySelector("[data-hld-modal]");
    const closeBtn = document.querySelector("[data-hld-close-modal]");

    if (!openBtn || !modal || !closeBtn) return;

    openBtn.addEventListener("click", () => {
      modal.classList.add("is-active");
      document.body.style.overflow = "hidden";
    });

    closeBtn.addEventListener("click", closeModal);
    modal.addEventListener("click", (e) => {
      if (e.target === modal) closeModal();
    });

    function closeModal() {
      modal.classList.remove("is-active");
      document.body.style.overflow = "";
    }
  }

  hldInitGLPSlider() {
    const track = document.querySelector(".hld-slider-track");
    const slides = document.querySelectorAll(".hld-slide");
    const prevBtn = document.querySelector(".hld-prev");
    const nextBtn = document.querySelector(".hld-next");

    if (!track || slides.length === 0) return;

    let currentIndex = 0;
    let slidesToShow = window.innerWidth <= 768 ? 1 : 3;
    let autoSlideInterval;

    function updateSlider() {
      const slideWidth = slides[0].offsetWidth + 24;
      track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    }

    function nextSlide() {
      if (currentIndex < slides.length - slidesToShow) {
        currentIndex++;
      } else {
        currentIndex = 0;
      }
      updateSlider();
    }

    function prevSlide() {
      if (currentIndex > 0) {
        currentIndex--;
      } else {
        currentIndex = slides.length - slidesToShow;
      }
      updateSlider();
    }

    function startAutoSlide() {
      autoSlideInterval = setInterval(nextSlide, 4000);
    }

    function stopAutoSlide() {
      clearInterval(autoSlideInterval);
    }

    window.addEventListener("resize", () => {
      slidesToShow = window.innerWidth <= 768 ? 1 : 3;
      updateSlider();
    });

    nextBtn.addEventListener("click", () => {
      stopAutoSlide();
      nextSlide();
      startAutoSlide();
    });

    prevBtn.addEventListener("click", () => {
      stopAutoSlide();
      prevSlide();
      startAutoSlide();
    });

    startAutoSlide();
  }

  /* ADD THIS METHOD INSIDE YOUR EXISTING HLDProduct CLASS */
  initFloatingCTA() {
    const cta = document.querySelector(".hld-floating-cta");
    if (!cta) return;

    let lastScrollY = window.scrollY;

    window.addEventListener("scroll", () => {
      const currentScrollY = window.scrollY;

      if (currentScrollY > 120 && currentScrollY > lastScrollY) {
        // scrolling down → show
        cta.classList.add("is-visible");
        cta.classList.remove("is-hidden");
      } else if (currentScrollY < lastScrollY) {
        // scrolling up → hide (animate down)
        cta.classList.add("is-hidden");
        cta.classList.remove("is-visible");
      }

      if (currentScrollY < 80) {
        cta.classList.remove("is-visible", "is-hidden");
      }

      lastScrollY = currentScrollY;
    });
  }

  /* PUT THIS METHOD INSIDE YOUR EXISTING HLDProduct CLASS */
  initReviewsSlider() {
    const slider = document.querySelector("[data-hld-reviews-slider]");
    if (!slider) return;

    const cards = slider.children;
    const prevBtn = document.querySelector(".hld-reviews__arrow--left");
    const nextBtn = document.querySelector(".hld-reviews__arrow--right");

    let index = 0;

    const getVisibleCards = () => (window.innerWidth <= 768 ? 1 : 3);

    const updateSlider = () => {
      const cardWidth = cards[0].offsetWidth + 24;
      slider.style.transform = `translateX(-${index * cardWidth}px)`;
    };

    nextBtn.addEventListener("click", () => {
      const maxIndex = cards.length - getVisibleCards();
      index = index >= maxIndex ? 0 : index + 1;
      updateSlider();
    });

    prevBtn.addEventListener("click", () => {
      const maxIndex = cards.length - getVisibleCards();
      index = index <= 0 ? maxIndex : index - 1;
      updateSlider();
    });

    // Auto slide (mobile only)
    let autoSlide = setInterval(() => {
      if (window.innerWidth <= 768) {
        nextBtn.click();
      }
    }, 3500);

    window.addEventListener("resize", updateSlider);
  }

  /* PUT THIS METHOD INSIDE YOUR EXISTING HLDProduct CLASS */
  initFAQ() {
    const faqItems = document.querySelectorAll(".hld-faq-item");

    faqItems.forEach((item) => {
      const button = item.querySelector(".hld-faq-question");
      const icon = item.querySelector(".hld-faq-icon");

      button.addEventListener("click", () => {
        const isOpen = item.classList.contains("is-open");

        item.classList.toggle("is-open");
        icon.textContent = isOpen ? "+" : "×";
      });
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  new HLDProduct();
});
