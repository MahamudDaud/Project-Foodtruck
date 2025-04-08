const burger = document.querySelector(".burger");
const nav = document.querySelector("#nav-links");

burger.addEventListener("click", () => {
    nav.classList.toggle("active");
    burger.classList.toggle("toggle");
});

document.getElementById('call-btn').addEventListener('click', function() {
    window.location.href = 'tel:+91-885-521-8571';
});