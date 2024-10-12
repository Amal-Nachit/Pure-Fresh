function activateIcon(button) {
  const buttons = document.querySelectorAll(".group button");
  buttons.forEach((btn) => {
    btn.classList.remove("active");
    const icon = btn.querySelector("img, svg");
    const span = btn.querySelector("span");
    icon.classList.remove("translate-y-2");
    span.classList.remove("opacity-100");
  });

  button.classList.add("active");
  const icon = button.querySelector("img, svg");
  const span = button.querySelector("span");
  icon.classList.add("translate-y-2");
  span.classList.add("opacity-100");
}

document.addEventListener("DOMContentLoaded", function () {
  const activeButton = document.querySelector(".group button.active");
  if (activeButton) {
    activateIcon(activeButton);
  }
});
