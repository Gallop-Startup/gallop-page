document.addEventListener("DOMContentLoaded", () => {
    const sections = document.querySelectorAll("section[id]");
    const navLinks = document.querySelectorAll("nav ul li a");
    const heroSection = document.querySelector(".hero");

    function activateNavLink() {
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const heroBottom = heroSection.offsetTop + heroSection.offsetHeight;

        // If in the hero section, don't activate any nav links
        if (scrollY < heroBottom - 100) {
            navLinks.forEach(link => link.classList.remove("active"));
            return;
        }

        // If scrolled to the bottom of the document, activate the last nav link
        if (windowHeight + scrollY >= documentHeight - 2) {
            navLinks.forEach(link => {
                link.classList.remove("active");
                if (link.getAttribute("href") === "#contact") {
                    link.classList.add("active");
                }
            });
            return;
        }

        // Loop through each section to determine which one is currently in view
        sections.forEach(current => {
            const sectionHeight = current.offsetHeight;
            const sectionTop = current.offsetTop - 100; // Adjust for fixed navbar height
            const sectionId = current.getAttribute("id");

            if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove("active");
                    if (link.getAttribute("href") === `#${sectionId}`) {
                        link.classList.add("active");
                    }
                });
            }
        });
    }

    window.addEventListener("scroll", activateNavLink);
});