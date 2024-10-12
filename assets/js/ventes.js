document.addEventListener("DOMContentLoaded", function () {
  const statusColors = {
    1: {
      bg: "bg-yellow-500",
      text: "text-yellow-900",
      border: "border-yellow-500",
    },
    2: { bg: "bg-blue-500", text: "text-blue-900", border: "border-blue-500" },
    3: {
      bg: "bg-green-500",
      text: "text-green-900",
      border: "border-green-500",
    },
    4: { bg: "bg-red-500", text: "text-red-900", border: "border-red-500" },
  };

  function updateCardAppearance(card, statusId, statusText) {
    const statusBadge = card.querySelector("[data-status-badge]");
    const borderDiv = card.querySelector("[data-border]");

    Object.values(statusColors).forEach((colors) => {
      statusBadge.classList.remove(colors.bg, colors.text);
      borderDiv.classList.remove(colors.border);
    });

    if (statusColors[statusId]) {
      statusBadge.classList.add(
        statusColors[statusId].bg,
        statusColors[statusId].text
      );
      borderDiv.classList.add(statusColors[statusId].border);
    }

    statusBadge.textContent = statusText;
    card.dataset.statutId = statusId;
  }

  function showNotification(message, type = "success") {
    const notification = document.createElement("div");
    notification.textContent = message;
    notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-md text-white ${
      type === "success" ? "bg-green-500" : "bg-red-500"
    } transition-opacity duration-300 opacity-0`;
    document.body.appendChild(notification);

    setTimeout(() => notification.classList.remove("opacity-0"), 100);
    setTimeout(() => {
      notification.classList.add("opacity-0");
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  document.querySelectorAll(".vente-card").forEach((card) => {
    const statusId = card.dataset.statutId;
    const statusText = card
      .querySelector("[data-status-badge]")
      .textContent.trim();
    updateCardAppearance(card, statusId, statusText);
  });

  document.querySelectorAll(".update-status-form").forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(form);
      const card = form.closest(".vente-card");
      const statusSelect = form.querySelector("select");
      const newStatusId = statusSelect.value;
      const newStatusText =
        statusSelect.options[statusSelect.selectedIndex].text;

      fetch(form.action, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            updateCardAppearance(card, data.newStatusId, data.newStatusText);
            card.classList.add("scale-105");
            setTimeout(() => card.classList.remove("scale-105"), 300);
            showNotification(data.message, "success");
          } else {
            showNotification(data.message, "error");
          }
        })
        .catch((error) => {
          showNotification("Une erreur est survenue", "error");
        });
    });
  });
});
