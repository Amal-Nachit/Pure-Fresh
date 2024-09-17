document.addEventListener("DOMContentLoaded", function () {
  // Gérer l'approbation des annonces
  document.querySelectorAll(".approuver-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const annonceId = this.dataset.id;
      console.log("Annonce ID:", annonceId);

      fetch(`/accepter/${annonceId}`, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => {
          if (response.ok) {
            // document.getElementById(`annonce-${annonceId}`).remove();
            fetchAnnonces();
          }
        })
        .catch((error) => console.error("Erreur:", error));
    });
  });

  // Gérer le refus des annonces
  document.querySelectorAll(".refuser-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const annonceId = this.dataset.id;
      fetch(`/refuser/${annonceId}`, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => {
          if (response.ok) {
            document.getElementById(`annonce-${annonceId}`).remove();
          }
        })
        .catch((error) => console.error("Erreur:", error));
    });
  });
});

function checkIfEmpty() {
  const annonceList = document.getElementById("annonce-list");
  if (!annonceList.querySelector("tr")) {
    annonceList.innerHTML =
      '<p class="text-gray-600">Aucune annonce à approuver.</p>';
  }
}

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".approuver-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const annonceId = this.dataset.id;
      fetch(`/accepter/${annonceId}`, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => {
          if (response.ok) {
            const selectedElement = document.getElementById(
              `annonce-${annonceId}`
            );
            if (selectedElement) {
              selectedElement.remove();
            }

            checkIfEmpty();
          }
        })
        .catch((error) => console.error("Erreur:", error));
    });
  });

  document.querySelectorAll(".refuser-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const annonceId = this.dataset.id;
      fetch(`/refuser/${annonceId}`, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => {
          if (response.ok) {
            document.getElementById(`annonce-${annonceId}`).remove();
            checkIfEmpty();
          }
        })
        .catch((error) => console.error("Erreur:", error));
    });
  });
});

function fetchAnnonces() {
  fetch("/admin/annonces")
    .then(async (response) => await response.json())
    .then((data) => {
      const nbAnnoncesPubliees = document.querySelector(".annonceCount");
      nbAnnoncesPubliees.innerHTML = `Annonces publiées ${data.nbAnnoncesPubliees}`;
    })
    .catch((error) => console.error("Erreur:", error));
}

fetchAnnonces();
setInterval(fetchAnnonces, 30000);


